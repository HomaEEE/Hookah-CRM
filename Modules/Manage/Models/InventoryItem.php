<?php

namespace Modules\Manage\Models;

use App\Casts\MoneyCast;
use App\Models\Place;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Manage\Enums\OwnEnum;
use Modules\Manage\Enums\StateEnum;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class InventoryItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'warehouse_id',
        'name',
        'serial_number',
        'quantity',
        'price',
        'comment',
    ];

    protected $casts = [
        'own' => OwnEnum::class,
        'state' => StateEnum::class,
        'quantity' => 'integer',
        'price' => MoneyCast::class,
        'serial_number' => 'array',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->quantity * $this->price,
        );
    }
}
