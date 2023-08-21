<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indication extends Model
{
    use HasFactory, Compoships;

    protected $fillable = ['meter_id', 'place_id', 'data'];

    public $timestamps = false;

    public function meter()
    {
        return $this->belongsTo(Meter::class, ['room_id', 'place_id'], ['id', 'place_id']);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, ['room_id', 'place_id'], ['id', 'place_id']);
    }

    public function getDebtAttribute()
    {
        $previos = Indication::where('type_id', $this->type)
            ->where('room_id', $this->room)
            ->orderByDesc('created_at')
            ->where('created_at', '<', $this->created_at)->first('data');
        if (! empty($previos)) {
            return $this->data - $previos->data;
        } else {
            return 0;
        }
    }

    public function getSumAttribute()
    {
        return $this->debt * $this->tarif;
    }
}
