<div class="page-header">
  <h1><i class="fa-solid fa-user-gear"></i> Mon profil</h1>
  <p class="subtitle">Gérez vos informations personnelles et préférences.</p>
</div>

<!-- HERO PROFIL -->
<div class="card" style="background:linear-gradient(135deg,var(--leaf),var(--leaf-dark));color:#fff;margin-bottom:24px;padding:30px;border:none">
  <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap">
    <div style="width:90px;height:90px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:800;color:#fff;flex-shrink:0">
      <?= h(strtoupper(substr($user['nom'], 0, 1))) ?>
    </div>
    <div style="flex:1;min-width:200px">
      <h2 style="font-size:24px;margin-bottom:4px;color:#fff"><?= h(trim(($user['prenom'] ?? '') . ' ' . $user['nom'])) ?></h2>
      <p style="opacity:.9;margin-bottom:8px"><i class="fa-solid fa-envelope"></i> <?= h($user['email']) ?></p>
      <div style="display:flex;gap:14px;flex-wrap:wrap;font-size:12px;opacity:.85">
        <span><i class="fa-regular fa-calendar"></i> Inscrit le <?= format_date($user['created_at'], 'd/m/Y') ?></span>
        <?php if ($user['derniere_connexion']): ?>
          <span><i class="fa-regular fa-clock"></i> Dernière connexion : <?= time_ago($user['derniere_connexion']) ?></span>
        <?php endif; ?>
      </div>
    </div>
    <div style="text-align:center;background:rgba(255,255,255,.15);padding:18px 24px;border-radius:14px">
      <div style="font-size:32px;font-weight:800"><?= (int) ($stats['total'] ?? 0) ?></div>
      <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;opacity:.85">Diagnostics</div>
    </div>
  </div>
</div>

