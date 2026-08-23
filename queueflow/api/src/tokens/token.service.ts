import { PoolClient } from 'pg';
import { withTenant, DatabaseTimeoutError, DatabaseConnectionError } from '../common/database';
import { EventEmitter } from '../events/event-emitter';
import { CallNextService, CalledToken, CallNextInput } from './call-next.service';

export type TokenStatus =
  | 'pending'
  | 'called'
  | 'serving'
  | 'served'
  | 'cancelled'
  | 'no_show';

export interface TokenActionResult {
  id: string;
  tokenNumber: string;
  status: TokenStatus;
  counterId: string | null;
  calledAt: string | null;
  servingAt: string | null;
  completedAt: string | null;
  waitDurationSeconds: number | null;
  serviceDurationSeconds: number | null;
  notes: string | null;
}

export interface QueueItem {
  id: string;
  tokenNumber: string;
  status: TokenStatus;
  priority: string;
  priorityScore: number;
  customerName: string | null;
  serviceName: string;
  serviceCode: string;
  issuedAt: string;
  waitMinutes: number;
  callAttempts: number;
}

/**
 * Full token lifecycle service.
 * Extends the original CallNextService with the remaining operator actions.
 */
export class TokenService {
  private readonly callNextService: CallNextService;

  constructor(private readonly events: EventEmitter) {
    this.callNextService = new CallNextService(events);
  }

  // ---------------------------------------------------------------------------
  // Call / Recall (delegated)
  // ---------------------------------------------------------------------------

  callNext(input: CallNextInput): Promise<CalledToken | null> {
    return this.callNextService.callNext(input);
  }

  recall(tenantId: string, tokenId: string, operatorId: string) {
    return this.callNextService.recall(tenantId, tokenId, operatorId);
  }

  // ---------------------------------------------------------------------------
  // Start Serving
  // ---------------------------------------------------------------------------

  async startServing(
    tenantId: string,
    tokenId: string,
    operatorId: string,
  ): Promise<TokenActionResult> {
    return withTenant(tenantId, false, async (client) => {
      const token = await this.lockToken(client, tokenId, ['called']);

      const now = new Date();
      await client.query(
        `
        UPDATE tokens
        SET status = 'serving',
            serving_at = $1,
            updated_at = $1
        WHERE id = $2
        `,
        [now, tokenId],
      );

      const result = this.toActionResult({ ...token, status: 'serving', serving_at: now });

      this.events.emit('token.serving', {
        tenantId,
        locationId: token.location_id,
        token: result,
        operatorId,
      });

      return result;
    });
  }

  // ---------------------------------------------------------------------------
  // Complete (Served)
  // ---------------------------------------------------------------------------

  async complete(
    tenantId: string,
    tokenId: string,
    operatorId: string,
    notes?: string,
  ): Promise<TokenActionResult> {
    return withTenant(tenantId, false, async (client) => {
      const token = await this.lockToken(client, tokenId, ['serving', 'called']);

      const now = new Date();
      let serviceDuration: number | null = null;

      if (token.serving_at) {
        serviceDuration = Math.floor(
          (now.getTime() - new Date(token.serving_at).getTime()) / 1000,
        );
      }

      await client.query(
        `
        UPDATE tokens
        SET status = 'served',
            completed_at = $1,
            service_duration_seconds = $2,
            notes = COALESCE($3, notes),
            updated_at = $1
        WHERE id = $4
        `,
        [now, serviceDuration, notes ?? null, tokenId],
      );

      // Clear current_token_id on the counter
      if (token.counter_id) {
        await client.query(
          `
          UPDATE counters
          SET current_token_id = NULL, updated_at = $1
          WHERE id = $2 AND current_token_id = $3
          `,
          [now, token.counter_id, tokenId],
        );
      }

      // Increment customer visit count
      if (token.customer_id) {
        await client.query(
          `
          UPDATE customers
          SET total_visits = total_visits + 1, updated_at = $1
          WHERE id = $2
          `,
          [now, token.customer_id],
        );
      }

      const result = this.toActionResult({
        ...token,
        status: 'served',
        completed_at: now,
        service_duration_seconds: serviceDuration,
        notes: notes ?? token.notes,
      });

      this.events.emit('token.served', {
        tenantId,
        locationId: token.location_id,
        token: result,
        operatorId,
      });

      return result;
    });
  }

  // ---------------------------------------------------------------------------
  // No-Show
  // ---------------------------------------------------------------------------

