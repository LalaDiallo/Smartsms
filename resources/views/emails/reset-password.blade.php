@extends('emails.layouts.smartsms')

@section('preheader')
Réinitialisez votre mot de passe SmartSMS. Ce lien expire dans 60 minutes.
@endsection

@section('title')
Réinitialisation du mot de passe — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#fef3c7;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#b45309;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Réinitialisation du mot de passe
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $notifiable->first_name ?? $notifiable->name ?? 'Utilisateur' }},
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 16px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte SmartSMS. Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
</p>

{{-- CTA --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="{{ $url }}"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Réinitialiser le mot de passe &rarr;
            </a>
        </td>
    </tr>
</table>

<div style="background-color:#fef9c3;border:1px solid #fde047;border-radius:10px;padding:14px 18px;margin:20px 0;">
    <p style="margin:0;font-size:13px;color:#713f12;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        <strong>Attention :</strong> Ce lien expire dans <strong>60 minutes</strong>. Après expiration, vous devrez soumettre une nouvelle demande.
    </p>
</div>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">

<p style="font-size:13px;color:#94a3b8;line-height:1.6;margin:0 0 8px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Si vous n'avez pas demandé de réinitialisation, ignorez cet email. Votre mot de passe restera inchangé.
</p>
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br>
    <strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
