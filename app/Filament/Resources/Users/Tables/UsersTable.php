<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'admin' => 'danger',
                        'customer' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('invitations_count')
                    ->label('Undangan')
                    ->counts('invitations')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('role')
                    ->options(['admin' => 'Admin', 'customer' => 'Customer']),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
