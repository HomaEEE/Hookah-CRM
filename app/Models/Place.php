<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Place extends Model implements HasAvatar
{
    use HasFactory,  HasTranslations;

    protected $fillable = ['name', 'city', 'address', 'phone', 'opened', 'instagram', 'sufix', 'time', 'settings', 'properties', 'pays'];

    public $translatable = ['name','city', 'address'];

    protected $casts = ['opened' => 'boolean', 'time' => 'array', 'properties' => 'array', 'settings' => 'array', 'pays' => 'array'];

    public $timestamps = false;

    public function meters()
    {
        return $this->hasMany(Meter::class, 'place_id');
    }

    public function bill()
    {
        return $this->HasMany(Bill::class, 'place_id');
    }

    public function supplier()
    {
        return $this->hasMany(Supplier::class, 'place_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function service()
    {
        return $this->hasMany(Service::class, 'place_id');
    }

       //Показания счетчиков
    public function indications()
    {
        return $this->hasMany(Indication::class, 'place_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'place_id');
    }

    public function seets()
    {
        return $this->hasMany(Seet::class, 'place_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'place_id');
    }

    public function storage()
    {
        return $this->hasMany(Storage::class, 'place_id');
    }

    public function workshops()
    {
        return $this->hasMany(Workshop::class, 'place_id');
    }

    public function wages()
    {
        return $this->hasMany(Wage::class, 'place_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'place_id');
    }

    public function excises()
    {
        return $this->hasMany(Excise::class, 'place_id');
    }

    public function docs()
    {
        return $this->hasMany(Doc::class, 'place_id');
    }

    public function expensecategories()
    {
        return $this->hasMany(ExpenseCategory::class, 'place_id');
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'place_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class, ['category_id', 'palce_id']);
    }

    public function scopeVisibleMenu($query, $value)
    {
        return $query->where('visible_menu', $value);
    }


    public function getNameCityAttribute()
    {
        return $this->name.' '.$this->city;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
