-- =============================================================================
-- QueueFlow – Row Level Security (RLS) Policies
-- =============================================================================
-- Strategy:
--   • Application sets `app.current_tenant_id` (UUID) via SET LOCAL
--   • Super-admin bypasses tenant isolation when `app.is_super_admin = 'true'`
--   • All tenant-scoped tables enforce tenant_id = current_setting(...)
-- =============================================================================

-- Helper functions (SECURITY DEFINER so they can read the settings)
CREATE OR REPLACE FUNCTION current_tenant_id()
RETURNS UUID AS $$
BEGIN
    RETURN NULLIF(current_setting('app.current_tenant_id', true), '')::UUID;
EXCEPTION WHEN OTHERS THEN
    RETURN NULL;
END;
$$ LANGUAGE plpgsql STABLE;

CREATE OR REPLACE FUNCTION is_super_admin()
RETURNS BOOLEAN AS $$
BEGIN
    RETURN COALESCE(current_setting('app.is_super_admin', true), 'false') = 'true';
EXCEPTION WHEN OTHERS THEN
    RETURN false;
END;
$$ LANGUAGE plpgsql STABLE;

-- -----------------------------------------------------------------------------
-- Enable RLS on all tenant-scoped tables
-- -----------------------------------------------------------------------------

ALTER TABLE tenants              ENABLE ROW LEVEL SECURITY;
ALTER TABLE roles                ENABLE ROW LEVEL SECURITY;
ALTER TABLE users                ENABLE ROW LEVEL SECURITY;
ALTER TABLE locations            ENABLE ROW LEVEL SECURITY;
ALTER TABLE services             ENABLE ROW LEVEL SECURITY;
ALTER TABLE counters             ENABLE ROW LEVEL SECURITY;
ALTER TABLE counter_assignments  ENABLE ROW LEVEL SECURITY;
ALTER TABLE customers            ENABLE ROW LEVEL SECURITY;
ALTER TABLE tokens               ENABLE ROW LEVEL SECURITY;
ALTER TABLE appointments         ENABLE ROW LEVEL SECURITY;
ALTER TABLE appointment_blockouts ENABLE ROW LEVEL SECURITY;
ALTER TABLE notifications        ENABLE ROW LEVEL SECURITY;
ALTER TABLE workflow_rules       ENABLE ROW LEVEL SECURITY;
ALTER TABLE workflow_executions  ENABLE ROW LEVEL SECURITY;
ALTER TABLE projects             ENABLE ROW LEVEL SECURITY;
ALTER TABLE tasks                ENABLE ROW LEVEL SECURITY;
ALTER TABLE task_comments        ENABLE ROW LEVEL SECURITY;
ALTER TABLE chat_channels        ENABLE ROW LEVEL SECURITY;
ALTER TABLE chat_messages        ENABLE ROW LEVEL SECURITY;
ALTER TABLE meetings             ENABLE ROW LEVEL SECURITY;
ALTER TABLE staff_schedules      ENABLE ROW LEVEL SECURITY;
ALTER TABLE marketing_contents   ENABLE ROW LEVEL SECURITY;
ALTER TABLE surveys              ENABLE ROW LEVEL SECURITY;
ALTER TABLE subscriptions        ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments             ENABLE ROW LEVEL SECURITY;
ALTER TABLE tenant_settings      ENABLE ROW LEVEL SECURITY;

-- plans & super_admins stay without RLS (global)

-- -----------------------------------------------------------------------------
-- TENANTS – special case (users can only see their own tenant)
-- -----------------------------------------------------------------------------

CREATE POLICY tenants_isolation ON tenants
    USING (
        is_super_admin()
        OR id = current_tenant_id()
    );

CREATE POLICY tenants_insert ON tenants
    FOR INSERT
    WITH CHECK (is_super_admin());   -- only super-admins create tenants

CREATE POLICY tenants_update ON tenants
    FOR UPDATE
    USING (is_super_admin() OR id = current_tenant_id());

-- -----------------------------------------------------------------------------
-- GENERIC TENANT ISOLATION POLICY (applied to most tables)
-- -----------------------------------------------------------------------------

-- Helper macro-style: every table gets the same three policies

DO $$
DECLARE
    tbl TEXT;
    tables TEXT[] := ARRAY[
        'roles', 'users', 'locations', 'services', 'counters',
        'counter_assignments', 'customers', 'tokens', 'appointments',
        'appointment_blockouts', 'notifications', 'workflow_rules',
        'workflow_executions', 'projects', 'tasks', 'task_comments',
        'chat_channels', 'chat_messages', 'meetings', 'staff_schedules',
        'marketing_contents', 'surveys', 'subscriptions', 'payments',
        'tenant_settings'
    ];
BEGIN
    FOREACH tbl IN ARRAY tables
    LOOP
        -- SELECT / UPDATE / DELETE
        EXECUTE format('
            CREATE POLICY %I_tenant_isolation ON %I
                USING (
                    is_super_admin()
                    OR tenant_id = current_tenant_id()
                )',
            tbl, tbl);

        -- INSERT
        EXECUTE format('
            CREATE POLICY %I_tenant_insert ON %I
                FOR INSERT
                WITH CHECK (
                    is_super_admin()
                    OR tenant_id = current_tenant_id()
                )',
            tbl, tbl);
    END LOOP;
END;
$$;

-- -----------------------------------------------------------------------------
-- How the application should set the context (example)
-- -----------------------------------------------------------------------------
/*
-- At the start of every request / transaction:

BEGIN;
SET LOCAL app.current_tenant_id = '11111111-1111-1111-1111-111111111111';
SET LOCAL app.is_super_admin    = 'false';   -- or 'true' for platform admins

-- … all queries now automatically filtered …

COMMIT;
*/

-- Optional: force RLS even for table owners (recommended in production)
-- ALTER TABLE tokens FORCE ROW LEVEL SECURITY;
-- (repeat for every table if desired)
