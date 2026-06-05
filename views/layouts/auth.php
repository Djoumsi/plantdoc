<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= h($title) ?> — PlantDoc</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="stylesheet" href="<?= asset('css/redesign.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
</head>
<body>

<div class="auth-split">

  <!-- Côté image (gauche) -->
  <aside class="visual <?= (str_contains($_SERVER['REQUEST_URI'], 'register')) ? 'register-img' : '' ?>">
    <a href="<?= url('/') ?>" class="brand-light">
      <i class="fa-solid fa-seedling"></i> Plant<span>Doc</span>
    </a>

    <div class="quote-block">
      <?php if (str_contains($_SERVER['REQUEST_URI'], 'register')): ?>
        <h2>Rejoignez la communauté des agriculteurs connectés du Cameroun</h2>
        <p>Plus de 1 200 producteurs utilisent déjà PlantDoc pour protéger leurs récoltes du cacao, du café, de la tomate et du manioc.</p>

        <div class="feature-list">
          <div class="fl"><i class="fa-solid fa-bolt"></i> Diagnostic en moins de 5 secondes</div>
          <div class="fl"><i class="fa-solid fa-flask-vial"></i> Traitements biologiques recommandés</div>
          <div class="fl"><i class="fa-solid fa-wifi"></i> Fonctionne même sans connexion</div>
          <div class="fl"><i class="fa-solid fa-shield-halved"></i> Vos données restent privées</div>
        </div>
      <?php else: ?>
        <h2>Sauvez vos récoltes grâce à l'intelligence artificielle</h2>
        <p>Identifiez les maladies de vos plantes en quelques secondes, recevez des traitements adaptés à votre région et préservez la sécurité alimentaire de votre famille.</p>

        <div class="feature-list">
          <div class="fl"><i class="fa-solid fa-camera-retro"></i> Photographiez la plante malade</div>
          <div class="fl"><i class="fa-solid fa-brain"></i> Notre IA analyse en temps réel</div>
          <div class="fl"><i class="fa-solid fa-prescription-bottle-medical"></i> Recevez un traitement personnalisé</div>
        </div>
      <?php endif; ?>
    </div>

    <div class="stats-line">
      <div><strong>1 247</strong> Agriculteurs</div>
      <div><strong>8 542</strong> Diagnostics</div>
      <div><strong>87%</strong> Précision IA</div>
    </div>
  </aside>

  <!-- Côté formulaire (droite) -->
  <main class="form-side">
    <div class="form-card">
      <div class="top-link">
        <a href="<?= url('/') ?>"><i class="fa-solid fa-arrow-left"></i> Retour</a>
        <?php if (str_contains($_SERVER['REQUEST_URI'], 'register')): ?>
          <span>Déjà un compte ? <a href="<?= url('/login') ?>">Se connecter</a></span>
        <?php else: ?>
          <span>Pas de compte ? <a href="<?= url('/register') ?>">S'inscrire</a></span>
        <?php endif; ?>
      </div>

      <div class="brand-mobile">
        <a href="<?= url('/') ?>" class="brand"><i class="fa-solid fa-seedling" style="color:var(--leaf)"></i> Plant<span style="color:var(--gold)">Doc</span></a>
      </div>

      <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($msg) ?></div>
      <?php endif; ?>
      <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= h($msg) ?></div>
      <?php endif; ?>

      <?= $content ?>
    </div>
  </main>

</div>

</body>
</html>
