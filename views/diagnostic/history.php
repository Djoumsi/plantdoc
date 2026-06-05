<div class="page-header">
  <h1><i class="fa-solid fa-clock-rotate-left"></i> Mon historique</h1>
  <p class="subtitle"><?= count($diagnostics) ?> diagnostic(s) au total</p>
</div>

<div class="card">
<?php if (empty($diagnostics)): ?>
  <div class="empty">
    <i class="fa-solid fa-folder-open"></i>
    <p>Aucun diagnostic.<br><a href="<?= url('/diagnostic/new') ?>" class="btn btn-primary" style="margin-top:14px"><i class="fa-solid fa-camera"></i> Lancer mon premier diagnostic</a></p>
  </div>
<?php else: ?>
  <div class="diag-list">
    <?php foreach ($diagnostics as $d): ?>
      <a href="<?= url('/diagnostic/' . $d['id']) ?>" class="diag-item">
        <div class="diag-thumb" style="background:linear-gradient(135deg, <?= h($d['couleur'] ?? '#52b788') ?>, var(--leaf-dark))">
          <i class="fa-solid <?= h($d['icone'] ?? 'fa-seedling') ?>"></i>
        </div>
        <div class="diag-info">
          <h4><?= h($d['maladie_nom'] ?? $d['maladie_detectee'] ?? 'Plante saine') ?></h4>
          <p>
            <i class="fa-regular fa-calendar"></i> <?= format_date($d['created_at'], 'd M Y H:i') ?>
            <?php if ($d['culture_nom']): ?>· <?= h($d['culture_nom']) ?><?php endif; ?>
          </p>
          <div class="diag-tags">
            <?php if ($d['plante_saine']): ?>
              <span class="tag tag-legere"><i class="fa-solid fa-check"></i> Saine</span>
            <?php else: ?>
              <span class="tag tag-<?= h($d['gravite']) ?>"><?= h(ucfirst($d['gravite'])) ?></span>
            <?php endif; ?>
            <span class="tag tag-confidence"><i class="fa-solid fa-percent"></i> <?= (int) $d['confiance'] ?></span>
            <span class="tag tag-<?= h($d['statut']) ?>"><?= h(str_replace('_',' ',$d['statut'])) ?></span>
          </div>
        </div>
        <i class="fa-solid fa-chevron-right diag-arrow"></i>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
</div>
