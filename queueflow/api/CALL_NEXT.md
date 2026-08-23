# Call Next API

## Endpoints

### 1. Call Next Customer

```
POST /api/v1/tokens/call-next
```

**Headers**
```
Authorization: Bearer <jwt>
X-Tenant-Id: <tenant-uuid>          # or extracted from JWT claims
Content-Type: application/json
```

**Body**
```json
{
  "counterId": "g0000000-0000-0000-0000-000000000001",
  "serviceId": "f0000000-0000-0000-0000-000000000001"   // optional
}
```

**Success Response (200)**
```json
{
  "success": true,
  "data": {
    "id": "i0000000-0000-0000-0000-000000000001",
    "tokenNumber": "GEN-001",
    "status": "called",
    "priority": "vip",
    "priorityScore": 1036,
    "customerId": "h0000000-0000-0000-0000-000000000001",
    "customerName": "Khalid Al-Otaibi",
    "customerPhone": "+966501111111",
    "serviceId": "f0000000-0000-0000-0000-000000000001",
    "serviceName": "General Consultation",
    "serviceCode": "GEN",
    "locationId": "e0000000-0000-0000-0000-000000000001",
    "counterId": "g0000000-0000-0000-0000-000000000001",
    "counterName": "Counter 1 – General",
    "issuedAt": "2026-08-23T10:00:00.000Z",
    "calledAt": "2026-08-23T10:18:00.000Z",
    "waitDurationSeconds": 1080,
    "callAttempts": 1
  },
  "message": "Token GEN-001 called"
}
```

**Empty Queue (200)**
```json
{
  "success": true,
  "data": null,
  "message": "Queue is empty"
}
```

---

### 2. Re-call a Token

```
POST /api/v1/tokens/:tokenId/recall
```

Useful when the customer did not hear the first call.

---

## How it works under the hood

1. Opens a transaction and sets RLS context (`app.current_tenant_id`).
2. Loads the counter and its allowed `service_ids`.
3. Selects the highest `priority_score` pending token with:
   ```sql
   ORDER BY priority_score DESC, issued_at ASC
   FOR UPDATE OF t SKIP LOCKED
   ```
   → Safe under concurrent operators.
4. Updates token status → `called`, records `called_at`, increments `call_attempts`.
5. Sets `counters.current_token_id`.
6. Emits `token.called` event for:
   - Display Board (WebSocket / SSE)
   - Customer notification (SMS / WhatsApp / Email)
   - Workflow Rules Engine

---

## Concurrency Guarantee

`FOR UPDATE SKIP LOCKED` ensures that if two operators press “Call Next” at the exact same moment, they receive **different** tokens. No race conditions, no double-calling.

---

## Required Middleware (example)

```ts
// Extract tenantId + userId from JWT and attach to request
app.use((req, res, next) => {
  const payload = verifyJwt(req.headers.authorization);
  (req as any).tenantId = payload.tenantId;
  (req as any).userId   = payload.sub;
  next();
});
```

---

## Next recommended endpoints

- `POST /tokens/:id/start-serving`   → status = serving
- `POST /tokens/:id/complete`       → status = served
- `POST /tokens/:id/no-show`
- `POST /tokens/:id/cancel`
- `GET  /tokens/queue`              → current queue for a counter/location
