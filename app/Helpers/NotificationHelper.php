<?php

use App\Models\User;
use App\Models\Notification;

if (!function_exists('notifyReaction')) {

    /**
     * Envoie une notification lorsqu'un utilisateur réagit à un contenu
     *
     * @param User $fromUser   Celui qui réagit
     * @param mixed $target    Post, Comment, etc.
     * @param string $reaction Type de réaction (like, love, etc.)
     */
    function notifyReaction(User $fromUser, $target, string $reaction)
    {
        $toUser = $target->user;

        // Éviter de se notifier soi-même
        if ($fromUser->id === $toUser->id) {
            return;
        }

        Notification::create([
            "user_id"        => $toUser->id,
            "from_user_id"   => $fromUser->id,
            "type"           => "reaction",
            "reaction"       => $reaction,
            "notifiable_id"  => $target->id,
            "notifiable_type"=> get_class($target),
            "message"        => "{$fromUser->name} a réagi avec {$reaction}"
        ]);
    }
}
