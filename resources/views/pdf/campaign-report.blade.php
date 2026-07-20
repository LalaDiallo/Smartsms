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

  /* ── En-tête ──────────────────────────────────── */
  .header {
    background: #1e40af;
    color: #fff;
    padding: 18px 24px;
    margin-bottom: 20px;
  }
  .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
  .brand { font-size: 20px; font-weight: 700; letter-spacing: 1px; }
  .brand span { color: #93c5fd; }
  .doc-title { font-size: 13px; text-align: right; opacity: .85; }
  .doc-ref { font-size: 10px; opacity: .65; margin-top: 2px; }

  .header-meta {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid rgba(255,255,255,.2);
    display: flex;
    gap: 30px;
  }
  .meta-item { font-size: 10px; opacity: .8; }
  .meta-item strong { display: block; font-size: 12px; opacity: 1; }

  /* ── Cartes KPI ───────────────────────────────── */
  .kpi-grid {
    display: flex;
    gap: 10px;
    margin: 0 24px 18px;
  }
  .kpi-card {
    flex: 1;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 14px;
    text-align: center;
  }
  .kpi-value { font-size: 22px; font-weight: 700; color: #1e40af; }
  .kpi-label { font-size: 9px; color: #6b7280; margin-top: 2px; text-transform: uppercase; letter-spacing: .5px; }

  /* ── Section ──────────────────────────────────── */
  .section {
    margin: 0 24px 18px;
  }
  .section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #374151;
    border-bottom: 2px solid #1e40af;
    padding-bottom: 4px;
    margin-bottom: 10px;
  }

  /* ── Tableau ──────────────────────────────────── */
  table { width: 100%; border-collapse: collapse; font-size: 10px; }
  thead tr { background: #f3f4f6; }
  th { text-align: left; padding: 6px 8px; font-weight: 600; color: #374151; border-bottom: 1px solid #d1d5db; }
  td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; color: #4b5563; }
  tr:last-child td { border-bottom: none; }
  .text-right { text-align: right; }
  .text-center { text-align: center; }

  /* Badges statut */
  .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 600; }
  .badge-blue    { background: #dbeafe; color: #1d4ed8; }
  .badge-green   { background: #d1fae5; color: #065f46; }
  .badge-red     { background: #fee2e2; color: #991b1b; }
  .badge-yellow  { background: #fef3c7; color: #92400e; }
  .badge-gray    { background: #f3f4f6; color: #374151; }

  /* ── Infos campagne ───────────────────────────── */
  .info-grid { display: flex; flex-wrap: wrap; gap: 8px; }
  .info-item { width: 48%; }
  .info-label { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; }
  .info-value { font-size: 11px; color: #111827; font-weight: 500; }

  /* ── Pied de page ─────────────────────────────── */
  .footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 72px;
    background: #f8fafc;
    border-top: 2px solid #1e40af;
    padding: 8px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .footer-left { font-size: 8.5px; color: #6b7280; line-height: 1.5; }
  .footer-left strong { color: #1e40af; }
  .footer-center { font-size: 9px; color: #9ca3af; text-align: center; }
  .footer-qr { text-align: right; }
  .footer-qr img { width: 50px; height: 50px; }

  /* ── Espacement bas de page pour le footer fixe ─ */
  .page-body { padding-bottom: 85px; }
</style>
</head>
<body>

<!-- ── En-tête ──────────────────────────────────────────── -->
<div class="header">
  <div class="header-top">
    <div>
      <div class="brand">Smart<span>SMS</span></div>
    </div>
    <div>
      <div class="doc-title">Rapport de Campagne</div>
      <div class="doc-ref">Réf : {{ $ref }}</div>
    </div>
  </div>
  <div class="header-meta">
    <div class="meta-item">
      <strong>{{ $campaign->name }}</strong>
      Campagne
    </div>
    <div class="meta-item">
      <strong>{{ ucfirst($campaign->channel) }}</strong>
      Canal
    </div>
    <div class="meta-item">
      <strong>
        @if($campaign->status === 'terminer') Terminée
        @elseif($campaign->status === 'active') Active
        @elseif($campaign->status === 'programmer') Programmée
        @elseif($campaign->status === 'brouillon') Brouillon
        @elseif($campaign->status === 'attente') En attente
        @else {{ $campaign->status }}
        @endif
      </strong>
      Statut
    </div>
    <div class="meta-item">
      <strong>{{ \Carbon\Carbon::parse($campaign->start_date)->format('d/m/Y') }}</strong>
      Date de début
    </div>
    <div class="meta-item">
      <strong>{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</strong>
      Généré le
    </div>
  </div>
</div>

<div class="page-body">

  <!-- ── KPI ──────────────────────────────────────────────── -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-value">{{ number_format($stats['total']) }}</div>
      <div class="kpi-label">Total envois</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-value" style="color:#059669">{{ number_format($stats['delivered']) }}</div>
      <div class="kpi-label">Délivrés</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-value" style="color:#dc2626">{{ number_format($stats['failed']) }}</div>
      <div class="kpi-label">Échoués</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-value">{{ $stats['delivery_rate'] }}%</div>
      <div class="kpi-label">Taux délivraison</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-value" style="color:#7c3aed">{{ number_format($stats['responses']) }}</div>
      <div class="kpi-label">Réponses</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-value">{{ $stats['reply_rate'] }}%</div>
      <div class="kpi-label">Taux réponse</div>
    </div>
  </div>

  <!-- ── Informations campagne ────────────────────────────── -->
  <div class="section">
    <div class="section-title">Informations</div>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Description</div>
        <div class="info-value">{{ $campaign->description ?: '—' }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Région</div>
        <div class="info-value">{{ $campaign->region ?: '—' }}</div>
      </div>
      @if($campaign->end_date)
      <div class="info-item">
        <div class="info-label">Date de fin</div>
        <div class="info-value">{{ \Carbon\Carbon::parse($campaign->end_date)->format('d/m/Y') }}</div>
      </div>
      @endif
      <div class="info-item">
        <div class="info-label">Client</div>
        <div class="info-value">{{ $clientName }}</div>
      </div>
    </div>
  </div>

  <!-- ── Répartition par canal ────────────────────────────── -->
  @if(count($byChannel) > 0)
  <div class="section">
    <div class="section-title">Performances par canal</div>
    <table>
      <thead>
        <tr>
          <th>Canal</th>
          <th class="text-right">Total</th>
          <th class="text-right">Envoyés</th>
          <th class="text-right">Délivrés</th>
          <th class="text-right">Échoués</th>
          <th class="text-right">Réponses</th>
          <th class="text-right">Taux rép.</th>
        </tr>
      </thead>
      <tbody>
        @foreach($byChannel as $ch)
        <tr>
          <td><span class="badge badge-blue">{{ $ch['name'] }}</span></td>
          <td class="text-right">{{ number_format($ch['total']) }}</td>
          <td class="text-right">{{ number_format($ch['sent']) }}</td>
          <td class="text-right">{{ number_format($ch['delivered']) }}</td>
          <td class="text-right">{{ number_format($ch['failed']) }}</td>
          <td class="text-right">{{ number_format($ch['responses']) }}</td>
          <td class="text-right">{{ $ch['reply_rate'] }}%</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  <!-- ── Évolution quotidienne ────────────────────────────── -->
  @if(count($trend) > 0)
  <div class="section">
    <div class="section-title">Évolution quotidienne ({{ count($trend) }} jours)</div>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th class="text-right">Total</th>
          <th class="text-right">Envoyés</th>
          <th class="text-right">Délivrés</th>
          <th class="text-right">Échoués</th>
          <th class="text-right">Réponses</th>
        </tr>
      </thead>
      <tbody>
        @foreach($trend as $day)
        <tr>
          <td>{{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
          <td class="text-right">{{ number_format($day['total']) }}</td>
          <td class="text-right">{{ number_format($day['sent']) }}</td>
          <td class="text-right">{{ number_format($day['delivered']) }}</td>
          <td class="text-right">{{ number_format($day['failed']) }}</td>
          <td class="text-right">{{ number_format($day['responses']) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

</div><!-- /page-body -->

<!-- ── Pied de page avec code-barres QR ───────────────────── -->
<div class="footer">
  <div class="footer-left">
    <strong>SmartSMS</strong><br>
    Réf : {{ $ref }}<br>
    Généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}
  </div>
  <div class="footer-center">
    Scannez le code QR pour vérifier<br>l'authenticité de ce document
  </div>
  <div class="footer-qr">
    <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code" />
  </div>
</div>

</body>
</html>
