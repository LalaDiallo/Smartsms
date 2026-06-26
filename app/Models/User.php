<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasApiTokens, HasPermissions;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activation_token',
        'client_id',
        'zone_id',
        'phone',
        'status',
        'bio',
        'profil',

        'google_id',
        'facebook_id',
        'github_id',
        'gitlab_id',
        'bitbucket_id',
        'slack_id',
        'twitch_id',
        'twitter_openid_id',
        'linkedin_openid_id',
        'two_factor_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'two_factor_enabled' => 'boolean',
];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Clients::class,'client_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function permissions()
    {
        return $this->hasOne(RolesPermissions::class, 'role', 'role');
    }

    public function campaigns()
    {
        return $this->hasMany(Campagnes::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // User.php
    public function sessions()
    {
        return $this->hasMany(UserSession::class);
    }

}
