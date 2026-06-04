<?php

namespace Modules\Finance\Models;

use App\Casts\MoneyCast;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class); // ID поставки
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class); // ID счета
    }
}
