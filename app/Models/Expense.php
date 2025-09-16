<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia; // Import this
use Spatie\MediaLibrary\InteractsWithMedia;

class Expense extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = ['sum', 'added_at', 'poster', 'bill_id',
        'category_id', 'supply_id', 'user_id', 'place_id', 'comment'];

    protected $casts = [
        'sum' => MoneyCast::class,
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function supplyPhoto()
    {
        return $this->hasOne(Supply::class, 'id', 'supply_id');
    }

    public function registerMediaConversions(): void
    {
        // add this function to register conversion for media
        // and choice what format is it
        $this->addMediaConversion('media-webp')
            ->quality(75)
            ->optimize() // Optimize the converted image
            ->nonQueued();

    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function getInvoiceAttribute()
    {
        return $this->supply_id ?
               $this->supply->getFirstMediaUrl('invoce') : $this->getFirstMediaUrl('invoce');

    }
}
