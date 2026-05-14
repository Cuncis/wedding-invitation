<?php

namespace App\Filament\Resources\AnimationPacks\Pages;

use App\Filament\Resources\AnimationPacks\AnimationPackResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnimationPacks extends ListRecords
{
    protected static string $resource = AnimationPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
