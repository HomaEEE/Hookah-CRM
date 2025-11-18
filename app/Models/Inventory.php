<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inventory extends Model
{
    use HasFactory,  InteractsWithMedia;

    protected $fillable = ['name', 'price'];

    protected $casts = ['sum' => MoneyCast::class];

    public $timestamps = false;

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
