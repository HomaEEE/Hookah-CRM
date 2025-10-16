<?php

namespace App\Providers;

use App\Listeners\ConvertMediaToWebp;
use App\Models\Expense;
use App\Models\Supply;
use App\Models\Withdrawal;
use App\Observers\SupplyToExpense;
use App\Observers\UpdateBill;
use App\Observers\WithdrawalBill;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
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
        Supply::observe(SupplyToExpense::class);
        Supply::observe(UpdateBill::class);
        Expense::observe(UpdateBill::class);
        Withdrawal::observe(WithdrawalBill::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
