<?php

namespace App\Providers;

use App\Models\FlashcardSet;
use App\Observers\FlashcardSetObserver;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        FlashcardSet::observe(FlashcardSetObserver::class);
    }
}
