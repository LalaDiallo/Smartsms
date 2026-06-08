@extends('emails.layouts.smartsms')

@section('preheader')
{{ $user->name }} a soumis une campagne « {{ $campaign->name }} » en attente de votre validation.
@endsection

@section('title')
Campagne en attente de validation — SmartSMS Admin
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#dbeafe;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#1d4ed8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Action requise — Validation campagne
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nouvelle demande d'approbation
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <strong style="color:#111827;">{{ $user->name }}</strong> a soumis une campagne qui nécessite votre approbation avant d'être diffusée.
</p>

{{-- Détails campagne --}}
<div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px 24px;margin:0 0 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Nom de la campagne</span>
                <br>
                <span style="font-size:16px;color:#111827;font-weight:700;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $campaign->name }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Canal</span>
                <br>
                <span style="display:inline-block;margin-top:4px;background-color:#ede9fe;border-radius:100px;padding:4px 12px;font-size:12px;font-weight:700;color:#6d28d9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ strtoupper($campaign->channel ?? 'SMS') }}</span>
            </td>
        </tr>
        @if($campaign->region)
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Région</span>
                <br>
                <span style="font-size:14px;color:#374151;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $campaign->region }}</span>
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding:8px 0;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Soumis par</span>
                <br>
                <span style="font-size:14px;color:#374151;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $user->name }} &mdash; {{ $user->email }}</span>
            </td>
        </tr>
    </table>
</div>

{{-- CTA --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/campaigns/{{ $campaign->id }}"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Examiner la campagne &rarr;
            </a>
        </td>
    </tr>
</table>

<p style="font-size:13px;color:#94a3b8;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cet email a été envoyé automatiquement par SmartSMS. Ne pas répondre directement.
</p>
@endsection
