<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="theme-color" content="#2d6a4f">
<link rel="manifest" href="<?= asset('manifest.json') ?>">
<link rel="icon" href="<?= asset('images/icon-192.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('images/icon-192.png') ?>">
<title><?= h($title) ?> — PlantDoc</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<link rel="stylesheet" href="<?= asset('css/redesign.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
<link rel="stylesheet" href="<?= asset('css/pages.css') ?>">
<style>
.pub-toggle{display:none;background:none;border:none;font-size:22px;color:var(--leaf-dark);cursor:pointer;padding:6px}
.pub-drawer{display:none;position:fixed;top:0;right:-100%;width:75%;max-width:300px;height:100vh;background:#fff;
  box-shadow:-10px 0 40px rgba(0,0,0,.15);z-index:999;transition:right .3s;padding:30px 24px}
.pub-drawer.open{right:0}
.pub-drawer .close{position:absolute;top:14px;right:14px;background:none;border:none;font-size:22px;color:var(--muted);cursor:pointer}
.pub-drawer .brand{margin-bottom:30px}
.pub-drawer a{display:block;padding:14px 0;color:var(--text);text-decoration:none;font-weight:500;font-size:15px;border-bottom:1px solid var(--border)}
.pub-drawer a.btn-primary{background:var(--leaf);color:#fff;text-align:center;border-radius:10px;border:none;margin-top:18px;padding:14px}
.pub-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:998}
.pub-overlay.open{display:block}
@media(max-width:900px){
  .pub-toggle{display:block}
  .pub-drawer{display:block}
  .public-nav .nav-links{display:none}
}
</style>
</head>
<body>

<nav class="public-nav">
  <div class="container">
    <a href="<?= url('/') ?>" class="brand"><i class="fa-solid fa-seedling"></i> Plant<span>Doc</span></a>
    <div class="nav-links">
      <a href="<?= url('/maladies') ?>">Maladies</a>
      <a href="<?= url('/about') ?>">À propos</a>
      <a href="<?= url('/contact') ?>">Contact</a>
      <a href="<?= url('/login') ?>">Connexion</a>
      <a href="<?= url('/register') ?>" class="btn-primary">S'inscrire</a>
    </div>
    <button class="pub-toggle" onclick="document.querySelector('.pub-drawer').classList.add('open');document.querySelector('.pub-overlay').classList.add('open')"><i class="fa-solid fa-bars"></i></button>
  </div>
</nav>

<div class="pub-overlay" onclick="document.querySelector('.pub-drawer').classList.remove('open');this.classList.remove('open')"></div>
<aside class="pub-drawer">
  <button class="close" onclick="document.querySelector('.pub-drawer').classList.remove('open');document.querySelector('.pub-overlay').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
  <a href="<?= url('/') ?>" class="brand"><i class="fa-solid fa-seedling" style="color:var(--leaf)"></i> Plant<span style="color:var(--gold)">Doc</span></a>
  <a href="<?= url('/') ?>"><i class="fa-solid fa-house" style="color:var(--leaf);width:24px"></i> Accueil</a>
  <a href="<?= url('/maladies') ?>"><i class="fa-solid fa-virus" style="color:var(--leaf);width:24px"></i> Maladies</a>
  <a href="<?= url('/about') ?>"><i class="fa-solid fa-circle-info" style="color:var(--leaf);width:24px"></i> À propos</a>
  <a href="<?= url('/contact') ?>"><i class="fa-solid fa-envelope" style="color:var(--leaf);width:24px"></i> Contact</a>
  <a href="<?= url('/login') ?>"><i class="fa-solid fa-right-to-bracket" style="color:var(--leaf);width:24px"></i> Connexion</a>
  <a href="<?= url('/register') ?>" class="btn-primary"><i class="fa-solid fa-user-plus"></i> S'inscrire gratuitement</a>
</aside>

<?= $content ?>

<footer>
  <div class="container">
    <p>Fait avec <i class="fa-solid fa-heart"></i> au Cameroun · <strong>PlantDoc</strong> &copy; <?= date('Y') ?></p>
  </div>
</footer>

</body>
</html>
