-- =============================================================================
-- QueueFlow – Complete Multi-Tenant Database Schema
-- PostgreSQL 15+
-- Shared Database + tenant_id Row-Level Isolation
-- =============================================================================

CREATE EXTENSION IF NOT EXISTS "pgcrypto";
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- -----------------------------------------------------------------------------
-- ENUMS
-- -----------------------------------------------------------------------------

CREATE TYPE plan_type AS ENUM ('free', 'basic', 'pro', 'enterprise');
CREATE TYPE tenant_status AS ENUM ('pending', 'approved', 'suspended', 'rejected');
CREATE TYPE role_name AS ENUM ('admin', 'manager', 'operator', 'reception', 'kiosk');
CREATE TYPE customer_segment AS ENUM ('regular', 'vip', 'premium', 'new');
CREATE TYPE token_status AS ENUM ('pending', 'called', 'serving', 'served', 'cancelled', 'no_show');
CREATE TYPE token_source AS ENUM ('kiosk', 'mobile', 'reception', 'appointment');
CREATE TYPE priority_level AS ENUM ('regular', 'new', 'premium', 'vip');
CREATE TYPE appointment_status AS ENUM ('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show');
CREATE TYPE appointment_type AS ENUM ('in_person', 'online');
CREATE TYPE recurrence_type AS ENUM ('none', 'daily', 'weekly', 'monthly');
CREATE TYPE notification_channel AS ENUM ('email', 'sms', 'whatsapp');
CREATE TYPE notification_type AS ENUM ('token_issued', 'token_called', 'reminder', 'served', 'appointment_reminder', 'survey');
CREATE TYPE notification_status AS ENUM ('pending', 'sent', 'failed');
CREATE TYPE trigger_type AS ENUM (
    'token_created', 'token_called', 'token_served', 'wait_exceeded',
    'queue_length_exceeded', 'no_show', 'rating_received', 'appointment_created'
);
CREATE TYPE task_status AS ENUM ('todo', 'in_progress', 'review', 'done');
CREATE TYPE task_priority AS ENUM ('low', 'medium', 'high', 'urgent');
CREATE TYPE shift_type AS ENUM ('morning', 'afternoon', 'evening', 'full_day', 'custom');
CREATE TYPE schedule_status AS ENUM ('scheduled', 'confirmed', 'cancelled');

-- -----------------------------------------------------------------------------
-- 1. PLANS & TENANTS
-- -----------------------------------------------------------------------------

