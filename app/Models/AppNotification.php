<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id', 'client_id', 'type', 'title', 'body',
        'action', 'resource_type', 'resource_id', 'link', 'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function user()   { return $this->belongsTo(User::class); }
    public function client() { return $this->belongsTo(Clients::class); }

    /** Marque comme lue */
    public function markRead(): void
    {
        $this->update(['read' => true]);
    }

    /** Scope : non lues */
    public function scopeUnread($q) { return $q->where('read', false); }
}
