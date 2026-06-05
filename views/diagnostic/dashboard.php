<?php
$heure = (int) date('H');
$salutation = $heure < 12 ? 'Bonjour' : ($heure < 18 ? 'Bon après-midi' : 'Bonsoir');
$prenom = user()['prenom'] ?? user()['nom'] ?? 'agriculteur';
$region = user()['region'] ?? 'Cameroun';
?>

<!-- HERO ACCUEIL CHALEUREUX -->
<section class="welcome-hero">
  <div class="greet-row">
    <div class="greet">
      <h1><?= h($salutation) ?>, <?= h($prenom) ?></h1>
      <p>Prêt à protéger vos cultures aujourd'hui ? Diagnostiquez une plante malade en quelques secondes.</p>
    </div>
    <a href="<?= url('/diagnostic/new') ?>" class="quick-action">
      <i class="fa-solid fa-camera"></i> Nouveau diagnostic
    </a>
  </div>

  <div class="meta-row">
    <div class="mi"><i class="fa-solid fa-location-dot"></i> <?= h($region) ?></div>
    <div class="mi"><i class="fa-regular fa-calendar"></i> <?= date('l j F Y') ?></div>
    <div class="mi"><i class="fa-solid fa-cloud-sun"></i> Saison : <?= (int) date('n') >= 4 && (int) date('n') <= 10 ? 'Pluvieuse' : 'Sèche' ?></div>
    <div class="mi"><i class="fa-solid fa-shield-halved"></i> Compte vérifié</div>
  </div>
</section>

<!-- TIP DU JOUR -->
<div class="tip-card">
  <div class="ic"><i class="fa-solid fa-lightbulb"></i></div>
  <div class="ct">
    <h4>Conseil du jour</h4>
    <p>En saison des pluies, inspectez quotidiennement vos plants de tomate — le mildiou se propage en moins de 48h en milieu humide.</p>
  </div>
</div>

<!-- STATS PRO -->
<div class="stats-row-pro">
  <div class="stat-pro leaf">
    <div class="ic"><i class="fa-solid fa-microscope"></i></div>
    <div class="lbl">Diagnostics totaux</div>
    <div class="val"><?= (int) ($stats['total'] ?? 0) ?></div>
    <div class="delta"><i class="fa-solid fa-arrow-up"></i> Depuis votre inscription</div>
  </div>
  <div class="stat-pro danger">
    <div class="ic"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="lbl">Cas sévères détectés</div>
    <div class="val"><?= (int) ($stats['severes'] ?? 0) ?></div>
    <div class="delta" style="color:var(--danger)"><i class="fa-solid fa-shield-halved"></i> Action requise</div>
  </div>
  <div class="stat-pro success">
    <div class="ic"><i class="fa-solid fa-leaf"></i></div>
    <div class="lbl">Plantes saines</div>
    <div class="val"><?= (int) ($stats['saines'] ?? 0) ?></div>
    <div class="delta"><i class="fa-solid fa-check"></i> Bonne santé</div>
  </div>
  <div class="stat-pro gold">
    <div class="ic"><i class="fa-solid fa-virus"></i></div>
    <div class="lbl">Maladies distinctes</div>
    <div class="val"><?= (int) ($stats['maladies_distinctes'] ?? 0) ?></div>
    <div class="delta"><i class="fa-solid fa-book"></i> À surveiller</div>
  </div>
</div>

<!-- ACTIONS PRINCIPALES -->
<div class="action-row-pro">
  <a href="<?= url('/diagnostic/new') ?>" class="action-pro hero-card">
    <div>
      <h3>Diagnostiquer maintenant</h3>
      <p>Prenez une photo de votre plante et obtenez une analyse complète en moins de 5 secondes.</p>
    </div>
    <span class="btn-inline">
      <i class="fa-solid fa-camera"></i> Lancer une analyse
    </span>
  </a>

  <a href="<?= url('/history') ?>" class="action-pro alt info">
    <div class="ic"><i class="fa-solid fa-clock-rotate-left"></i></div>
    <h3>Historique</h3>
    <p>Consultez tous vos diagnostics précédents</p>
  </a>

  <a href="<?= url('/map') ?>" class="action-pro alt gold">
    <div class="ic"><i class="fa-solid fa-map-location-dot"></i></div>
    <h3>Carte régionale</h3>
    <p>Alertes maladies dans votre zone</p>
  </a>
