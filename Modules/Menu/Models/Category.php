<?php

namespace Modules\Menu\Models;

use App\Models\Place;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use Compoships, HasFactory, HasTranslations;

    protected $fillable = ['category_name', 'frontend_name', 'frontend_menu_name', 'place_id', 'parent_category',
        'category_id', 'image', 'class', 'logo', 'sort_order', 'visible', 'visible_menu',
        'description', 'slug', ];

    public $timestamps = false;

    protected $casts = [
        'parent_category' => 'int',
        'visible' => 'boolean',
        'visible_menu' => 'boolean',
        'class' => 'array',
    ];

    protected $guarded = ['parent_category'];

    public $translatable = ['frontend_name', 'frontend_menu_name', 'description'];

    public function childs()
    {
        return $this->hasMany(Category::class, ['parent_category', 'place_id'], ['category_id', 'place_id'])
            ->orderBy('sort_order', 'asc')->VisibleMenu(true);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, ['parent_category', 'place_id'], ['category_id', 'place_id'])
            ->orderBy('sort_order', 'asc')->VisibleMenu(true);
    }

    public function scopeVisibleMenu($query, $value)
    {
        return $query->where('visible_menu', $value);
    }

    public function products()
    {
        return $this->hasMany(Product::class, ['menu_category_id', 'place_id'], ['category_id', 'place_id'])
            ->orderBy('new', 'desc')
            ->orderBy('product_name', 'asc')
            ->VisibleMenu(true);
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
