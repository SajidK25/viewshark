# EasyStream Setup Guide

## 🚀 Quick Start

### 1. Start EasyStream
```bash
docker-compose up -d
```

### 2. Wait for Services
Wait 1-2 minutes for all services to start and the database to initialize.

### 3. Complete Setup
Visit: **http://localhost:8083/setup.php**

Follow the setup wizard to configure your admin account and site settings.

### 4. Access Admin Panel
After setup, access your admin panel at: **http://localhost:8083/admin**

## 📋 Default Configuration

- **Main Site:** http://localhost:8083
- **Admin Panel:** http://localhost:8083/admin
- **Default Admin:** admin / admin123 (changeable during setup)

## 🔧 Services Included

- **Web Server:** Caddy (Port 8083)
- **Database:** MariaDB (Port 3306)
- **Cache/Queue:** Redis (Port 6379)
- **Streaming:** SRS (Port 1935)
- **Background Jobs:** Queue Worker

## 🛠️ Troubleshooting

### Services Not Starting
```bash
# Check service status
docker ps

# View logs
docker logs vs-db
docker logs vs-php
docker logs vs-caddy
```

### Database Issues
```bash
# Restart database
docker-compose restart db

# Check database logs
docker logs vs-db -f
```

### Setup Page Not Loading
1. Ensure all services are running: `docker ps`
2. Wait 2-3 minutes for database initialization
3. Check web server logs: `docker logs vs-caddy`

## 🎯 Features

Your EasyStream installation includes:

- **Video Streaming** - Upload and stream videos
- **Live Broadcasting** - RTMP streaming support
- **User Management** - User accounts and permissions
- **Queue System** - Background video processing
- **Security Features** - IP tracking, fingerprinting, CSRF protection
- **Admin Dashboard** - Complete management interface
- **Redis Caching** - High-performance caching
- **Responsive Design** - Mobile-friendly interface

## 🔒 Security

- Change default admin credentials during setup
- The setup.php file is automatically disabled after first use
- All user inputs are sanitized and validated
- CSRF protection enabled by default
- IP tracking and rate limiting included

## 📞 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Docker logs for error messages
3. Ensure all required ports are available (8083, 3306, 6379, 1935)

---

**EasyStream** - Professional Video Streaming Platform