<?php

namespace App\Providers;

use App\Models\DriverIssues;
use App\Models\WebsiteSettings;
use App\Services\DriverPeriodService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        {
            View::composer('*', function ($view) {
                $view->with('settings', WebsiteSettings::first());
            });
        }

        View::composer('driver.layouts.master', function ($view) {
            $pendingDriverIssueCount = 0;
            if (auth()->check() && auth()->user()->driver_id) {
                $pendingDriverIssueCount = DriverIssues::query()
                    ->where('driver_id', auth()->user()->driver_id)
                    ->where('status', 'open')
                    ->whereHas('items')
                    ->count();
            }
            $view->with('pendingDriverIssueCount', $pendingDriverIssueCount);

            if (auth()->user()->driver_id) {
                $view->with('driverPeriod', DriverPeriodService::periodForDriver((int) auth()->user()->driver_id));
            }
        });

        View::composer('driver.*', function ($view) {
            if (auth()->check() && auth()->user()->driver_id && ! $view->offsetExists('driverPeriod')) {
                $view->with('driverPeriod', DriverPeriodService::periodForDriver((int) auth()->user()->driver_id));
            }
            if (! $view->offsetExists('driverPanelDayClosed')) {
                $closed = auth()->check() && auth()->user()->driver_id
                    && DriverPeriodService::isDriverPanelDateInClosedLedger(
                        (int) auth()->user()->driver_id,
                        now()->toDateString()
                    );
                $view->with('driverPanelDayClosed', $closed);
            }
        });
    }
}
