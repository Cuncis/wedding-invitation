<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('order_number')
                    ->label('No. Order')
                    ->copyable(),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                TextEntry::make('user.name')
                    ->label('Pelanggan'),
                TextEntry::make('user.email')
                    ->label('Email Pelanggan'),
                TextEntry::make('couple_names')
                    ->label('Mempelai')
                    ->getStateUsing(fn($record) => $record->invitation?->coupleNames() ?? '—'),
                TextEntry::make('invitation.slug')
                    ->label('Slug Undangan')
                    ->placeholder('—'),
                TextEntry::make('theme_price')
                    ->label('Harga Tema')
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('addon_price')
                    ->label('Harga Addon')
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('animation_price')
                    ->label('Harga Animasi')
                    ->money('IDR', locale: 'id_ID'),
                TextEntry::make('total_amount')
                    ->label('Total')
                    ->money('IDR', locale: 'id_ID')
                    ->weight('bold'),
                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i'),
                TextEntry::make('expires_at')
                    ->label('Kadaluarsa')
                    ->dateTime('d M Y H:i')
                    ->placeholder('—'),
            ]);
    }
}
