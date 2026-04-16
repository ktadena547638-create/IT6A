# TaskFlow: Sovereign Project Management Ecosystem

## Lab Deployment Quick-Start

### Prerequisites
- PHP 8.3+ with Composer
- Node.js 18+ with npm
- SQLite (included with most systems) or MySQL

### 4-Command Deployment

```bash
# Step 1: Install PHP dependencies
composer install

# Step 2: Configure environment
cp .env.example .env
php artisan key:generate

# Step 3: Setup database and seed sample data
php artisan migrate:fresh --seed

# Step 4: Build frontend assets
npm install && npm run build
```

### Run the System

**Development mode with live reload:**
```bash
php artisan serve
```

Then open: `http://localhost:8000`

**Separate terminal for frontend dev:**
```bash
npm run dev
```

### Sample Credentials

After running `php artisan migrate:fresh --seed`:

| User | Email | Password | Role |
|------|-------|----------|------|
| Admin User | `admin@test.com` | `password` | Administrator |
| Project Manager One | `pm1@test.com` | `password` | Project Manager |
| Team Member One | `team1@test.com` | `password` | Team Member |

All 8 sample users created. See `database/seeders/DatabaseSeeder.php`.

---

## Troubleshooting Lab Deployment

### Missing Composer?
```bash
# Install globally or run via php
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); php composer-setup.php;"
php composer.phar install
```

### SQLite Permission Error
```bash
# Ensure storage directory is writable
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Port 8000 Already In Use
```bash
php artisan serve --port=8001
# Open: http://localhost:8001
```

### Database File Not Syncing
```bash
# SQLite database stored at: database/database.sqlite
# In .env: DB_DATABASE=database/database.sqlite (relative path)
```

### Assets Not Loading
```bash
# Clear cache and rebuild
php artisan view:clear
npm run build
```

---

## Architecture Highlights

- **Atomic Transactions**: All operations complete or rollback entirely
- **Centralized Authorization**: Gate::before() pattern prevents scattered logic
- **Zero N+1 Queries**: Eager loading + 5-minute caching = sub-250ms response
- **Immutable Audit Trails**: Complete TaskActivity logging for compliance
- **Three-Tier Delegation**: Admin → Manager → Team Member role hierarchy

---

## Documentation

See `TASKFLOW_DISSERTATION.md` for comprehensive technical specifications.

---

## System Requirements

- **Database**: SQLite (default) or MySQL 5.7+
- **Cache**: Database (default) or Redis for production
- **Queue**: Database (default) for reliable job processing
- **Storage**: Disk-based (default) for attachments

All using relative paths. No hardcoded system paths.