CREATE TABLE plans (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name                TEXT NOT NULL UNIQUE,
    type                plan_type NOT NULL,
    max_locations       INT,
    max_counters        INT,
    max_users           INT,
    max_monthly_tokens  INT,
    features            JSONB NOT NULL DEFAULT '{}',
    price_monthly       DECIMAL(10,2),
    price_yearly        DECIMAL(10,2),
    is_active           BOOLEAN NOT NULL DEFAULT true,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE tenants (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name                TEXT NOT NULL,
    slug                TEXT NOT NULL UNIQUE,
    status              tenant_status NOT NULL DEFAULT 'pending',
    plan_id             UUID REFERENCES plans(id),
    logo_url            TEXT,
    brand_color         TEXT DEFAULT '#3B82F6',
    timezone            TEXT NOT NULL DEFAULT 'UTC',
    default_language    TEXT NOT NULL DEFAULT 'en',
    settings            JSONB NOT NULL DEFAULT '{}',
    approved_at         TIMESTAMPTZ,
    approved_by         UUID,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE TABLE super_admins (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email               TEXT NOT NULL UNIQUE,
    password_hash       TEXT NOT NULL,
    name                TEXT NOT NULL,
    is_active           BOOLEAN NOT NULL DEFAULT true,
    last_login_at       TIMESTAMPTZ,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 2. ROLES & USERS
-- -----------------------------------------------------------------------------

CREATE TABLE roles (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name                role_name NOT NULL,
    permissions         JSONB NOT NULL DEFAULT '[]',
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (tenant_id, name)
);

CREATE TABLE users (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    email               TEXT NOT NULL,
    password_hash       TEXT,
    name                TEXT NOT NULL,
    phone               TEXT,
    avatar_url          TEXT,
    role_id             UUID NOT NULL REFERENCES roles(id),
    is_active           BOOLEAN NOT NULL DEFAULT true,
    last_login_at       TIMESTAMPTZ,
    invite_token        TEXT,
    invite_expires_at   TIMESTAMPTZ,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ,
    UNIQUE (tenant_id, email)
);

-- -----------------------------------------------------------------------------
-- 3. LOCATIONS, SERVICES, COUNTERS
-- -----------------------------------------------------------------------------

CREATE TABLE locations (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name                TEXT NOT NULL,
    code                TEXT,
    address             TEXT,
    city                TEXT,
    country             TEXT,
    timezone            TEXT NOT NULL DEFAULT 'UTC',
    opening_hours       JSONB NOT NULL DEFAULT '{}',
    is_active           BOOLEAN NOT NULL DEFAULT true,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE TABLE services (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    location_id         UUID REFERENCES locations(id),
    name                TEXT NOT NULL,
    code                TEXT,
    description         TEXT,
    estimated_duration_minutes INT DEFAULT 10,
    is_active           BOOLEAN NOT NULL DEFAULT true,
    priority_weight     INT DEFAULT 0,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE TABLE counters (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    location_id         UUID NOT NULL REFERENCES locations(id) ON DELETE CASCADE,
    name                TEXT NOT NULL,
    code                TEXT,
    service_ids         UUID[] DEFAULT '{}',
    is_active           BOOLEAN NOT NULL DEFAULT true,
    current_token_id    UUID,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE TABLE counter_assignments (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    user_id             UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    counter_id          UUID NOT NULL REFERENCES counters(id) ON DELETE CASCADE,
    assigned_at         TIMESTAMPTZ NOT NULL DEFAULT now(),
    unassigned_at       TIMESTAMPTZ
);

CREATE UNIQUE INDEX counter_assignments_active_uidx
    ON counter_assignments (user_id, counter_id)
    WHERE unassigned_at IS NULL;

-- -----------------------------------------------------------------------------
-- 4. CUSTOMERS
-- -----------------------------------------------------------------------------

CREATE TABLE customers (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name                TEXT,
    phone               TEXT,
    email               TEXT,
    national_id         TEXT,
    segment             customer_segment NOT NULL DEFAULT 'new',
    preferences         JSONB NOT NULL DEFAULT '{}',
    tags                TEXT[] DEFAULT '{}',
    total_visits        INT NOT NULL DEFAULT 0,
    notes               TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE UNIQUE INDEX customers_tenant_phone_uidx
    ON customers (tenant_id, phone) WHERE phone IS NOT NULL AND deleted_at IS NULL;
CREATE UNIQUE INDEX customers_tenant_email_uidx
    ON customers (tenant_id, email) WHERE email IS NOT NULL AND deleted_at IS NULL;

-- -----------------------------------------------------------------------------
-- 5. TOKENS (Core Queue)
-- -----------------------------------------------------------------------------

CREATE TABLE tokens (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    location_id         UUID NOT NULL REFERENCES locations(id),
    service_id          UUID NOT NULL REFERENCES services(id),
    counter_id          UUID REFERENCES counters(id),
    customer_id         UUID REFERENCES customers(id),
    appointment_id      UUID,

    token_number        TEXT NOT NULL,
    status              token_status NOT NULL DEFAULT 'pending',
    priority            priority_level NOT NULL DEFAULT 'regular',
    priority_score      INT NOT NULL DEFAULT 0,

    source              token_source NOT NULL DEFAULT 'kiosk',
    issued_at           TIMESTAMPTZ NOT NULL DEFAULT now(),
    called_at           TIMESTAMPTZ,
    serving_at          TIMESTAMPTZ,
    completed_at        TIMESTAMPTZ,

    wait_duration_seconds   INT,
    service_duration_seconds INT,

    call_attempts       INT NOT NULL DEFAULT 0,
    notes               TEXT,
    signature_url       TEXT,
    qr_code_data        TEXT,

    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Fast queue ordering
CREATE INDEX tokens_queue_idx
    ON tokens (tenant_id, location_id, service_id, status, priority_score DESC, issued_at)
    WHERE status IN ('pending', 'called');

CREATE INDEX tokens_tenant_status_idx ON tokens (tenant_id, status);
CREATE INDEX tokens_tenant_issued_at_idx ON tokens (tenant_id, issued_at DESC);

-- -----------------------------------------------------------------------------
-- 6. APPOINTMENTS
-- -----------------------------------------------------------------------------

CREATE TABLE appointments (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    location_id         UUID NOT NULL REFERENCES locations(id),
    service_id          UUID NOT NULL REFERENCES services(id),
    customer_id         UUID REFERENCES customers(id),
    assigned_user_id    UUID REFERENCES users(id),

    title               TEXT,
    start_at            TIMESTAMPTZ NOT NULL,
    end_at              TIMESTAMPTZ NOT NULL,
    status              appointment_status NOT NULL DEFAULT 'scheduled',
    type                appointment_type NOT NULL DEFAULT 'in_person',

    meeting_url         TEXT,
    meeting_platform    TEXT,

    recurrence          recurrence_type NOT NULL DEFAULT 'none',
    recurrence_rule     JSONB,

    notes               TEXT,
    reminder_sent       BOOLEAN NOT NULL DEFAULT false,
    token_id            UUID REFERENCES tokens(id),

    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE INDEX appointments_tenant_start_idx ON appointments (tenant_id, start_at);

CREATE TABLE appointment_blockouts (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    location_id         UUID REFERENCES locations(id),
    start_at            TIMESTAMPTZ NOT NULL,
    end_at              TIMESTAMPTZ NOT NULL,
    reason              TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 7. NOTIFICATIONS
-- -----------------------------------------------------------------------------

CREATE TABLE notifications (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    customer_id         UUID REFERENCES customers(id),
    token_id            UUID REFERENCES tokens(id),
    appointment_id      UUID REFERENCES appointments(id),

    channel             notification_channel NOT NULL,
    type                notification_type NOT NULL,
    status              notification_status NOT NULL DEFAULT 'pending',

    recipient           TEXT NOT NULL,
    subject             TEXT,
    body                TEXT NOT NULL,

    provider_response   JSONB,
    sent_at             TIMESTAMPTZ,
    failed_reason       TEXT,

    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX notifications_tenant_status_idx ON notifications (tenant_id, status);

-- -----------------------------------------------------------------------------
-- 8. WORKFLOW AUTOMATION
-- -----------------------------------------------------------------------------

CREATE TABLE workflow_rules (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name                TEXT NOT NULL,
    description         TEXT,
    is_active           BOOLEAN NOT NULL DEFAULT true,

    trigger_type        trigger_type NOT NULL,
    conditions          JSONB NOT NULL DEFAULT '[]',
    actions             JSONB NOT NULL DEFAULT '[]',

    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE workflow_executions (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    rule_id             UUID NOT NULL REFERENCES workflow_rules(id),
    token_id            UUID REFERENCES tokens(id),

    status              TEXT NOT NULL,
    input_data          JSONB,
    result              JSONB,
    error_message       TEXT,

    executed_at         TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 9. TASKS, PROJECTS, CHAT, MEETINGS
-- -----------------------------------------------------------------------------

CREATE TABLE projects (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name                TEXT NOT NULL,
    description         TEXT,
    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE TABLE tasks (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    project_id          UUID REFERENCES projects(id),
    title               TEXT NOT NULL,
    description         TEXT,
    status              task_status NOT NULL DEFAULT 'todo',
    priority            task_priority NOT NULL DEFAULT 'medium',
    assignee_id         UUID REFERENCES users(id),
    due_date            DATE,
    attachments         JSONB DEFAULT '[]',
    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at          TIMESTAMPTZ
);

CREATE TABLE task_comments (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    task_id             UUID NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    user_id             UUID NOT NULL REFERENCES users(id),
    body                TEXT NOT NULL,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE chat_channels (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name                TEXT NOT NULL,
    is_private          BOOLEAN NOT NULL DEFAULT false,
    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE chat_messages (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    channel_id          UUID NOT NULL REFERENCES chat_channels(id) ON DELETE CASCADE,
    user_id             UUID NOT NULL REFERENCES users(id),
    body                TEXT NOT NULL,
    attachments         JSONB DEFAULT '[]',
    mentions            UUID[] DEFAULT '{}',
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE meetings (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    title               TEXT NOT NULL,
    start_at            TIMESTAMPTZ NOT NULL,
    end_at              TIMESTAMPTZ NOT NULL,
    meeting_url         TEXT,
    platform            TEXT,
    attendees           UUID[] DEFAULT '{}',
    task_id             UUID REFERENCES tasks(id),
    project_id          UUID REFERENCES projects(id),
    created_by          UUID REFERENCES users(id),
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 10. STAFF SCHEDULING
-- -----------------------------------------------------------------------------

CREATE TABLE staff_schedules (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    user_id             UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    counter_id          UUID REFERENCES counters(id),
    location_id         UUID REFERENCES locations(id),

    date                DATE NOT NULL,
    shift_type          shift_type NOT NULL,
    start_time          TIME,
    end_time            TIME,
    status              schedule_status NOT NULL DEFAULT 'scheduled',

    notes               TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 11. MARKETING, SURVEYS, DISPLAY
-- -----------------------------------------------------------------------------

CREATE TABLE marketing_contents (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    location_id         UUID REFERENCES locations(id),
    title               TEXT NOT NULL,
    type                TEXT NOT NULL,
    content_url         TEXT,
    text_content        TEXT,
    language            TEXT NOT NULL DEFAULT 'en',
    display_order       INT NOT NULL DEFAULT 0,
    duration_seconds    INT DEFAULT 10,
    is_active           BOOLEAN NOT NULL DEFAULT true,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE surveys (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    token_id            UUID REFERENCES tokens(id),
    customer_id         UUID REFERENCES customers(id),

    overall_rating      SMALLINT CHECK (overall_rating BETWEEN 1 AND 5),
    wait_time_rating    SMALLINT,
    service_quality_rating SMALLINT,
    staff_rating        SMALLINT,
    cleanliness_rating  SMALLINT,

    comments            TEXT,
    suggestions         TEXT,
    would_recommend     BOOLEAN,

    status              TEXT NOT NULL DEFAULT 'pending',
    sent_via            notification_channel,
    completed_at        TIMESTAMPTZ,

    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 12. SUBSCRIPTIONS & PAYMENTS
-- -----------------------------------------------------------------------------

CREATE TABLE subscriptions (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    plan_id             UUID NOT NULL REFERENCES plans(id),
    status              TEXT NOT NULL,
    current_period_start TIMESTAMPTZ NOT NULL,
    current_period_end   TIMESTAMPTZ NOT NULL,
    cancel_at_period_end BOOLEAN NOT NULL DEFAULT false,
    stripe_subscription_id TEXT,
    paypal_subscription_id TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE payments (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    subscription_id     UUID REFERENCES subscriptions(id),
    amount              DECIMAL(10,2) NOT NULL,
    currency            TEXT NOT NULL DEFAULT 'USD',
    status              TEXT NOT NULL,
    provider            TEXT NOT NULL,
    invoice_number      TEXT,
    transaction_id      TEXT,
    paid_at             TIMESTAMPTZ,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- 13. TENANT SETTINGS
-- -----------------------------------------------------------------------------

CREATE TABLE tenant_settings (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id           UUID NOT NULL UNIQUE REFERENCES tenants(id) ON DELETE CASCADE,

    logo_url            TEXT,
    brand_color         TEXT,

    kiosk_qr_enabled    BOOLEAN DEFAULT true,
    kiosk_auto_print    BOOLEAN DEFAULT false,
    kiosk_welcome_message JSONB,

    display_title       JSONB,
    display_text        JSONB,

    email_provider      JSONB,
    sms_provider        JSONB,
    whatsapp_provider   JSONB,

    stripe_public_key   TEXT,
    stripe_secret_key_encrypted TEXT,
    paypal_client_id    TEXT,
    paypal_secret_encrypted TEXT,

    updated_at          TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- -----------------------------------------------------------------------------
-- HELPER: updated_at trigger
-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Apply to main tables (example – add more as needed)
CREATE TRIGGER trg_tenants_updated_at BEFORE UPDATE ON tenants
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_tokens_updated_at BEFORE UPDATE ON tokens
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_locations_updated_at BEFORE UPDATE ON locations
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_services_updated_at BEFORE UPDATE ON services
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_counters_updated_at BEFORE UPDATE ON counters
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_customers_updated_at BEFORE UPDATE ON customers
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_appointments_updated_at BEFORE UPDATE ON appointments
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_workflow_rules_updated_at BEFORE UPDATE ON workflow_rules
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_tasks_updated_at BEFORE UPDATE ON tasks
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_staff_schedules_updated_at BEFORE UPDATE ON staff_schedules
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_marketing_contents_updated_at BEFORE UPDATE ON marketing_contents
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_subscriptions_updated_at BEFORE UPDATE ON subscriptions
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_tenant_settings_updated_at BEFORE UPDATE ON tenant_settings
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
