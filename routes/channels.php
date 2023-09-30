<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('company.{id}', function ($user, $id) {
    return (int) $user->company_id === (int) $id  && get_class($user) === 'App\Models\User';
}, ['guards'=>'user']);

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id  && get_class($user) === 'App\Models\User';
}, ['guards'=>'user']);

Broadcast::channel('manager.{id}', function ($manager, $id) {
    return (int) $manager->id === (int) $id && get_class($manager) === 'App\Models\Manager';
}, ['guards'=>'manager']);

Broadcast::channel('admin.{id}', function ($admin, $id) {
    return (int) $admin->id === (int) $id && get_class($admin) === 'App\Models\Admin';
}, ['guards'=>'admin']);

