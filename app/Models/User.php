<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birth',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        //'places' => 'array',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function place(): BelongsToMany
    {
        return $this->belongsToMany(Place::class);
    }

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->places;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->places()->whereKey($tenant)->exists();
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }

    public function wage()
    {
        return $this->belongsTo(Wage::class);
    }

    public function wages()
    {
        return $this->belongsTo(Wage::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function task()
    {
        return $this->hasMany(Task::class, 'user_id');
    }
}
