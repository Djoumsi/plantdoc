<?php $isPublic = !auth(); ?>

<?php if ($isPublic): ?>
<section class="page-hero" style="padding:50px 0">
  <div class="container">
    <div class="breadcrumb"><a href="<?= url('/maladies') ?>" style="color:#fff"><i class="fa-solid fa-arrow-left"></i> Retour au catalogue</a></div>
    <h1><?= h($maladie['nom_commun']) ?></h1>
    <p class="lead" style="font-style:italic;font-size:16px"><?= h($maladie['nom_scientifique'] ?? '') ?></p>
  </div>
</section>
<section class="section" style="background:#fff">
  <div class="container">
<?php else: ?>
<div class="page-header">
  <a href="<?= url('/maladies') ?>" style="font-size:13px;color:var(--muted);text-decoration:none"><i class="fa-solid fa-arrow-left"></i> Retour au catalogue</a>
  <h1><?= h($maladie['nom_commun']) ?></h1>
  <p class="subtitle" style="font-style:italic"><?= h($maladie['nom_scientifique'] ?? '') ?></p>
</div>
<?php endif; ?>

<!-- Bandeau infos -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px">
  <div class="card" style="margin:0;padding:18px;text-align:center">
    <div style="width:50px;height:50px;border-radius:12px;background:<?= h($culture['couleur'] ?? '#52b788') ?>;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:10px">
      <i class="fa-solid <?= h($culture['icone'] ?? 'fa-seedling') ?>"></i>
    </div>
    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px">Culture</div>
    <strong style="color:var(--leaf-dark)"><?= h($culture['nom'] ?? '—') ?></strong>
  </div>
  <div class="card" style="margin:0;padding:18px;text-align:center">
    <div class="icon-circle icon-info" style="margin:0 auto 10px"><i class="fa-solid fa-microscope"></i></div>
    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px">Type</div>
    <strong style="color:var(--leaf-dark)"><?= h(ucfirst($maladie['type_pathologie'])) ?></strong>
  </div>
  <div class="card" style="margin:0;padding:18px;text-align:center">
    <div class="icon-circle icon-<?= $maladie['severite_typique'] === 'severe' ? 'danger' : ($maladie['severite_typique'] === 'moderee' ? 'warning' : 'success') ?>" style="margin:0 auto 10px"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px">Gravité typique</div>
    <strong style="color:var(--leaf-dark)"><?= h(ucfirst($maladie['severite_typique'])) ?></strong>
  </div>
</div>

<!-- Contenu fiche -->
<div class="info-block">
  <h3><i class="fa-solid fa-eye"></i> Symptômes</h3>
  <p><?= nl2br(h($maladie['symptomes'])) ?></p>
</div>

<?php if ($maladie['causes']): ?>
<div class="info-block warning">
  <h3><i class="fa-solid fa-virus" style="color:var(--warning)"></i> Causes</h3>
  <p><?= nl2br(h($maladie['causes'])) ?></p>
</div>
<?php endif; ?>

<?php if ($maladie['traitements_bio']): ?>
<div class="info-block success">
  <h3><i class="fa-solid fa-seedling"></i> Traitements biologiques (recommandés)</h3>
  <p><?= nl2br(h($maladie['traitements_bio'])) ?></p>
</div>
<?php endif; ?>

<?php if ($maladie['traitements_chim']): ?>
<div class="info-block danger">
  <h3><i class="fa-solid fa-flask-vial"></i> Traitements chimiques (en dernier recours)</h3>
  <p><?= nl2br(h($maladie['traitements_chim'])) ?></p>
</div>
<?php endif; ?>

<?php if ($maladie['prevention']): ?>
<div class="info-block">
  <h3><i class="fa-solid fa-shield-halved"></i> Prévention</h3>
  <p><?= nl2br(h($maladie['prevention'])) ?></p>
</div>
<?php endif; ?>

<!-- CTA -->
<div class="card" style="background:linear-gradient(135deg,var(--leaf),var(--leaf-dark));color:#fff;text-align:center;border:none;margin-top:30px">
  <h3 style="color:#fff;font-size:22px;margin-bottom:8px"><i class="fa-solid fa-camera"></i> Pensez-vous être touché par cette maladie ?</h3>
  <p style="opacity:.9;margin-bottom:18px;font-size:14px">Téléversez une photo de votre plante et obtenez un diagnostic IA en quelques secondes.</p>
  <a href="<?= auth() ? url('/diagnostic/new') : url('/register') ?>" class="btn" style="background:var(--gold);color:#1a1a1a;font-weight:700">
    <i class="fa-solid fa-wand-magic-sparkles"></i> <?= auth() ? 'Lancer un diagnostic' : 'S\'inscrire gratuitement' ?>
  </a>
</div>

<?php if ($isPublic): ?>
  </div>
</section>
<?php endif; ?>
