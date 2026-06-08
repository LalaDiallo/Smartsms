<?php

namespace App\Providers;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            // Define the custom reset URL (replace with your correct link)
            $url = url('/reset-password?token=' . $token . '&email=' . urlencode($notifiable->getEmailForPasswordReset()));

            // Build the customized email using a Blade template
            return (new MailMessage)
                ->subject(Lang::get('Réinitialisation de votre mot de passe'))
                ->view('mails.password-reset', [
                    'url' => $url,
                    'name' => $notifiable->name ?? 'Utilisateur',
                    'expire' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
                    'appName' => config('app.name'),
                ]);
        });
    }
}
