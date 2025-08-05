# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Architecture

This is a Laravel 12 application for the **Kunjung Platform** - a curated accommodation discovery and booking platform for authentic travel experiences in Indonesia. The application follows Laravel conventions with specific technology choices:

- **MVC Structure**: Controllers in `app/Http/Controllers/`, Models in `app/Models/`, Views in `resources/views/`
- **Frontend Stack**: 
  - **Tailwind CSS 4.0** for styling and responsive design
  - **Laravel Livewire** for reactive components and real-time interactions
  - **Vite** for asset compilation and hot reload
- **Admin Panel**: **Filament PHP** for comprehensive admin interface
- **Testing**: Uses Pest PHP testing framework with PHPUnit as the underlying engine
- **Database**: SQLite database located at `database/database.sqlite` with migrations in `database/migrations/`
- **Routes**: Web routes defined in `routes/web.php`, console commands in `routes/console.php`

### Kunjung Platform Specific Architecture

**Core Domain Models**:
- Properties (accommodations with detailed info, amenities, pricing)
- Users (guests, hosts, admins with role-based permissions)
- Bookings (reservations with payment integration)
- Reviews (verified guest reviews with photo uploads)
- Local Guides (curated recommendations and experiences)

**Key Integrations**:
- **Payment Processing**: Midtrans for Indonesian payment methods
- **Maps Integration**: For location-based search and property clustering  
- **Image Management**: CDN integration for optimized property photos
- **Real-time Features**: Livewire for instant availability updates

## Development Commands

