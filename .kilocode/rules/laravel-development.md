## Brief overview
This rule file provides specific guidance for Laravel development on the Kunjung Platform, including required packages and model schemas.

## Required Packages and Their Purposes

### Authentication & Authorization
- **Filament Shield** (`bezhansalleh/filament-shield`): Role-based access control for Filament admin panel
- **Laravel Socialite** (`laravel/socialite`): Social authentication (Google, Facebook)

### Media Management
- **Filament Curator** (`awcodes/filament-curator`): Advanced media gallery management for Filament
- **Note**: We use Filament Curator exclusively for media management, not Spatie MediaLibrary

### Payment Integration
- **Midtrans PHP** (`midtrans/midtrans-php`): Official Midtrans payment gateway integration for Indonesian payment methods

### Search & Filtering
- **Laravel Scout** (`laravel/scout`): Search functionality for models
- **Meilisearch** (`meilisearch/meilisearch-php`): Search engine for Laravel Scout
- **Livewire PowerGrid** (`power-components/livewire-powergrid`): Advanced datatables with filtering

### Maps & Location
- **Laravel Geocoder** (`geocoder-php/geocoder-laravel`): Address geocoding and reverse geocoding

### Analytics & Monitoring
- **Laravel Analytics** (`spatie/laravel-analytics`): Google Analytics integration

### Email & Notifications
- **Laravel Notification Channels** (`laravel-notification-channels/*`): Various notification channels (SMS, Slack, etc.)

### Performance & Caching
- **Laravel Responsecache** (`spatie/laravel-responsecache`): Cache entire responses
- **Laravel Model Caching** (`genealabs/laravel-model-caching`): Automatic model caching
- **Laravel Query Cache** (`rennokki/laravel-eloquent-query-cache`): Query result caching

### Testing & Quality
- **Laravel Pest** (`pestphp/pest`): Testing framework

### Development Tools
- **Laravel Debugbar** (`barryvdh/laravel-debugbar`): Debug toolbar for development

## Models and Their Schemas

### User Model
Represents guests, hosts, and admins with role-based permissions.

