<?php

namespace App\Filament\Resources\AnimationPacks\Pages;

use App\Filament\Resources\AnimationPacks\AnimationPackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnimationPack extends EditRecord
{
    protected static string $resource = AnimationPackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
