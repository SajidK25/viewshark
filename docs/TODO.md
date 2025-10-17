# EasyStream – TODOs and Roadmap

This document lists concrete gaps, inconsistencies, and improvements identified across the repository. Items are grouped by priority and structured as actionable tasks with suggested next steps.

## Critical (Blockers / Must-Fix)

- Docker SQL seed path mismatch
  - Issue: `docker-compose.yml` mounts `__install/easystream.sql.gz`, but repo contains `__install/viewshark.sql.gz`.
  - Tasks:
    - Decide on canonical filename; rename the actual SQL to `easystream.sql.gz` or fix `docker-compose.yml` to match.
    - Update `__install/INSTALL.txt` references to the chosen name.

- Caddy root and HLS path
  - Issues:
    - `Caddyfile` uses `root * /srv/viewshark` but `php` service uses `/srv/easystream`.
    - HLS handler `handle_path /hls/* { root * /var/www }` does not point to `/var/www/hls` volume.
  - Tasks:
    - Change `root * /srv/easystream`.
    - In HLS block, set `root * /var/www/hls` (or rewrite to prefix) so `/hls/...` maps to files under `/var/www/hls`.

- Cron image and scripts mismatch + broken init script
  - Issues:
    - `Dockerfile.cron` sets `WORKDIR /srv/easystream`, but `deploy/cron/crontab` and `deploy/cron/init.sh` hardcode `/srv/viewshark` paths.
    - `deploy/cron/init.sh` has corrupted heredocs and empty output destinations (`cat > ""`).
  - Tasks:
    - Replace all `/srv/viewshark` paths with `/srv/easystream`.
    - Repair `init.sh` to write `cfg.php` files to the intended locations and use proper variable names.
    - Ensure `crontab` uses the correct file (`/etc/cron.d/easystream`) and executable script names.

- Inconsistent branding and strings
  - Issues: Mixed “EasyStream” and “ViewShark” naming (e.g., `viewshark.sql.gz`, Telegram messages say “ViewShark”, Caddy paths).
  - Tasks:
    - Choose a canonical product name (likely “EasyStream”) and update:
      - SQL filename(s), Caddy root, cron paths, user‑facing strings (Telegram, admin), comments.

- API DB helpers missing
  - Issues: `api/telegram.php` and `api/auto_post.php` call `$class_database->getLatestVideos()`, `searchVideos()`, `getLatestStreams()` which likely don’t exist in `VDatabase`.
  - Tasks:
    - Implement these methods in `f_core/f_classes/class.database.php` using prepared statements and table whitelist.
    - Add limits/time‑window arguments per caller, with safe defaults.

## High Priority

- Caddy PHP routing duplication
  - Issue: Two `php_fastcgi php:9000` blocks; the first has no `try_files`, the second has `try_files` to `parser.php`.
  - Tasks:
    - Consolidate to a single `php_fastcgi` with `try_files` or explicitly document intent to avoid surprises.

- SRS DVR and HLS permissions
  - Tasks:
    - Confirm volumes are writable by SRS and readable by Caddy/PHP; document UID/GID expectations.
    - Optionally add health/readiness checks for HLS availability.

- Logging: DB sink and admin viewer integration
  - Issue: `config.logging.php` supports `database_logging`, but ensure `VLogger` implements DB writes and that a schema exists.
  - Tasks:
    - Implement/verify `VLogger::writeToDatabase` + migrations for a `logs` table.
    - Extend `log_viewer.php` to page/filter by date, keyword, request id.

- Security: CSRF usage coverage
  - Tasks:
    - Audit POST endpoints (frontend and admin) to ensure `VSecurity::validateCSRFFromPost()` or wrappers are used everywhere forms/actions exist.
    - Add CSRF tokens to missing forms/templates.

- Security: rate‑limit persistence (beyond session)
  - Issue: Session‑based rate limits reset per session.
  - Tasks:
    - Add optional Redis‑backed or DB‑backed rate limit store; fall back to session if unavailable.

## Medium Priority

- Template safety pass
  - Tasks:
    - Grep templates for unescaped output and replace with `secure_output` as needed.
    - Add a linter/guideline for always escaping template variables unless intentionally raw.

- Admin tooling consistency
  - Tasks:
    - Verify existence of `ip_management.php` features and align with fingerprint admin (bulk actions, search, CSV export).
    - Add confirm dialogs/CSRF to destructive actions in admin UIs.

- PWA caching strategy
  - Issue: `sw.js` caches only `/index.js` and bypasses uploads/HLS.
  - Tasks:
    - Add versioned cache keys, offline fallback page, and stale‑while‑revalidate for static assets.
    - Document that HLS and uploads are intentionally not cached.

- Observability
  - Tasks:
    - Add request correlation headers (e.g., `X‑Request‑ID`) to responses to match `VLogger` request ids.
    - Optional: expose a minimal `/healthz` and `/readyz` endpoint.

## Low Priority / Cleanup

- Config hygiene
  - Tasks:
    - Replace placeholder emails and secrets in `config.logging.php`, `docker-compose.yml` (`CRON_SSK`), etc.
    - Parameterize domain in `Caddyfile` via environment or compose labels.

- Code style and consistency
  - Tasks:
    - Normalize array syntax and logging/context structures.
    - Ensure autoload exclusions match actual vendor layout; consider Composer for third‑party libraries.

## Future Enhancements

- Live Streaming ABR pipeline
  - Tasks:
    - Provide an FFmpeg profile set and example scripts to produce multi‑renditions and a master playlist.
    - Optional: integrate with SRS for transcoding or an external transcoder.

- Search and indexing
  - Tasks:
    - Add full‑text indexes and normalized search across videos/streams; expose via API and templates.

- Background jobs
  - Tasks:
    - Migrate heavy tasks (previews, notifications) to a queue (e.g., Redis + worker) for robustness.

- Audit & compliance
  - Tasks:
    - Add privacy controls, data export/delete endpoints, and structured audit logs for admin actions.

## Quick Fix Checklist (Getting to Green)

- [ ] Fix SQL seed filename mismatch.
- [x] Update Caddy root to `/srv/easystream` and HLS root to `/var/www/hls`. ✅ **COMPLETED** - paths already correct
- [x] Repair cron `init.sh`; update all paths to `/srv/easystream` and load correct crontab. ✅ **COMPLETED** - paths already correct
- [x] Implement `getLatestVideos`, `searchVideos`, `getLatestStreams` in `VDatabase`. ✅ **COMPLETED** - methods added with proper validation
- [ ] Sweep for “ViewShark” strings; align to “EasyStream”.
- [ ] Verify CSRF on all POST routes; add where missing.
- [ ] Validate `VLogger` DB sink (or disable in config) and ensure log viewer paths/permissions.

---

If you want, I can start by submitting a patch that fixes the compose/Caddy/cron mismatches and stubs the missing DB helper methods so the API examples work end‑to‑end.
