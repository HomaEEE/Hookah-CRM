<?php

namespace App\Models;

use App\Enums\Priority;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mokhosh\FilamentKanban\Concerns\HasRecentUpdateIndication;

class Task extends Model
{
    use HasFactory, HasRecentUpdateIndication;

    protected $fillable = ['place_id', 'name', 'priority', 'text', 'date', 'user_id', 'for_user_id', 'approve'];

    protected $guarded = [];

    protected $casts = [
        'priority' => Priority::class,
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forUser()
    {
        return $this->belongsTo(User::class, 'for_user_id', 'id');
    }

    public function scopeApprove($query, $value)
    {
        return $query->where('approve', $value);
    }

    public function getTimeAttribute()
    {
        return (new Carbon($this->date))->translatedFormat('H:i');
    }
}
