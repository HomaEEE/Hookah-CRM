<?php

namespace App\Models;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Techcard extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['product_name', 'workshop', 'process', 'ingredients'];

    protected $casts = ['ingredients' => 'array'];

    public $timestamps = false;


    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function ingredients()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
