<?php

namespace App\Providers;

use App\Models\FlashcardSet;
use App\Models\User;
use App\Observers\FlashcardSetObserver;
use App\Policies\UserPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        FlashcardSet::observe(FlashcardSetObserver::class);
        
        // Register User Policy
        Gate::policy(User::class, UserPolicy::class);
    }
}
