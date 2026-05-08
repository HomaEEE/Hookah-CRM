<?php

namespace Modules\Finance\Models;

use App\Casts\MoneyCast;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Enums\Period;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'place_id', 'category_id', 'price', 'period', 'notes', 'next_date'];

    public $timestamps = false;

    protected $dates = ['next_date'];

    protected $casts = [
        'period' => Period::class,
        'price' => MoneyCast::class,
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function bill()
    {
        return $this->HasOne(Bill::class);
    }
}
