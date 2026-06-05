<?php $isPublic = !auth(); ?>

<?php if ($isPublic): ?>
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><i class="fa-solid fa-virus"></i> Catalogue maladies</div>
    <h1>Encyclopédie des maladies des cultures</h1>
    <p class="lead">Consultez librement les <?= count($maladies) ?> pathologies couvertes par PlantDoc : symptômes, causes, traitements biologiques et préventifs.</p>
  </div>
</section>
<section class="section" style="background:var(--bg)">
  <div class="container">
<?php else: ?>
<div class="page-header">
  <h1><i class="fa-solid fa-virus"></i> Encyclopédie des maladies</h1>
  <p class="subtitle">Consultez le catalogue complet des pathologies couvertes par PlantDoc.</p>
</div>
<?php endif; ?>

<!-- FILTRES -->
<div class="card" style="margin-bottom:24px">
  <form method="get" action="" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:end">
    <div class="form-group" style="margin:0">
      <label style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:600;margin-bottom:6px;display:block">Rechercher</label>
      <div class="input-wrap" style="position:relative">
        <i class="fa-solid fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted)"></i>
        <input type="text" name="q" value="<?= h($search) ?>" placeholder="Nom de la maladie, symptômes…" class="form-control" style="padding-left:42px;width:100%;padding-top:10px;padding-bottom:10px;border:1px solid var(--border);border-radius:8px;font-size:14px">
      </div>
    </div>
    <div class="form-group" style="margin:0">
      <label style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:600;margin-bottom:6px;display:block">Culture</label>
      <select name="culture" class="form-control" style="padding:10px;width:100%;border:1px solid var(--border);border-radius:8px;font-size:14px">
        <option value="">Toutes</option>
        <?php foreach ($cultures as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $filters['culture'] == $c['id'] ? 'selected' : '' ?>><?= h($c['nom']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group" style="margin:0">
      <label style="font-size:11px;color:var(--muted);text-transform:uppercase;font-weight:600;margin-bottom:6px;display:block">Type</label>
      <select name="type" class="form-control" style="padding:10px;width:100%;border:1px solid var(--border);border-radius:8px;font-size:14px">
        <option value="">Tous</option>
        <?php foreach (['fongique','bactérienne','virale','ravageur','carence','autre'] as $t): ?>
          <option value="<?= $t ?>" <?= $filters['type'] === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="height:42px"><i class="fa-solid fa-filter"></i> Filtrer</button>
  </form>
</div>

<!-- RÉSULTATS -->
<?php if (empty($maladies)): ?>
  <div class="card" style="text-align:center;padding:50px 20px">
    <i class="fa-solid fa-magnifying-glass" style="font-size:50px;color:var(--border);margin-bottom:18px"></i>
    <p style="color:var(--leaf-dark);font-weight:600;margin-bottom:6px">Aucune maladie ne correspond à vos critères</p>
    <a href="<?= url('/maladies') ?>" class="btn btn-secondary" style="margin-top:14px">Réinitialiser les filtres</a>
  </div>
<?php else: ?>
  <div style="margin-bottom:14px;color:var(--muted);font-size:13px"><strong><?= count($maladies) ?></strong> maladie<?= count($maladies) > 1 ? 's' : '' ?> trouvée<?= count($maladies) > 1 ? 's' : '' ?></div>

  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px">
    <?php foreach ($maladies as $m): ?>
      <a href="<?= url('/maladies/' . $m['id']) ?>" style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px;text-decoration:none;color:inherit;transition:.3s;display:block;position:relative" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 15px 35px rgba(0,0,0,.08)';this.style.borderColor='var(--leaf-light)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';this.style.borderColor='var(--border)'">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
          <div style="width:44px;height:44px;border-radius:12px;background:<?= h($m['couleur'] ?? '#52b788') ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
            <i class="fa-solid <?= h($m['icone'] ?? 'fa-seedling') ?>"></i>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:11px;color:var(--gold);font-weight:700;text-transform:uppercase;letter-spacing:1px"><?= h($m['culture_nom']) ?></div>
            <span class="tag tag-<?= $m['severite_typique'] === 'severe' ? 'severe' : ($m['severite_typique'] === 'moderee' ? 'moderee' : 'legere') ?>" style="font-size:9px;margin-top:2px;display:inline-block"><?= h(ucfirst($m['type_pathologie'])) ?></span>
          </div>
        </div>
        <h3 style="font-size:16px;color:var(--leaf-dark);font-weight:700;margin-bottom:4px"><?= h($m['nom_commun']) ?></h3>
        <p style="font-style:italic;color:var(--muted);font-size:12px;margin-bottom:12px"><?= h($m['nom_scientifique'] ?? '—') ?></p>
        <p style="font-size:13px;color:#374151;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden"><?= h(mb_substr($m['symptomes'], 0, 120)) ?>…</p>
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:11px;color:var(--muted)"><i class="fa-solid fa-book-open"></i> Lire la fiche</span>
          <i class="fa-solid fa-arrow-right" style="color:var(--leaf);font-size:13px"></i>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php if ($isPublic): ?>
  </div>
</section>

<section class="cta-final">
  <div class="container">
    <h2>Une maladie sur vos plantes ?</h2>
    <p>Identifiez-la en 5 secondes avec l'IA — inscription gratuite.</p>
    <a href="<?= url('/register') ?>" class="btn">
      <i class="fa-solid fa-camera"></i> Diagnostiquer ma plante
    </a>
  </div>
</section>
<?php endif; ?>
