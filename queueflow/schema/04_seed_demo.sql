-- =============================================================================
-- QueueFlow – Demo Tenant Seed Data
-- “MediCare Clinic” – a realistic multi-location healthcare example
-- =============================================================================

BEGIN;

-- -----------------------------------------------------------------------------
-- 1. Plans
-- -----------------------------------------------------------------------------
INSERT INTO plans (id, name, type, max_locations, max_counters, max_users, max_monthly_tokens, features, price_monthly, price_yearly)
VALUES
    ('a0000000-0000-0000-0000-000000000001', 'Free',       'free',       1,  2,  3,   500,  '{"appointments":false,"analytics":false,"surveys":false,"whatsapp":false,"sms":false}', 0, 0),
    ('a0000000-0000-0000-0000-000000000002', 'Basic',      'basic',      3,  8, 10,  3000,  '{"appointments":true,"analytics":true,"surveys":true,"whatsapp":false,"sms":true}', 49, 490),
    ('a0000000-0000-0000-0000-000000000003', 'Pro',        'pro',        10, 30, 50, 15000,  '{"appointments":true,"analytics":true,"surveys":true,"whatsapp":true,"sms":true}', 149, 1490),
    ('a0000000-0000-0000-0000-000000000004', 'Enterprise', 'enterprise', NULL, NULL, NULL, NULL, '{"appointments":true,"analytics":true,"surveys":true,"whatsapp":true,"sms":true,"custom_branding":true,"api_access":true}', 499, 4990);

-- -----------------------------------------------------------------------------
-- 2. Demo Tenant
-- -----------------------------------------------------------------------------
INSERT INTO tenants (id, name, slug, status, plan_id, brand_color, timezone, default_language, approved_at)
VALUES (
    'b0000000-0000-0000-0000-000000000001',
    'MediCare Clinic',
    'medicare-clinic',
    'approved',
    'a0000000-0000-0000-0000-000000000003',  -- Pro plan
    '#0D9488',                               -- teal
    'Asia/Riyadh',
    'en',
    now()
);

-- -----------------------------------------------------------------------------
-- 3. Roles
-- -----------------------------------------------------------------------------
INSERT INTO roles (id, tenant_id, name, permissions) VALUES
    ('c0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001', 'admin',     '["*"]'),
    ('c0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001', 'manager',   '["tokens.*","appointments.*","reports.*","customers.*","staff.*"]'),
    ('c0000000-0000-0000-0000-000000000003', 'b0000000-0000-0000-0000-000000000001', 'operator',  '["tokens.read","tokens.call","tokens.serve","tokens.notes"]'),
    ('c0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001', 'reception', '["tokens.create","customers.*","appointments.create"]'),
    ('c0000000-0000-0000-0000-000000000005', 'b0000000-0000-0000-0000-000000000001', 'kiosk',     '["tokens.create"]');

-- -----------------------------------------------------------------------------
-- 4. Users
-- -----------------------------------------------------------------------------
INSERT INTO users (id, tenant_id, email, password_hash, name, role_id, phone) VALUES
    ('d0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001', 'admin@medicare.clinic',   crypt('demo1234', gen_salt('bf')), 'Dr. Sara Al-Rashid',  'c0000000-0000-0000-0000-000000000001', '+966500000001'),
    ('d0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001', 'manager@medicare.clinic', crypt('demo1234', gen_salt('bf')), 'Ahmed Al-Mutairi',    'c0000000-0000-0000-0000-000000000002', '+966500000002'),
    ('d0000000-0000-0000-0000-000000000003', 'b0000000-0000-0000-0000-000000000001', 'op1@medicare.clinic',     crypt('demo1234', gen_salt('bf')), 'Fatima Al-Harbi',     'c0000000-0000-0000-0000-000000000003', '+966500000003'),
    ('d0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001', 'op2@medicare.clinic',     crypt('demo1234', gen_salt('bf')), 'Omar Al-Qahtani',     'c0000000-0000-0000-0000-000000000003', '+966500000004'),
    ('d0000000-0000-0000-0000-000000000005', 'b0000000-0000-0000-0000-000000000001', 'reception@medicare.clinic', crypt('demo1234', gen_salt('bf')), 'Noura Al-Zahrani', 'c0000000-0000-0000-0000-000000000004', '+966500000005');

