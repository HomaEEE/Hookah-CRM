<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use Compoships, HasFactory, HasTranslations;
    use InteractsWithMedia;

    protected $fillable = ['product_name', 'frontend_name', 'place_id', 'product_id', 'menu_category_id',
        'cost', 'price', 'barcode', 'out', 'ingredients', 'frontend_desc', 'sup',
        'category_id', 'image', 'sort_order', 'visible',  'visible_menu'];

    protected $casts = [
        'cost' => MoneyCast::class,
        'price' => MoneyCast::class,
        'visible' => 'boolean',
        'visible_menu' => 'boolean'];

    public $timestamps = false;

    public $translatable = ['frontend_name', 'frontend_desc'];

    public function category()
    {
        return $this->belongsTo(Category::class, ['menu_category_id', 'place_id'], ['category_id', 'place_id']);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function scopeVisibleMenu($query, $value)
    {
        return $query->where('visible_menu', $value);
    }
}
