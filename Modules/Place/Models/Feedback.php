<?php

namespace Modules\Place\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = ['name', 'place_id', 'text', 'contact', 'rating'];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function scopeApprove($query, $value)
    {
        return $query->where('approve', $value);
    }
}
