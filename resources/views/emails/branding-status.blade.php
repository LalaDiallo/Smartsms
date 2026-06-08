@extends('emails.layouts.smartsms')

@section('preheader')
@if($branding->status === 'approved')
Votre demande de branding a été approuvée. Félicitations !
@else
Votre demande de branding a été rejetée. Consultez le motif.
@endif
@endsection

@section('title')
{{ $branding->status === 'approved' ? 'Branding approuvé' : 'Branding rejeté' }} — SmartSMS
@endsection

@section('content')
@if($branding->status === 'approved')
{{-- ── APPROUVÉ ── --}}
<div style="text-align:center;margin-bottom:28px;">
    <div style="display:inline-block;background-color:#dcfce7;border-radius:50%;width:64px;height:64px;line-height:64px;text-align:center;margin-bottom:12px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-top:18px;">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>
    <br>
    <span style="display:inline-block;background-color:#dcfce7;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#15803d;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Demande approuvée
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Félicitations, {{ $branding->user->name ?? 'Madame/Monsieur' }} !
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Votre demande de personnalisation de branding pour <strong style="color:#111827;">{{ $branding->brand_name }}</strong> a été <strong style="color:#15803d;">approuvée</strong>.
    Votre identité visuelle est maintenant active sur la plateforme SmartSMS.
</p>

@if($branding->primary_color || $branding->logo)
<div style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:20px 24px;margin:0 0 24px;">
    <p style="margin:0 0 12px;font-size:13px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Votre branding activé</p>
    @if($branding->brand_name)
    <p style="margin:0 0 6px;font-size:14px;color:#374151;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;"><strong>Nom :</strong> {{ $branding->brand_name }}</p>
    @endif
    @if($branding->primary_color)
    <p style="margin:0;font-size:14px;color:#374151;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;"><strong>Couleur principale :</strong> {{ $branding->primary_color }}</p>
    @endif
</div>
@endif

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#16a34a" style="background-color:#16a34a;border-radius:12px;">
            <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/settings"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Voir mes paramètres &rarr;
            </a>
        </td>
    </tr>
</table>

@else
{{-- ── REJETÉ ── --}}
<div style="text-align:center;margin-bottom:28px;">
    <div style="display:inline-block;background-color:#fee2e2;border-radius:50%;width:64px;height:64px;line-height:64px;text-align:center;margin-bottom:12px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-top:18px;">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </div>
    <br>
    <span style="display:inline-block;background-color:#fee2e2;border-radius:100px;padding:8px 20px;font-size:13px;font-weight:700;color:#dc2626;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;letter-spacing:0.3px;">
        Demande rejetée
    </span>
</div>

<h2 style="font-size:22px;font-weight:700;color:#111827;margin:0 0 12px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Bonjour {{ $branding->user->name ?? 'Madame/Monsieur' }},
</h2>

<p style="font-size:15px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Après examen, votre demande de branding pour <strong style="color:#111827;">{{ $branding->brand_name }}</strong> n'a pas pu être approuvée.
</p>

@if($branding->status_motif)
<div style="background-color:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:0 12px 12px 0;padding:16px 20px;margin:0 0 24px;">
    <p style="margin:0 0 6px;font-size:12px;font-weight:700;color:#dc2626;text-transform:uppercase;letter-spacing:0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Motif de rejet</p>
    <p style="margin:0;font-size:14px;color:#7f1d1d;line-height:1.6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $branding->status_motif }}</p>
</div>
@endif

<p style="font-size:14px;color:#4b5563;line-height:1.75;margin:0 0 20px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Vous pouvez soumettre une nouvelle demande en tenant compte des remarques ci-dessus.
</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td bgcolor="#4f46e5" style="background-color:#4f46e5;border-radius:12px;">
            <a href="{{ config('app.frontend_url', 'http://localhost:5173') }}/settings"
               style="display:inline-block;padding:15px 32px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                Soumettre une nouvelle demande &rarr;
            </a>
        </td>
    </tr>
</table>
@endif

<hr style="border:none;border-top:1px solid #f1f5f9;margin:24px 0;">
<p style="font-size:14px;color:#4b5563;margin:0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    Cordialement,<br><strong style="color:#111827;">L'équipe SmartSMS</strong>
</p>
@endsection
