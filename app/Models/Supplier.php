<?php

namespace App\Models;

use App\Enums\SupplierType;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company', 'place_id', 'phone', 'comment', 'mail', 'data'];

    protected $casts = ['data' => 'array','type' => SupplierType::class];
    public $timestamps = false;


    public function place()
    {
        return $this->BelongsTo(Place::class);
    }

    public function supply()
    {
        return $this->hasOne(Supply::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function getNameCompanyAttribute()
    {
        return $this->name.' '.$this->company;
    }

    public function scopeVisible($query)
    {
        return $query->where('visible', 1);
    }
    public function scopeType($query)
    {
        return $query->where('type', $query);
    }

}
