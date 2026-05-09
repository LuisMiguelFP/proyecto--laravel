<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin', function ($user) {
    return $user !== null && $user->is_admin;
});
