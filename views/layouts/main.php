<!DOCTYPE html>
<html lang="<?= h(current_lang()) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="theme-color" content="#2d6a4f">
<link rel="manifest" href="<?= asset('manifest.json') ?>">
<link rel="icon" href="<?= asset('images/icon-192.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('images/icon-192.png') ?>">
<title><?= h($title ?? 'PlantDoc') ?> — PlantDoc</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="stylesheet" href="<?= asset('css/redesign.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
<style>
/* Menu mobile (hamburger + bottom nav) */
.mobile-toggle{display:none;background:none;border:none;font-size:22px;color:var(--leaf-dark);cursor:pointer;padding:6px}
.mobile-menu{display:none;position:fixed;top:0;right:-100%;width:80%;max-width:320px;height:100vh;background:#fff;
  box-shadow:-10px 0 40px rgba(0,0,0,.15);z-index:999;transition:right .3s ease;padding:24px;overflow-y:auto}
.mobile-menu.open{right:0}
.mobile-menu .close{position:absolute;top:16px;right:16px;background:none;border:none;font-size:24px;color:var(--muted);cursor:pointer}
.mobile-menu .user-card{display:flex;align-items:center;gap:14px;padding:18px;background:var(--leaf-pale);border-radius:14px;margin-bottom:24px;margin-top:24px}
.mobile-menu .user-card .avatar{width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,var(--leaf-light),var(--leaf-dark));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px}
.mobile-menu .user-card strong{display:block;color:var(--leaf-dark)}
.mobile-menu .user-card small{color:var(--muted);font-size:12px}
.mobile-menu .menu-list{display:flex;flex-direction:column;gap:6px}
.mobile-menu .menu-list a{display:flex;align-items:center;gap:14px;padding:14px;border-radius:10px;color:var(--text);text-decoration:none;font-weight:500;font-size:14px;transition:.2s}
.mobile-menu .menu-list a:hover,.mobile-menu .menu-list a.active{background:var(--leaf-pale);color:var(--leaf-dark)}
.mobile-menu .menu-list a i{width:24px;text-align:center;font-size:16px;color:var(--leaf)}
.mobile-menu .menu-list a.logout{color:var(--danger);margin-top:14px;border-top:1px solid var(--border);padding-top:18px;border-radius:0}
.mobile-menu .menu-list a.logout i{color:var(--danger)}
.mobile-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:998}
.mobile-overlay.open{display:block}

/* Bottom nav (app native style) */
.bottom-nav{display:none;position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid var(--border);
  z-index:50;padding:8px 0 calc(8px + env(safe-area-inset-bottom));box-shadow:0 -4px 20px rgba(0,0,0,.06)}
