<?php

namespace App\Providers;

use App\Listeners\ConvertMediaToWebp;
use App\Observers\SupplyObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Manage\Models\Supply;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        MediaHasBeenAddedEvent::class => [
            ConvertMediaToWebp::class,
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Supply::observe(SupplyObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
