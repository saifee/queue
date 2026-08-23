# QueueFlow Operator API

Base path: `/api/v1/tokens`

All endpoints require:
```
Authorization: Bearer <jwt>          # or demo headers
X-Tenant-Id: <tenant-uuid>
X-User-Id: <user-uuid>
```

## Endpoints

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/call-next` | Call the next highest-priority customer |
| `POST` | `/:tokenId/recall` | Re-announce a previously called token |
| `POST` | `/:tokenId/start-serving` | Mark token as currently being served |
| `POST` | `/:tokenId/complete` | Mark token as served (finished) |
| `POST` | `/:tokenId/no-show` | Mark customer as no-show |
| `POST` | `/:tokenId/cancel` | Cancel a token |
| `GET`  | `/queue` | List current pending + called tokens |

---

### POST /call-next

```json
{ "counterId": "uuid", "serviceId": "uuid" }   // serviceId optional
```

### POST /:tokenId/complete

```json
{ "notes": "Optional operator notes" }
```

### POST /:tokenId/cancel

```json
{ "reason": "Customer left" }
```

### GET /queue

Query params:
- `locationId` (required)
- `serviceIds` (optional, comma-separated)
- `limit` (default 50)

---

## Error Codes

| HTTP | Code | Meaning |
|------|------|---------|
| 400 | – | Validation error |
| 401 | – | Missing tenant / user context |
| 404 | – | Token not found or wrong status |
| 503 | `DATABASE_UNAVAILABLE` | Could not connect to DB |
| 504 | `DATABASE_TIMEOUT` | Query timed out / cancelled |

---

## Real-time WebSocket

```
ws://host/ws?tenantId=<uuid>&locationId=<uuid>&role=display
```

Roles: `display` | `operator` | `kiosk` | `admin`

### Events pushed to clients

```json
{
  "type": "token.called",
  "data": { ... },
  "timestamp": "2026-08-23T10:30:00.000Z"
}
```

Event types:
- `token.called`
- `token.recalled`
- `token.serving`
- `token.served`
- `token.no_show`
- `token.cancelled`

---

## Typical Operator Flow

1. `GET /queue?locationId=...` → see waiting customers
2. `POST /call-next` → announce next token (display board updates live)
3. Customer arrives → `POST /:id/start-serving`
4. Finished → `POST /:id/complete`
5. Or → `POST /:id/no-show` / `POST /:id/cancel`
