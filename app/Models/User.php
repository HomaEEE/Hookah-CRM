<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
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
        'places',
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
        'places' => 'array',
    ];



    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }


    public function place()
    {
        return $this->BelongsToMany(Place::class);
    }

    public function placeId()
    {
        return $this->BelongsTo(Place::class);
    }

    public function places()
    {
        return $this->belongsToMany(Place::class, 'place_user', 'user_id', 'place_id');
    }

    public function wages()
    {
        return $this->belongsTo(Wage::class, 'wage_id');
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
