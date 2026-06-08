@extends('emails.layouts.smartsms')

@section('preheader')
Votre campagne « {{ $campaignName }} » n'a pas été retenue. Consultez le motif ci-dessous.
@endsection

@section('title')
Campagne rejetée — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#fee2e2;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#dc2626;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Campagne rejetée
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $recipientName }},
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 16px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nous vous remercions pour la soumission de la campagne <strong style="color:#111827;">« {{ $campaignName }} »</strong>.
    Après examen, nous sommes au regret de vous informer qu'elle n'a pas été approuvée.
</p>

@if(isset($reason) && $reason)
<div style="background-color:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:0 12px 12px 0;padding:16px 20px;margin:0 0 24px;">
    <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Motif de rejet</p>
    <p style="margin:0;font-size:14px;color:#7f1d1d;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $reason }}</p>
</div>
@endif

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nous vous encourageons à prendre en compte ces remarques et à soumettre une nouvelle campagne. Notre équipe reste disponible pour vous accompagner.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/campaigns"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Créer une nouvelle campagne &rarr;
            </a>
        </td>
    </tr>
</table>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Pour toute question, contactez-nous à
    <a href="mailto:{{ $contactEmail ?? config('mail.from.address', 'support@smartsms.gn') }}"
       style="color:#6366f1;text-decoration:none;font-weight:600;">{{ $contactEmail ?? config('mail.from.address', 'support@smartsms.gn') }}</a>.
    <br><br>
    Cordialement,<br><strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
