<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;


    protected $fillable = ['place_id', 'user_id', 'cash', 'terminal','old_shift', 'bar',
                            'kitchen', 'hookah', 'bulbs', 'report',
                            'wages', 'tips', 'expense', 'income', 'calc_shift', 'shift',
                            ];

    protected $casts = [
        'report' => 'array'
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function users()
    {
        return $this->hasMany(ShiftUser::class, 'shift_id');
    }

    public function Shift_Users()
    {
        return $this->hasMany(ShiftUser::class, 'shift_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'place_id', 'place_id');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'place_id');
    }
}
