@extends('emails.layouts.smartsms')

@section('preheader')
Votre compte SmartSMS a été suspendu. Contactez le support pour plus d'informations.
@endsection

@section('title')
Compte suspendu — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#fef3c7;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#b45309;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Compte suspendu
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $user->name }},
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nous vous informons que votre compte SmartSMS a été <strong style="color:#b45309;">temporairement suspendu</strong>.
    Pendant la suspension, l'accès à la plateforme et l'envoi de campagnes sont désactivés.
</p>

@if(isset($reason) && $reason)
<div style="background-color:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:0 12px 12px 0;padding:16px 20px;margin:0 0 24px;">
    <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#b45309;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Motif</p>
    <p style="margin:0;font-size:14px;color:#92400e;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $reason }}</p>
</div>
@endif

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Si vous pensez qu'il s'agit d'une erreur ou souhaitez régulariser votre situation, contactez notre équipe support.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="mailto:{{ config('mail.from.address', 'support@smartsms.gn') }}"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Contacter le support &rarr;
            </a>
        </td>
    </tr>
</table>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br><strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
