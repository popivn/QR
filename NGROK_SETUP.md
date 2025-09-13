# Hướng dẫn cấu hình ngrok cho QR Scanner

## Vấn đề hiện tại
Browser chặn mixed content (HTTP/HTTPS) khiến request không thể gửi được.

## Giải pháp

### 1. Cập nhật file .env
```bash
# Thay đổi APP_URL thành HTTPS ngrok URL
APP_URL=https://f48b02984710.ngrok-free.app

# Cấu hình session cho ngrok
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=none
SESSION_DRIVER=database
```

### 2. Chạy ngrok với HTTPS
```bash
# Chạy ngrok với HTTPS
ngrok http 8000 --host-header=localhost:8000
```

### 3. Cập nhật URL trong browser
- Sử dụng URL HTTPS từ ngrok: `https://f48b02984710.ngrok-free.app`
- Không sử dụng HTTP URL

### 4. Kiểm tra CORS headers
Middleware đã được cập nhật để:
- `HandleNgrok`: Xử lý CORS cho QR scanner và API
- `HandleAuthNgrok`: Xử lý CORS cho authentication (login/logout)
- Xử lý preflight OPTIONS requests
- Thêm CORS headers phù hợp
- Thêm Content Security Policy
- Xử lý mixed content issues

### 5. Xử lý Authentication Issues
Nếu không thể đăng nhập/đăng xuất:
1. Kiểm tra session configuration trong .env
2. Đảm bảo SESSION_DRIVER=database
3. Chạy migration: `php artisan migrate`
4. Clear cache: `php artisan config:clear`
5. Clear session: `php artisan session:table`

### 6. Debug steps
1. Mở Developer Tools (F12)
2. Kiểm tra Console tab để xem lỗi
3. Kiểm tra Network tab để xem request/response
4. Đảm bảo request được gửi đến HTTPS URL

### 7. Test
1. Truy cập: `https://f48b02984710.ngrok-free.app/qr/scanner`
2. Thử quét QR code hoặc nhập thủ công
3. Kiểm tra response trong Network tab

## Lưu ý
- Luôn sử dụng HTTPS URL từ ngrok
- Đảm bảo session được cấu hình đúng
- Kiểm tra CORS headers trong response

## Xử lý CSP và Mixed Content Issues

### CSP (Content Security Policy) Issues
Nếu gặp lỗi "Content Security Policy blocks some resources":
1. CSP đã được cấu hình relaxed cho development
2. Cho phép cả HTTP và HTTPS resources
3. Nếu vẫn bị chặn, kiểm tra browser console để xem resource nào bị chặn

### Mixed Content Issues
Nếu gặp lỗi "Mixed content: load all resources via HTTPS":
1. Đảm bảo tất cả URLs sử dụng HTTPS
2. JavaScript đã được cập nhật để tự động convert HTTP → HTTPS
3. Logout form đã được cập nhật để sử dụng HTTPS

### Debug CSP Issues
1. Mở Developer Tools (F12)
2. Kiểm tra Console tab để xem CSP violations
3. Kiểm tra Network tab để xem resources bị chặn
4. Nếu cần, có thể tạm thời disable CSP trong browser để test
