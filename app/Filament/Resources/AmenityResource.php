<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AmenityResource\Pages;
use App\Filament\Resources\AmenityResource\RelationManagers;
use App\Models\Amenity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AmenityResource extends Resource
{
    protected static ?string $model = Amenity::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationGroup = 'Property Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                Forms\Components\Textarea::make('description')
                    ->maxLength(500)
                    ->rows(3),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon (CSS class or emoji)')
                    ->maxLength(50)
                    ->placeholder('e.g., heroicon-o-wifi or 🏊'),
                Forms\Components\Select::make('category')
                    ->options([
                        'essential' => 'Essential',
                        'entertainment' => 'Entertainment',
                        'safety' => 'Safety & Security',
                        'accessibility' => 'Accessibility',
                        'outdoor' => 'Outdoor',
                        'services' => 'Services',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_popular')
                    ->label('Popular Amenity')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state ? '<span class="text-lg">' . $state . '</span>' : ''),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'danger' => 'essential',
                        'warning' => 'entertainment',
                        'success' => 'safety',
                        'primary' => 'accessibility',
                        'info' => 'outdoor',
                        'secondary' => 'services',
                    ])
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('properties_count')
                    ->label('Properties')
                    ->counts('properties')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'essential' => 'Essential',
                        'entertainment' => 'Entertainment',
                        'safety' => 'Safety & Security',
                        'accessibility' => 'Accessibility',
                        'outdoor' => 'Outdoor',
                        'services' => 'Services',
                    ])
                    ->multiple(),
                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label('Popular')
                    ->boolean()
                    ->trueLabel('Popular Amenities')
                    ->falseLabel('Regular Amenities')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_popular')
                        ->label('Mark as Popular')
                        ->icon('heroicon-o-star')
                        ->action(function ($records) {
                            $records->each->update(['is_popular' => true]);
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
            'index' => Pages\ListAmenities::route('/'),
            'create' => Pages\CreateAmenity::route('/create'),
            'edit' => Pages\EditAmenity::route('/{record}/edit'),
        ];
    }
}