  async markNoShow(
    tenantId: string,
    tokenId: string,
    operatorId: string,
  ): Promise<TokenActionResult> {
    return withTenant(tenantId, false, async (client) => {
      const token = await this.lockToken(client, tokenId, ['called', 'serving']);

      const now = new Date();
      await client.query(
        `
        UPDATE tokens
        SET status = 'no_show',
            completed_at = $1,
            updated_at = $1
        WHERE id = $2
        `,
        [now, tokenId],
      );

      if (token.counter_id) {
        await client.query(
          `
          UPDATE counters
          SET current_token_id = NULL, updated_at = $1
          WHERE id = $2 AND current_token_id = $3
          `,
          [now, token.counter_id, tokenId],
        );
      }

      const result = this.toActionResult({
        ...token,
        status: 'no_show',
        completed_at: now,
      });

      this.events.emit('token.no_show', {
        tenantId,
        locationId: token.location_id,
        token: result,
        operatorId,
      });

      return result;
    });
  }

  // ---------------------------------------------------------------------------
  // Cancel
  // ---------------------------------------------------------------------------

  async cancel(
    tenantId: string,
    tokenId: string,
    operatorId: string,
    reason?: string,
  ): Promise<TokenActionResult> {
    return withTenant(tenantId, false, async (client) => {
      const token = await this.lockToken(client, tokenId, [
        'pending',
        'called',
        'serving',
      ]);

      const now = new Date();
      const notes = reason
        ? `${token.notes ? token.notes + ' | ' : ''}Cancelled: ${reason}`
        : token.notes;

      await client.query(
        `
        UPDATE tokens
        SET status = 'cancelled',
            completed_at = $1,
            notes = $2,
            updated_at = $1
        WHERE id = $3
        `,
        [now, notes, tokenId],
      );

      if (token.counter_id) {
        await client.query(
          `
          UPDATE counters
          SET current_token_id = NULL, updated_at = $1
          WHERE id = $2 AND current_token_id = $3
          `,
          [now, token.counter_id, tokenId],
        );
      }

      const result = this.toActionResult({
        ...token,
        status: 'cancelled',
        completed_at: now,
        notes,
      });

      this.events.emit('token.cancelled', {
        tenantId,
        locationId: token.location_id,
        token: result,
        operatorId,
      });

      return result;
    });
  }

  // ---------------------------------------------------------------------------
  // Current Queue (for operator dashboard)
  // ---------------------------------------------------------------------------

  async getQueue(
    tenantId: string,
    locationId: string,
    serviceIds?: string[],
    limit = 50,
  ): Promise<QueueItem[]> {
    return withTenant(tenantId, false, async (client) => {
      const params: any[] = [tenantId, locationId];
      let serviceFilter = '';

      if (serviceIds && serviceIds.length > 0) {
        params.push(serviceIds);
        serviceFilter = `AND t.service_id = ANY($${params.length}::uuid[])`;
      }

      params.push(limit);

      const res = await client.query(
        `
        SELECT
          t.id,
          t.token_number,
          t.status,
          t.priority,
          t.priority_score,
          t.issued_at,
          t.call_attempts,
          c.name AS customer_name,
          s.name AS service_name,
          s.code AS service_code,
          EXTRACT(EPOCH FROM (now() - t.issued_at)) / 60 AS wait_minutes
        FROM tokens t
        LEFT JOIN customers c ON c.id = t.customer_id
        JOIN services s ON s.id = t.service_id
        WHERE t.tenant_id = $1
          AND t.location_id = $2
          AND t.status IN ('pending', 'called')
          ${serviceFilter}
        ORDER BY t.priority_score DESC, t.issued_at ASC
        LIMIT $${params.length}
        `,
        params,
      );

      return res.rows.map((r) => ({
        id: r.id,
        tokenNumber: r.token_number,
        status: r.status,
        priority: r.priority,
        priorityScore: r.priority_score,
        customerName: r.customer_name,
        serviceName: r.service_name,
        serviceCode: r.service_code,
        issuedAt: r.issued_at,
        waitMinutes: Math.floor(r.wait_minutes),
        callAttempts: r.call_attempts,
      }));
    });
  }

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  private async lockToken(
    client: PoolClient,
    tokenId: string,
    allowedStatuses: TokenStatus[],
  ) {
    const res = await client.query(
      `
      SELECT *
      FROM tokens
      WHERE id = $1
        AND status = ANY($2::text[])
      FOR UPDATE
      `,
      [tokenId, allowedStatuses],
    );

    if (res.rowCount === 0) {
      throw new Error(
        `Token not found or not in an allowed state (${allowedStatuses.join(', ')})`,
      );
    }

    return res.rows[0];
  }

  private toActionResult(row: any): TokenActionResult {
    return {
      id: row.id,
      tokenNumber: row.token_number,
      status: row.status,
      counterId: row.counter_id,
      calledAt: row.called_at ? new Date(row.called_at).toISOString() : null,
      servingAt: row.serving_at ? new Date(row.serving_at).toISOString() : null,
      completedAt: row.completed_at
        ? new Date(row.completed_at).toISOString()
        : null,
      waitDurationSeconds: row.wait_duration_seconds,
      serviceDurationSeconds: row.service_duration_seconds,
      notes: row.notes,
    };
  }
}

/** Re-export for convenience */
export { DatabaseTimeoutError, DatabaseConnectionError };
