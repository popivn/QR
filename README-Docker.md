# Laravel QR Scanner - Docker Deployment

## Tổng quan

Dự án này sử dụng Docker để deploy ứng dụng Laravel QR Scanner lên server production. Tất cả các file Docker được tối ưu cho production, không phải cho development.

## Cấu trúc file Docker

```
├── Dockerfile                 # Multi-stage build cho production
├── .dockerignore             # Tối ưu build context
├── docker-compose.yml        # Docker Compose cho development/testing
├── docker-compose.prod.yml   # Docker Compose cho production với SSL
├── deploy.sh                 # Script tự động deploy
├── docker/
│   ├── nginx.conf           # Cấu hình Nginx chính
│   ├── default.conf         # Virtual host cho Laravel
│   ├── supervisord.conf     # Quản lý processes
│   ├── www.conf            # Cấu hình PHP-FPM
│   └── php.ini             # Cấu hình PHP production
└── README-Docker.md         # Tài liệu này
```

## Tính năng chính

### 🐳 Dockerfile Production-Ready
- **Multi-stage build** để tối ưu kích thước image
- **PHP 8.2-FPM** với Alpine Linux (nhẹ và bảo mật)
- **Nginx** làm web server
- **Supervisor** quản lý multiple processes
- **OPcache** và các tối ưu PHP khác
- **Health check** tự động

### 🔧 Cấu hình tối ưu
- **Nginx**: Gzip compression, security headers, static file caching
- **PHP-FPM**: Process management, security settings
- **PHP**: OPcache, memory limits, error handling
- **Laravel**: Config cache, route cache, view cache

### 🚀 Deployment
- **Docker Compose** cho easy deployment
- **Volume mapping** cho persistent data
- **Health checks** và monitoring
- **SSL support** cho production

## Cách sử dụng

### 1. Deploy cơ bản (HTTP)

```bash
# Clone repository và cd vào thư mục
git clone <repository-url>
cd QRScan

# Tạo file .env từ .env.example
cp .env.example .env
# Chỉnh sửa .env với cấu hình production

# Chạy script deploy tự động
chmod +x deploy.sh
./deploy.sh
```

### 2. Deploy thủ công

```bash
# Build image
docker-compose build

# Start containers
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

### 3. Deploy production với SSL

```bash
# Sử dụng docker-compose.prod.yml
docker-compose -f docker-compose.prod.yml up -d
```

## Quản lý ứng dụng

### Xem logs
```bash
# Tất cả logs
docker-compose logs -f

# Chỉ logs của app
docker-compose logs -f app

# Logs với timestamp
docker-compose logs -f -t
```

### Restart services
```bash
# Restart tất cả
docker-compose restart

# Restart chỉ app
docker-compose restart app
```

### Update ứng dụng
```bash
# Pull code mới
git pull

# Rebuild và restart
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Backup database
```bash
# Copy database file
docker cp qr-scanner-app:/var/www/html/database/database.sqlite ./backup-$(date +%Y%m%d).sqlite
```

## Cấu hình môi trường

### Biến môi trường quan trọng

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Security
SESSION_SECURE_COOKIE=true
```

### SSL Certificate

Để sử dụng SSL trong production:

1. Đặt certificate files vào thư mục `ssl/`:
   - `ssl/cert.pem` - Certificate file
   - `ssl/key.pem` - Private key file

2. Sử dụng `docker-compose.prod.yml`

## Monitoring và Health Checks

### Health Check Endpoint
- URL: `http://yourdomain.com/health`
- Trả về: `200 OK` với "healthy"

### Container Health
```bash
# Check container health
docker ps

# Detailed health status
docker inspect qr-scanner-app | grep -A 10 "Health"
```

## Troubleshooting

### Container không start
```bash
# Check logs
docker-compose logs app

# Check container status
docker-compose ps

# Restart container
docker-compose restart app
```

### Permission issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data /var/www/html/storage
docker-compose exec app chmod -R 755 /var/www/html/storage
```

### Database issues
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
```

### Performance issues
```bash
# Check resource usage
docker stats

# Optimize Laravel
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Security Notes

- ✅ Không expose PHP version
- ✅ Security headers được set
- ✅ Sensitive files được block
- ✅ PHP functions nguy hiểm bị disable
- ✅ File upload limits
- ✅ Session security
- ✅ OPcache enabled

## Performance Optimizations

- ✅ Multi-stage Docker build
- ✅ Nginx gzip compression
- ✅ Static file caching
- ✅ PHP OPcache
- ✅ Laravel caching (config, routes, views)
- ✅ Process management với Supervisor

## Support

Nếu gặp vấn đề, hãy check:
1. Container logs: `docker-compose logs -f`
2. Health check: `curl http://localhost/health`
3. Container status: `docker-compose ps`
4. Resource usage: `docker stats`
