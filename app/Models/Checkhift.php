<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkhift extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'revenue', 'terminal', 'bar', 'hookah', 'kitchen', 'cash'];
}