-- -----------------------------------------------------------------------------
-- 5. Locations
-- -----------------------------------------------------------------------------
INSERT INTO locations (id, tenant_id, name, code, address, city, country, timezone, opening_hours, is_active) VALUES
    ('e0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001',
     'Main Branch – Olaya', 'MAIN',
     'Olaya Street, Building 12', 'Riyadh', 'SA', 'Asia/Riyadh',
     '{"sun":{"open":"08:00","close":"22:00"},"mon":{"open":"08:00","close":"22:00"},"tue":{"open":"08:00","close":"22:00"},"wed":{"open":"08:00","close":"22:00"},"thu":{"open":"08:00","close":"22:00"},"fri":{"open":"14:00","close":"22:00"},"sat":{"open":"08:00","close":"22:00"}}',
     true),
    ('e0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001',
     'North Branch – Al-Nakheel', 'NORTH',
     'Al-Nakheel District, Plaza 4', 'Riyadh', 'SA', 'Asia/Riyadh',
     '{"sun":{"open":"09:00","close":"21:00"},"mon":{"open":"09:00","close":"21:00"},"tue":{"open":"09:00","close":"21:00"},"wed":{"open":"09:00","close":"21:00"},"thu":{"open":"09:00","close":"21:00"},"fri":{"open":"16:00","close":"21:00"},"sat":{"open":"09:00","close":"21:00"}}',
     true);

-- -----------------------------------------------------------------------------
-- 6. Services
-- -----------------------------------------------------------------------------
INSERT INTO services (id, tenant_id, location_id, name, code, estimated_duration_minutes, priority_weight, is_active) VALUES
    ('f0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001', NULL, 'General Consultation', 'GEN', 15, 0, true),
    ('f0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001', NULL, 'Dental Care',          'DEN', 30, 10, true),
    ('f0000000-0000-0000-0000-000000000003', 'b0000000-0000-0000-0000-000000000001', NULL, 'Laboratory',           'LAB', 10, 0, true),
    ('f0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001', NULL, 'Emergency',            'EMR', 20, 200, true),  -- high weight
    ('f0000000-0000-0000-0000-000000000005', 'b0000000-0000-0000-0000-000000000001', NULL, 'Pharmacy',             'PHR', 5,  0, true);

-- -----------------------------------------------------------------------------
-- 7. Counters
-- -----------------------------------------------------------------------------
INSERT INTO counters (id, tenant_id, location_id, name, code, service_ids, is_active) VALUES
    ('g0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001', 'e0000000-0000-0000-0000-000000000001',
     'Counter 1 – General', 'C1', ARRAY['f0000000-0000-0000-0000-000000000001']::UUID[], true),
    ('g0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001', 'e0000000-0000-0000-0000-000000000001',
     'Counter 2 – Dental',  'C2', ARRAY['f0000000-0000-0000-0000-000000000002']::UUID[], true),
    ('g0000000-0000-0000-0000-000000000003', 'b0000000-0000-0000-0000-000000000001', 'e0000000-0000-0000-0000-000000000001',
     'Counter 3 – Lab',     'C3', ARRAY['f0000000-0000-0000-0000-000000000003']::UUID[], true),
    ('g0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001', 'e0000000-0000-0000-0000-000000000001',
     'Counter 4 – Emergency','C4', ARRAY['f0000000-0000-0000-0000-000000000004']::UUID[], true),
    ('g0000000-0000-0000-0000-000000000005', 'b0000000-0000-0000-0000-000000000001', 'e0000000-0000-0000-0000-000000000002',
     'North Counter 1',     'N1', ARRAY['f0000000-0000-0000-0000-000000000001','f0000000-0000-0000-0000-000000000005']::UUID[], true);

