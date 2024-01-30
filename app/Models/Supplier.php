<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company', 'place_id', 'phone', 'comment', 'mail', 'data'];

    protected $casts = ['data' => 'array'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function places()
    {
        return $this->BelongsToMany(Place::class, 'place_supplier', 'supplier_id', 'place_id');
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
