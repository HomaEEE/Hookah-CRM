<?php

namespace Modules\Cms\Filament\Cms\Resources\Pages\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Cms\Filament\Cms\Resources\Pages\PageResource;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