-- Assign operators
INSERT INTO counter_assignments (tenant_id, user_id, counter_id) VALUES
    ('b0000000-0000-0000-0000-000000000001', 'd0000000-0000-0000-0000-000000000003', 'g0000000-0000-0000-0000-000000000001'),
    ('b0000000-0000-0000-0000-000000000001', 'd0000000-0000-0000-0000-000000000004', 'g0000000-0000-0000-0000-000000000002');

-- -----------------------------------------------------------------------------
-- 8. Customers (mix of segments)
-- -----------------------------------------------------------------------------
INSERT INTO customers (id, tenant_id, name, phone, email, segment, total_visits, preferences) VALUES
    ('h0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001', 'Khalid Al-Otaibi',   '+966501111111', 'khalid@email.com',   'vip',     24, '{"language":"ar","preferred_doctor":"Dr. Sara"}'),
    ('h0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001', 'Layla Al-Harbi',     '+966502222222', 'layla@email.com',    'premium', 11, '{"language":"en"}'),
    ('h0000000-0000-0000-0000-000000000003', 'b0000000-0000-0000-0000-000000000001', 'Youssef Al-Saud',    '+966503333333', NULL,                'regular',  5, '{}'),
    ('h0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001', 'Nouf Al-Qahtani',    '+966504444444', 'nouf@email.com',     'new',      1, '{"language":"ar"}'),
    ('h0000000-0000-0000-0000-000000000005', 'b0000000-0000-0000-0000-000000000001', 'Faisal Al-Mutairi',  '+966505555555', NULL,                'regular',  3, '{}');

-- -----------------------------------------------------------------------------
-- 9. Live Queue Tokens (interesting mix)
-- -----------------------------------------------------------------------------
-- Note: priority_score will be calculated by the trigger

INSERT INTO tokens (id, tenant_id, location_id, service_id, customer_id, token_number, status, priority, source, issued_at, call_attempts) VALUES
    -- VIP waiting longest
    ('i0000000-0000-0000-0000-000000000001', 'b0000000-0000-0000-0000-000000000001',
     'e0000000-0000-0000-0000-000000000001', 'f0000000-0000-0000-0000-000000000001',
     'h0000000-0000-0000-0000-000000000001', 'GEN-001', 'pending', 'vip', 'kiosk',
     now() - INTERVAL '18 minutes', 0),

    -- Premium
    ('i0000000-0000-0000-0000-000000000002', 'b0000000-0000-0000-0000-000000000001',
     'e0000000-0000-0000-0000-000000000001', 'f0000000-0000-0000-0000-000000000002',
     'h0000000-0000-0000-0000-000000000002', 'DEN-002', 'pending', 'premium', 'mobile',
     now() - INTERVAL '12 minutes', 0),

    -- Regular who has been waiting a while (will get wait boost)
    ('i0000000-0000-0000-0000-000000000003', 'b0000000-0000-0000-0000-000000000001',
     'e0000000-0000-0000-0000-000000000001', 'f0000000-0000-0000-0000-000000000001',
     'h0000000-0000-0000-0000-000000000003', 'GEN-003', 'pending', 'regular', 'kiosk',
     now() - INTERVAL '25 minutes', 1),

    -- New customer
    ('i0000000-0000-0000-0000-000000000004', 'b0000000-0000-0000-0000-000000000001',
     'e0000000-0000-0000-0000-000000000001', 'f0000000-0000-0000-0000-000000000003',
     'h0000000-0000-0000-0000-000000000004', 'LAB-004', 'pending', 'new', 'kiosk',
     now() - INTERVAL '5 minutes', 0),

    -- Emergency (should jump almost to the top because of service weight)
    ('i0000000-0000-0000-0000-000000000005', 'b0000000-0000-0000-0000-000000000001',
     'e0000000-0000-0000-0000-000000000001', 'f0000000-0000-0000-0000-000000000004',
     'h0000000-0000-0000-0000-000000000005', 'EMR-005', 'pending', 'regular', 'reception',
     now() - INTERVAL '3 minutes', 0),

    -- Currently being served
    ('i0000000-0000-0000-0000-000000000006', 'b0000000-0000-0000-0000-000000000001',
     'e0000000-0000-0000-0000-000000000001', 'f0000000-0000-0000-0000-000000000001',
     NULL, 'GEN-000', 'serving', 'regular', 'kiosk',
     now() - INTERVAL '40 minutes', 0);

