<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderStatusHistory;
use App\Observers\OrderObserver;
use App\Observers\OrderPaymentObserver;
use App\Observers\OrderStatusHistoryObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        OrderStatusHistory::observe(OrderStatusHistoryObserver::class);
        OrderPayment::observe(OrderPaymentObserver::class);
        Order::observe(OrderObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