.bottom-nav-inner{display:flex;justify-content:space-around;max-width:600px;margin:0 auto}
.bottom-nav a{display:flex;flex-direction:column;align-items:center;gap:4px;padding:6px 12px;color:var(--muted);text-decoration:none;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.bottom-nav a.active{color:var(--leaf)}
.bottom-nav a i{font-size:20px}
.bottom-nav a.center{background:var(--leaf);color:#fff;width:54px;height:54px;border-radius:50%;justify-content:center;margin-top:-22px;box-shadow:0 6px 20px rgba(45,106,79,.4)}
.bottom-nav a.center i{font-size:22px}
.bottom-nav a.center span{display:none}

@media(max-width:900px){
  .mobile-toggle{display:block}
  .mobile-menu{display:block}
  .app-header .nav-links{display:none}
  .bottom-nav{display:block}
  main{padding-bottom:90px !important}
}

/* Sélecteur de langue + boutons accessibilité */
.a11y-btn{background:var(--leaf-pale);border:1px solid var(--border);color:var(--leaf-dark);border-radius:8px;
  padding:7px 10px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s}
.a11y-btn:hover{background:var(--leaf);color:#fff}
.a11y-btn.active{background:var(--leaf);color:#fff}
.lang-switch{position:relative}
.lang-switch .lang-menu{position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid var(--border);
  border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,.12);min-width:150px;padding:6px;display:none;z-index:200}
.lang-switch:hover .lang-menu{display:block}
.lang-switch .lang-menu a{display:block;padding:9px 12px;border-radius:8px;color:var(--text);text-decoration:none;font-size:13px;font-weight:500}
.lang-switch .lang-menu a:hover,.lang-switch .lang-menu a.active{background:var(--leaf-pale);color:var(--leaf-dark)}

/* Mode icônes seules (accessibilité illettrisme) */
body.icons-only .nav-txt,
body.icons-only .bottom-nav a span{display:none !important}
body.icons-only .a11y-btn .a11y-lbl{display:none}

/* Lecture vocale — bouton flottant */
.tts-btn{display:inline-flex;align-items:center;gap:8px;background:var(--info,#2196f3);color:#fff;border:none;
  border-radius:10px;padding:10px 16px;font-size:14px;font-weight:600;cursor:pointer;transition:.2s}
.tts-btn:hover{filter:brightness(1.05)}
.tts-btn.speaking{background:var(--danger,#e53935)}

/* Bouton retour admin — visible uniquement pour admin/expert sur l'espace utilisateur */
.back-admin-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#2d6a4f,#52b788);
  color:#fff !important;border:none;border-radius:10px;padding:9px 16px;font-size:13px;font-weight:700;
  text-decoration:none;box-shadow:0 4px 12px rgba(45,106,79,.25);transition:.2s;text-transform:uppercase;letter-spacing:.3px}
.back-admin-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(45,106,79,.35)}
.back-admin-btn i{font-size:14px}

</style>
</head>
<body>
<div class="app-shell">

<header class="app-header">
  <div class="container">
    <a href="<?= url('/dashboard') ?>" class="brand">
      <i class="fa-solid fa-seedling"></i> Plant<span>Doc</span>
    </a>
    <nav class="nav-links">
      <a href="<?= url('/dashboard') ?>"><i class="fa-solid fa-house" style="color:var(--leaf)"></i> <span class="nav-txt"><?= t('nav.home') ?></span></a>
      <a href="<?= url('/diagnostic/new') ?>"><i class="fa-solid fa-camera" style="color:var(--gold)"></i> <span class="nav-txt"><?= t('nav.diagnose') ?></span></a>
      <a href="<?= url('/history') ?>"><i class="fa-solid fa-clock-rotate-left" style="color:var(--info)"></i> <span class="nav-txt"><?= t('nav.history') ?></span></a>

      <?php if (is_admin() || is_expert()): ?>
        <a href="<?= url('/admin') ?>" class="back-admin-btn" title="Retour à l'espace d'administration">
          <i class="fa-solid fa-shield-halved"></i> <span class="nav-txt">Espace Admin</span>
        </a>
      <?php endif; ?>

      <!-- Accessibilité : mode icônes seules -->
      <button type="button" id="iconsOnlyToggle" class="a11y-btn" title="<?= t('common.icons_only') ?>" aria-label="<?= t('common.icons_only') ?>">
        <i class="fa-solid fa-icons"></i>
      </button>

      <!-- Sélecteur de langue -->
      <div class="lang-switch">
        <button type="button" class="a11y-btn" title="<?= t('common.language') ?>"><?= available_langs()[current_lang()]['flag'] ?> <?= strtoupper(current_lang()) ?></button>
        <div class="lang-menu">
          <?php foreach (available_langs() as $code => $info): ?>
            <a href="<?= url('/setlang/' . $code) ?>" class="<?= $code === current_lang() ? 'active' : '' ?>"><?= $info['flag'] ?> <?= h($info['label']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="user-menu">
        <div class="info" style="text-align:right">
          <strong><?= h(user()['prenom'] ?? user()['nom'] ?? '') ?></strong>
          <small><?= h(ucfirst(user_role() ?? '')) ?></small>
        </div>
        <div class="avatar"><?= h(strtoupper(substr(user()['nom'] ?? '?', 0, 1))) ?></div>
        <a href="<?= url('/logout') ?>" title="Déconnexion" style="color:var(--danger)"><i class="fa-solid fa-right-from-bracket"></i></a>
      </div>
    </nav>
    <button class="mobile-toggle" onclick="document.querySelector('.mobile-menu').classList.add('open');document.querySelector('.mobile-overlay').classList.add('open')" aria-label="Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>
</header>

<!-- Menu mobile drawer -->
<div class="mobile-overlay" onclick="document.querySelector('.mobile-menu').classList.remove('open');this.classList.remove('open')"></div>
<aside class="mobile-menu">
  <button class="close" onclick="document.querySelector('.mobile-menu').classList.remove('open');document.querySelector('.mobile-overlay').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
  <div class="user-card">
    <div class="avatar"><?= h(strtoupper(substr(user()['nom'] ?? '?', 0, 1))) ?></div>
    <div>
      <strong><?= h(trim((user()['prenom'] ?? '') . ' ' . (user()['nom'] ?? ''))) ?></strong>
      <small><?= h(user()['email'] ?? '') ?></small>
    </div>
  </div>
  <nav class="menu-list">
    <a href="<?= url('/dashboard') ?>"><i class="fa-solid fa-house"></i> <?= t('nav.dashboard') ?></a>
    <a href="<?= url('/diagnostic/new') ?>"><i class="fa-solid fa-camera"></i> <?= t('nav.new_diag') ?></a>
    <a href="<?= url('/history') ?>"><i class="fa-solid fa-clock-rotate-left"></i> <?= t('nav.my_history') ?></a>
    <a href="<?= url('/map') ?>"><i class="fa-solid fa-map-location-dot"></i> <?= t('nav.map') ?></a>
    <a href="<?= url('/profile') ?>"><i class="fa-solid fa-user"></i> <?= t('nav.profile') ?></a>
    <?php if (is_admin() || is_expert()): ?>
      <a href="<?= url('/admin') ?>"><i class="fa-solid fa-gauge-high"></i> <?= t('nav.admin') ?></a>
    <?php endif; ?>
    <div style="display:flex;gap:8px;padding:14px 14px 0">
      <?php foreach (available_langs() as $code => $info): ?>
        <a href="<?= url('/setlang/' . $code) ?>" style="flex:1;justify-content:center;border:1px solid var(--border);<?= $code === current_lang() ? 'background:var(--leaf-pale);' : '' ?>"><?= $info['flag'] ?> <?= strtoupper($code) ?></a>
      <?php endforeach; ?>
    </div>
    <a href="<?= url('/logout') ?>" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <?= t('nav.logout') ?></a>
  </nav>
</aside>

<main class="container" style="padding-top:20px;padding-bottom:60px">
  <?php if ($msg = flash('error')): ?>
    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= h($msg) ?></div>
  <?php endif; ?>
  <?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= h($msg) ?></div>
  <?php endif; ?>

  <?= $content ?>
</main>

<!-- Bottom nav mobile -->
<nav class="bottom-nav">
  <div class="bottom-nav-inner">
    <a href="<?= url('/dashboard') ?>" class="<?= str_ends_with($_SERVER['REQUEST_URI'],'/dashboard') ? 'active' : '' ?>">
      <i class="fa-solid fa-house"></i><span><?= t('nav.home') ?></span>
    </a>
    <a href="<?= url('/history') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'],'/history') ? 'active' : '' ?>">
      <i class="fa-solid fa-clock-rotate-left"></i><span><?= t('nav.history') ?></span>
    </a>
    <a href="<?= url('/diagnostic/new') ?>" class="center">
      <i class="fa-solid fa-camera"></i><span><?= t('nav.scan') ?></span>
    </a>
    <a href="<?= url('/map') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'],'/map') ? 'active' : '' ?>"><i class="fa-solid fa-map-location-dot"></i><span><?= t('nav.map_short') ?></span></a>
    <a href="<?= url('/profile') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'],'/profile') ? 'active' : '' ?>"><i class="fa-solid fa-user"></i><span><?= t('nav.profile_short') ?></span></a>
  </div>
</nav>

<footer>
  <div class="container">
    <p><?= t('footer.made') ?> <i class="fa-solid fa-heart"></i> <?= t('footer.in_cameroon') ?> · <strong>PlantDoc</strong> &copy; <?= date('Y') ?></p>
  </div>
</footer>

</div>
<script>
// --- Mode icônes seules (accessibilité illettrisme), mémorisé ---
(function () {
  const KEY = 'plantdoc_icons_only';
  const apply = on => {
    document.body.classList.toggle('icons-only', on);
    const btn = document.getElementById('iconsOnlyToggle');
    if (btn) btn.classList.toggle('active', on);
  };
  apply(localStorage.getItem(KEY) === '1');
  const btn = document.getElementById('iconsOnlyToggle');
  if (btn) btn.addEventListener('click', () => {
    const on = !(localStorage.getItem(KEY) === '1');
    localStorage.setItem(KEY, on ? '1' : '0');
    apply(on);
  });
})();
</script>
<script>window.PLANTDOC_BASE = '<?= rtrim(parse_url(config('url'), PHP_URL_PATH) ?: '/plantdoc', '/') ?>' || '/plantdoc';</script>
<script src="<?= asset('js/offline-queue.js') ?>"></script>
<script>
// Service Worker pour PWA
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('<?= asset('sw.js') ?>', { scope: '/plantdoc/' })
      .then(reg => console.log('[PWA] Service Worker actif', reg.scope))
      .catch(err => console.warn('[PWA] SW erreur', err));
  });
}
</script>
</body>
</html>