**Schema**:
- id (integer, primary key)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- remember_token (string, nullable)
- phone (string, nullable)
- date_of_birth (date, nullable)
- gender (string, nullable)
- bio (text, nullable)
- location (string, nullable)
- host_verification_status (enum: pending, verified, rejected)
- host_documents_submitted_at (timestamp, nullable)
- host_verified_at (timestamp, nullable)
- host_rejected_at (timestamp, nullable)
- host_rejection_reason (text, nullable)
- profile_photo_url (string, nullable)
- cover_photo_url (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### Property Model
Represents accommodation listings with detailed information.

**Schema**:
- id (integer, primary key)
- host_id (foreign key to users table)
- title (string)
- slug (string, unique)
- description (longText)
- short_description (text, nullable)
- property_type (enum: villa, house, apartment, resort, hotel, guesthouse, other)
- service_types (json) - Stays, Wedding venues, Photo shoot locations
- max_guests (integer)
- bedrooms (integer)
- bathrooms (integer)
- beds (integer)
- location (string)
- address (text)
- city (string)
- region (string)
- postal_code (string, nullable)
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- base_price_per_night (decimal)
- weekend_price_per_night (decimal, nullable)
- cleaning_fee (decimal)
- service_fee (decimal)
- security_deposit (decimal)
- minimum_stay_nights (integer)
- maximum_stay_nights (integer, nullable)
- check_in_time (time)
- check_out_time (time)
- cancellation_policy (enum: flexible, moderate, strict)
- house_rules (text, nullable)
- space_description (text, nullable)
- guest_access (text, nullable)
- interaction_style (text, nullable)
- neighborhood_overview (text, nullable)
- transit_info (text, nullable)
- is_published (boolean)
- is_featured (boolean)
- is_instant_book (boolean)
- status (enum: draft, active, inactive, suspended)
- authenticity_verified (boolean)
- last_booked_at (timestamp, nullable)
- average_rating (decimal)
- total_reviews (integer)
- response_rate (decimal, nullable)
- view_count (integer)
- favorite_count (integer)
- meta_title (string, nullable)
- meta_description (text, nullable)
- seo_keywords (text, nullable)
- cover_photo_url (string, nullable)
- gallery_photos (json, nullable)
- created_at (timestamp)
- updated_at (timestamp)
- deleted_at (timestamp, nullable)

### Booking Model
Represents reservation records.

**Schema**:
- id (integer, primary key)
- property_id (foreign key to properties table)
- guest_id (foreign key to users table)
- booking_reference (string, unique)
- check_in_date (date)
- check_out_date (date)
- guests_adult (integer)
- guests_children (integer)
- guests_infants (integer)
- guests_pets (integer)
- total_nights (integer)
- base_price (decimal)
- weekend_nights (integer)
- weekend_price (decimal)
- cleaning_fee (decimal)
- service_fee (decimal)
- security_deposit (decimal)
- total_amount (decimal)
- currency (string)
- status (enum: pending, confirmed, cancelled, completed)
- payment_status (enum: unpaid, pending, paid, refunded, failed)
- payment_method (string, nullable)
- special_requests (text, nullable)
- guest_message (text, nullable)
- host_message (text, nullable)
- check_in_instructions (text, nullable)
- confirmed_at (timestamp, nullable)
- cancelled_at (timestamp, nullable)
- cancellation_reason (text, nullable)
- refund_amount (decimal, nullable)
- guest_name (string)
- guest_email (string)
- guest_phone (string, nullable)
- emergency_contact_name (string, nullable)
- emergency_contact_phone (string, nullable)
- estimated_arrival_time (time, nullable)
- actual_check_in_time (timestamp, nullable)
- actual_check_out_time (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### Review Model
Represents verified guest reviews with ratings.

**Schema**:
- id (integer, primary key)
- property_id (foreign key to properties table)
- booking_id (foreign key to bookings table)
- user_id (foreign key to users table)
- rating_overall (decimal)
- rating_cleanliness (decimal, nullable)
- rating_location (decimal, nullable)
- rating_value (decimal, nullable)
- rating_communication (decimal, nullable)
- rating_check_in (decimal, nullable)
- rating_accuracy (decimal, nullable)
- review_text (text)
- host_response (text, nullable)
- is_verified (boolean)
- is_featured (boolean)
- is_approved (boolean)
- helpful_count (integer)
- reported_count (integer)
- language (string)
- stays_count (integer)
- review_photos (json, nullable) - Array of photo objects managed by Filament Curator
- created_at (timestamp)
- updated_at (timestamp)

### Amenity Model
Represents property features.

**Schema**:
- id (integer, primary key)
- name (string)
- slug (string, unique)
- description (text, nullable)
- icon (string, nullable)
- category (enum: essentials, features, location, safety, accessibility, entertainment, kitchen, bathroom, outdoor, services)
- is_popular (boolean)
- sort_order (integer)
- created_at (timestamp)
- updated_at (timestamp)

### PropertyAmenity Model (Pivot)
Many-to-many relationship between properties and amenities.

**Schema**:
- id (integer, primary key)
- property_id (foreign key to properties table)
- amenity_id (foreign key to amenities table)
- created_at (timestamp)
- updated_at (timestamp)

### PaymentTransaction Model
Tracks Midtrans payment processing records.

**Schema**:
- id (integer, primary key)
- booking_id (foreign key to bookings table)
- transaction_id (string, unique)
- midtrans_transaction_id (string, nullable)
- midtrans_order_id (string, nullable)
- payment_type (string, nullable)
- payment_method (string, nullable)
- gross_amount (decimal)
- currency (string)
- transaction_status (string, nullable)
- transaction_time (timestamp, nullable)
- settlement_time (timestamp, nullable)
- fraud_status (string, nullable)
- status_code (string, nullable)
- status_message (string, nullable)
- midtrans_response (json, nullable)
- refund_amount (decimal, nullable)
- refund_reason (string, nullable)
- refunded_at (timestamp, nullable)
- va_number (string, nullable)
- bank (string, nullable)
- bill_key (string, nullable)
- biller_code (string, nullable)
- pdf_url (string, nullable)
- expired_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### LocalGuide Model
Curated local recommendations and experiences.

**Schema**:
- id (integer, primary key)
- property_id (foreign key to properties table)
- title (string)
- description (text)
- category (enum: restaurant, attraction, activity, shopping, transportation, healthcare, emergency, other)
- type (enum: recommendation, essential, emergency, nearby)
- location (string)
- address (text, nullable)
- latitude (decimal, nullable)
- longitude (decimal, nullable)
- distance_km (decimal, nullable)
- estimated_travel_time (string, nullable)
- price_range (string, nullable)
- rating (decimal, nullable)
- phone (string, nullable)
- website (string, nullable)
- opening_hours (json, nullable)
- is_featured (boolean)
- is_verified (boolean)
- sort_order (integer)
- photos (json, nullable) - Array of photo objects managed by Filament Curator
- created_at (timestamp)
- updated_at (timestamp)

### PropertyAvailability Model
Tracks property availability calendar.

**Schema**:
- id (integer, primary key)
- property_id (foreign key to properties table)
- date (date)
- is_available (boolean)
- price_override (decimal, nullable)
- minimum_stay_override (integer, nullable)
- notes (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)

### PropertyFavorite Model
Tracks user's saved properties (wishlists).

**Schema**:
- id (integer, primary key)
- user_id (foreign key to users table)
- property_id (foreign key to properties table)
- created_at (timestamp)
- updated_at (timestamp)