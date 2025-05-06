<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasFactory,  HasTranslations;

    protected $fillable = ['name', 'place_id'];

    public $translatable = ['name'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function categories()
    {
        return $this->HasMany(Category::class, 'place_id', 'place_id');
    }

    public function products()
    {
        return $this->HasMany(Product::class, 'place_id', 'place_id');
    }
}
