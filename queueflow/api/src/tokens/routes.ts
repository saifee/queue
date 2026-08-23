import { Router } from 'express';
import { TokenController } from './token.controller';
import { EventEmitter } from '../events/event-emitter';

/**
 * Registers all token / queue operator routes.
 *
 *   app.use('/api/v1/tokens', createTokensRouter(events));
 */
export function createTokensRouter(events: EventEmitter): Router {
  const router = Router();
  const controller = new TokenController(events);

  // Call next customer
  router.post('/call-next', controller.callNext);

  // Current queue for a location / services
  router.get('/queue', controller.getQueue);

  // Token lifecycle actions
  router.post('/:tokenId/recall', controller.recall);
  router.post('/:tokenId/start-serving', controller.startServing);
  router.post('/:tokenId/complete', controller.complete);
  router.post('/:tokenId/no-show', controller.noShow);
  router.post('/:tokenId/cancel', controller.cancel);

  return router;
}
