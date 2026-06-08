<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clients extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'plan_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'website',
        'forme_juridique',
        'numero_immatriculation',
        'city',
        'country',
        'industry',
        'status',
        'joined_at',
        'last_activity',
        'contract_end',
        'monthly_revenue',
        'total_spent',
        'messages_sent',
        'satisfaction',
        'support_tickets',
        'features',
        'logo',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'plan_id'          => 'integer',
        'joined_at'        => 'date',
        'contract_end'     => 'date',
        'last_activity'    => 'datetime',
        'monthly_revenue'  => 'decimal:2',
        'total_spent'      => 'decimal:2',
        'satisfaction'     => 'decimal:1',
        'features'         => 'array', // JSON → tableau PHP
        'support_tickets'  => 'integer',
        'messages_sent'    => 'integer',
    ];

    /**
     * Valeurs par défaut pour certains attributs
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status'           => 'essaie',
        'country'          => 'Guinée',
        'monthly_revenue'  => 0.00,
        'total_spent'      => 0.00,
        'messages_sent'    => 0,
        'satisfaction'     => 4.0,
        'support_tickets'  => 0,
    ];

    // ===================================================================
    // Relations
    // ===================================================================

    /**
     * Le plan auquel appartient ce client
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'client_id');
    }

    public function branding()
    {
        return $this->hasMany(Branding::class,'client_id');
    }

    public function developerApiKeys()
    {
        return $this->hasMany(DeveloperApiKey::class, 'client_id');
    }

    // ===================================================================
    // Accessors & Mutators (optionnels mais utiles)
    // ===================================================================

    /**
     * Retourne le statut du client avec une traduction lisible
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'Actif',
            'essaie'    => 'En essai',
            'suspended' => 'Suspendu',
            'resilie'   => 'Résilié',
            default     => ucfirst($this->status),
        };
    }

    /**
     * Formate le revenu mensuel avec devise (ex: pour affichage)
     */
    public function getFormattedMonthlyRevenueAttribute(): string
    {
        return number_format($this->monthly_revenue, 2) . ' GNF';
    }

    /**
     * Formate le total dépensé
     */
    public function getFormattedTotalSpentAttribute(): string
    {
        return number_format($this->total_spent, 2) . ' GNF';
    }

    public function SpamViolation()
    {
        return $this->hasMany(SpamViolation::class);
    }

    public function notificationSettings()
    {
        return $this->hasMany(NotificationSetting::class, 'client_id');
    }
}
