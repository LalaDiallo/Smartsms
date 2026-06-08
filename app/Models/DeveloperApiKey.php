<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperApiKey extends Model
{
    use HasFactory;

    /**
     * Champs autorisés en mass assignment
     */
    protected $fillable = [
        'client_id',
        'name',
        'service_id',
        'secret_token',
        'webhook_url',
        'is_active',
        'last_used_at',
    ];

    /**
     * Casts automatiques
     */
    protected $casts = [
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Champs masqués (jamais envoyés au frontend)
     */
    protected $hidden = [
        'secret_token',
    ];

    /**
     * Relations
     */
    public function client()
    {
        return $this->belongsTo(Clients::class);
    }

    /**
     * Scope : clés actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
