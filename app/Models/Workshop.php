<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Models\Wage;
use Modules\Manage\Models\Writeoff;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'name', 'workshop_id'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function wage()
    {
        return $this->belongsTo(Wage::class, 'place_id', 'workshop_id');
    }

    public function writeoff()
    {
        return $this->hasMany(Writeoff::class, 'workshop_id');
    }
}
