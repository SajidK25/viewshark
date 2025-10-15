# ViewShark Donations Module

A comprehensive donation system for ViewShark with Square integration, analytics, goals, and notifications.

## Structure
```
m_donations/
├── src/              # Source code
│   ├── Core/        # Core functionality
│   ├── Handlers/    # Business logic
│   ├── Models/      # Data models
│   └── Utils/       # Utilities
├── config/          # Configuration
├── sql/             # Database schemas
├── public/          # Public files
├── views/           # Templates
└── assets/          # Static assets
    ├── css/        # Styles
    ├── js/         # Scripts
    └── img/        # Images
```

## Features
- Square payment integration
- Donation goals and milestones
- Real-time analytics
- Notification system
- RESTful API
- Rate limiting
- Security features

## Installation
1. Copy module files:
   ```bash
   cp -r m_donations/ /path/to/viewshark/f_modules/m_frontend/
   ```

2. Import database:
   ```bash
   mysql -u your_username -p your_database < sql/install.sql
   ```

3. Configure:
   - Copy `config/config.example.php` to `config/config.php`
   - Update Square credentials
   - Set webhook URL: `https://your-domain.com/f_modules/m_frontend/m_donations/public/webhook.php`

## Database Tables
- `donations` - Donation records
- `donation_goals` - Streamer goals
- `donation_milestones` - Goal milestones
- `donation_analytics` - Analytics data
- `donation_notifications` - System notifications
- `api_keys` - API authentication
- `api_rate_limits` - Rate limiting

## Security
- API key authentication
- Rate limiting
- Input validation
- XSS protection
- CSRF protection
- SQL injection prevention

## Development
1. Add database tables in `sql/install.sql`
2. Update `config/config.php`
3. Create model in `src/Models/`
4. Create handler in `src/Handlers/`
5. Add API endpoints in `api/index.php`
6. Create views in `views/`
7. Add assets in `assets/`

## Testing
- Unit tests: `phpunit tests/`
- API tests: `phpunit tests/api/`
- Integration tests: `phpunit tests/integration/`

## Support
- Email: support@viewshark.com
- Docs: https://docs.viewshark.com/donations
- Issues: https://github.com/viewshark/donations/issues

## License
ViewShark License Agreement 