<div class="dash-grid">

  <!-- FORMULAIRE INFOS -->
  <div class="card">
    <div class="card-header">
      <h2><i class="fa-solid fa-id-card"></i> Informations personnelles</h2>
    </div>

    <form method="post" action="<?= url('/profile/update') ?>">
      <?= Csrf::field() ?>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="form-group">
          <label>Nom *</label>
          <div class="input-wrap">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="nom" class="form-control" value="<?= h($user['nom']) ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label>Prénom</label>
          <div class="input-wrap">
            <i class="fa-solid fa-id-badge"></i>
            <input type="text" name="prenom" class="form-control" value="<?= h($user['prenom'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Email</label>
        <div class="input-wrap">
          <i class="fa-solid fa-envelope"></i>
          <input type="email" class="form-control" value="<?= h($user['email']) ?>" disabled style="background:#f3f4f6">
        </div>
        <small style="color:var(--muted);font-size:11px">Pour changer l'email, contactez le support.</small>
      </div>

      <div class="form-group">
        <label>Téléphone</label>
        <div class="input-wrap">
          <i class="fa-solid fa-mobile-screen"></i>
          <input type="tel" name="telephone" class="form-control" value="<?= h($user['telephone'] ?? '') ?>" placeholder="+237 6XX XX XX XX">
        </div>
      </div>

      <div class="form-group">
        <label>Région</label>
        <div class="input-wrap">
          <i class="fa-solid fa-map-location-dot"></i>
          <select name="region_id" class="form-control" style="padding-left:42px;appearance:none">
            <option value="">— Aucune —</option>
            <?php foreach ($regions as $r): ?>
              <option value="<?= $r['id'] ?>" <?= $user['region_id'] == $r['id'] ? 'selected' : '' ?>>
                <?= h($r['nom']) ?> · <?= h($r['chef_lieu']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Langue</label>
        <div class="input-wrap">
          <i class="fa-solid fa-globe"></i>
          <select name="langue" class="form-control" style="padding-left:42px;appearance:none">
            <option value="fr" <?= $user['langue'] === 'fr' ? 'selected' : '' ?>>🇫🇷 Français</option>
            <option value="en" <?= $user['langue'] === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
      </button>
    </form>
  </div>

  <!-- SIDEBAR -->
  <div>

    <!-- Mot de passe -->
    <div class="card">
      <div class="card-header">
        <h2 style="font-size:15px"><i class="fa-solid fa-lock"></i> Sécurité</h2>
      </div>

      <form method="post" action="<?= url('/profile/password') ?>">
        <?= Csrf::field() ?>

        <div class="form-group">
          <label style="font-size:11px">Mot de passe actuel</label>
          <div class="input-wrap pwd-wrap">
            <i class="fa-solid fa-key"></i>
            <input type="password" name="current_password" class="form-control" required>
            <button type="button" class="pwd-toggle" aria-label="Afficher / masquer" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>

        <div class="form-group">
          <label style="font-size:11px">Nouveau mot de passe</label>
          <div class="input-wrap pwd-wrap">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="new_password" class="form-control" minlength="8" required>
            <button type="button" class="pwd-toggle" aria-label="Afficher / masquer" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>

        <div class="form-group">
          <label style="font-size:11px">Confirmer</label>
          <div class="input-wrap pwd-wrap">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="confirm_password" class="form-control" minlength="8" required>
            <button type="button" class="pwd-toggle" aria-label="Afficher / masquer" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%">
          <i class="fa-solid fa-shield-halved"></i> Changer le mot de passe
        </button>
      </form>

      <style>
        .pwd-wrap{position:relative}
        .pwd-toggle{position:absolute;top:50%;right:12px;transform:translateY(-50%);
          background:none;border:none;color:var(--muted,#6b7280);cursor:pointer;
          width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;
          font-size:14px;transition:.15s;z-index:2}
        .pwd-toggle i{position:static !important;left:auto !important;top:auto !important;transform:none !important;
          color:inherit !important;font-size:inherit !important}
        .pwd-toggle:hover{color:var(--leaf-dark,#1b4332);background:rgba(82,183,136,.12)}
        .pwd-wrap input.form-control{padding-right:46px}
      </style>
      <script>
        document.querySelectorAll('.pwd-toggle').forEach(btn => {
          if (btn.dataset.bound) return;
          btn.dataset.bound = '1';
          btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') { input.type = 'text'; icon.className = 'fa-regular fa-eye-slash'; }
            else { input.type = 'password'; icon.className = 'fa-regular fa-eye'; }
          });
        });
      </script>
    </div>

    <!-- Stats rapides -->
    <div class="card">
      <div class="card-header">
        <h2 style="font-size:15px"><i class="fa-solid fa-chart-line"></i> Mes statistiques</h2>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
          <span style="color:var(--muted);font-size:13px"><i class="fa-solid fa-microscope" style="color:var(--leaf);margin-right:6px"></i> Total diagnostics</span>
          <strong><?= (int) ($stats['total'] ?? 0) ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
          <span style="color:var(--muted);font-size:13px"><i class="fa-solid fa-triangle-exclamation" style="color:var(--danger);margin-right:6px"></i> Cas sévères</span>
          <strong><?= (int) ($stats['severes'] ?? 0) ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
          <span style="color:var(--muted);font-size:13px"><i class="fa-solid fa-leaf" style="color:var(--success);margin-right:6px"></i> Plantes saines</span>
          <strong><?= (int) ($stats['saines'] ?? 0) ?></strong>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0">
          <span style="color:var(--muted);font-size:13px"><i class="fa-solid fa-virus" style="color:var(--gold);margin-right:6px"></i> Maladies distinctes</span>
          <strong><?= (int) ($stats['maladies_distinctes'] ?? 0) ?></strong>
        </div>
      </div>
    </div>

    <!-- Compte -->
    <div class="card" style="border-left:4px solid var(--danger);background:#fef9f9">
      <h2 style="font-size:15px;color:var(--danger);margin-bottom:10px"><i class="fa-solid fa-triangle-exclamation"></i> Zone dangereuse</h2>
      <p style="font-size:12px;color:var(--muted);margin-bottom:14px">La suppression de votre compte est définitive et irréversible.</p>
      <button type="button" class="btn" style="background:#fff;color:var(--danger);border:1px solid var(--danger);width:100%" onclick="alert('Fonctionnalité bientôt disponible. Contactez le support pour supprimer votre compte.')">
        <i class="fa-solid fa-user-slash"></i> Supprimer mon compte
      </button>
    </div>

  </div>
</div>
