# EasyStream

High‑end YouTube‑style media platform supporting Videos, Shorts, Live Streams, Images, Audio, Documents, and Blogs. It ships with an admin backend, strong security primitives, detailed logging, containerized deployment, and RTMP/HLS live streaming via SRS.

This document helps you understand the codebase, run it locally, and work productively within the project.

## Quick Start (Docker)

Requirements: Docker and Docker Compose.

1) Copy the repo locally and start the stack:

```
docker-compose up -d --build
```

Services started:
- `db` (MariaDB)
- `php` (PHP‑FPM 8.2 with extensions)
- `caddy` (TLS/HTTP proxy)
- `srs` (RTMP/HLS server for live)
- `cron` (scheduled jobs)
- `abr` (optional FFmpeg adaptive bitrate helper)

2) Environment defaults (override as needed):
- `DB_HOST=db`, `DB_NAME=easystream`, `DB_USER=easystream`, `DB_PASS=easystream`
- `MAIN_URL=http://localhost:8083` (configurable during setup or via /configure_url.php)

3) Database bootstrap: the `db` service imports SQL from `__install/` on first run.

4) Open the site via Caddy on ports 80/443. Update DNS/TLS if using a custom domain.

For manual install steps instead of Docker, see `__install/INSTALL.txt`.

## What’s Inside

Core entry points
- `index.php`: main frontend controller (routing, notifications, actions) (index.php:1)
- `f_core/config.core.php`: bootstraps config, autoload, DB, session, security, logging (f_core/config.core.php:1)
- `f_core/config.database.php`: DB connector via env vars (f_core/config.database.php:1)
- `f_core/config.logging.php`: logging and alert configuration (f_core/config.logging.php:1)
- `api/`: lightweight API utilities (Telegram, auto‑posting) (api/config.php:1)

Key subsystems
- Security: `VSecurity` helpers and safe wrappers in `f_core/f_functions/functions.security.php` (f_core/f_classes/class.security.php:1, f_core/f_functions/functions.security.php:1)
- Database: `VDatabase` on top of ADOdb; prepared queries/validation (f_core/f_classes/class.database.php:1)
- Logging: `VLogger` and `VErrorHandler`, file rotation, alerting, admin log viewer (f_core/f_classes/class.logger.php:1, f_core/f_classes/class.errorhandler.php:1, f_modules/m_backend/log_viewer.php:1)
- IP + Fingerprint: tracking and ban management (f_core/f_classes/class.iptracker.php:1, f_core/f_classes/class.fingerprint.php:1, f_modules/m_backend/ip_management.php:1, f_modules/m_backend/fingerprint_management.php:1)
- Templates: Smarty configuration (f_core/config.smarty.php:1)
- Live Streaming: SRS RTMP/HLS (`deploy/srs.conf`), HLS published to shared volumes

Third‑party vendor libs
- ADOdb, Smarty, AWS SDK (S3 et al.), Google client, Zend Uri/Validate (see `f_core/f_classes` and vendor‑like trees in repo)

## Directory Structure

- `__install/` – SQL schema, upgrade scripts, install docs
- `api/` – integration endpoints (Telegram bot and autopost)
- `deploy/` – infra configs (SRS, cron scripts, SQL seeders)
- `f_core/` – framework core: configs, autoload, classes, functions
- `f_modules/` – feature modules; includes backend admin utilities
- `f_scripts/` – auxiliary scripts and assets
- `f_data/` – runtime data (logs, cache, sitemaps, user files, sessions)
- Public root – entrypoints, PWA assets (`index.js`, `sw.js`, favicons)

## Configuration

Application
- Main URL: set `MAIN_URL` or edit `f_core/config.set.php` (f_core/config.set.php:1)
- Theme/Smarty: configured in `f_core/config.smarty.php` (f_core/config.smarty.php:1)

