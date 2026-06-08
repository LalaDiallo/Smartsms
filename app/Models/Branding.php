<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branding extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'brand_name',
        'logo',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'description',
        'status',
        'status_motif',
        'approved_at',
        'approved_by',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /* ================= Relations ================= */

    public function client()
    {
        return $this->belongsTo(Clients::class,'client_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /* ================= Scopes utiles ================= */

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
