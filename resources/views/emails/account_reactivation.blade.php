@extends('emails.layouts.smartsms')

@section('preheader')
Bonne nouvelle ! Votre compte SmartSMS a été réactivé. Vous pouvez vous connecter dès maintenant.
@endsection

@section('title')
Compte réactivé — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <div style="display:inline-block;background-color:#dcfce7;border-radius:50%;width:64px;height:64px;line-height:64px;text-align:center;margin-bottom:12px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-top:18px;">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>
    <br>
    <span style="display:inline-block;background-color:#dcfce7;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#15803d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Compte réactivé
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonne nouvelle, {{ $user->name }} !
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 16px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Votre compte SmartSMS a été <strong style="color:#15803d;">réactivé avec succès</strong>.
    Vous pouvez désormais vous connecter et reprendre l'utilisation de toutes les fonctionnalités de la plateforme.
</p>

<div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin:0 0 24px;">
    <p style="margin:0;font-size:13px;color:#15803d;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Vos campagnes, contacts et paramètres ont été préservés et sont accessibles immédiatement.
    </p>
</div>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#16a34a" style="background-color:#16a34a;border-radius:12px;">
            <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/login"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Se connecter &rarr;
            </a>
        </td>
    </tr>
</table>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br><strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
