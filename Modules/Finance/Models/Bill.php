<?php

namespace Modules\Finance\Models;

use App\Casts\MoneyCast;
use App\Enums\Currency;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Enums\BillType;
use Modules\Manage\Models\Supply;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'currency', 'balance'];

    protected $casts = [
        'balance' => MoneyCast::class,
        'currency' => Currency::class,
        'type' => BillType::class,
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
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
