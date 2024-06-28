<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'place_id', 'poster_category_id', 'comment'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Expense::class, 'category_id');
    }

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }

}
