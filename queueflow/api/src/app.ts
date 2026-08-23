import express from 'express';
import http from 'http';
import { EventEmitter } from './events/event-emitter';
import { createTokensRouter } from './tokens/routes';
import { WebSocketGateway } from './realtime/websocket-gateway';

/**
 * Minimal bootstrap example.
 * In a real project you would add auth middleware, CORS, logging, etc.
 */
export function createApp() {
  const app = express();
  const server = http.createServer(app);
  const events = new EventEmitter();

  app.use(express.json());

  // --- Placeholder auth middleware (replace with real JWT validation) ---
  app.use((req, _res, next) => {
    // Example: extract from headers for demo
    (req as any).tenantId =
      req.headers['x-tenant-id'] || process.env.DEMO_TENANT_ID;
    (req as any).userId =
      req.headers['x-user-id'] || process.env.DEMO_USER_ID;
    next();
  });

  // Token / operator routes
  app.use('/api/v1/tokens', createTokensRouter(events));

  // Health
  app.get('/health', (_req, res) => {
    res.json({ status: 'ok', wsConnections: gateway.connectionCount });
  });

  // Real-time WebSocket
  const gateway = new WebSocketGateway(server, events, '/ws');

  return { app, server, events, gateway };
}

// Allow running directly: `ts-node src/app.ts` or `node dist/app.js`
if (require.main === module) {
  const port = Number(process.env.PORT || 3000);
  const { server } = createApp();
  server.listen(port, () => {
    console.log(`QueueFlow API listening on :${port}`);
    console.log(`WebSocket endpoint: ws://localhost:${port}/ws`);
  });
}
