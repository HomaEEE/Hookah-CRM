<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Supply extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['place_id', 'supplier_id', 'added_at', 'sum', 'comment', 'bill_id', 'pay', 'poster', 'user_id'];

    protected $casts = [
        'pay' => 'boolean',
        'poster' => 'boolean',
        'invoice' => 'array',
        'added_at' => 'datetime',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function suppliers()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }


    public function scopeAllPaid($query)
    {
        return $query->whereIn('pay', [0, 1]);
    }

    public function scopePaid($query)
    {
        return $query->where('pay', 1);
    }

    public function scopeNotPaid($query)
    {
        return $query->where('pay', 0);
    }

    public function scopeAllPoster($query)
    {
        return $query->whereIn('poster', [0, 1]);
    }

    public function scopeInPoster($query)
    {
        return $query->where('poster', 1);
    }

    public function scopeNotPoster($query)
    {
        return $query->where('poster', 0);
    }

    public function Pay($query)
    {
        return $query->update('pay', 1);
    }
}
