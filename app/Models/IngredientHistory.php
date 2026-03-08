<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientHistory extends Model
{
    protected $table = 'ingredient_history';

    protected $fillable = ['ingredient_id', 'price'];

    protected $casts = [
        'price' => MoneyCast::class,
    ];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
