# Kunjung Platform Setup Instructions

## Quick Setup

**Run the setup script to install all dependencies and configure the platform:**

```bash
# Run the automated setup script
./setup.bat

# Or manually run these commands:
composer install
php artisan filament:install --panels
php artisan vendor:publish --tag="filament-shield-config"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate
php artisan shield:install
```

## Manual Setup Steps

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Update .env with your settings:
APP_NAME="Kunjung Platform"
APP_URL=http://localhost:8000

# Database (SQLite is default)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Midtrans Configuration (for payments)
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false

# Mail Configuration
MAIL_MAILER=smtp
# ... configure your mail settings
```

### 3. Install Filament Admin Panel
```bash
php artisan filament:install --panels
```

### 4. Publish Package Configurations
```bash
# Filament Shield (Role-based access control)
php artisan vendor:publish --tag="filament-shield-config"

# Laravel Permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Filament Curator for media management
php artisan filament:install-plugin awcodes/filament-curator
```

### 5. Run Database Migrations
```bash
php artisan migrate
```

### 6. Setup Filament Shield
```bash
php artisan shield:install
```

### 7. Create Admin User
```bash
php artisan make:filament-user
```

### 8. Seed Database (Optional)
```bash
php artisan db:seed
```

### 9. Build Frontend Assets
```bash
npm run build
# or for development:
npm run dev
```

### 10. Start Development Server
```bash
php artisan serve
```

## Access Points

- **Main Website**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
- **Login with the admin user created in step 7**

## Next Steps After Setup

1. **Configure Roles and Permissions**:
   - Login to admin panel
   - Go to Shield > Roles
   - Create roles: `guest`, `host`, `admin`, `super_admin`

2. **Add Sample Data**:
   - Create amenities (WiFi, Pool, Kitchen, etc.)
   - Add sample properties
   - Create test bookings

3. **Configure Midtrans Payment**:
   - Sign up for Midtrans account
   - Add your API keys to `.env`
   - Test payment integration

4. **Customize Filament Admin**:
   - Review created Filament resources
   - Customize admin dashboard
   - Configure user permissions

## Troubleshooting

### Common Issues:

1. **Permission Errors**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database Issues**:
   ```bash
   # Ensure SQLite file exists
   touch database/database.sqlite
   php artisan migrate:fresh
   ```

3. **Filament Installation Issues**:
   ```bash
   php artisan filament:upgrade
   php artisan optimize:clear
   ```

4. **Package Conflicts**:
   ```bash
   composer dump-autoload
   php artisan config:clear
   php artisan cache:clear
   ```

## Development Workflow

1. Follow the TODO list in `DEVELOPMENT_TODO.md`
2. Use the package recommendations in `CLAUDE.md`
3. Reference the PRD in `PRD_Kunjung_Platform.md`
4. Run tests: `php artisan test`
5. Check code style: `composer pint`

## Package Versions Used

- **Laravel**: ^12.0
- **Filament**: ^3.0
- **Filament Shield**: ^3.0
- **Filament Curator**: ^3.0
- **Laravel Socialite**: ^5.0
- **Midtrans PHP**: ^2.5
- **Livewire**: ^3.0
- **Spatie Permission**: ^6.0
- **Note**: We use Filament Curator for media management instead of Spatie MediaLibrary

All packages are configured to work together seamlessly for rapid development of the Kunjung platform.