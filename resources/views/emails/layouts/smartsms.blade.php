<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#eef2f7;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    {{-- Preheader text (hidden preview in inbox) --}}
    @hasSection('preheader')
    <div style="display:none;font-size:1px;color:#eef2f7;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">@yield('preheader') &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;</div>
    @endif

    {{-- Outer wrapper --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef2f7" style="background-color:#eef2f7;">
        <tr>
            <td align="center" valign="top" style="padding:40px 16px 48px;">

                {{-- Email card --}}
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 32px rgba(79,70,229,0.10),0 2px 8px rgba(0,0,0,0.06);">

                    {{-- ══════════ HEADER ══════════ --}}
                    <tr>
                        <td bgcolor="#4338ca" style="background:linear-gradient(135deg,#4338ca 0%,#6366f1 100%);padding:36px 40px 28px;text-align:center;">
                            {{-- Logo badge --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td bgcolor="rgba(255,255,255,0.15)" style="background-color:rgba(255,255,255,0.15);border-radius:18px;padding:14px 18px;">
                                                    <table cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="vertical-align:middle;padding-right:10px;">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                                                </svg>
                                                            </td>
                                                            <td style="vertical-align:middle;">
                                                                <span style="font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.3px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Smart<span style="color:#c7d2fe;">SMS</span></span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <br>
                                        <span style="font-size:12px;color:rgba(255,255,255,0.60);letter-spacing:1.5px;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">Plateforme Marketing SMS</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ══════════ CONTENT ══════════ --}}
                    <tr>
                        <td style="padding:40px 44px 32px;background-color:#ffffff;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- ══════════ FOOTER ══════════ --}}
                    <tr>
                        <td bgcolor="#f8fafc" style="background-color:#f8fafc;border-top:1px solid #e8edf5;padding:24px 44px;text-align:center;">
                            <p style="margin:0 0 6px;font-size:13px;color:#64748b;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                Besoin d'aide ?
                                <a href="mailto:{{ config('mail.from.address', 'support@smartsms.gn') }}" style="color:#6366f1;text-decoration:none;font-weight:600;">{{ config('mail.from.address', 'support@smartsms.gn') }}</a>
                            </p>
                            <p style="margin:0;font-size:12px;color:#94a3b8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
                                &copy; {{ date('Y') }} SmartSMS &middot; Conakry, Guinée &middot; Tous droits réservés
                            </p>
                        </td>
                    </tr>

                </table>
                {{-- /Email card --}}

            </td>
        </tr>
    </table>

</body>
</html>
