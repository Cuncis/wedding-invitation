<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('order_number'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('invitation.id')
                    ->label('Invitation'),
                TextEntry::make('theme_price')
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('addon_price')
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('animation_price')
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('total_amount')
                    ->money('IDR', locale: 'id_ID')
                    ->weight('bold'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('expires_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
