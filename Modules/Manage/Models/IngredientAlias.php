<?php

namespace Modules\Manage\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientAlias extends Model
{
    protected $fillable = [
        'alias',
        'ingredient_id',
    ];
}
