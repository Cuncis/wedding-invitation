<?php

namespace App\Filament\Resources\Themes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ThemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('preview_image')
                    ->image(),
                TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Harga dalam Rupiah (tanpa desimal). Contoh: 99000 = Rp 99.000'),
                \Filament\Forms\Components\KeyValue::make('default_colors')
                    ->keyLabel('Slot')
                    ->valueLabel('Hex color')
                    ->columnSpanFull(),
                \Filament\Forms\Components\KeyValue::make('default_fonts')
                    ->keyLabel('Role')
                    ->valueLabel('Font family')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
