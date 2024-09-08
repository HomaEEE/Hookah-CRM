<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Attribute;
use Awobaz\Compoships\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;


    protected $fillable = ['place_id', 'user_id', 'cash', 'terminal','old_shift', 'bar',
                            'kitchen', 'hookah', 'bulbs', 'report', 'withdrawal',
                            'wages', 'tips', 'expense', 'income', 'calc_shift', 'shift',
                            ];

    protected $casts = [
        'report' => 'json',
        'cash' => MoneyCast::class,
        'terminal' => MoneyCast::class,
        'old_shift' => MoneyCast::class,
        'bar' => MoneyCast::class,
        'kitchen' => MoneyCast::class,
        'wages' => MoneyCast::class,
        'tips' => MoneyCast::class,
        'expense' => MoneyCast::class,
        'income' => MoneyCast::class,
        'withdrawal' => MoneyCast::class,
        'calc_shift' => MoneyCast::class,
        'shift' => MoneyCast::class
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

    protected function getOldShift(): Attribute
    {
        return $this->shift;
    }


    protected function getMonthsAndYearsForSelect()
    {
        return $this->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month')
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc');
    }
}
