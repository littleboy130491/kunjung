<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    
    protected static ?string $navigationGroup = 'Property Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('host_id')
                            ->label('Host')
                            ->relationship('host', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, callable $set) => 
                                $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                            ),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),
                        Forms\Components\Select::make('property_type')
                            ->options([
                                'villa' => 'Villa',
                                'house' => 'House',
                                'apartment' => 'Apartment',
                                'studio' => 'Studio',
                                'cottage' => 'Cottage',
                                'cabin' => 'Cabin',
                                'treehouse' => 'Treehouse',
                                'tent' => 'Tent',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\CheckboxList::make('service_types')
                            ->options([
                                'accommodation' => 'Accommodation',
                                'wedding_venue' => 'Wedding Venue',
                                'photo_shoot' => 'Photo Shoot Location',
                                'event_space' => 'Event Space',
                                'retreat_center' => 'Retreat Center',
                            ])
                            ->columns(2)
                            ->required(),
                        Forms\Components\Textarea::make('short_description')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Property Details')
                    ->schema([
                        Forms\Components\TextInput::make('max_guests')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(50),
                        Forms\Components\TextInput::make('bedrooms')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(20),
                        Forms\Components\TextInput::make('bathrooms')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(20)
                            ->step(0.5),
                        Forms\Components\TextInput::make('beds')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(30),
                        Forms\Components\TextInput::make('property_size_sqm')
                            ->label('Property Size (sqm)')
                            ->numeric()
                            ->minValue(10)
                            ->suffix('sqm'),
                        Forms\Components\TextInput::make('land_size_sqm')
                            ->label('Land Size (sqm)')
                            ->numeric()
                            ->minValue(10)
                            ->suffix('sqm'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('region')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->step(0.000001)
                            ->rules(['between:-90,90']),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->step(0.000001)
                            ->rules(['between:-180,180']),
                        Forms\Components\Textarea::make('directions')
                            ->maxLength(1000)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('base_price_per_night')
                            ->label('Base Price per Night')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('weekend_price_per_night')
                            ->label('Weekend Price per Night')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('cleaning_fee')
                            ->label('Cleaning Fee')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('service_fee')
                            ->label('Service Fee')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('security_deposit')
                            ->label('Security Deposit')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('minimum_stay_nights')
                            ->label('Minimum Stay (Nights)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(30)
                            ->default(1),
                        Forms\Components\TextInput::make('maximum_stay_nights')
                            ->label('Maximum Stay (Nights)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Check-in & Rules')
                    ->schema([
                        Forms\Components\TimePicker::make('check_in_time')
                            ->label('Check-in Time')
                            ->required()
                            ->default('15:00'),
                        Forms\Components\TimePicker::make('check_out_time')
                            ->label('Check-out Time')
                            ->required()
                            ->default('11:00'),
                        Forms\Components\Select::make('cancellation_policy')
                            ->options([
                                'flexible' => 'Flexible - Full refund 1 day prior',
                                'moderate' => 'Moderate - Full refund 5 days prior',
                                'strict' => 'Strict - 50% refund up to 1 week prior',
                                'super_strict' => 'Super Strict - 50% refund up to 30 days prior',
                            ])
                            ->required()
                            ->default('moderate'),
                        Forms\Components\Textarea::make('house_rules')
                            ->maxLength(2000)
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('space_description')
                            ->label('Space Description')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('guest_access_description')
                            ->label('Guest Access Description')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('neighborhood_description')
                            ->label('Neighborhood Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Photo Gallery')
                    ->schema([
                        CuratorPicker::make('main_photo_url')
                            ->label('Main Photo')
                            ->buttonLabel('Select Main Photo')
                            ->required(),
                        CuratorPicker::make('photo_urls')
                            ->label('Additional Photos')
                            ->buttonLabel('Select Photos')
                            ->multiple()
                            ->reorderable(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Property Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified'),
                        Forms\Components\Toggle::make('accepts_instant_booking')
                            ->label('Accepts Instant Booking'),
                        Forms\Components\TextInput::make('view_count')
                            ->label('View Count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        Forms\Components\TextInput::make('booking_count')
                            ->label('Booking Count')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_photo_url')
                    ->label('Photo')
                    ->square()
                    ->defaultImageUrl(url('/images/default-property.png')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('host.name')
                    ->label('Host')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('property_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'villa',
                        'success' => 'house',
                        'warning' => 'apartment',
                        'info' => 'studio',
                        'secondary' => 'cottage',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('base_price_per_night')
                    ->label('Price/Night')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Guests')
                    ->suffix(' guests')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bedrooms')
                    ->label('Bed/Bath')
                    ->formatStateUsing(fn ($record) => $record->bedrooms . '/' . $record->bathrooms)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviews_count')
                    ->label('Reviews')
                    ->counts('reviews')
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('property_type')
                    ->options([
                        'villa' => 'Villa',
                        'house' => 'House',
                        'apartment' => 'Apartment',
                        'studio' => 'Studio',
                        'cottage' => 'Cottage',
                        'cabin' => 'Cabin',
                        'treehouse' => 'Treehouse',
                        'tent' => 'Tent',
                        'other' => 'Other',
                    ])
                    ->multiple(),
                SelectFilter::make('city')
                    ->relationship('city', 'city')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('region')
                    ->relationship('region', 'region')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('host_id')
                    ->label('Host')
                    ->relationship('host', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueLabel('Active Properties')
                    ->falseLabel('Inactive Properties')
                    ->native(false),
                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueLabel('Featured Properties')
                    ->falseLabel('Non-Featured Properties')
                    ->native(false),
                TernaryFilter::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueLabel('Verified Properties')
                    ->falseLabel('Unverified Properties')
                    ->native(false),
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Price From')
                            ->numeric()
                            ->prefix('IDR'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Price To')
                            ->numeric()
                            ->prefix('IDR'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('base_price_per_night', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('base_price_per_night', '<=', $price),
                            );
                    }),
                Tables\Filters\Filter::make('guest_capacity')
                    ->form([
                        Forms\Components\TextInput::make('min_guests')
                            ->label('Minimum Guests')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_guests'],
                                fn (Builder $query, $guests): Builder => $query->where('max_guests', '>=', $guests),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-mark')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Feature')
                        ->icon('heroicon-o-star')
                        ->action(function ($records) {
                            $records->each->update(['is_featured' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
