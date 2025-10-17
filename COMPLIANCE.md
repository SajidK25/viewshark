Privacy, Data Export/Delete, and Admin Audit

This document outlines how to implement user privacy controls and admin auditing in EasyStream.

User Data Export
- Endpoint: `api/privacy.php?action=export` (requires login)
- Returns a JSON bundle of key user data (profile, uploads, subscriptions). The current implementation returns a stub template; extend to include all relevant fields.

User Data Delete (Account Deletion)
- Endpoint: `api/privacy.php?action=delete` (requires login and CSRF token)
- Performs a soft-delete or anonymization pass across user-owned content and PII. The current implementation is a stub returning 202; extend with real logic gated by configuration and admin review.

Admin Audit Logs
- Enable database logging in `f_core/config.logging.php` via `logging_database_logging`.
- The logger writes to `db_logs` with request id, user id, IP, and optional context.
- Use `f_modules/m_backend/log_viewer.php` to browse logs; it supports search and time filtering.

Security Considerations
- Require authentication and CSRF validation for destructive actions.
- Enforce rate limiting via `VSecurity::checkRateLimit`.
- Consider adding a review workflow for delete requests.

