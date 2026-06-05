<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
  <div>
    <h1><i class="fa-solid fa-clipboard-check"></i> Diagnostic #<?= (int) $diag['id'] ?></h1>
    <p class="subtitle"><i class="fa-regular fa-calendar"></i> <?= format_date($diag['created_at']) ?></p>
  </div>
  <button type="button" id="ttsBtn" class="tts-btn" data-read="<?= t('common.read_aloud') ?>" data-stop="<?= t('common.stop_reading') ?>">
    <i class="fa-solid fa-volume-high"></i> <span><?= t('common.read_aloud') ?></span>
  </button>
</div>

<?php
// Texte lu à voix haute (accessibilité illettrisme)
$ttsParts = [];
$ttsParts[] = 'Diagnostic numéro ' . (int) $diag['id'] . '.';
if ($diag['plante_saine']) {
    $ttsParts[] = "Bonne nouvelle, votre plante est en bonne santé. Aucun signe de maladie détecté.";
} else {
    $ttsParts[] = 'Maladie détectée : ' . ($diag['maladie_nom'] ?? $diag['maladie_detectee'] ?? 'inconnue') . '.';
    $ttsParts[] = 'Niveau de confiance : ' . (int) $diag['confiance'] . ' pour cent.';
    $ttsParts[] = 'Gravité : ' . ($diag['gravite'] ?? 'inconnue') . '.';
    if (!empty($diag['traitement_propose'])) $ttsParts[] = 'Traitement recommandé : ' . $diag['traitement_propose'] . '.';
    if (!empty($diag['prevention_proposee'] ?? $diag['prevention_ref'])) $ttsParts[] = 'Prévention : ' . ($diag['prevention_proposee'] ?? $diag['prevention_ref']) . '.';
}
$ttsText = implode(' ', $ttsParts);
?>

<div class="result-hero">
  <img src="<?= asset($diag['photo_path']) ?>" alt="Photo analysée">
  <div class="result-body">
    <?php if ($diag['plante_saine']): ?>
      <h2><i class="fa-solid fa-circle-check" style="color:var(--success)"></i> Plante en bonne santé</h2>
      <p>Aucun signe de maladie détecté sur cette photo.</p>
    <?php else: ?>
      <h2><?= h($diag['maladie_nom'] ?? $diag['maladie_detectee']) ?></h2>
      <p class="sci"><?= h($diag['maladie_sci'] ?? $diag['nom_scientifique'] ?? '') ?></p>
    <?php endif; ?>

    <div class="confidence-meter">
      <div style="display:flex;align-items:baseline;justify-content:space-between">
        <span style="font-size:13px;opacity:.9">Confiance du diagnostic</span>
        <span class="pct"><?= (int) $diag['confiance'] ?>%</span>
      </div>
      <div class="progress"><div class="progress-fill" style="width:<?= (int) $diag['confiance'] ?>%"></div></div>
    </div>
  </div>
</div>

<?php if (!$diag['plante_saine']): ?>

<div class="info-block <?= $diag['gravite'] === 'severe' ? 'danger' : ($diag['gravite'] === 'moderee' ? 'warning' : 'success') ?>">
  <h3>
    <i class="fa-solid fa-triangle-exclamation"></i>
    Gravité : <?= h(ucfirst($diag['gravite'])) ?>
  </h3>
  <?php if ($diag['gravite'] === 'severe'): ?>
    <p>Action immédiate recommandée pour éviter la propagation et la perte de récolte.</p>
  <?php elseif ($diag['gravite'] === 'moderee'): ?>
    <p>Traitement à appliquer dans les prochains jours.</p>
  <?php else: ?>
    <p>Surveillance suffisante. Inspectez régulièrement vos plants.</p>
  <?php endif; ?>
</div>

<?php if ($diag['traitement_propose']): ?>
<div class="info-block">
  <h3><i class="fa-solid fa-prescription-bottle-medical"></i> Traitement recommandé</h3>
  <p><?= nl2br(h($diag['traitement_propose'])) ?></p>
</div>
<?php endif; ?>

