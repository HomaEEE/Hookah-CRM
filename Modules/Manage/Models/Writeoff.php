<?php

namespace Modules\Manage\Models;

use App\Models\Place;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Writeoff extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'user_id', 'workshop_id', 'whiteoff', 'approve'];

    protected $casts = [
        'whiteoff' => 'array',
        'approve' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class, 'workshop_id');
    }

    public function ingredient()
    {
        return $this->hasMany(Ingredient::class);
    }

    public function getWhiteoffAttribute($value)
    {
        $decodedValue = json_decode($value, true);
        $transformedValue = [];

        if (is_array($decodedValue)) {
            foreach ($decodedValue as $item) {
                $ingredient = Ingredient::find($item['ingredient_id']);
                if ($ingredient) {
                    $transformedValue[] = [
                        'ingredient' => $ingredient, // Передаем всю модель Ingredient
                        'quantity' => $item['quantity'] ?? null,
                        'unit' => $item['unit'] ?? null,

                    ];
                }
            }
        }

        return $transformedValue;
    }
}
