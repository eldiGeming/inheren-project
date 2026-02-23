<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Company;
use Illuminate\Support\Facades\View;

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
        if (app()->environment('production')) {
            app()->useStoragePath('/tmp');
        }
        View::composer('*', function ($view) {
            $view->with('allCompany', Company::all());
        });
    }
}
