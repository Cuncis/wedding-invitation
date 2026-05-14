<?php

namespace App\Filament\Resources\AnimationPacks;

use App\Filament\Resources\AnimationPacks\Pages\CreateAnimationPack;
use App\Filament\Resources\AnimationPacks\Pages\EditAnimationPack;
use App\Filament\Resources\AnimationPacks\Pages\ListAnimationPacks;
use App\Filament\Resources\AnimationPacks\Schemas\AnimationPackForm;
use App\Filament\Resources\AnimationPacks\Tables\AnimationPacksTable;
use App\Models\AnimationPack;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnimationPackResource extends Resource
{
    protected static ?string $model = AnimationPack::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static \UnitEnum|string|null $navigationGroup = 'Katalog';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Animation Pack';

    protected static ?string $pluralModelLabel = 'Animation Packs';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AnimationPackForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnimationPacksTable::configure($table);
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
            'index' => ListAnimationPacks::route('/'),
            'create' => CreateAnimationPack::route('/create'),
            'edit' => EditAnimationPack::route('/{record}/edit'),
        ];
    }
}
