import { Request, Response } from 'express';
import { CallNextService } from './call-next.service';
import { EventEmitter } from '../events/event-emitter';

/**
 * Express-style controller for the Call Next endpoints.
 * Can be easily adapted to NestJS, Fastify, Hono, etc.
 */
export class CallNextController {
  private readonly service: CallNextService;

  constructor(events: EventEmitter) {
    this.service = new CallNextService(events);
  }

  /**
   * POST /api/v1/tokens/call-next
   *
   * Body:
   * {
   *   "counterId": "uuid",
   *   "serviceId": "uuid"   // optional
   * }
   *
   * Headers:
   *   Authorization: Bearer <token>
   *   X-Tenant-Id: <tenant-uuid>   (or extracted from JWT)
   */
  callNext = async (req: Request, res: Response) => {
    try {
      const tenantId = (req as any).tenantId as string;
      const operatorId = (req as any).userId as string;

      if (!tenantId || !operatorId) {
        return res.status(401).json({
          success: false,
          error: 'Unauthorized – missing tenant or user context',
        });
      }

      const { counterId, serviceId } = req.body;

      if (!counterId) {
        return res.status(400).json({
          success: false,
          error: 'counterId is required',
        });
      }

      const token = await this.service.callNext({
        tenantId,
        counterId,
        operatorId,
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
    } catch (err: any) {
      console.error('[CallNext] Error:', err);
      return res.status(500).json({
        success: false,
        error: err.message || 'Internal server error',
      });
    }
  };

  /**
   * POST /api/v1/tokens/:tokenId/recall
   */
  recall = async (req: Request, res: Response) => {
    try {
      const tenantId = (req as any).tenantId as string;
      const operatorId = (req as any).userId as string;
      const { tokenId } = req.params;

      if (!tenantId || !operatorId) {
        return res.status(401).json({
          success: false,
          error: 'Unauthorized',
        });
      }

      const token = await this.service.recall(tenantId, tokenId, operatorId);

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
    } catch (err: any) {
      console.error('[Recall] Error:', err);
      return res.status(500).json({
        success: false,
        error: err.message || 'Internal server error',
      });
    }
  };
}
