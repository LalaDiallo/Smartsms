@extends('emails.layouts.smartsms')

@section('preheader')
{{ Str::limit(strip_tags($body ?? ''), 90) }}
@endsection

@section('title')
{{ $subject ?? 'Message de SmartSMS' }}
@endsection

@section('content')
{{-- Badge --}}
<div style="text-align:center;margin-bottom:28px;">
    <span style="display:inline-block;background-color:#ede9fe;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#6d28d9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Message de SmartSMS
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $client->contact_name ?? $client->company_name ?? 'Madame / Monsieur' }},
</h2>

<div style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 24px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    {!! nl2br(e($body)) !!}
</div>

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br><strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
