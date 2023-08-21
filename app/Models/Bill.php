<?php

namespace App\Models;

use App\Enums\BILL as BILLEnum;
use App\Enums\CURRENCY;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'currency', 'balance'];

    protected $casts = [
        'currency' => CURRENCY::class,
        'type' => BILLEnum::class,
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Expense::class);
    }
}
