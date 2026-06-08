@php
    $primary   = $branding->primary_color   ?? '#4f46e5';
    $secondary = $branding->secondary_color ?? '#6366f1';
    $brandName = $branding->brand_name      ?? config('app.name');
    $logoPath  = $branding->logo ? asset('storage/' . $branding->logo) : null;
@endphp
<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', $brandName)</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    @hasSection('preheader')
    <div style="display:none;font-size:1px;color:#f1f5f9;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">@yield('preheader') &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;</div>
    @endif

    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f1f5f9" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" valign="top" style="padding:40px 16px 48px;">

                {{-- Card --}}
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.10);">

                    {{-- ══ Header avec branding client ══ --}}
                    <tr>
                        <td style="background-color:{{ $primary }};padding:28px 40px;text-align:center;">
                            @if($logoPath)
                                <img src="{{ $logoPath }}" alt="{{ $brandName }}"
                                     width="auto" height="56"
                                     style="max-width:200px;max-height:56px;height:56px;object-fit:contain;display:block;margin:0 auto;">
                            @else
                                <span style="font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">{{ $brandName }}</span>
                            @endif
                        </td>
                    </tr>

                    {{-- ══ Bandeau titre campagne ══ --}}
                    @hasSection('campaign_title')
                    <tr>
                        <td style="background-color:#fafafa;border-bottom:1px solid #f1f5f9;padding:16px 40px;text-align:center;">
                            <span style="font-size:13px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:1px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">@yield('campaign_title')</span>
                        </td>
                    </tr>
                    @endif

                    {{-- ══ Contenu principal ══ --}}
                    <tr>
                        <td style="padding:40px 44px 32px;background-color:#ffffff;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- ══ CTA section (optionnel) ══ --}}
                    @hasSection('cta')
                    <tr>
                        <td style="padding:0 44px 32px;background-color:#ffffff;text-align:center;">
                            @yield('cta')
                        </td>
                    </tr>
                    @endif

                    {{-- ══ Footer ══ --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color:#f8fafc;border-top:1px solid #e8edf5;padding:20px 44px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:12px;color:#64748b;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                Vous recevez cet email car vous êtes inscrit chez <strong style="color:#374151;">{{ $brandName }}</strong>.
                            </p>
                            @if(isset($unsubscribeUrl) && !empty($unsubscribeUrl))
                            <p style="margin:0 0 8px;font-size:12px;color:#94a3b8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                <a href="{{ $unsubscribeUrl }}" style="color:#94a3b8;text-decoration:underline;">Se désabonner</a>
                            </p>
                            @endif
                            <p style="margin:0;font-size:11px;color:#cbd5e1;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                Propulsé par <a href="{{ config('app.url') }}" style="color:{{ $primary }};text-decoration:none;font-weight:600;">SmartSMS</a>
                                &mdash; &copy; {{ date('Y') }} {{ $brandName }}
                            </p>
                        </td>
                    </tr>

                    {{-- Pixel de tracking ouverture --}}
                    @if(isset($trackingPixelUrl) && !empty($trackingPixelUrl))
                    <tr>
                        <td style="line-height:0;font-size:0;">
                            <img src="{{ $trackingPixelUrl }}" width="1" height="1" alt="" style="display:block;width:1px;height:1px;border:0;">
                        </td>
                    </tr>
                    @endif

                </table>
                {{-- /Card --}}

            </td>
        </tr>
    </table>

</body>
</html>
