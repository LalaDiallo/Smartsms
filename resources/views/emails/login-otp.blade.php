@extends('emails.layouts.smartsms')

@section('preheader')
Votre code de connexion SmartSMS est {{ $code }}. Valable 5 minutes.
@endsection

@section('title')
Code de connexion — SmartSMS
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#dbeafe;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#1d4ed8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Code de vérification
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Connexion à SmartSMS
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 24px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Utilisez le code ci-dessous pour finaliser votre connexion. Ne partagez jamais ce code avec quelqu'un.
</p>

{{-- Code box --}}
<div style="background:linear-gradient(135deg,#f5f3ff 0%,#ede9fe 100%);border:2px solid #c4b5fd;border-radius:16px;padding:28px;text-align:center;margin:0 0 24px;">
    <span style="font-size:42px;font-weight:800;color:#4f46e5;letter-spacing:14px;font-family:'Courier New',Courier,monospace;display:inline-block;">{{ $code }}</span>
    <p style="margin:12px 0 0;font-size:13px;color:#7c3aed;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        Expire dans <strong>5 minutes</strong>
    </p>
</div>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">

<p style="font-size:13px;color:#94a3b8;line-height:1.6;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Si vous n'êtes pas à l'origine de cette demande, votre compte est peut-être compromis. Contactez-nous immédiatement à
    <a href="mailto:{{ config('mail.from.address', 'support@smartsms.gn') }}" style="color:#6366f1;text-decoration:none;">{{ config('mail.from.address', 'support@smartsms.gn') }}</a>.
</p>
@endsection
