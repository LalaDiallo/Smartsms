@extends('emails.layouts.smartsms')

@section('preheader')
Nouvelle demande de branding de {{ $branding->brand_name }} — action requise.
@endsection

@section('title')
Nouvelle demande de branding — SmartSMS Admin
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#dbeafe;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#1d4ed8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Action requise — Demande de branding
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nouvelle demande de branding
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Un client a soumis une nouvelle demande de personnalisation de branding. Veuillez l'examiner et l'approuver ou la rejeter depuis l'interface admin.
</p>

{{-- Détails --}}
<div style="background-color:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px 24px;margin:0 0 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Client</span>
                <br>
                <span style="font-size:15px;color:#111827;font-weight:600;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $branding->client->company_name ?? $branding->client->contact_name ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Nom de marque</span>
                <br>
                <span style="font-size:15px;color:#111827;font-weight:600;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $branding->brand_name }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Couleurs</span>
                <br>
                <table cellpadding="0" cellspacing="0" border="0" style="margin-top:6px;">
                    <tr>
                        @if($branding->primary_color)
                        <td style="padding-right:12px;">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="18" height="18" style="background-color:{{ $branding->primary_color }};border-radius:4px;width:18px;height:18px;border:1px solid #e2e8f0;">&nbsp;</td>
                                    <td style="padding-left:6px;font-size:13px;color:#374151;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Primaire : {{ $branding->primary_color }}</td>
                                </tr>
                            </table>
                        </td>
                        @endif
                        @if($branding->secondary_color)
                        <td>
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="18" height="18" style="background-color:{{ $branding->secondary_color }};border-radius:4px;width:18px;height:18px;border:1px solid #e2e8f0;">&nbsp;</td>
                                    <td style="padding-left:6px;font-size:13px;color:#374151;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Secondaire : {{ $branding->secondary_color }}</td>
                                </tr>
                            </table>
                        </td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;">
                <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Statut</span>
                <br>
                <span style="display:inline-block;margin-top:4px;background-color:#fef3c7;border-radius:100px;padding:4px 12px;font-size:12px;font-weight:700;color:#b45309;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">En attente</span>
            </td>
        </tr>
    </table>
</div>

{{-- CTA --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/branding"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Examiner la demande &rarr;
            </a>
        </td>
    </tr>
</table>

<p style="font-size:13px;color:#94a3b8;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cet email a été envoyé automatiquement par SmartSMS. Ne pas répondre directement.
</p>
@endsection