<?php if ($diag['traitements_bio']): ?>
<div class="info-block success">
  <h3><i class="fa-solid fa-seedling"></i> Solutions biologiques (catalogue)</h3>
  <p><?= nl2br(h($diag['traitements_bio'])) ?></p>
</div>
<?php endif; ?>

<?php if ($diag['prevention_proposee'] || $diag['prevention_ref']): ?>
<div class="info-block warning">
  <h3><i class="fa-solid fa-shield-halved"></i> Prévention</h3>
  <p><?= nl2br(h($diag['prevention_proposee'] ?? $diag['prevention_ref'])) ?></p>
</div>
<?php endif; ?>

<?php endif; ?>

<?php if (!empty($diag['commentaire_expert'])): ?>
<div class="info-block">
  <h3><i class="fa-solid fa-user-doctor" style="color:var(--info)"></i> Avis de l'expert</h3>
  <p><?= nl2br(h($diag['commentaire_expert'])) ?></p>
</div>
<?php endif; ?>

<!-- FEEDBACK -->
<?php if ((int) $diag['user_id'] === (int) $_SESSION['user_id']): ?>
<div class="card" style="margin-top:24px;background:linear-gradient(135deg,var(--gold-light),#fff);border-left:4px solid var(--gold)">
  <?php if ($feedback): ?>
    <div style="display:flex;align-items:center;gap:14px">
      <div class="icon-circle icon-success" style="width:48px;height:48px;font-size:18px"><i class="fa-solid fa-circle-check"></i></div>
      <div style="flex:1">
        <h3 style="margin:0;color:var(--leaf-dark);font-size:16px">Merci pour votre retour</h3>
        <p style="margin:4px 0 0;font-size:13px;color:var(--muted)">
          Vous avez indiqué que ce diagnostic était
          <strong style="color:<?= $feedback['est_correct'] ? 'var(--success)' : 'var(--danger)' ?>">
            <?= $feedback['est_correct'] ? 'correct' : 'incorrect' ?>
          </strong>
          <?php if ($feedback['note']): ?>· Note : <?= str_repeat('⭐', (int) $feedback['note']) ?><?php endif; ?>
        </p>
      </div>
    </div>
  <?php else: ?>
    <h3 style="font-size:16px;color:var(--leaf-dark);display:flex;align-items:center;gap:10px;margin-bottom:6px">
      <i class="fa-solid fa-comment-dots" style="color:var(--gold)"></i> Ce diagnostic vous a-t-il aidé ?
    </h3>
    <p style="color:var(--muted);font-size:13px;margin-bottom:18px">Votre avis nous aide à améliorer la précision de l'IA.</p>

    <form method="post" action="<?= url('/diagnostic/' . $diag['id'] . '/feedback') ?>" id="feedback-form">
      <?= Csrf::field() ?>

      <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap">
        <label class="fb-choice" style="flex:1;min-width:140px">
          <input type="radio" name="correct" value="1" required style="display:none">
          <div style="border:2px solid var(--border);border-radius:10px;padding:12px;text-align:center;cursor:pointer;transition:.2s;background:#fff">
            <i class="fa-solid fa-thumbs-up" style="font-size:22px;color:var(--success);margin-bottom:6px;display:block"></i>
            <strong style="font-size:13px">Diagnostic correct</strong>
          </div>
        </label>
        <label class="fb-choice" style="flex:1;min-width:140px">
          <input type="radio" name="correct" value="0" required style="display:none">
          <div style="border:2px solid var(--border);border-radius:10px;padding:12px;text-align:center;cursor:pointer;transition:.2s;background:#fff">
            <i class="fa-solid fa-thumbs-down" style="font-size:22px;color:var(--danger);margin-bottom:6px;display:block"></i>
            <strong style="font-size:13px">Diagnostic incorrect</strong>
          </div>
        </label>
      </div>

      <div style="margin-bottom:14px">
        <label style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:600;display:block;margin-bottom:6px">Note (optionnel)</label>
        <div class="star-rating" style="display:flex;gap:6px;font-size:24px">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <label style="cursor:pointer">
              <input type="radio" name="note" value="<?= $i ?>" style="display:none">
              <i class="fa-regular fa-star" data-star="<?= $i ?>" style="color:var(--gold);transition:.2s"></i>
            </label>
          <?php endfor; ?>
        </div>
      </div>

      <div style="margin-bottom:14px">
        <label style="font-size:12px;color:var(--muted);text-transform:uppercase;font-weight:600;display:block;margin-bottom:6px">Commentaire (optionnel)</label>
        <textarea name="commentaire" rows="3" placeholder="Précisez votre retour..." style="width:100%;padding:12px;border:1px solid var(--border);border-radius:10px;font-family:inherit;font-size:13px;resize:vertical"></textarea>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-paper-plane"></i> Envoyer mon avis
      </button>
    </form>

    <style>
      .fb-choice input:checked + div{border-color:var(--leaf);background:var(--leaf-pale);transform:translateY(-2px)}
      .star-rating i.active{color:var(--gold)}
      .star-rating label:hover ~ label i{color:var(--border)}
    </style>
    <script>
      // Stars hover effect
      document.querySelectorAll('.star-rating label').forEach((lab, i, arr) => {
        lab.addEventListener('click', () => {
          arr.forEach((l, j) => {
            const ic = l.querySelector('i');
            ic.classList.toggle('fa-solid', j <= i);
            ic.classList.toggle('fa-regular', j > i);
          });
        });
      });
    </script>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php
  $diagUrl = url('/diagnostic/' . $diag['id']);
  $waText = rawurlencode(
    "PlantDoc — Diagnostic #" . $diag['id'] . " : " .
    ($diag['plante_saine'] ? 'Plante saine' : ($diag['maladie_nom'] ?? $diag['maladie_detectee'] ?? '')) .
    " (" . (int) $diag['confiance'] . "% de confiance). Détails : " . $diagUrl
  );
