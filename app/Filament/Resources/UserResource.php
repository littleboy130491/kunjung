<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Awcodes\Curator\Components\Forms\CuratorPicker;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->maxDate(now()),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                                'prefer_not_to_say' => 'Prefer not to say',
                            ]),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('bio')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Profile Photos')
                    ->schema([
                        CuratorPicker::make('profile_photo_url')
                            ->label('Profile Photo')
                            ->buttonLabel('Select Photo'),
                        CuratorPicker::make('avatar_url')
                            ->label('Avatar')
                            ->buttonLabel('Select Avatar'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Host Information')
                    ->schema([
                        Forms\Components\Toggle::make('is_host_verified')
                            ->label('Host Verified'),
                        Forms\Components\Select::make('host_verification_status')
                            ->options([
                                'unverified' => 'Unverified',
                                'pending' => 'Pending Verification',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected',
                            ])
                            ->default('unverified'),
                        Forms\Components\TextInput::make('host_document_path')
                            ->label('Host Document Path')
                            ->maxLength(500),
                        Forms\Components\TextInput::make('response_rate')
                            ->numeric()
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('response_time_hours')
                            ->label('Response Time (Hours)')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\DateTimePicker::make('last_active_at')
                            ->label('Last Active At'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Password')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->same('passwordConfirmation')
                            ->dehydrated(fn ($state): bool => filled($state)),
                        Forms\Components\TextInput::make('passwordConfirmation')
                            ->password()
                            ->label('Password Confirmation')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_host_verified')
                    ->label('Host Verified')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('host_verification_status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'unverified',
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('response_rate')
                    ->label('Response Rate')
                    ->suffix('%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('response_time_hours')
                    ->label('Response Time')
                    ->suffix('h')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('properties_count')
                    ->label('Properties')
                    ->counts('properties')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_active_at')
                    ->label('Last Active')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('host_verification_status')
                    ->label('Host Status')
                    ->options([
                        'unverified' => 'Unverified',
                        'pending' => 'Pending Verification',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_host_verified')
                    ->label('Host Verified')
                    ->boolean()
                    ->trueLabel('Verified Hosts')
                    ->falseLabel('Unverified Hosts')
                    ->native(false),
                Tables\Filters\Filter::make('has_properties')
                    ->query(fn (Builder $query): Builder => $query->has('properties'))
                    ->label('Has Properties'),
                Tables\Filters\Filter::make('active_recently')
                    ->query(fn (Builder $query): Builder => $query->where('last_active_at', '>=', now()->subDays(30)))
                    ->label('Active in Last 30 Days'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
