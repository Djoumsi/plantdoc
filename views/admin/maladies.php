<div class="card">
  <div class="card-header">
    <h2><i class="fa-solid fa-virus"></i> Catalogue des maladies (<?= count($maladies) ?>)</h2>
  </div>
  <div class="table-wrap" style="border:none">
    <table class="table">
      <thead>
        <tr><th>Culture</th><th>Maladie</th><th>Nom scientifique</th><th>Type</th><th>Gravité</th></tr>
      </thead>
      <tbody>
        <?php foreach ($maladies as $m): ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <div class="icon-circle" style="width:30px;height:30px;font-size:12px;background:<?= h($m['couleur']) ?>;color:#fff">
                <i class="fa-solid <?= h($m['icone']) ?>"></i>
              </div>
              <?= h($m['culture_nom']) ?>
            </div>
          </td>
          <td><strong><?= h($m['nom_commun']) ?></strong></td>
          <td><em style="color:var(--muted)"><?= h($m['nom_scientifique'] ?? '—') ?></em></td>
          <td><span class="tag tag-confidence"><?= h($m['type_pathologie']) ?></span></td>
          <td><span class="tag tag-<?= h($m['severite_typique']) ?>"><?= h(ucfirst($m['severite_typique'])) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
