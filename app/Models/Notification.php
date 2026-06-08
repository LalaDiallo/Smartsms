<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "from_user_id",
        "type",
        "title",
        "message",
        "campaign_id",
        "is_read",
        "read_at",
        "data",
    ];

    protected $casts = [
        "is_read" => "boolean",
        "read_at" => "datetime",
        "data"    => "array",
    ];

    /**
     * ✅ Celui qui reçoit la notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Celui qui déclenche la notification (admin, manager...)
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, "from_user_id");
    }

    /**
     * ✅ Campagne associée (si notification liée à une campagne SMS)
     */
    public function campaign()
    {
        return $this->belongsTo(Campagnes::class);
    }

    /**
     * ✅ Marquer comme lue facilement
     */
    public function markAsRead()
    {
        $this->update([
            "is_read" => true,
            "read_at" => now(),
        ]);
    }

    /**
     * ✅ Scope : notifications non lues
     */
    public function scopeUnread($query)
    {
        return $query->where("is_read", false);
    }

    /**
     * ✅ Scope : notifications lues
     */
    public function scopeRead($query)
    {
        return $query->where("is_read", true);
    }
}
