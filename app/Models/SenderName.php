<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SenderName extends Model
{
    protected $fillable = [
        'client_id', 'name', 'document_path', 'metadata', 'status',
        'is_active', 'is_default', 'status_motif',
        'approved_at', 'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_active'   => 'boolean',
        'is_default'  => 'boolean',
        'metadata'    => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'approved');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
