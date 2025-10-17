# EasyStream URL Configuration Guide

## Overview

EasyStream has been configured to use `http://localhost:8083` as the default URL for Docker deployments. This ensures consistency and repeatability for all users while allowing customization during setup.

## Default Configuration

- **Default URL**: `http://localhost:8083`
- **Docker Port**: 8083 (mapped to container port 80)
- **Admin Panel**: `http://localhost:8083/admin`
- **Environment Variable**: `MAIN_URL`

## Configuration Methods

### 1. During Setup (Recommended)

When running the setup process at `/setup.php`, you can specify your preferred URL:

- **Local Development**: `http://localhost:8083`
- **Local Network**: `http://192.168.1.100:8083`
- **Custom Domain**: `https://yourdomain.com`
- **Subdomain**: `https://stream.yourdomain.com`

### 2. Using the URL Configuration Tool

Visit `/configure_url.php` to change the URL after installation:

1. Enter your desired URL
2. Click "Update Configuration"
3. Restart Docker containers if using Docker

### 3. Manual Configuration

#### Update config.set.php
```php
$cfg['main_url'] = getenv('MAIN_URL') ?: 'http://your-new-url.com';
```

#### Update docker-compose.yml
```yaml
environment:
  MAIN_URL: http://your-new-url.com
  CRON_BASE_URL: http://your-new-url.com
```

#### Update Environment Variables
```bash
export MAIN_URL=http://your-new-url.com
```

## Docker Deployment

### Standard Deployment (Port 8083)
```bash
# Start services
docker-compose up -d

# Access the application
http://localhost:8083
```

### Custom Port Deployment
To use a different port, update `docker-compose.yml`:

```yaml
services:
  caddy:
    ports:
      - "8080:80"  # Change 8083 to your preferred port
      - "8443:443"
```

Then update the URL accordingly:
```yaml
environment:
  MAIN_URL: http://localhost:8080
```

## Production Deployment

### With Custom Domain

1. **Update DNS**: Point your domain to your server IP
2. **Configure URL**: Use your domain in setup or configuration
3. **SSL Certificate**: Configure SSL in your reverse proxy

Example configuration:
```yaml
environment:
  MAIN_URL: https://yourdomain.com
```

### With Reverse Proxy

If using a reverse proxy (nginx, Apache, etc.):

1. Configure your proxy to forward to `localhost:8083`
2. Set the `MAIN_URL` to your public URL
3. Ensure proper headers are forwarded

## Environment Variables

All URL-related environment variables:

| Variable | Default | Description |
|----------|---------|-------------|
| `MAIN_URL` | `http://localhost:8083` | Main application URL |
| `CRON_BASE_URL` | `http://localhost:8083` | Base URL for cron jobs |

## Verification

After changing the URL configuration:

1. **Check Configuration**: Visit `/configure_url.php`
2. **Test Access**: Visit your configured URL
3. **Admin Panel**: Access `/admin` with your URL
4. **System Test**: Run `/test_core.php`

## Troubleshooting

### URL Not Working
- Verify Docker containers are running: `docker-compose ps`
- Check port availability: `netstat -an | grep 8083`
- Restart containers: `docker-compose restart`

### Admin Panel 404
- Ensure URL is correctly configured
- Check if `/admin` redirects properly
- Try direct admin access: `/admin_direct.php`

### Database Connection Issues
- Verify environment variables are set
- Check database container status
- Review logs: `docker-compose logs db`

## Security Considerations

1. **Change Default Credentials**: Always change admin username/password
2. **Use HTTPS**: For production deployments
3. **Firewall Rules**: Restrict access to necessary ports only
4. **Regular Updates**: Keep Docker images updated

## Examples

### Local Development
```
URL: http://localhost:8083
Admin: http://localhost:8083/admin
```

### Local Network Access
```
URL: http://192.168.1.100:8083
Admin: http://192.168.1.100:8083/admin
```

### Production with Domain
```
URL: https://stream.mycompany.com
Admin: https://stream.mycompany.com/admin
```

### Custom Port
```
URL: http://localhost:9000
Admin: http://localhost:9000/admin
```

## Quick Commands

```bash
# Start EasyStream
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f

# Restart services
docker-compose restart

# Stop services
docker-compose down

# Update configuration and restart
docker-compose down && docker-compose up -d
```

## Support

For additional help:
- Check `/diagnose_404.php` for system diagnostics
- Use `/simple_test.php` for basic connectivity tests
- Review Docker logs for detailed error information