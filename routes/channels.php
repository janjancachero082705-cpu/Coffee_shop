<?php

use App\Models\Store;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Admin notifications
Broadcast::channel('admin.notifications', function ($user) {
    return $user !== null;
});

// Store-specific channel
Broadcast::channel('store.{storeId}', function ($user, $storeId) {
    if ($user instanceof Store) {
        return (int) $user->id === (int) $storeId;
    }
    return false;
});

// Public stats
Broadcast::channel('stats', function () {
    return true;
});