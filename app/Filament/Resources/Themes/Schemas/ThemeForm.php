<?php

namespace App\Filament\Resources\Themes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ThemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatis dari nama, atau isi manual.'),
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
