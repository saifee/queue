-- =============================================================================
-- QueueFlow – Token Priority Scoring Algorithm
-- =============================================================================
-- Higher priority_score = called sooner.
-- Design goals:
--   • VIP / Premium customers jump the queue fairly
--   • Long-waiting customers are gradually boosted (anti-starvation)
--   • Service priority_weight can bias certain services
--   • Simple, deterministic, explainable, and fast
-- =============================================================================

-- Base weights (tunable per tenant later via settings)
--   VIP      → 1000
--   Premium  →  500
--   New      →  100   (first-time customers get a small boost)
--   Regular  →    0

CREATE OR REPLACE FUNCTION calculate_priority_score(
    p_priority          priority_level,
    p_issued_at         TIMESTAMPTZ,
    p_service_weight    INT DEFAULT 0,
    p_call_attempts     INT DEFAULT 0
)
RETURNS INT
LANGUAGE plpgsql
IMMUTABLE
AS $$
DECLARE
    base_score          INT := 0;
    wait_boost          INT := 0;
    attempt_penalty     INT := 0;
    minutes_waiting     INT;
BEGIN
    -- 1. Segment base score
    base_score := CASE p_priority
        WHEN 'vip'     THEN 1000
        WHEN 'premium' THEN  500
        WHEN 'new'     THEN  100
        ELSE                   0
    END;

    -- 2. Wait-time boost (prevents starvation)
    --    +2 points for every full minute waited (capped at 30 min → +60)
    minutes_waiting := EXTRACT(EPOCH FROM (now() - p_issued_at))::INT / 60;
    wait_boost := LEAST(minutes_waiting * 2, 60);

    -- 3. Call-attempt penalty
    --    Each failed call reduces score slightly so the system prefers
    --    customers who respond quickly (optional – can be inverted)
    attempt_penalty := p_call_attempts * 5;

    -- 4. Service weight (from services.priority_weight)
    --    Allows certain services (e.g. emergency, express) to rank higher

    RETURN base_score
         + wait_boost
         + p_service_weight
         - attempt_penalty;
END;
$$;

-- -----------------------------------------------------------------------------
-- Trigger: keep priority_score always up-to-date on INSERT / UPDATE
-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION tokens_set_priority_score()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_service_weight INT := 0;
BEGIN
    -- Fetch service priority weight
    SELECT COALESCE(priority_weight, 0)
      INTO v_service_weight
      FROM services
     WHERE id = NEW.service_id;

    NEW.priority_score := calculate_priority_score(
        NEW.priority,
        NEW.issued_at,
        v_service_weight,
        NEW.call_attempts
    );

    RETURN NEW;
END;
$$;

CREATE TRIGGER trg_tokens_priority_score
    BEFORE INSERT OR UPDATE OF priority, issued_at, call_attempts, service_id
    ON tokens
    FOR EACH ROW
    EXECUTE FUNCTION tokens_set_priority_score();

-- -----------------------------------------------------------------------------
-- Background job helper: re-score all pending tokens (run every 1–2 min)
-- Useful because wait_boost changes over time.
-- -----------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION rescore_pending_tokens(p_tenant_id UUID DEFAULT NULL)
RETURNS INT
LANGUAGE plpgsql
AS $$
DECLARE
    updated_count INT;
BEGIN
    UPDATE tokens t
       SET priority_score = calculate_priority_score(
               t.priority,
               t.issued_at,
               COALESCE(s.priority_weight, 0),
               t.call_attempts
           ),
           updated_at = now()
      FROM services s
     WHERE t.service_id = s.id
       AND t.status IN ('pending', 'called')
       AND (p_tenant_id IS NULL OR t.tenant_id = p_tenant_id);

    GET DIAGNOSTICS updated_count = ROW_COUNT;
    RETURN updated_count;
END;
$$;

-- Example usage from a cron / worker:
-- SELECT rescore_pending_tokens();                 -- all tenants
-- SELECT rescore_pending_tokens('tenant-uuid');    -- single tenant

-- -----------------------------------------------------------------------------
-- How the operator “Call Next” query looks
-- -----------------------------------------------------------------------------
/*
SELECT *
  FROM tokens
 WHERE tenant_id   = current_tenant_id()
   AND location_id = :location_id
   AND service_id  = ANY(:service_ids)          -- services this counter handles
   AND status      = 'pending'
 ORDER BY priority_score DESC, issued_at ASC
 LIMIT 1
 FOR UPDATE SKIP LOCKED;   -- important for concurrent operators
*/