-- Update the serving token with proper timestamps
UPDATE tokens
   SET called_at = issued_at + INTERVAL '8 minutes',
       serving_at = issued_at + INTERVAL '9 minutes',
       counter_id = 'g0000000-0000-0000-0000-000000000001'
 WHERE id = 'i0000000-0000-0000-0000-000000000006';

-- -----------------------------------------------------------------------------
-- 10. Tenant Settings
-- -----------------------------------------------------------------------------
INSERT INTO tenant_settings (tenant_id, brand_color, kiosk_qr_enabled, kiosk_auto_print, kiosk_welcome_message, display_title, display_text)
VALUES (
    'b0000000-0000-0000-0000-000000000001',
    '#0D9488',
    true,
    true,
    '{"en":"Welcome to MediCare Clinic","ar":"مرحباً بكم في عيادة ميدي كير"}',
    '{"en":"Now Serving","ar":"الآن يتم خدمة"}',
    '{"en":"Please wait for your number to be called","ar":"يرجى الانتظار حتى يتم استدعاء رقمك"}'
);

-- -----------------------------------------------------------------------------
-- 11. One sample workflow rule
-- -----------------------------------------------------------------------------
INSERT INTO workflow_rules (tenant_id, name, description, trigger_type, conditions, actions, created_by)
VALUES (
    'b0000000-0000-0000-0000-000000000001',
    'Alert manager on long wait',
    'When any token waits more than 30 minutes, notify the manager via WhatsApp',
    'wait_exceeded',
    '[{"field":"wait_minutes","operator":">","value":30}]',
    '[{"type":"notify_manager","channel":"whatsapp","message":"Token {{token_number}} has been waiting {{wait_minutes}} minutes"}]',
    'd0000000-0000-0000-0000-000000000001'
);

-- -----------------------------------------------------------------------------
-- 12. Sample marketing content for display board
-- -----------------------------------------------------------------------------
INSERT INTO marketing_contents (tenant_id, location_id, title, type, text_content, language, display_order, duration_seconds, is_active)
VALUES
    ('b0000000-0000-0000-0000-000000000001', NULL, 'Flu Season Reminder', 'text',
     'Get your free flu shot this month – ask at reception!', 'en', 1, 12, true),
    ('b0000000-0000-0000-0000-000000000001', NULL, 'تذكير بموسم الإنفلونزا', 'text',
     'احصل على لقاح الإنفلونزا المجاني هذا الشهر – اسأل في الاستقبال!', 'ar', 2, 12, true);

COMMIT;

-- =============================================================================
-- Quick verification queries (run after seeding)
-- =============================================================================
/*
-- See the live queue ordered by priority
SELECT token_number, priority, priority_score, status,
       EXTRACT(EPOCH FROM (now() - issued_at))/60 AS wait_min
  FROM tokens
 WHERE tenant_id = 'b0000000-0000-0000-0000-000000000001'
   AND status = 'pending'
 ORDER BY priority_score DESC, issued_at;

-- Expected approximate order:
-- 1. EMR-005  (Emergency service weight 200)
-- 2. GEN-001  (VIP 1000 + wait boost)
-- 3. DEN-002  (Premium 500)
-- 4. GEN-003  (Regular + long wait boost)
-- 5. LAB-004  (New 100)
*/
