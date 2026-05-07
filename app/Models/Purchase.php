<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'place_id', 'supplier_id', 'status', 'total_sum', 'comment'];

    protected $casts = [
        'status' => PurchaseStatus::class,
        'total_sum' => MoneyCast::class,
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
