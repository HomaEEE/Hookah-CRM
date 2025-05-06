<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Concern\ModelTree;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use Compoships, HasFactory, HasTranslations, ModelTree;

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
        return $this->hasMany(Category::class, ['parent_category', 'place_id'], ['category_id', 'place_id'])->visibleMenu(true);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, ['parent_category', 'place_id'], ['category_id', 'place_id']);
    }

    public function scopeVisibleMenu($query, $value)
    {
        return $query->where('visible_menu', $value);
    }

    public function products()
    {
        return $this->hasMany(Product::class, ['menu_category_id', 'place_id'], ['category_id', 'place_id'])
            ->orderBy('price', 'asc')
            ->where('visible_menu', true)
            ->where('price', '>', 1);
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
