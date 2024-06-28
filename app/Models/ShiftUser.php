<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftUser extends Model
{
    use HasFactory;

    protected $table = 'shift_users';

    protected $fillable = [
        'shift_id',
        'user_id',
        'shift_id',
        'wage_id',
        'days',
        'wage',
        'procent_day',
        'cashout',
    ];

    public function shift()
    {
        return $this->hasOne(Shift::class, 'shift_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function wage()
    {
        return $this->hasOne(Wage::class, 'id', 'wage_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'id', 'user_id');
    }

    public function scopeTodayOnShift($query)
    {
        return $query->whereDate('created_at', Carbon::today());
    }
}
