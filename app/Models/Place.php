<?php

namespace App\Models;

use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Arrears\Models\Arrear;
use Modules\Finance\Models\Bill;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Shift;
use Modules\Finance\Models\Subscription;
use Modules\Finance\Models\Wage;
use Modules\Manage\Models\Excise;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\Purchase;
use Modules\Manage\Models\Supply;
use Modules\Manage\Models\Writeoff;
use Modules\Menu\Models\Category;
use Modules\Menu\Models\Menu;
use Modules\Menu\Models\Product;
use Modules\Menu\Models\Techcard;
use Modules\Place\Models\Booking;
use Modules\Place\Models\Doc;
use Modules\Place\Models\Event;
use Modules\Place\Models\Feedback;
use Modules\Place\Models\Generator;
use Modules\Place\Models\Guest;
use Modules\Place\Models\Reqvizite;
use Modules\Place\Models\Service;
use Modules\Place\Models\Supplier;
use Spatie\Translatable\HasTranslations;

class Place extends Model implements HasAvatar, HasCurrentTenantLabel
{
    use HasFactory,  HasTranslations;

    protected $fillable = ['name', 'slug', 'city', 'address', 'phone', 'opened', 'instagram', 'sufix', 'time', 'settings', 'properties', 'pays'];

    public $translatable = ['name', 'city', 'address'];

    protected $casts = ['opened' => 'boolean', 'time' => 'array', 'properties' => 'array', 'settings' => 'array', 'pays' => 'array'];

    public $timestamps = false;

    public function getCurrentTenantLabel(): string
    {
        return __('Current place');
    }

    public function arrears()
    {
        return $this->hasMany(Arrear::class);
    }

    public function reqvizites()
    {
        return $this->hasMany(Reqvizite::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function seets()
    {
        return $this->hasMany(Seet::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function storages()
    {
        return $this->hasMany(Storage::class);
    }

    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }

    public function wages()
    {
        return $this->hasMany(Wage::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function excises()
    {
        return $this->hasMany(Excise::class);
    }

    public function docs()
    {
        return $this->hasMany(Doc::class);
    }

    public function generators()
    {
        return $this->hasMany(Generator::class);
    }

    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function menu()
    {
        return $this->hasOne(Menu::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function writeoffs()
    {
        return $this->hasMany(Writeoff::class);
    }

    public function techcards()
    {
        return $this->hasMany(Techcard::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class, ['category_id', 'place_id']);
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

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