</div>

<!-- GRILLE PRINCIPALE -->
<div class="dash-grid">

  <!-- Diagnostics récents -->
  <div class="card">
    <div class="card-header">
      <h2><i class="fa-solid fa-clock-rotate-left"></i> Vos diagnostics récents</h2>
      <a href="<?= url('/history') ?>">Tout voir <i class="fa-solid fa-arrow-right"></i></a>
    </div>

    <?php if (empty($recents)): ?>
      <div class="empty" style="padding:50px 20px">
        <i class="fa-solid fa-seedling" style="font-size:60px;color:var(--leaf-pale);margin-bottom:18px"></i>
        <p style="font-size:15px;color:var(--leaf-dark);font-weight:600;margin-bottom:6px">Aucun diagnostic pour le moment</p>
        <p style="margin-bottom:18px">Lancez votre premier diagnostic et découvrez la puissance de l'IA.</p>
        <a href="<?= url('/diagnostic/new') ?>" class="btn btn-primary">
          <i class="fa-solid fa-camera"></i> Mon premier diagnostic
        </a>
      </div>
    <?php else: ?>
      <div class="diag-list">
        <?php foreach ($recents as $d): ?>
          <a href="<?= url('/diagnostic/' . $d['id']) ?>" class="diag-item">
            <div class="diag-thumb" style="background:linear-gradient(135deg, <?= h($d['couleur'] ?? '#52b788') ?>, var(--leaf-dark))">
              <i class="fa-solid <?= h($d['icone'] ?? 'fa-seedling') ?>"></i>
            </div>
            <div class="diag-info">
              <h4><?= h($d['maladie_nom'] ?? $d['maladie_detectee'] ?? 'Plante saine') ?></h4>
              <p><i class="fa-regular fa-clock"></i> <?= time_ago($d['created_at']) ?><?php if ($d['culture_nom']): ?> · <?= h($d['culture_nom']) ?><?php endif; ?></p>
              <div class="diag-tags">
                <?php if ($d['plante_saine']): ?>
                  <span class="tag tag-legere"><i class="fa-solid fa-check"></i> Saine</span>
                <?php else: ?>
                  <span class="tag tag-<?= h($d['gravite']) ?>"><?= h(ucfirst($d['gravite'])) ?></span>
                <?php endif; ?>
                <span class="tag tag-confidence"><?= (int) $d['confiance'] ?>%</span>
              </div>
            </div>
            <i class="fa-solid fa-chevron-right diag-arrow"></i>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Sidebar : météo + alertes -->
  <div>
    <div class="weather-card">
      <div class="top">
        <div>
          <h4>Météo aujourd'hui</h4>
          <div class="temp">26°C</div>
          <div class="loc"><i class="fa-solid fa-location-dot"></i> <?= h($region) ?></div>
        </div>
        <i class="fa-solid fa-cloud-sun ic-w"></i>
      </div>
      <div class="alert-mini">
        <i class="fa-solid fa-droplet"></i>
        <span>Humidité 78% — risque accru de mildiou</span>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 style="font-size:15px"><i class="fa-solid fa-bell"></i> Alertes régionales</h2>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="padding:12px;background:var(--danger-light);border-radius:10px;border-left:3px solid var(--danger)">
          <strong style="color:#991b1b;font-size:13px"><i class="fa-solid fa-triangle-exclamation"></i> Mildiou tomate</strong>
          <p style="font-size:12px;color:#374151;margin-top:4px">12 cas signalés cette semaine dans votre région</p>
        </div>
        <div style="padding:12px;background:var(--warning-light);border-radius:10px;border-left:3px solid var(--warning)">
          <strong style="color:#92400e;font-size:13px"><i class="fa-solid fa-circle-exclamation"></i> Pourriture cacao</strong>
          <p style="font-size:12px;color:#374151;margin-top:4px">Saison favorable — vigilance accrue</p>
        </div>
      </div>
    </div>
  </div>

</div>
