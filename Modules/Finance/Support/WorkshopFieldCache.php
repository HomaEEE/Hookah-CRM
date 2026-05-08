<?php

namespace Modules\Finance\Support;

use App\Models\Workshop;
use Illuminate\Support\Facades\Cache;

class WorkshopFieldCache
{
    public static function get(): array
    {
        return Cache::rememberForever('workshop_fields', function () {
            return Workshop::query()
                ->pluck('field', 'workshop_id')
                ->toArray();
        });
    }

    public static function field(int $workshopId): ?string
    {
        return self::get()[$workshopId] ?? null;
    }
}
