@extends('emails.layouts.smartsms')

@section('preheader')
Activez votre compte SmartSMS en cliquant sur le lien ci-dessous. Ce lien expire dans 24 heures.
@endsection

@section('title')
Activation de votre compte — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#ede9fe;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#6d28d9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Activation du compte
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $user->name }},
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bienvenue sur <strong style="color:#111827;">SmartSMS</strong> ! Votre compte a été créé par votre administrateur.
    Activez-le en cliquant sur le bouton ci-dessous, puis connectez-vous avec vos identifiants.
</p>

@if(isset($tempPassword) && $tempPassword)
{{-- Bloc identifiants --}}
<div style="background:linear-gradient(135deg,#f5f3ff 0%,#ede9fe 100%);border:1px solid #c4b5fd;border-radius:14px;padding:20px 24px;margin:0 0 24px;">
    <p style="margin:0 0 14px;font-size:13px;font-weight:700;color:#6d28d9;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Vos identifiants de connexion
    </p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="padding:6px 0;border-bottom:1px solid #ddd6fe;">
                <span style="font-size:12px;color:#7c3aed;font-weight:600;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Email</span>
                <br>
                <span style="font-size:15px;color:#111827;font-weight:700;font-family:'Courier New',Courier,monospace;">{{ $user->email }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:6px 0;">
                <span style="font-size:12px;color:#7c3aed;font-weight:600;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Mot de passe temporaire</span>
                <br>
                <span style="font-size:15px;color:#111827;font-weight:700;font-family:'Courier New',Courier,monospace;">{{ $tempPassword }}</span>
            </td>
        </tr>
    </table>
</div>

<div style="background-color:#fef9c3;border:1px solid #fde047;border-radius:10px;padding:12px 16px;margin:0 0 24px;">
    <p style="margin:0;font-size:13px;color:#713f12;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        <strong>Important :</strong> Changez ce mot de passe temporaire dès votre première connexion depuis vos Paramètres.
    </p>
</div>
@endif

{{-- CTA --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="{{ route('activation', ['token' => $user->activation_token]) }}"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.2px;">
                Activer mon compte &rarr;
            </a>
        </td>
    </tr>
</table>

{{-- Fallback URL --}}
<div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;margin:20px 0;">
    <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Ou copiez ce lien dans votre navigateur
    </p>
    <p style="margin:0;font-size:12px;color:#6366f1;word-break:break-all;font-family:monospace;">
        {{ route('activation', ['token' => $user->activation_token]) }}
    </p>
</div>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">

<p style="font-size:13px;color:#94a3b8;line-height:1.6;margin:0 0 8px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Ce lien expirera dans <strong style="color:#64748b;">24 heures</strong>. Si vous n'avez pas créé de compte, ignorez cet email.
</p>
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br>
    <strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
