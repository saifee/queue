import { Pool, PoolClient, DatabaseError } from 'pg';

/**
 * Shared PostgreSQL pool with sensible production defaults.
 */
export const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  max: 20,
  idleTimeoutMillis: 30_000,
  connectionTimeoutMillis: 5_000,
  statement_timeout: 15_000, // 15s statement timeout (ms) – prevents runaway queries
  query_timeout: 20_000,
});

/** Custom error class for timeout / connection issues */
export class DatabaseTimeoutError extends Error {
  constructor(message = 'Database operation timed out') {
    super(message);
    this.name = 'DatabaseTimeoutError';
  }
}

export class DatabaseConnectionError extends Error {
  constructor(message = 'Could not connect to the database') {
    super(message);
    this.name = 'DatabaseConnectionError';
  }
}

function isTimeoutError(err: unknown): boolean {
  if (!err || typeof err !== 'object') return false;
  const e = err as DatabaseError & { code?: string; message?: string };
  // PostgreSQL cancellation / timeout codes + node-pg timeout messages
  return (
    e.code === '57014' || // query_canceled
    e.code === '57P01' || // admin_shutdown
    e.code === '08006' || // connection_failure
    e.code === '08001' || // sqlclient_unable_to_establish_sqlconnection
    /timeout|timed out|canceling statement/i.test(e.message ?? '')
  );
}

/**
 * Run a function inside a transaction with the correct tenant context.
 * Sets RLS variables so all subsequent queries are automatically scoped.
 * Handles connection & statement timeouts gracefully.
 */
export async function withTenant<T>(
  tenantId: string,
  isSuperAdmin: boolean,
  fn: (client: PoolClient) => Promise<T>,
  options: { statementTimeoutMs?: number } = {},
): Promise<T> {
  let client: PoolClient;

  try {
    client = await pool.connect();
  } catch (err) {
    if (isTimeoutError(err)) {
      throw new DatabaseConnectionError(
        'Database connection timed out. Please try again.',
      );
    }
    throw err;
  }

  try {
    await client.query('BEGIN');

    // Optional per-transaction statement timeout override
    if (options.statementTimeoutMs) {
      await client.query(`SET LOCAL statement_timeout = $1`, [
        options.statementTimeoutMs,
      ]);
    }

    // Critical for RLS
    await client.query(`SET LOCAL app.current_tenant_id = $1`, [tenantId]);
    await client.query(`SET LOCAL app.is_super_admin = $1`, [
      isSuperAdmin ? 'true' : 'false',
    ]);

    const result = await fn(client);

    await client.query('COMMIT');
    return result;
  } catch (err) {
    try {
      await client.query('ROLLBACK');
    } catch {
      // ignore rollback errors
    }

    if (isTimeoutError(err)) {
      throw new DatabaseTimeoutError(
        'The database operation took too long and was cancelled. Please try again.',
      );
    }
    throw err;
  } finally {
    client.release();
  }
}
