<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- ADD THIS BLOCK ---
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        // ----------------------

        // Register the TicketPolicy
        Gate::policy(Ticket::class, TicketPolicy::class);
    }
}
