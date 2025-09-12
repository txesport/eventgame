<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Group;

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

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    $group = Group::find($groupId);
    return $group && $group->isMember($user);
});
