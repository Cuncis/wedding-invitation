<?php

namespace App\Filament\Resources\AnimationPacks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnimationPackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('key')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                \Filament\Forms\Components\TagsInput::make('features')
                    ->placeholder('Tambahkan fitur, tekan Enter')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Harga dalam Rupiah. 0 untuk tier gratis.'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
