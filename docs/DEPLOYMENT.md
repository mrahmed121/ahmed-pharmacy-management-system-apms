# APMS Deployment

## Windows (Recommended)
Double-click `RUN_APMS.bat`. See README for details.

## Manual (Linux/Mac)

```bash
cd backend
composer install --no-dev --optimize-autoloader
cp .env.example .env
# Edit .env: APP_ENV=production, APP_DEBUG=false, DB_*
php artisan key:generate
php artisan jwt:secret
php artisan migrate --force
php artisan config:cache
php artisan route:cache

cd ../frontend
npm install
npm run build
# Serve dist/ via Nginx/Apache
```

## Production Checklist
- [ ] `APP_DEBUG=false`
- [ ] Strong `APP_KEY` and `JWT_SECRET`
- [ ] MySQL/PostgreSQL (not SQLite)
- [ ] HTTPS enabled
- [ ] Regular database backups
- [ ] Set up scheduled job for expiry alerts: `php artisan schedule:run`
