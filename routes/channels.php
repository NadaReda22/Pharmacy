<?php


use Illuminate\Support\Facades\Broadcast;

// The {userId} parameter captures the ID from the channel name (ex: '11' from 'user:11').
// routes/channels.php (Change to Colon)
Broadcast::channel('user-{userId}', function ($user, $userId) {   // Logic must return true. Check if $user is null (not authenticated)
    // and ensure the IDs match. 
    return $user && (int) $user->id === (int) $userId; 
});