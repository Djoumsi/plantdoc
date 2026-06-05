<div class="card">
  <div class="card-header">
    <h2><i class="fa-solid fa-users"></i> Liste des utilisateurs (<?= count($users) ?>)</h2>
  </div>
  <div class="table-wrap" style="border:none">
    <table class="table">
      <thead>
        <tr>
          <th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Région</th><th>Diag.</th><th>Statut</th><th>Inscrit</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div class="icon-circle icon-leaf" style="width:32px;height:32px;font-size:13px"><?= h(strtoupper(substr($u['nom'],0,1))) ?></div>
              <strong><?= h(trim(($u['prenom'] ?? '') . ' ' . $u['nom'])) ?></strong>
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
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
