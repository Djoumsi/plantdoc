<div class="card">
  <div class="card-header">
    <h2><i class="fa-solid fa-microscope"></i> Tous les diagnostics</h2>
  </div>
  <div class="table-wrap" style="border:none">
    <table class="table">
      <thead>
        <tr>
          <th>#</th><th>Date</th><th>Utilisateur</th><th>Maladie</th><th>Gravité</th><th>Confiance</th><th>Statut</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($diagnostics as $d): ?>
        <tr>
          <td>#<?= (int) $d['id'] ?></td>
          <td><?= format_date($d['created_at'], 'd/m H:i') ?></td>
          <td><?= h(trim(($d['user_prenom'] ?? '') . ' ' . $d['user_nom'])) ?></td>
          <td><?= h($d['maladie_nom'] ?? $d['maladie_detectee'] ?? 'Plante saine') ?></td>
          <td>
            <?php if ($d['plante_saine']): ?>
              <span class="tag tag-legere"><i class="fa-solid fa-leaf"></i> Saine</span>
            <?php else: ?>
              <span class="tag tag-<?= h($d['gravite']) ?>"><?= h(ucfirst($d['gravite'])) ?></span>
            <?php endif; ?>
          </td>
          <td><strong style="color:var(--leaf)"><?= (int) $d['confiance'] ?>%</strong></td>
          <td><span class="tag tag-<?= h($d['statut']) ?>"><?= h(str_replace('_',' ',$d['statut'])) ?></span></td>
          <td>
            <div class="actions">
              <a href="<?= url('/diagnostic/' . $d['id']) ?>" class="btn-mini"><i class="fa-solid fa-eye"></i> Voir</a>
              <?php if ($d['statut'] === 'a_verifier'): ?>
                <form method="post" action="<?= url('/admin/diagnostic/' . $d['id'] . '/validate') ?>" style="display:inline">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="decision" value="valide">
                  <button class="btn-mini" style="color:var(--success);border-color:var(--success)">
                    <i class="fa-solid fa-check"></i> Valider
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($diagnostics)): ?><tr><td colspan="8" style="text-align:center;color:var(--muted)">Aucun diagnostic</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
