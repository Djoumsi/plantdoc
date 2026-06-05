<div class="card">
  <div class="card-header">
    <h2><i class="fa-solid fa-users"></i> Liste des utilisateurs (<?= count($users) ?>)</h2>
    <small style="color:var(--muted)">Vous ne pouvez pas modifier votre propre compte.</small>
  </div>

  <div class="table-wrap" style="border:none;overflow-x:auto">
    <table class="table">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Rôle</th>
          <th>Région</th>
          <th>Diag.</th>
          <th>Statut</th>
          <th>Inscrit</th>
          <th style="text-align:center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): $isMe = ((int) $u['id'] === (int) $_SESSION['user_id']); ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div class="icon-circle icon-leaf" style="width:32px;height:32px;font-size:13px"><?= h(strtoupper(substr($u['nom'],0,1))) ?></div>
              <strong>
                <?= h(trim(($u['prenom'] ?? '') . ' ' . $u['nom'])) ?>
                <?php if ($isMe): ?><span style="font-size:11px;color:var(--leaf-dark);font-weight:600">· vous</span><?php endif; ?>
              </strong>
            </div>
          </td>
          <td><i class="fa-solid fa-envelope" style="color:var(--muted)"></i> <?= h($u['email']) ?></td>
          <td><?= h($u['telephone'] ?? '—') ?></td>
          <td><span class="tag tag-confidence"><?= h($u['role']) ?></span></td>
          <td><?= h($u['region'] ?? '—') ?></td>
          <td><strong><?= (int) $u['nb_diag'] ?></strong></td>
          <td>
            <?php $col = $u['statut'] === 'actif' ? 'success' : 'danger'; ?>
            <span class="tag tag-<?= $col === 'success' ? 'legere' : 'severe' ?>">
              <i class="fa-solid fa-<?= $col === 'success' ? 'check' : 'ban' ?>"></i>
              <?= h($u['statut']) ?>
            </span>
          </td>
          <td><?= format_date($u['created_at'], 'd/m/Y') ?></td>
          <td style="text-align:center;white-space:nowrap">
            <?php if ($isMe): ?>
              <span style="color:var(--muted);font-size:11px;font-style:italic">—</span>
            <?php else: ?>
              <button type="button" class="btn-action" title="Changer le rôle"
                      onclick="openRole(<?= (int) $u['id'] ?>, '<?= h(trim(($u['prenom'] ?? '') . ' ' . $u['nom'])) ?>', <?= (int) $u['role_id'] ?>)">
                <i class="fa-solid fa-user-shield"></i>
              </button>

              <form method="post" action="<?= url('/admin/users/' . $u['id'] . '/status') ?>" style="display:inline">
                <?= Csrf::field() ?>
                <input type="hidden" name="statut" value="<?= $u['statut'] === 'actif' ? 'suspendu' : 'actif' ?>">
                <button type="submit" class="btn-action <?= $u['statut'] === 'actif' ? 'warn' : 'ok' ?>"
                        title="<?= $u['statut'] === 'actif' ? 'Suspendre' : 'Réactiver' ?>"
                        onclick="return confirm('<?= $u['statut'] === 'actif' ? 'Suspendre' : 'Réactiver' ?> ce compte ?')">
                  <i class="fa-solid fa-<?= $u['statut'] === 'actif' ? 'user-slash' : 'user-check' ?>"></i>
                </button>
              </form>

              <form method="post" action="<?= url('/admin/users/' . $u['id'] . '/delete') ?>" style="display:inline">
                <?= Csrf::field() ?>
                <button type="submit" class="btn-action danger" title="Supprimer"
                        onclick="return confirm('Supprimer définitivement ce compte ? Cette action est irréversible.')">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal changement de rôle -->
<div id="roleModal" class="role-modal" style="display:none">
  <div class="role-modal-overlay" onclick="closeRole()"></div>
  <div class="role-modal-box">
    <h3><i class="fa-solid fa-user-shield" style="color:var(--leaf)"></i> Modifier le rôle</h3>
    <p style="color:var(--muted);font-size:13px">Utilisateur : <strong id="roleUserName" style="color:var(--leaf-dark)"></strong></p>

    <form id="roleForm" method="post">
      <?= Csrf::field() ?>
      <label style="display:block;margin:14px 0 8px;font-size:13px;color:var(--muted);font-weight:600">Nouveau rôle</label>
      <select name="role_id" id="roleSelect" class="form-control" required>
        <option value="1">🌱 Agriculteur</option>
        <option value="2">🧑‍🔬 Expert agronome</option>
        <option value="3">🛡️ Administrateur</option>
      </select>
      <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end">
        <button type="button" onclick="closeRole()" class="btn btn-secondary">Annuler</button>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<style>
.btn-action{
  background:transparent;border:1.5px solid var(--border,#e5e7eb);color:var(--muted,#6b7280);
  width:34px;height:34px;border-radius:8px;cursor:pointer;font-size:13px;transition:.15s;margin:0 2px;
  display:inline-flex;align-items:center;justify-content:center
}
.btn-action:hover{border-color:var(--leaf,#52b788);color:var(--leaf-dark,#1b4332);background:var(--leaf-pale,#e8f5e9)}
.btn-action.warn:hover{border-color:#f4a261;color:#7c4a03;background:#fff3e0}
.btn-action.ok:hover{border-color:#2d6a4f;color:#fff;background:#2d6a4f}
.btn-action.danger:hover{border-color:#b91c1c;color:#fff;background:#b91c1c}

.role-modal{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center}
.role-modal-overlay{position:absolute;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(2px)}
.role-modal-box{position:relative;background:#fff;border-radius:14px;padding:26px 28px;
  box-shadow:0 20px 60px rgba(0,0,0,.25);min-width:380px;max-width:90vw}
.role-modal-box h3{margin:0 0 4px;color:var(--leaf-dark);font-size:18px}
</style>

<script>
function openRole(id, name, currentRole) {
  document.getElementById('roleUserName').textContent = name;
  document.getElementById('roleSelect').value = currentRole;
  document.getElementById('roleForm').action = '<?= url('/admin/users') ?>/' + id + '/role';
  document.getElementById('roleModal').style.display = 'flex';
}
function closeRole() {
  document.getElementById('roleModal').style.display = 'none';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRole(); });
</script>
