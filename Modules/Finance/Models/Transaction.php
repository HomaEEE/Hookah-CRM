<?php

namespace Modules\Finance\Models;

use App\Casts\MoneyCast;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Modules\Manage\Models\Supply;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'type',          // Enum TransactionType
        'sum',           // Общее: сумма транзакции
        'user_id',       // Общее: кто совершил/создал
        'place_id',      // Общее: привязка к месту/заведению
        'added_at',      // Специфичное: дата проведения (можно использовать transaction_date)
        'poster',        // Специфичное
        'bill_id',       // Специфичное: привязка к счету
        'expense_category_id',   // Специфичное: категория (расхода/прихода)
        'supply_id',     // Специфичное: связь с поставкой (Supply)
        'comment',       // Специфичное: текстовый комментарий
    ];

    protected function casts(): array
    {
        return [
            'bill_id' => BillType::class,
            'type' => TransactionType::class,
            'sum' => MoneyCast::class,
            'added_at' => 'datetime',
        ];
    }

    // Пример связей, которые тебе понадобятся
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function categories()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id', 'id');
    }

    public function supply()
    {
        return $this->HasOne(Supply::class, 'id', 'supply_id');
    }

    public function supplyPhoto()
    {
        return $this->hasOne(Supply::class, 'id', 'supply_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function getInvoiceAttribute()
    {
        return $this->supply_id ?
            $this->supply->getFirstMediaUrl('supplies') : $this->getFirstMediaUrl('supplies');
    }
}
