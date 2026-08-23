# QueueFlow

Multi-tenant queue management system built on shared PostgreSQL + Row-Level Security.

## Structure

```
queueflow/
├── schema/
│   ├── 01_schema.sql          # Core tables, enums, indexes, triggers
│   ├── 02_rls_policies.sql    # Tenant isolation policies
│   ├── 03_priority_scoring.sql# Priority algorithm + rescore helper
│   └── 04_seed_demo.sql       # MediCare Clinic demo data
└── api/
    ├── package.json
    ├── tsconfig.json
    ├── API.md
    ├── CALL_NEXT.md
    └── src/
        ├── app.ts
        ├── common/database.ts
        ├── events/event-emitter.ts
        ├── realtime/websocket-gateway.ts
        └── tokens/
            ├── call-next.service.ts
            ├── call-next.controller.ts
            ├── token.service.ts
            ├── token.controller.ts
            └── routes.ts
```

## Quick start (API)

```bash
cd queueflow/api
npm install
export DATABASE_URL=postgres://...
export DEMO_TENANT_ID=b0000000-0000-0000-0000-000000000001
export DEMO_USER_ID=d0000000-0000-0000-0000-000000000003
npm run dev
```

WebSocket: `ws://localhost:3000/ws?tenantId=...&locationId=...&role=display`

## Key features

- Concurrent-safe Call Next (`FOR UPDATE SKIP LOCKED`)
- Priority scoring (VIP / Premium / wait-boost / service weight)
- Full token lifecycle: call → recall → start-serving → complete / no-show / cancel
- Real-time WebSocket broadcasts per tenant + location
- RLS-enforced multi-tenancy on a single shared database
