<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 11px;
    color: #1f2937;
    background: #fff;
  }

  .header {
    background: #1e40af;
    color: #fff;
    padding: 22px 28px;
    margin-bottom: 28px;
  }
  .brand { font-size: 22px; font-weight: 700; letter-spacing: 1px; }
  .brand span { color: #93c5fd; }
  .doc-label { margin-top: 6px; font-size: 12px; opacity: .8; text-transform: uppercase; letter-spacing: 1px; }
  .badge-approved {
    display: inline-block;
    background: #059669;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 3px 10px;
    border-radius: 12px;
    margin-top: 10px;
  }

  .card {
    margin: 0 28px 18px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
  }
  .card-header {
    background: #f8fafc;
    padding: 9px 16px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
  }
  .card-body { padding: 14px 16px; }

  .brand-name-display {
    font-size: 28px;
    font-weight: 700;
    color: #1e40af;
    text-align: center;
    padding: 14px 0 8px;
  }
  .brand-sub {
    text-align: center;
    font-size: 10px;
    color: #6b7280;
    margin-bottom: 8px;
  }

  /* Palette de couleurs */
  .color-row { display: flex; gap: 12px; margin-top: 6px; }
  .color-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 10px;
    color: #374151;
  }
  .color-square {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    border: 1px solid rgba(0,0,0,.1);
  }

  table { width: 100%; border-collapse: collapse; font-size: 10px; }
  tr { border-bottom: 1px solid #f3f4f6; }
  tr:last-child { border-bottom: none; }
  td { padding: 7px 10px; }
  td:first-child { color: #6b7280; width: 45%; font-weight: 500; }
  td:last-child { color: #111827; }

  .seal {
    margin: 12px 28px;
    border: 2px dashed #1e40af;
    border-radius: 10px;
    padding: 12px 20px;
    text-align: center;
    color: #1e40af;
    font-size: 10px;
  }
  .seal-title { font-size: 13px; font-weight: 700; margin-bottom: 4px; }

  .footer {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    height: 68px;
    background: #f8fafc;
    border-top: 2px solid #1e40af;
    padding: 8px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .footer-left { font-size: 8.5px; color: #6b7280; line-height: 1.5; }
  .footer-left strong { color: #1e40af; }
  .footer-center { font-size: 8.5px; color: #9ca3af; text-align: center; }
  .footer-qr img { width: 50px; height: 50px; }

  .page-body { padding-bottom: 82px; }
</style>
</head>
<body>

<!-- En-tête -->
<div class="header">
  <div class="brand">Smart<span>SMS</span></div>
  <div class="doc-label">Certificat d'approbation — Charte de marque</div>
  <div class="badge-approved">&#10003; Approuvé</div>
</div>

<div class="page-body">

  <!-- Nom de marque mis en avant -->
  <div class="card">
    <div class="card-header">Marque approuvée</div>
    <div class="card-body">
      <div class="brand-name-display">{{ $branding->brand_name }}</div>
      <div class="brand-sub">
        Approuvé le {{ \Carbon\Carbon::parse($branding->approved_at)->format('d/m/Y') }}
        · Réf. {{ $ref }}
      </div>

      <!-- Palette couleurs -->
      @if($branding->primary_color || $branding->secondary_color || $branding->accent_color)
      <div class="color-row">
        @if($branding->primary_color)
        <div class="color-chip">
          <div class="color-square" style="background: {{ $branding->primary_color }}"></div>
          Principale · {{ $branding->primary_color }}
        </div>
        @endif
        @if($branding->secondary_color)
        <div class="color-chip">
          <div class="color-square" style="background: {{ $branding->secondary_color }}"></div>
          Secondaire · {{ $branding->secondary_color }}
        </div>
        @endif
        @if($branding->accent_color)
        <div class="color-chip">
          <div class="color-square" style="background: {{ $branding->accent_color }}"></div>
          Accentuation · {{ $branding->accent_color }}
        </div>
        @endif
      </div>
      @endif
    </div>
  </div>

  <!-- Informations -->
  <div class="card">
    <div class="card-header">Informations</div>
    <div class="card-body">
      <table>
        <tr><td>Client</td><td>{{ $clientName }}</td></tr>
        <tr><td>Nom de marque</td><td>{{ $branding->brand_name }}</td></tr>
        @if($branding->font_family)
        <tr><td>Police de caractères</td><td>{{ $branding->font_family }}</td></tr>
        @endif
        @if($branding->description)
        <tr><td>Description</td><td>{{ $branding->description }}</td></tr>
        @endif
        <tr><td>Date de demande</td><td>{{ \Carbon\Carbon::parse($branding->created_at)->format('d/m/Y') }}</td></tr>
        <tr><td>Date d'approbation</td><td>{{ \Carbon\Carbon::parse($branding->approved_at)->format('d/m/Y') }}</td></tr>
        <tr><td>Référence document</td><td>{{ $ref }}</td></tr>
      </table>
    </div>
  </div>

  <!-- Cachet -->
  <div class="seal">
    <div class="seal-title">Certificat officiel SmartSMS</div>
    <div>Ce document certifie que la charte de marque <strong>« {{ $branding->brand_name }} »</strong> a été</div>
    <div>approuvée et activée sur la plateforme SmartSMS.</div>
    <div style="margin-top:6px; font-size:9px; color:#6b7280">
      Scannez le QR code pour vérifier l'authenticité de ce document.
    </div>
  </div>

</div>

<!-- Pied de page avec QR Code -->
<div class="footer">
  <div class="footer-left">
    <strong>SmartSMS</strong><br>
    Réf : {{ $ref }}<br>
    Généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}
  </div>
  <div class="footer-center">
    Scannez le code QR<br>pour vérifier ce document
  </div>
  <div class="footer-qr">
    <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code" />
  </div>
</div>

</body>
</html>