Database
- Configure via env: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` (f_core/config.database.php:1)
- ADOdb cache dir is set from configs (f_core/config.core.php:29)

Logging
- Toggle file/database logging, levels, rotation, alerts (f_core/config.logging.php:1)
- Files stored under `f_data/logs/`
- Admin UI for viewing/clearing logs: `f_modules/m_backend/log_viewer.php` (requires backend access)

Security
- Central API: `VSecurity` for input validation, escaping, CSRF, rate limiting (f_core/f_classes/class.security.php:1)
- Convenience wrappers: `functions.security.php` (get_param, post_param, csrf_field, validate_csrf, etc.) (f_core/f_functions/functions.security.php:1)
- Strict autoload maps key classes by name to file (f_core/config.autoload.php:1)

Live Streaming
- SRS config at `deploy/srs.conf` (HLS fragments to `/srs/hls`; DVR to `/srs/rec`) (deploy/srs.conf:1)
- Docker volumes expose HLS as read‑only to web tier (docker-compose.yml:1)

API Integrations
- `api/telegram.php` – Telegram Bot webhook handler (api/telegram.php:1)
- `api/auto_post.php` – cron‑driven autopost of new videos/streams to Telegram channels (api/auto_post.php:1)
- `api/config.php` – tokens/channels and rate limits (api/config.php:1)

PWA/Frontend Enhancements
- `index.js` – service worker registration, Android/iOS install prompts (index.js:1)
- `sw.js` – simple cache for `index.js`, skip caching uploads/HLS requests (sw.js:1)

## Security Model

- Input validation: Use `VSecurity::getParam/postParam/validateInput` to sanitize all request data. Types supported include `int`, `email`, `url`, `alpha`, `alphanum`, `slug`, `filename`, `boolean`.
- CSRF protection: Embed `csrf_field('action')` in forms and validate with `validate_csrf('action')`.
- Output escaping: Use `secure_output` for HTML and `secure_js` for JavaScript contexts.
- File upload validation: `validate_file_upload($file, $allowedTypes, $maxSize)` inspects MIME and size.
- Rate limiting: `check_rate_limit($key, $maxAttempts, $windowSeconds)` stores attempt windows in session.
- IP tracking/bans: `VIPTracker::logActivity`, `VIPTracker::banIP/unbanIP/isBanned` with geo hints and stats.
- Browser fingerprinting: `VFingerprint::generateFingerprint/trackFingerprint/ban/unban/isBanned` for evasive users.

See examples:
- `example_secure_form.php` – CSRF, rate limit, validated inputs, and safe upload
- `example_enhanced_logging.php` – application/security/performance logging

## Logging & Observability

- Central logger: `VLogger` with levels EMERGENCY→DEBUG + global request context and backtraces; file rotation and optional DB sinks (f_core/f_classes/class.logger.php:1)
- Global error handler: `VErrorHandler` captures PHP errors/exceptions/shutdown fatals and renders user‑friendly responses depending on `debug_mode` (f_core/f_classes/class.errorhandler.php:1)
- Configurable alerting (email) and retention (f_core/config.logging.php:1)
- Admin Log Viewer (`f_modules/m_backend/log_viewer.php`) with level/limit filters and clear‑all action

## Admin Utilities

- Fingerprint Management: lookup stats, detect threats, ban/unban (`f_modules/m_backend/fingerprint_management.php`)
- IP Management: banlist maintenance, activity review (`f_modules/m_backend/ip_management.php`)
- Log Viewer: browse/clear logs (`f_modules/m_backend/log_viewer.php`)

Backend access control is enforced; ensure you are logged in as an admin before invoking these scripts directly.

## Development Notes

- Autoloading: `spl_autoload_register` resolves class names like `VSecurity`, `VLogger`, `VErrorHandler` to files under `f_core/f_classes/` (f_core/config.autoload.php:1). Avoid namespace collisions with bundled vendor libs (Google, Zend, AWS, etc.).
- Database: Prefer `VDatabase` methods which validate table/field names and bind parameters. Do not construct ad‑hoc SQL strings without validation.
- Templates: Smarty is configured with template/cache directories from `cfg` and exposes common URLs to templates (f_core/config.smarty.php:1).
- Configuration: Read via `$class_database->getConfigurations(...)` and `cfg` array; environment variables override DB where applicable.

## Manual Installation (Non‑Docker)

Summary (see `__install/INSTALL.txt` for full details):
1) Upload files to your web root.
2) Create a MariaDB/MySQL database; import `__install/easystream.sql.gz` and the `updatedb_*.sql`/`deploy/init_settings.sql` scripts.
3) Edit `f_core/config.database.php` and `f_core/config.set.php`.
4) Set writable permissions on `f_data/` subfolders as noted in `INSTALL.txt`.
5) Configure your web server (Apache/Nginx/Caddy) to serve the repository root; PHP‑FPM 8.2+ recommended.
6) Configure cron jobs for conversions, expirations, dashboards, and housekeeping as listed in `INSTALL.txt`.

## Live Streaming

- Push RTMP to `rtmp://<host>:1935/live/<stream_key>` (SRS). HLS is written to the `rtmp_hls` volume and served by Caddy as static content; DVR records to `rtmp_rec`.
- SRS parameters (HLS fragment/window, DVR layout) in `deploy/srs.conf`.
- For ABR (multi‑rendition) examples, see `deploy/abr.sh` and the `abr` service.

### HLS Permissions & Health

- Ensure the SRS container can write to the mounted HLS and recordings volumes and Caddy/PHP can read them:
  - `rtmp_hls` → writable by SRS, readable by Caddy (served at `/hls/*`).
  - `rtmp_rec` → writable by SRS, readable by PHP/cron for VOD processing.
- If you bind host directories, align ownership/permissions (commonly UID/GID 0 in SRS image; Caddy runs as `caddy` user). Use `chown`/`chmod` on host paths accordingly.
- Quick HLS check: once streaming, an HLS playlist should appear at `/hls/<app>/<stream_key>/index.m3u8`.

## API: Telegram

- Configure `api/config.php` with your Telegram `bot_token` and `channel_id`.
- Webhook handler: `api/telegram.php` supports commands like `/start`, `/videos`, `/search`.
- Autopost script: `api/auto_post.php` queries recent videos/streams and posts to the configured channel; run via cron or the `cron` service.

## PWA Hints

- `index.js` registers `sw.js` and provides install prompts on mobile; `sw.js` implements minimal caching and avoids caching uploads/HLS.

## Troubleshooting

- White screen or 500s: check `f_data/logs/*` and the admin log viewer.
- DB connection errors: validate env vars and connectivity (f_core/f_classes/class.database.php).
- Permissions: ensure `f_data/` directories are writable per `__install/INSTALL.txt`.
- Live not working: verify SRS is running and HLS path mounted to Caddy; inspect `deploy/srs.conf` and container logs.
- Telegram autopost: ensure valid token/channel and outbound network access from the PHP/cron containers.

## License

This project is distributed under the EasyStream Proprietary License. See `LICENSE.txt`.
