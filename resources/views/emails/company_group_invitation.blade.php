@extends('emails.layouts.smartsms')

@section('preheader')
{{ $group->ownerClient?->company_name }} vous invite à rejoindre son groupe d'entreprise sur SmartSMS. Ce lien expire dans 7 jours.
@endsection

@section('title')
Invitation à un groupe d'entreprise — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#e0e7ff;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#4338ca;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Invitation de groupe d'entreprise
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour,
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <strong style="color:#111827;">{{ $group->ownerClient?->company_name }}</strong> vous invite à rattacher votre compte SmartSMS au groupe d'entreprise
    <strong style="color:#111827;">« {{ $group->name }} »</strong> en tant que branche/agence
    (<strong>{{ $branch->zone_name }}</strong>).
</p>

<div style="background:linear-gradient(135deg,#eef2ff 0%,#e0e7ff 100%);border:1px solid #c7d2fe;border-radius:14px;padding:20px 24px;margin:0 0 24px;">
    <p style="margin:0;font-size:13px;color:#4338ca;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        En acceptant, le propriétaire du groupe pourra voir les statistiques consolidées de votre compte
        (campagnes, SMS envoyés, taux de livraison) dans son tableau de bord. Votre abonnement et vos données
        restent entièrement séparés et gérés par vous seul — vous pouvez refuser ou quitter le groupe à tout moment.
    </p>
</div>

{{-- CTA --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
    <tr>
        <td bgcolor="#4338ca" style="background-color:#4338ca;border-radius:12px;">
            <a href="{{ $acceptUrl }}"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.2px;">
                Voir l'invitation &rarr;
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
        {{ $acceptUrl }}
    </p>
</div>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">

<p style="font-size:13px;color:#94a3b8;line-height:1.6;margin:0 0 8px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Ce lien expirera dans <strong style="color:#64748b;">7 jours</strong>. Seul l'administrateur de votre compte SmartSMS peut accepter ou refuser cette invitation.
</p>
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br>
    <strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
