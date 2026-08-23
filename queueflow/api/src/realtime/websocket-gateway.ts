import { Server as HttpServer } from 'http';
import { WebSocketServer, WebSocket } from 'ws';
import { EventEmitter } from '../events/event-emitter';
import { IncomingMessage } from 'http';
import { parse } from 'url';

interface ClientMeta {
  tenantId: string;
  locationId?: string;
  role: 'display' | 'operator' | 'kiosk' | 'admin';
  userId?: string;
}

/**
 * Lightweight WebSocket gateway for QueueFlow real-time updates.
 *
 * Clients connect with query params:
 *   ws://host/ws?tenantId=...&locationId=...&role=display
 *
 * Events pushed to clients:
 *   - token.called
 *   - token.recalled
 *   - token.serving
 *   - token.served
 *   - token.no_show
 *   - token.cancelled
 *   - queue.updated
 */
export class WebSocketGateway {
  private wss: WebSocketServer;
  private clients = new Map<WebSocket, ClientMeta>();

  constructor(
    httpServer: HttpServer,
    private readonly events: EventEmitter,
    path = '/ws',
  ) {
    this.wss = new WebSocketServer({ server: httpServer, path });

    this.wss.on('connection', (ws, req) => this.onConnection(ws, req));
    this.registerEventHandlers();
  }

  private onConnection(ws: WebSocket, req: IncomingMessage) {
    const { query } = parse(req.url || '', true);
    const tenantId = String(query.tenantId || '');
    const locationId = query.locationId ? String(query.locationId) : undefined;
    const role = (query.role as ClientMeta['role']) || 'display';
    const userId = query.userId ? String(query.userId) : undefined;

    if (!tenantId) {
      ws.close(4001, 'tenantId is required');
      return;
    }

    const meta: ClientMeta = { tenantId, locationId, role, userId };
    this.clients.set(ws, meta);

    ws.send(
      JSON.stringify({
        type: 'connected',
        message: 'QueueFlow real-time connected',
        meta,
      }),
    );

    ws.on('close', () => {
      this.clients.delete(ws);
    });

    ws.on('error', () => {
      this.clients.delete(ws);
    });

    // Optional ping/pong keep-alive
    ws.on('pong', () => {
      /* alive */
    });
  }

  private registerEventHandlers() {
    const forward = (eventName: string) => {
      this.events.on(eventName, (payload) => {
        this.broadcast(payload.tenantId, payload.locationId, {
          type: eventName,
          data: payload,
          timestamp: new Date().toISOString(),
        });
      });
    };

    forward('token.called');
    forward('token.recalled');
    forward('token.serving');
    forward('token.served');
    forward('token.no_show');
    forward('token.cancelled');
  }

  /**
   * Send a message to all clients matching the tenant (and optionally location).
   */
  broadcast(
    tenantId: string,
    locationId: string | undefined,
    message: object,
  ) {
    const payload = JSON.stringify(message);

    for (const [ws, meta] of this.clients.entries()) {
      if (ws.readyState !== WebSocket.OPEN) continue;
      if (meta.tenantId !== tenantId) continue;

      // If client subscribed to a specific location, only send matching events
      if (meta.locationId && locationId && meta.locationId !== locationId) {
        continue;
      }

      try {
        ws.send(payload);
      } catch {
        this.clients.delete(ws);
      }
    }
  }

  /** Number of currently connected clients (useful for health checks) */
  get connectionCount() {
    return this.clients.size;
  }
}
