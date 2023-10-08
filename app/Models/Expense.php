<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = ['sum', 'added_at', 'poster', 'type_pay', 'category_id', 'supply_id', 'user_id', 'place_id', 'comment'];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->HasOne(User::class, 'id', 'user_id');
    }

    public function categories()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function supply()
    {
        return $this->HasOne(Supply::class, 'id', 'supply_id');
    }

    public function bill()
    {
        return $this->HasOne(Bill::class);
    }

}
