@extends('emails.layouts.smartsms')

@section('preheader')
Votre compte SmartSMS a été supprimé. Toutes vos données ont été effacées.
@endsection

@section('title')
Compte supprimé — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#fee2e2;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#dc2626;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Compte supprimé
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $user->name }},
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 16px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Nous vous confirmons que votre compte SmartSMS a été <strong style="color:#dc2626;">définitivement supprimé</strong>.
    L'ensemble de vos données (campagnes, contacts, paramètres) a été effacé de nos serveurs.
</p>

<div style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px 20px;margin:0 0 24px;">
    <p style="margin:0;font-size:13px;color:#7f1d1d;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Cette action est <strong>irréversible</strong>. Si vous souhaitez revenir sur la plateforme, vous devrez créer un nouveau compte.
    </p>
</div>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Si vous pensez qu'il s'agit d'une erreur, contactez notre support dans les plus brefs délais.
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
