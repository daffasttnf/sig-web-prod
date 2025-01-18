<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProvinceResource\Pages;
use App\Filament\Resources\ProvinceResource\RelationManagers;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('alt_name')
                    ->required()
                    ->maxLength(255)
                    ->default(''),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('population')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('type_polygon')
                    ->required(),
                Forms\Components\Textarea::make('polygon')
                    ->columnSpanFull(),
                // Tambahan kolom baru
                Forms\Components\TextInput::make('river')
                    ->label('Main River'),
                Forms\Components\TextInput::make('water_quality')
                    ->label('Water Quality'),
                Forms\Components\TextInput::make('ika')
                    ->label('IKA (Water Index)')
                    ->numeric(),
                Forms\Components\TextInput::make('soil_type')
                    ->label('Soil Type'),
                Forms\Components\Textarea::make('soil_characteristics')
                    ->label('Soil Characteristics'),
                Forms\Components\TextInput::make('rainfall')
                    ->label('Rainfall (mm)')
                    ->numeric(),
                Forms\Components\TextInput::make('rainfall_category')
                    ->label('Rainfall Category'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alt_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('population')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_polygon'),
                // Tambahan kolom baru
                Tables\Columns\TextColumn::make('river')
                    ->label('Main River')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('water_quality')
                    ->label('Water Quality')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ika')
                    ->label('IKA (Water Index)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('soil_type')
                    ->label('Soil Type'),
                Tables\Columns\TextColumn::make('soil_characteristics')
                    ->label('Soil Characteristics')
                    ->limit(50),
                Tables\Columns\TextColumn::make('rainfall')
                    ->label('Rainfall (mm)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rainfall_category')
                    ->label('Rainfall Category'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProvinces::route('/'),
            'create' => Pages\CreateProvince::route('/create'),
            'edit' => Pages\EditProvince::route('/{record}/edit'),
        ];
    }
}
