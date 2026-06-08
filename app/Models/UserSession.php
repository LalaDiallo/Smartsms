<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $table = 'user_sessions';

    /**
     * Champs autorisés en mass assignment
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'browser',
        'os',
        'ip_address',
        'country',
        'last_activity_at',
        'is_current',
    ];

    /**
     * Casts automatiques
     */
    protected $casts = [
        'last_activity_at' => 'datetime',
        'is_current' => 'boolean',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
