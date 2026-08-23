type EventHandler = (payload: any) => void | Promise<void>;

/**
 * Lightweight in-process event bus.
 * In production replace with Redis Pub/Sub, NATS, or a proper queue
 * so multiple API instances and the display-board websocket servers stay in sync.
 */
export class EventEmitter {
  private handlers = new Map<string, EventHandler[]>();

  on(event: string, handler: EventHandler) {
    const list = this.handlers.get(event) ?? [];
    list.push(handler);
    this.handlers.set(event, list);
  }

  off(event: string, handler: EventHandler) {
    const list = this.handlers.get(event) ?? [];
    this.handlers.set(
      event,
      list.filter((h) => h !== handler),
    );
  }

  async emit(event: string, payload: any) {
    const list = this.handlers.get(event) ?? [];
    await Promise.allSettled(list.map((h) => h(payload)));
  }
}

/**
 * Example listeners you would register at app bootstrap:
 *
 * events.on('token.called', async (payload) => {
 *   // 1. Push to Display Board via WebSocket / SSE
 *   displayBoardGateway.broadcast(payload.locationId, {
 *     type: 'TOKEN_CALLED',
 *     tokenNumber: payload.token.tokenNumber,
 *     counterName: payload.token.counterName,
 *   });
 *
 *   // 2. Send notification (SMS / WhatsApp / Email)
 *   await notificationService.sendTokenCalled(payload.token);
 *
 *   // 3. Trigger workflow rules engine
 *   await workflowEngine.evaluate('token_called', payload);
 * });
 */
