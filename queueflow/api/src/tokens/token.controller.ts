import { Request, Response } from 'express';
import {
  TokenService,
  DatabaseTimeoutError,
  DatabaseConnectionError,
} from './token.service';
import { EventEmitter } from '../events/event-emitter';

/**
 * Express controller covering the full operator token lifecycle.
 */
export class TokenController {
  private readonly service: TokenService;

  constructor(events: EventEmitter) {
    this.service = new TokenService(events);
  }

  // -------------------------------------------------------------------------
  // Helpers
  // -------------------------------------------------------------------------

  private getContext(req: Request) {
    const tenantId = (req as any).tenantId as string | undefined;
    const userId = (req as any).userId as string | undefined;
    return { tenantId, userId };
  }

  private handleError(res: Response, err: unknown) {
    console.error('[TokenController]', err);

    if (err instanceof DatabaseTimeoutError) {
      return res.status(504).json({
        success: false,
        error: err.message,
        code: 'DATABASE_TIMEOUT',
      });
    }

    if (err instanceof DatabaseConnectionError) {
      return res.status(503).json({
        success: false,
        error: err.message,
        code: 'DATABASE_UNAVAILABLE',
      });
    }

    const message = err instanceof Error ? err.message : 'Internal server error';
    const status = message.includes('not found') || message.includes('not in an allowed')
      ? 404
      : 500;

    return res.status(status).json({
      success: false,
      error: message,
    });
  }

  // -------------------------------------------------------------------------
  // POST /call-next
  // -------------------------------------------------------------------------

  callNext = async (req: Request, res: Response) => {
    try {
      const { tenantId, userId } = this.getContext(req);
      if (!tenantId || !userId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const { counterId, serviceId } = req.body;
      if (!counterId) {
        return res.status(400).json({ success: false, error: 'counterId is required' });
      }

      const token = await this.service.callNext({
        tenantId,
        counterId,
        operatorId: userId,
        serviceId,
      });

      if (!token) {
        return res.status(200).json({
          success: true,
          data: null,
          message: 'Queue is empty',
        });
      }

      return res.status(200).json({
        success: true,
        data: token,
        message: `Token ${token.tokenNumber} called`,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };

  // -------------------------------------------------------------------------
  // POST /:tokenId/recall
  // -------------------------------------------------------------------------

  recall = async (req: Request, res: Response) => {
    try {
      const { tenantId, userId } = this.getContext(req);
      if (!tenantId || !userId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const token = await this.service.recall(tenantId, req.params.tokenId, userId);

      if (!token) {
        return res.status(404).json({
          success: false,
          error: 'Token not found or cannot be recalled',
        });
      }

      return res.status(200).json({
        success: true,
        data: token,
        message: `Token ${token.tokenNumber} recalled`,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };

  // -------------------------------------------------------------------------
  // POST /:tokenId/start-serving
  // -------------------------------------------------------------------------

  startServing = async (req: Request, res: Response) => {
    try {
      const { tenantId, userId } = this.getContext(req);
      if (!tenantId || !userId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const result = await this.service.startServing(
        tenantId,
        req.params.tokenId,
        userId,
      );

      return res.status(200).json({
        success: true,
        data: result,
        message: `Token ${result.tokenNumber} is now being served`,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };

  // -------------------------------------------------------------------------
  // POST /:tokenId/complete
  // -------------------------------------------------------------------------

  complete = async (req: Request, res: Response) => {
    try {
      const { tenantId, userId } = this.getContext(req);
      if (!tenantId || !userId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const { notes } = req.body ?? {};
      const result = await this.service.complete(
        tenantId,
        req.params.tokenId,
        userId,
        notes,
      );

      return res.status(200).json({
        success: true,
        data: result,
        message: `Token ${result.tokenNumber} completed`,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };

  // -------------------------------------------------------------------------
  // POST /:tokenId/no-show
  // -------------------------------------------------------------------------

  noShow = async (req: Request, res: Response) => {
    try {
      const { tenantId, userId } = this.getContext(req);
      if (!tenantId || !userId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const result = await this.service.markNoShow(
        tenantId,
        req.params.tokenId,
        userId,
      );

      return res.status(200).json({
        success: true,
        data: result,
        message: `Token ${result.tokenNumber} marked as no-show`,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };

  // -------------------------------------------------------------------------
  // POST /:tokenId/cancel
  // -------------------------------------------------------------------------

  cancel = async (req: Request, res: Response) => {
    try {
      const { tenantId, userId } = this.getContext(req);
      if (!tenantId || !userId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const { reason } = req.body ?? {};
      const result = await this.service.cancel(
        tenantId,
        req.params.tokenId,
        userId,
        reason,
      );

      return res.status(200).json({
        success: true,
        data: result,
        message: `Token ${result.tokenNumber} cancelled`,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };

  // -------------------------------------------------------------------------
  // GET /queue
  // -------------------------------------------------------------------------

  getQueue = async (req: Request, res: Response) => {
    try {
      const { tenantId } = this.getContext(req);
      if (!tenantId) {
        return res.status(401).json({ success: false, error: 'Unauthorized' });
      }

      const locationId = req.query.locationId as string;
      if (!locationId) {
        return res.status(400).json({
          success: false,
          error: 'locationId query parameter is required',
        });
      }

      const serviceIds = req.query.serviceIds
        ? String(req.query.serviceIds).split(',')
        : undefined;

      const limit = req.query.limit ? parseInt(String(req.query.limit), 10) : 50;

      const queue = await this.service.getQueue(
        tenantId,
        locationId,
        serviceIds,
        limit,
      );

      return res.status(200).json({
        success: true,
        data: queue,
        count: queue.length,
      });
    } catch (err) {
      return this.handleError(res, err);
    }
  };
}
