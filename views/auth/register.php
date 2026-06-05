<h1>Créer mon compte</h1>
<p class="subt">Inscription gratuite — moins de 2 minutes pour commencer à protéger vos récoltes.</p>

<form method="post" action="<?= url('/register') ?>">
  <?= Csrf::field() ?>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
    <div class="form-group">
      <label>Nom</label>
      <div class="input-wrap">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="nom" class="form-control" placeholder="Mbarga" required>
      </div>
    </div>
    <div class="form-group">
      <label>Prénom</label>
      <div class="input-wrap">
        <i class="fa-solid fa-id-badge"></i>
        <input type="text" name="prenom" class="form-control" placeholder="Jean">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label>Email</label>
    <div class="input-wrap">
      <i class="fa-solid fa-envelope"></i>
      <input type="email" name="email" class="form-control" placeholder="vous@exemple.com" required>
    </div>
  </div>

  <div class="form-group">
    <label>Téléphone (Mobile Money)</label>
    <div class="input-wrap">
      <i class="fa-solid fa-mobile-screen"></i>
      <input type="tel" name="telephone" class="form-control" placeholder="+237 6XX XX XX XX">
    </div>
  </div>

  <div class="form-group">
    <label>Région</label>
    <div class="input-wrap">
      <i class="fa-solid fa-map-location-dot"></i>
      <select name="region_id" class="form-control" style="padding-left:46px;appearance:none">
        <option value="">— Choisir votre région —</option>
        <?php foreach ($regions as $r): ?>
          <option value="<?= $r['id'] ?>"><?= h($r['nom']) ?> · <?= h($r['chef_lieu']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label>Mot de passe</label>
    <div class="input-wrap pwd-wrap">
      <i class="fa-solid fa-lock"></i>
      <input type="password" name="password" class="form-control" minlength="8" placeholder="Au moins 8 caractères" required>
      <button type="button" class="pwd-toggle" aria-label="Afficher / masquer le mot de passe" tabindex="-1">
        <i class="fa-regular fa-eye"></i>
      </button>
    </div>
  </div>

  <button type="submit" class="btn-primary">
    <i class="fa-solid fa-user-plus"></i> Créer mon compte gratuitement
  </button>

  <p style="text-align:center;margin-top:18px;font-size:11px;color:var(--muted);line-height:1.6">
    En vous inscrivant, vous acceptez nos <a href="#" style="color:var(--leaf);font-weight:600">Conditions</a> et notre <a href="#" style="color:var(--leaf);font-weight:600">Politique de confidentialité</a>.
  </p>
</form>

<style>
  .pwd-wrap{position:relative}
  .pwd-toggle{position:absolute;top:50%;right:12px;transform:translateY(-50%);
    background:none;border:none;color:var(--muted,#6b7280);cursor:pointer;
    width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    font-size:15px;transition:.15s;z-index:2}
  .pwd-toggle i{position:static !important;left:auto !important;top:auto !important;transform:none !important;
    color:inherit !important;font-size:inherit !important}
  .pwd-toggle:hover{color:var(--leaf-dark,#1b4332);background:rgba(82,183,136,.12)}
  .pwd-wrap input.form-control{padding-right:48px}
</style>
<script>
  document.querySelectorAll('.pwd-toggle').forEach(btn => {
    btn.addEventListener('click', function () {
      const input = this.parentElement.querySelector('input');
      const icon = this.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-regular fa-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'fa-regular fa-eye';
      }
    });
  });
</script>
