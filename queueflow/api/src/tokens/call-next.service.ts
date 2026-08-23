import { PoolClient } from 'pg';
import { withTenant } from '../common/database';
import { EventEmitter } from '../events/event-emitter';

export interface CallNextInput {
  tenantId: string;
  counterId: string;
  operatorId: string;
  /** Optional: force a specific service. If omitted, uses the services assigned to the counter. */
  serviceId?: string;
}

export interface CalledToken {
  id: string;
  tokenNumber: string;
  status: string;
  priority: string;
  priorityScore: number;
  customerId: string | null;
  customerName: string | null;
  customerPhone: string | null;
  serviceId: string;
  serviceName: string;
  serviceCode: string;
  locationId: string;
  counterId: string;
  counterName: string;
  issuedAt: string;
  calledAt: string;
  waitDurationSeconds: number;
  callAttempts: number;
}

export class CallNextService {
  constructor(private readonly events: EventEmitter) {}

  /**
   * Call the next highest-priority pending token for the given counter.
   *
   * Concurrency-safe using `FOR UPDATE SKIP LOCKED`.
   * Returns null if the queue is empty.
   */
  async callNext(input: CallNextInput): Promise<CalledToken | null> {
    return withTenant(input.tenantId, false, async (client) => {
      // 1. Load counter + its allowed services
      const counter = await this.getCounter(client, input.counterId);
      if (!counter) {
        throw new Error('Counter not found or inactive');
      }

      const serviceIds =
        input.serviceId != null
          ? [input.serviceId]
          : counter.service_ids;

      if (serviceIds.length === 0) {
        throw new Error('Counter has no services assigned');
      }

      // 2. Select the best next token (priority_score DESC, then oldest)
      //    SKIP LOCKED prevents two operators from grabbing the same token
      const tokenResult = await client.query(
        `
        SELECT
          t.id,
          t.token_number,
          t.status,
          t.priority,
          t.priority_score,
          t.customer_id,
          t.service_id,
          t.location_id,
          t.issued_at,
          t.call_attempts,
          c.name          AS customer_name,
          c.phone         AS customer_phone,
          s.name          AS service_name,
          s.code          AS service_code
        FROM tokens t
        LEFT JOIN customers c ON c.id = t.customer_id
        JOIN services s       ON s.id = t.service_id
        WHERE t.tenant_id   = $1
          AND t.location_id = $2
          AND t.service_id  = ANY($3::uuid[])
          AND t.status      = 'pending'
        ORDER BY t.priority_score DESC, t.issued_at ASC
        LIMIT 1
        FOR UPDATE OF t SKIP LOCKED
        `,
        [input.tenantId, counter.location_id, serviceIds],
      );

      if (tokenResult.rowCount === 0) {
        return null; // Queue empty
      }

      const row = tokenResult.rows[0];
      const now = new Date();
      const waitSeconds = Math.floor(
        (now.getTime() - new Date(row.issued_at).getTime()) / 1000,
      );

      // 3. Update token → called
      await client.query(
        `
        UPDATE tokens
        SET
          status        = 'called',
          called_at     = $1,
          counter_id    = $2,
          call_attempts = call_attempts + 1,
          wait_duration_seconds = $3,
          updated_at    = $1
        WHERE id = $4
        `,
        [now, input.counterId, waitSeconds, row.id],
      );

      // 4. Mark this counter as currently serving this token
      await client.query(
        `
        UPDATE counters
        SET current_token_id = $1, updated_at = $2
        WHERE id = $3
        `,
        [row.id, now, input.counterId],
      );

      const called: CalledToken = {
        id: row.id,
        tokenNumber: row.token_number,
        status: 'called',
        priority: row.priority,
        priorityScore: row.priority_score,
        customerId: row.customer_id,
        customerName: row.customer_name,
        customerPhone: row.customer_phone,
        serviceId: row.service_id,
        serviceName: row.service_name,
        serviceCode: row.service_code,
        locationId: row.location_id,
        counterId: input.counterId,
        counterName: counter.name,
        issuedAt: row.issued_at,
        calledAt: now.toISOString(),
        waitDurationSeconds: waitSeconds,
        callAttempts: row.call_attempts + 1,
      };

      // 5. Emit real-time events (display board + notifications)
      this.events.emit('token.called', {
        tenantId: input.tenantId,
        locationId: row.location_id,
        token: called,
        operatorId: input.operatorId,
      });

      return called;
    });
  }

  /**
   * Re-call a token that was already called but the customer did not show up.
   */
  async recall(
    tenantId: string,
    tokenId: string,
    operatorId: string,
  ): Promise<CalledToken | null> {
    return withTenant(tenantId, false, async (client) => {
      const result = await client.query(
        `
        SELECT
          t.*,
          c.name  AS customer_name,
          c.phone AS customer_phone,
          s.name  AS service_name,
          s.code  AS service_code,
          ctr.name AS counter_name
        FROM tokens t
        LEFT JOIN customers c ON c.id = t.customer_id
        JOIN services s       ON s.id = t.service_id
        LEFT JOIN counters ctr ON ctr.id = t.counter_id
        WHERE t.id = $1
          AND t.status IN ('called', 'no_show')
        FOR UPDATE OF t
        `,
        [tokenId],
      );

      if (result.rowCount === 0) {
        return null;
      }

      const row = result.rows[0];
      const now = new Date();

      await client.query(
        `
        UPDATE tokens
        SET
          status        = 'called',
          called_at     = $1,
          call_attempts = call_attempts + 1,
          updated_at    = $1
        WHERE id = $2
        `,
        [now, tokenId],
      );

      const recalled: CalledToken = {
        id: row.id,
        tokenNumber: row.token_number,
        status: 'called',
        priority: row.priority,
        priorityScore: row.priority_score,
        customerId: row.customer_id,
        customerName: row.customer_name,
        customerPhone: row.customer_phone,
        serviceId: row.service_id,
        serviceName: row.service_name,
        serviceCode: row.service_code,
        locationId: row.location_id,
        counterId: row.counter_id,
        counterName: row.counter_name,
        issuedAt: row.issued_at,
        calledAt: now.toISOString(),
        waitDurationSeconds: row.wait_duration_seconds,
        callAttempts: row.call_attempts + 1,
      };

      this.events.emit('token.recalled', {
        tenantId,
        locationId: row.location_id,
        token: recalled,
        operatorId,
      });

      return recalled;
    });
  }

  private async getCounter(client: PoolClient, counterId: string) {
    const res = await client.query(
      `
      SELECT id, name, location_id, service_ids, is_active
      FROM counters
      WHERE id = $1 AND is_active = true AND deleted_at IS NULL
      `,
      [counterId],
    );
    return res.rows[0] ?? null;
  }
}
