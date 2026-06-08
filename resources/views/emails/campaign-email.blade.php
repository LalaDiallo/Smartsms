@extends('emails.layouts.campaign')

@section('preheader')
{{ \Illuminate\Support\Str::limit(strip_tags($content ?? $campaign->description ?? ''), 100) }}
@endsection

@section('title')
{{ $campaign->name }}
@endsection

@section('campaign_title')
{{ $campaign->name }}
@endsection

@section('content')
{{-- Salutation --}}
<p style="font-size:16px;color:#374151;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour <strong style="color:#111827;">{{ $contact->name ?? $contact->phone ?? 'Cher(e) client(e)' }}</strong>,
</p>

{{-- Contenu de la campagne (HTML libre depuis l'éditeur) --}}
<div style="font-size:15px;color:#374151;line-height:1.75;margin:0 0 24px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    {!! $content !!}
</div>

{{-- CTA optionnel --}}
@if(isset($ctaText) && isset($ctaUrl) && $ctaText && $ctaUrl)
@php $primary = $branding->primary_color ?? '#4f46e5'; @endphp
<table cellpadding="0" cellspacing="0" border="0" style="margin:28px 0;">
    <tr>
        <td style="background-color:{{ $primary }};border-radius:12px;">
            <a href="{{ $ctaUrl }}"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                {{ $ctaText }} &rarr;
            </a>
        </td>
    </tr>
</table>
@endif

{{-- Séparateur + signature --}}
<hr style="border:none;border-top:1px solid #f1f5f9;margin:28px 0 20px;">
<p style="font-size:14px;color:#6b7280;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br>
    <strong style="color:#111827;">{{ $branding->brand_name ?? config('app.name') }}</strong>
</p>
@endsection
