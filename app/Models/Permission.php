<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends Model
{
    use HasFactory;

    public function place()
    {
        return $this->BelongsToMany(Place::class);
    }

}
