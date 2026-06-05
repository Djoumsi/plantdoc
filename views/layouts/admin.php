<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= h($title) ?> — PlantDoc Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
</head>
<body>
<div class="admin-shell">

<aside class="admin-side">
  <a href="<?= url('/admin') ?>" class="brand"><i class="fa-solid fa-seedling"></i> Plant<span style="color:var(--gold)">Doc</span></a>
  <nav class="admin-menu">
    <a href="<?= url('/admin') ?>" class="<?= str_ends_with($_SERVER['REQUEST_URI'],'/admin') ? 'active' : '' ?>">
      <i class="fa-solid fa-gauge-high"></i> Tableau de bord
    </a>
    <a href="<?= url('/admin/diagnostics') ?>" class="<?= strpos($_SERVER['REQUEST_URI'],'/diagnostics') !== false ? 'active' : '' ?>">
      <i class="fa-solid fa-microscope"></i> Diagnostics
    </a>
    <a href="<?= url('/admin/maladies') ?>" class="<?= strpos($_SERVER['REQUEST_URI'],'/maladies') !== false ? 'active' : '' ?>">
      <i class="fa-solid fa-virus"></i> Catalogue maladies
    </a>
    <?php if (is_admin()): ?>
    <a href="<?= url('/admin/users') ?>" class="<?= strpos($_SERVER['REQUEST_URI'],'/users') !== false ? 'active' : '' ?>">
      <i class="fa-solid fa-users"></i> Utilisateurs
    </a>
    <?php endif; ?>
    <a href="<?= url('/dashboard') ?>"><i class="fa-solid fa-house"></i> Espace utilisateur</a>
    <a href="<?= url('/logout') ?>" style="margin-top:20px;color:#fca5a5"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
  </nav>
</aside>

<main class="admin-main">
  <div class="admin-topbar">
    <h1><?= h($title) ?></h1>
    <div class="user-menu">
      <div class="info" style="text-align:right">
        <strong><?= h(user()['nom']) ?></strong>
        <small><?= h(ucfirst(user_role())) ?></small>
      </div>
      <div class="avatar"><?= h(strtoupper(substr(user()['nom'], 0, 1))) ?></div>
    </div>
  </div>

  <?php if ($msg = flash('error')): ?>
    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($msg) ?></div>
  <?php endif; ?>
  <?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= h($msg) ?></div>
  <?php endif; ?>

  <?= $content ?>
</main>

</div>
</body>
</html>
