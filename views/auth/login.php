<h1>Bon retour !</h1>
<p class="subt">Connectez-vous pour diagnostiquer vos cultures et préserver votre récolte.</p>

<form method="post" action="<?= url('/login') ?>">
  <?= Csrf::field() ?>

  <div class="form-group">
    <label>Email ou téléphone</label>
    <div class="input-wrap">
      <i class="fa-solid fa-envelope"></i>
      <input type="email" name="email" class="form-control" placeholder="vous@exemple.com" required autofocus>
    </div>
  </div>

  <div class="form-group">
    <label style="display:flex;justify-content:space-between;align-items:center">
      <span>Mot de passe</span>
      <a href="#" style="font-size:11px;color:var(--leaf);font-weight:600;text-decoration:none">Mot de passe oublié ?</a>
    </label>
    <div class="input-wrap pwd-wrap">
      <i class="fa-solid fa-lock"></i>
      <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      <button type="button" class="pwd-toggle" aria-label="Afficher / masquer le mot de passe" tabindex="-1">
        <i class="fa-regular fa-eye"></i>
      </button>
    </div>
  </div>

  <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);margin-bottom:20px;cursor:pointer">
    <input type="checkbox" name="remember" style="accent-color:var(--leaf)">
    Se souvenir de moi sur cet appareil
  </label>

  <button type="submit" class="btn-primary">
    <i class="fa-solid fa-right-to-bracket"></i> Se connecter
  </button>
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