### PHP/Laravel Commands
- `php artisan serve` - Start development server
- `php artisan test` - Run all tests using Pest
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh` - Drop all tables and re-run migrations
- `php artisan tinker` - Interactive REPL for Laravel
- `php artisan make:controller ControllerName` - Generate new controller
- `php artisan make:model ModelName` - Generate new model
- `php artisan make:migration migration_name` - Generate new migration

### Livewire Commands
- `php artisan make:livewire ComponentName` - Generate new Livewire component
- `php artisan livewire:publish --config` - Publish Livewire configuration
- `php artisan livewire:discover` - Discover Livewire components

### Filament Commands
- `php artisan make:filament-resource ModelName` - Generate Filament resource for admin panel
- `php artisan make:filament-page PageName` - Generate custom Filament page
- `php artisan make:filament-widget WidgetName` - Generate admin dashboard widget
- `php artisan filament:install --panels` - Install Filament panels

### Composer Commands
- `composer install` - Install PHP dependencies
- `composer dev` - Start full development environment (server + queue + logs + vite)
- `composer test` - Clear config cache and run tests

### Frontend Commands
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build assets for production

### Testing
- Tests are written using Pest PHP framework
- Feature tests in `tests/Feature/`, Unit tests in `tests/Unit/`
- Test configuration extends `Tests\TestCase` class for Feature tests
- Uses SQLite in-memory database for testing (configured in `phpunit.xml`)

## Key Configuration Files

- `composer.json` - PHP dependencies and scripts
- `package.json` - Frontend dependencies and build scripts
- `vite.config.js` - Vite configuration with Laravel plugin and Tailwind CSS
- `phpunit.xml` - PHPUnit/Pest testing configuration
- `tests/Pest.php` - Pest framework configuration and global test helpers

## Database

- Uses SQLite database (`database/database.sqlite`)
- Migrations located in `database/migrations/`
- Factories in `database/factories/`
- Seeders in `database/seeders/`

### Kunjung Platform Database Schema

**Core Tables**:
- `users` - Guest, host, and admin user accounts with role-based permissions
- `properties` - Accommodation listings with detailed information, amenities, and pricing
- `bookings` - Reservation records with payment status and guest information  
- `reviews` - Verified guest reviews with ratings and photo uploads
- `local_guides` - Curated local recommendations and experiences
- `property_amenities` - Many-to-many relationship for property features
- `property_photos` - Property image gallery with optimization metadata
- `payment_transactions` - Midtrans payment processing records

**Key Relationships**:
- Properties belong to hosts (users with host role)
- Bookings connect guests to properties with date ranges
- Reviews are linked to completed bookings for verification
- Local guides are associated with property locations

## Frontend Requirements (Based on PRD)

### UI/UX Implementation with Tailwind CSS & Livewire

**Visual-First Design**:
- Mobile-first responsive design using Tailwind CSS utility classes
- High-quality image galleries with lazy loading and CDN optimization
- Interactive property cards with hover effects and smooth transitions
- Custom Tailwind components for consistent branding

**Reactive Components with Livewire**:
- Real-time property search and filtering without page refreshes
- Live availability updates during booking process
- Interactive map integration with property clustering
- Dynamic pricing calculator showing fees and taxes in real-time
- Instant booking confirmation flow

**Mobile Optimization**:
- Touch-friendly interface elements (minimum 44px touch targets)
- Optimized mobile checkout process with Livewire form handling
- Progressive Web App features for offline booking access
- Swipe gestures for photo galleries and property browsing

### Key Frontend Components

**Search & Discovery**:
- `PropertySearch.php` - Livewire component for smart filtering
- `PropertyCard.php` - Reusable property display component
- `InteractiveMap.php` - Map integration with property clustering
- `CuratedCollections.php` - Themed property groupings

**Booking System**:
- `BookingFlow.php` - Multi-step booking process
- `AvailabilityCalendar.php` - Real-time date selection
- `PricingCalculator.php` - Dynamic price calculation
- `PaymentGateway.php` - Midtrans payment integration

## Backend Requirements (Based on PRD)

### Core Business Logic

**Property Management**:
- Property CRUD operations with image upload and optimization
- Availability calendar management with real-time sync
- Pricing rules with seasonal adjustments and discounts
- Amenity and feature tagging system

**Booking System**:
- Real-time inventory management preventing double bookings
- Payment processing integration with Midtrans
- Automated booking confirmation and notification system
- Booking modification and cancellation handling

**User Management**:
- Role-based access control (guests, hosts, admins)
- Host verification system with document uploads
- Guest review and rating system with photo uploads
- User preference tracking for personalization

### API Integrations

**Payment Processing**:
- Midtrans payment gateway integration for Indonesian payment methods
- Support for credit cards, bank transfers, and digital wallets
- Installment payment options handling
- Fraud detection and payment security

**External Services**:
- Maps API integration for location services and geocoding
- CDN integration for optimized image delivery
- Email service for booking confirmations and notifications
- SMS integration for booking updates and verification

### Admin Panel with Filament PHP

**Property Management**:
- Comprehensive property listing creation and editing
- Photo gallery management with drag-and-drop reordering
- Availability calendar administration
- Pricing and discount rule configuration

**Booking Management**:
- Real-time booking dashboard with status tracking
- Payment transaction monitoring and reconciliation
- Guest communication tools and automated messaging
- Booking analytics and reporting

**User Administration**:
- User role management and permissions
- Host verification workflow and document review
- Review moderation and authenticity verification
- User analytics and behavior tracking

**Content Management**:
- Local guide creation and curation tools
- SEO content optimization tools

## Development Philosophy & Package Strategy

### Rapid Development Approach
**Prefer reputable third-party packages over building from scratch** to accelerate development and ensure robust, well-tested functionality. Focus development time on business logic unique to Kunjung rather than reinventing common solutions.

### Package Selection Criteria
When choosing third-party packages, prioritize:
- **Active maintenance** - Recent commits and issue responses
- **Strong community** - High GitHub stars, active discussions
- **Laravel ecosystem compatibility** - Built for Laravel/Livewire/Filament
- **Documentation quality** - Comprehensive guides and examples
- **Production readiness** - Used by other commercial applications

### Recommended Third-Party Packages

#### Authentication & Authorization
- **Filament Shield** (`bezhansalleh/filament-shield`) - Role-based access control for Filament admin panel
- **Laravel Socialite** (`laravel/socialite`) - Social authentication (Google, Facebook)

#### Media Management
- **Filament Curator** (`awcodes/filament-curator`) - Advanced media gallery management for Filament
- **Note**: We use Filament Curator exclusively for media management, not Spatie MediaLibrary

#### Payment Integration
- **Midtrans PHP** (`midtrans/midtrans-php`) - Official Midtrans payment gateway integration

#### Search & Filtering
- **Livewire PowerGrid** (`power-components/livewire-powergrid`) - Advanced datatables with filtering


#### Maps & Location
- **Laravel Geocoder** (`geocoder-php/geocoder-laravel`) - Address geocoding and reverse geocoding
- **Laravel Maps** (`googlmapper/laravel-googlemaps`) - Google Maps integration

#### Analytics & Monitoring
- **Laravel Analytics** (`spatie/laravel-analytics`) - Google Analytics integration

#### Email & Notifications
- **Laravel Notification Channels** (`laravel-notification-channels/*`) - Various notification channels (SMS, Slack, etc.)

#### Performance & Caching
- **Laravel Responsecache** (`spatie/laravel-responsecache`) - Cache entire responses
- **Laravel Model Caching** (`genealabs/laravel-model-caching`) - Automatic model caching
- **Laravel Query Cache** (`rennokki/laravel-eloquent-query-cache`) - Query result caching

#### Testing & Quality
- **Laravel Pest** (`pestphp/pest`) - Testing framework (already included)

#### Development Tools
- **Laravel Debugbar** (`barryvdh/laravel-debugbar`) - Debug toolbar for development

### Implementation Guidelines

#### For Role-Based Access Control
```php
// Use Filament Shield instead of building custom permissions
php artisan install:filament-shield
// Automatically generates permissions for all Filament resources
```

#### For Media Gallery Management
```php
// Use Filament Curator for property photo galleries
php artisan filament:install-plugin awcodes/filament-curator
// Provides drag-and-drop, cropping, alt text, and CDN integration
```

#### For Search and Filtering
```php
// Use Laravel Scout for property search
php artisan scout:install
// Configure with Meilisearch for fast, typo-tolerant search
```

#### For Payment Processing
```php
// Use official Midtrans SDK
composer require midtrans/midtrans-php
// Follow Indonesian payment method best practices
```

### Package Integration Strategy

#### Phase 1 (MVP) - Essential Packages
- Filament Shield (role-based access)
- Filament Curator (media management)
- Laravel Socialite (social login)
- Midtrans PHP (payments)
- Laravel Scout (search)
- Spatie Query Builder (filtering)

#### Phase 2 (Enhanced) - User Experience Packages
- Laravel Notification Channels (SMS, push notifications)
- Laravel Translatable (multi-language)
- Laravel Analytics (user behavior tracking)
- Livewire PowerGrid (advanced admin tables)

#### Phase 3 (Scale) - Performance and Analytics
- Laravel Responsecache (performance)
- Laravel Model Caching (database optimization)

### Custom Development Focus Areas

Build custom solutions only for:
- **Kunjung-specific business logic** (booking algorithms, pricing rules)
- **Unique user experience features** (curated collections, local recommendations)
- **Integration between third-party services** (connecting maps with bookings)
- **Custom Livewire components** for reactive user interfaces

### Package Maintenance Strategy

- **Regular updates** - Schedule monthly package updates
- **Security monitoring** - Subscribe to security advisories
- **Performance testing** - Monitor package impact on application performance
- **Fallback planning** - Document alternatives for critical packages
- **Contribution back** - Submit bug fixes and improvements to package maintainers

This approach ensures rapid development while maintaining code quality and reducing technical debt through battle-tested, community-maintained solutions.

## PRD & Development Alignment

### Reference Documents
- **PRD**: `PRD_Kunjung_Platform.md` - Complete product requirements and user journey specifications
- **TODO**: `DEVELOPMENT_TODO.md` - Comprehensive development task breakdown with 125+ actionable items
- **User Research**: Based on `storage/app/private/Kunjung — User Research.pdf` analysis

### Key PRD Implementation Notes


#### Core Feature Requirements
All development should align with these PRD-defined core features:
- **Enhanced Search & Discovery** - Visual-first interface with smart filtering
- **Streamlined Booking System** - One-click booking with Midtrans integration
- **Trust & Verification System** - Host verification, verified reviews, authenticity badges
- **Personalized Experience Engine** - Local recommendations, user preference learning
- **Mobile-First Platform** - PWA functionality, offline access, push notifications


### TODO List Integration

#### Phase-Based Development
Follow the TODO list's 3-phase approach:
- **Phase 1 (MVP)**: Focus on foundation features with essential packages
- **Phase 2 (Enhanced)**: User experience improvements with advanced packages
- **Phase 3 (Scale)**: Performance optimization and growth features

#### Task-Package Mapping
When working on TODO items, use recommended packages:
- Authentication tasks → Filament Shield + Laravel Socialite
- Media management tasks → Filament Curator (NOT Spatie MediaLibrary)
- Payment integration → Midtrans PHP
- Admin panel features → Filament ecosystem packages

**Important**: All media handling (user photos, property galleries, review photos) uses Filament Curator's media management system. Models store photo URLs/arrays directly in database columns instead of using Spatie MediaLibrary relationships.


### Implementation Consistency

#### Code Organization
- Follow Laravel conventions outlined in this document
- Use Livewire for reactive components
- Apply Tailwind CSS utility-first approach
- Leverage Filament for all admin interfaces

#### Data Models
Ensure all models align with PRD requirements:
- Properties with detailed amenities and pricing
- Users with role-based permissions (guests, hosts, admins)
- Bookings with payment integration and status tracking
- Reviews with photo uploads and verification
- Local guides with location-based recommendations

