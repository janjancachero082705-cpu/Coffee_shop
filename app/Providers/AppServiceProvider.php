<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Global View Composer - Fresh Data & Notifications
        View::composer('*', function ($view) {
            if (Auth::guard('store')->check()) {
                $currentStore = Store::find(Auth::guard('store')->id());
                $view->with('currentStore', $currentStore);

                // Kunin ang unread notifications para sa Notification Bell
                if (class_exists('\App\Models\StoreNotification')) {
                    $storeNotifications = \App\Models\StoreNotification::where('store_id', $currentStore->id)
                        ->latest()
                        ->take(5)
                        ->get();
                    $unreadNotificationsCount = \App\Models\StoreNotification::where('store_id', $currentStore->id)
                        ->whereNull('read_at')
                        ->count();
                        
                    $view->with('storeNotifications', $storeNotifications);
                    $view->with('unreadNotificationsCount', $unreadNotificationsCount);
                }
            }
        });
    }
}