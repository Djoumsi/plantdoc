<?php
$saine    = (int) ($diag['plante_saine'] ?? 0) === 1;
$maladie  = $diag['maladie_nom'] ?? $diag['maladie_detectee'] ?? 'Plante saine';
$sci      = $diag['maladie_sci'] ?? $diag['nom_scientifique'] ?? '';
$gravite  = ucfirst($diag['gravite'] ?? 'inconnue');
$gravColor = ($diag['gravite'] ?? '') === 'severe' ? '#b91c1c' : (($diag['gravite'] ?? '') === 'moderee' ? '#b45309' : '#2d6a4f');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche diagnostic #<?= (int) $diag['id'] ?> — PlantDoc</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'Segoe UI', Arial, sans-serif; color:#1b4332; background:#eef2f0; padding:24px; }
  .sheet { background:#fff; width:210mm; min-height:297mm; margin:0 auto; padding:18mm 16mm; box-shadow:0 8px 30px rgba(0,0,0,.12); }
  .pdf-head { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #52b788; padding-bottom:14px; margin-bottom:22px; }
  .brand { font-size:26px; font-weight:800; color:#1b4332; }
  .brand span { color:#f4a261; }
  .brand small { display:block; font-size:11px; font-weight:500; color:#6b7280; margin-top:2px; }
  .meta { text-align:right; font-size:12px; color:#6b7280; }
  .meta strong { color:#1b4332; }
  .row { display:flex; gap:20px; margin-bottom:22px; }
  .photo { width:58mm; height:58mm; object-fit:cover; border-radius:10px; border:2px solid #e5e7eb; flex-shrink:0; }
  .diag-title { font-size:22px; font-weight:800; margin-bottom:4px; }
  .diag-sci { font-style:italic; color:#6b7280; font-size:13px; margin-bottom:14px; }
  .badge { display:inline-block; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; color:#fff; }
  .conf-bar { height:12px; background:#e5e7eb; border-radius:8px; overflow:hidden; margin-top:12px; }
  .conf-fill { height:100%; background:linear-gradient(90deg,#52b788,#2d6a4f); }
  .conf-label { font-size:12px; color:#6b7280; margin:10px 0 4px; display:flex; justify-content:space-between; }
  .block { border:1px solid #e5e7eb; border-left:4px solid #52b788; border-radius:8px; padding:14px 16px; margin-bottom:14px; }
  .block h3 { font-size:14px; margin-bottom:6px; color:#1b4332; }
  .block.warn { border-left-color:#f4a261; }
  .block.danger { border-left-color:#b91c1c; }
  .block p { font-size:13px; line-height:1.6; color:#374151; }
  .footer { margin-top:auto; border-top:1px solid #e5e7eb; padding-top:14px; display:flex; justify-content:space-between; align-items:center; }
  .qr-wrap { text-align:center; }
  .qr-wrap small { display:block; font-size:10px; color:#6b7280; margin-top:4px; }
  .foot-note { font-size:10px; color:#9ca3af; max-width:120mm; line-height:1.5; }
  .toolbar { width:210mm; margin:0 auto 16px; display:flex; gap:10px; justify-content:flex-end; }
  .btn { padding:10px 18px; border-radius:8px; border:none; font-size:13px; font-weight:600; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
  .btn-print { background:#2d6a4f; color:#fff; }
  .btn-back { background:#fff; color:#1b4332; border:1px solid #d1d5db; }
  @media print {
    body { background:#fff; padding:0; }
    .sheet { box-shadow:none; width:auto; min-height:auto; padding:12mm; }
    .toolbar { display:none; }
    @page { size:A4; margin:0; }
  }
</style>
</head>
<body>

<div class="toolbar">
  <a href="<?= h($shareUrl) ?>" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> Retour</a>
  <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-download"></i> Télécharger / Imprimer en PDF</button>
</div>

<div class="sheet">
  <div class="pdf-head">
    <div class="brand">Plant<span>Doc</span><small>Diagnostic phytosanitaire intelligent</small></div>
    <div class="meta">
      <strong>Fiche n° <?= (int) $diag['id'] ?></strong><br>
      <?= format_date($diag['created_at'], 'd/m/Y à H:i') ?><br>
      <?php if (!empty($diag['region_nom'])): ?>Région : <?= h($diag['region_nom']) ?><?php endif; ?>
    </div>
  </div>

  <div class="row">
    <img class="photo" src="<?= asset($diag['photo_path']) ?>" alt="Photo analysée">
    <div style="flex:1">
      <?php if ($saine): ?>
        <div class="diag-title" style="color:#2d6a4f"><i class="fa-solid fa-circle-check"></i> Plante en bonne santé</div>
        <p class="diag-sci">Aucun signe de maladie détecté.</p>
      <?php else: ?>
        <div class="diag-title"><?= h($maladie) ?></div>
        <p class="diag-sci"><?= h($sci) ?></p>
        <span class="badge" style="background:<?= $gravColor ?>">Gravité : <?= h($gravite) ?></span>
      <?php endif; ?>
      <div class="conf-label"><span>Confiance du diagnostic IA</span><strong><?= (int) $diag['confiance'] ?>%</strong></div>
      <div class="conf-bar"><div class="conf-fill" style="width:<?= (int) $diag['confiance'] ?>%"></div></div>
    </div>
  </div>

  <?php if (!$saine): ?>
    <?php if (!empty($diag['traitement_propose']) || !empty($diag['traitements_bio'])): ?>
    <div class="block">
      <h3><i class="fa-solid fa-prescription-bottle-medical"></i> Traitement recommandé</h3>
      <p><?= nl2br(h($diag['traitement_propose'] ?? $diag['traitements_bio'])) ?></p>
    </div>
    <?php endif; ?>
    <?php if (!empty($diag['prevention_proposee']) || !empty($diag['prevention_ref'])): ?>
    <div class="block warn">
      <h3><i class="fa-solid fa-shield-halved"></i> Mesures de prévention</h3>
      <p><?= nl2br(h($diag['prevention_proposee'] ?? $diag['prevention_ref'])) ?></p>
    </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (!empty($diag['commentaire_expert'])): ?>
  <div class="block danger">
    <h3><i class="fa-solid fa-user-doctor"></i> Avis de l'expert agronome</h3>
    <p><?= nl2br(h($diag['commentaire_expert'])) ?></p>
  </div>
  <?php endif; ?>

  <div class="footer">
    <div class="foot-note">
      <strong>Avertissement :</strong> ce diagnostic est généré par intelligence artificielle à titre indicatif.
      Pour les cas sévères, consultez un agronome ou un service phytosanitaire agréé.<br>
      Document généré par PlantDoc — <?= date('Y') ?>.
    </div>
    <div class="qr-wrap">
      <div id="qrcode"></div>
      <small>Scannez pour consulter en ligne</small>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
  new QRCode(document.getElementById('qrcode'), {
    text: <?= json_encode($shareUrl) ?>,
    width: 90, height: 90,
    colorDark: '#1b4332', colorLight: '#ffffff'
  });
</script>
</body>
</html>
<?php exit; ?>
