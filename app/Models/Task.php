<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'name', 'text', 'user_id', 'for_user_id', 'approve'];

    protected $casts = [
        'pay' => 'approve',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function forUser()
    {
        return $this->belongsTo(User::class, 'for_user_id', 'id');
    }

    public function scopeApprove($query, $value)
    {
        return $query->where('approve', $value);
    }
}
