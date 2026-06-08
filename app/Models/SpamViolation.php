<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamViolation extends Model
{
    /**
     * Les attributs autorisés en écriture
     */
    protected $fillable = [
        'client_id',
        'campaign_id',
        'spam_rule_id',
        'channel',
        'action',
        'severity',
        'reason',
        'score',
    ];

    /**
     * Casts utiles
     */
    protected $casts = [
        'score' => 'integer',
    ];

    /* ============================
     | Relations
     |============================ */

    /**
     * Le client qui a déclenché la violation
     */
    public function client()
    {
        return $this->belongsTo(Clients::class);
    }

    /**
     * La campagne concernée (nullable)
     * null si la règle a bloqué AVANT création
     */
    public function campaign()
    {
        return $this->belongsTo(Campagnes::class);
    }

    /**
     * La règle anti-spam déclenchée
     */
    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }

    /* ============================
     | Scopes utiles (BONUS)
     |============================ */

    /**
     * Violations bloquantes
     */
    public function scopeBlocked($query)
    {
        return $query->where('action', 'block');
    }

    /**
     * Violations critiques
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Violations d'un client donné
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