?>
<div style="display:flex;gap:12px;margin-top:24px;flex-wrap:wrap">
  <a href="<?= url('/diagnostic/new') ?>" class="btn btn-primary"><i class="fa-solid fa-camera"></i> Nouveau diagnostic</a>
  <a href="<?= url('/diagnostic/' . $diag['id'] . '/pdf') ?>" class="btn btn-secondary"><i class="fa-solid fa-file-pdf"></i> Télécharger le PDF</a>
  <a href="https://wa.me/?text=<?= $waText ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="background:#25D366;color:#fff;border-color:#25D366">
    <i class="fa-brands fa-whatsapp"></i> Partager sur WhatsApp
  </a>
  <a href="<?= url('/history') ?>" class="btn btn-secondary"><i class="fa-solid fa-clock-rotate-left"></i> Historique</a>
  <button onclick="navigator.share && navigator.share({title:'Mon diagnostic PlantDoc',url:location.href})" class="btn btn-secondary">
    <i class="fa-solid fa-share-nodes"></i> Partager
  </button>
</div>

<script>
// --- Lecture vocale du diagnostic (Web Speech API, gratuit, hors-ligne) ---
(function () {
  const btn = document.getElementById('ttsBtn');
  if (!btn) return;
  const synth = window.speechSynthesis;
  if (!synth) { btn.style.display = 'none'; return; }

  const text = <?= json_encode($ttsText) ?>;
  const lang = '<?= current_lang() ?>' === 'en' ? 'en-US' : 'fr-FR';
  let speaking = false;

  function setLabel(reading) {
    btn.classList.toggle('speaking', reading);
    btn.querySelector('i').className = reading ? 'fa-solid fa-stop' : 'fa-solid fa-volume-high';
    btn.querySelector('span').textContent = reading ? btn.dataset.stop : btn.dataset.read;
  }
  function stop() { synth.cancel(); speaking = false; setLabel(false); }

  btn.addEventListener('click', function () {
    if (speaking) { stop(); return; }
    const u = new SpeechSynthesisUtterance(text);
    u.lang = lang; u.rate = 0.95; u.pitch = 1;
    u.onend = () => { speaking = false; setLabel(false); };
    u.onerror = () => { speaking = false; setLabel(false); };
    synth.cancel();
    synth.speak(u);
    speaking = true; setLabel(true);
  });
  window.addEventListener('beforeunload', stop);
})();
</script>
