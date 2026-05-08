<?php

namespace Modules\Place\Models;

use App\Models\Place;
use App\Models\Seet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Booking extends Model
{
    use HasFactory;
    use HasJsonRelationships;

    protected $fillable = ['user_id', 'place_id', 'seet_ids', 'date', 'guest_id', 'time', 'person', 'comment'];

    protected $casts = ['seet_ids' => 'json'];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function seet()
    {
        return $this->belongsToJson(Seet::class, 'seet_ids');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getBookingTimeAttribute()
    {
        return (new Carbon($this->time))->translatedFormat('l d/m').', '
        .(new Carbon($this->time))->translatedFormat('H:i');
    }

    public function setTimeAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['time'] = Carbon::parse(
                $value['date'].' '.$value['time']
            );

            return;
        }

        $this->attributes['time'] = $value;
    }
}
