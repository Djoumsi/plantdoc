<div class="kpi-grid">
  <div class="kpi">
    <div class="top">
      <div class="lbl">Diagnostics totaux</div>
      <div class="icon-circle icon-leaf"><i class="fa-solid fa-microscope"></i></div>
    </div>
    <div class="val"><?= number_format($stats['total'], 0, ',', ' ') ?></div>
    <div class="trend"><i class="fa-solid fa-arrow-trend-up"></i> Cumulé</div>
  </div>

  <div class="kpi gold">
    <div class="top">
      <div class="lbl">Aujourd'hui</div>
      <div class="icon-circle icon-gold"><i class="fa-solid fa-calendar-day"></i></div>
    </div>
    <div class="val"><?= (int) $stats['aujourdhui'] ?></div>
    <div class="trend"><i class="fa-regular fa-clock"></i> En temps réel</div>
  </div>

  <div class="kpi success">
    <div class="top">
      <div class="lbl">Précision IA</div>
      <div class="icon-circle icon-success"><i class="fa-solid fa-bullseye"></i></div>
    </div>
    <div class="val"><?= number_format($stats['precision'], 1) ?>%</div>
    <div class="trend"><i class="fa-solid fa-arrow-trend-up"></i> Moyenne</div>
  </div>

  <div class="kpi danger">
    <div class="top">
      <div class="lbl">À valider</div>
      <div class="icon-circle icon-danger"><i class="fa-solid fa-circle-exclamation"></i></div>
    </div>
    <div class="val"><?= (int) $stats['a_valider'] ?></div>
    <div class="trend down"><i class="fa-solid fa-bell"></i> Action requise</div>
  </div>
</div>

<div class="card" style="margin-bottom:20px">
  <div class="card-header">
    <h2><i class="fa-solid fa-chart-line"></i> Évolution des diagnostics (30 derniers jours)</h2>
    <a href="<?= url('/admin/export/csv') ?>" class="btn btn-sm" style="background:var(--leaf);color:#fff;padding:8px 16px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
      <i class="fa-solid fa-file-csv"></i> Exporter CSV
    </a>
  </div>
  <div style="padding:10px 4px"><canvas id="chartPerDay" height="90"></canvas></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
  <div class="card">
    <div class="card-header"><h2><i class="fa-solid fa-fire"></i> Top 10 maladies en tendance</h2></div>
    <div class="table-wrap" style="border:none">
      <table class="table">
        <thead><tr><th>#</th><th>Maladie</th><th>Culture</th><th>Cas</th><th>Conf.</th></tr></thead>
        <tbody>
          <?php foreach ($top10 as $i => $t): ?>
            <tr>
              <td><strong style="color:var(--gold)"><?= $i + 1 ?></strong></td>
              <td><?= h($t['nom']) ?></td>
              <td style="color:var(--muted);font-size:12px"><?= h($t['culture'] ?? '—') ?></td>
              <td><span class="tag" style="background:#fee2e2;color:#b91c1c"><?= (int) $t['occurrences'] ?></span></td>
              <td><?= (int) $t['confiance_moy'] ?>%</td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($top10)): ?><tr><td colspan="5" style="text-align:center;color:var(--muted)">Aucune donnée</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2><i class="fa-solid fa-map-location-dot"></i> Carte de chaleur — Cameroun</h2></div>
    <div id="heatmap" style="height:340px;border-radius:12px;overflow:hidden"></div>
  </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px">

  <div class="card">
    <div class="card-header">
      <h2><i class="fa-solid fa-clock-rotate-left"></i> Diagnostics récents</h2>
      <a href="<?= url('/admin/diagnostics') ?>">Tout voir <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="table-wrap" style="border:none">
      <table class="table">
        <thead>
          <tr>
            <th>Date</th><th>Utilisateur</th><th>Maladie</th><th>Confiance</th><th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recents as $r): ?>
            <tr>
              <td><?= time_ago($r['created_at']) ?></td>
              <td><?= h(trim(($r['user_prenom'] ?? '') . ' ' . $r['user_nom'])) ?></td>
              <td><?= h($r['maladie_nom'] ?? $r['maladie_detectee'] ?? 'Plante saine') ?></td>
              <td><strong style="color:var(--leaf)"><?= (int) $r['confiance'] ?>%</strong></td>
              <td><span class="tag tag-<?= h($r['statut']) ?>"><?= h(str_replace('_',' ',$r['statut'])) ?></span></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($recents)): ?><tr><td colspan="5" style="text-align:center;color:var(--muted)">Aucun diagnostic</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2><i class="fa-solid fa-ranking-star"></i> Top maladies</h2>
    </div>
    <?php foreach ($top_maladies as $m): ?>
      <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
        <div class="icon-circle icon-danger" style="width:34px;height:34px;font-size:14px"><i class="fa-solid fa-virus"></i></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= h($m['nom_commun']) ?></div>
          <div style="font-size:11px;color:var(--muted)"><?= h($m['culture']) ?></div>
        </div>
        <strong style="color:var(--leaf-dark)"><?= (int) $m['occurrences'] ?></strong>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
  // ---- Courbe diagnostics / jour ----
  const perDay = <?= json_encode($per_day, JSON_NUMERIC_CHECK) ?>;
  const labels = perDay.map(p => {
    const d = new Date(p.jour);
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
  });
  const data = perDay.map(p => p.total);
  const ctx = document.getElementById('chartPerDay');
  if (ctx && window.Chart) {
    const grad = ctx.getContext('2d').createLinearGradient(0, 0, 0, 220);
    grad.addColorStop(0, 'rgba(82,183,136,0.45)');
    grad.addColorStop(1, 'rgba(82,183,136,0.02)');
    new Chart(ctx, {
      type: 'line',
      data: { labels, datasets: [{
        label: 'Diagnostics', data,
        borderColor: '#2d6a4f', backgroundColor: grad,
        borderWidth: 2.5, fill: true, tension: 0.35,
        pointRadius: 2, pointHoverRadius: 5, pointBackgroundColor: '#f4a261'
      }]},
      options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef2f0' } },
          x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }
        }
      }
    });
  }

  // ---- Carte de chaleur régions ----
  const regions = <?= json_encode($by_region, JSON_NUMERIC_CHECK) ?>;
  const mapEl = document.getElementById('heatmap');
  if (mapEl && window.L) {
    const map = L.map('heatmap', { scrollWheelZoom: false }).setView([5.6, 12.3], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap', maxZoom: 10
    }).addTo(map);
    const max = Math.max(1, ...regions.map(r => Number(r.total)));
    regions.forEach(r => {
      if (!r.latitude || !r.longitude) return;
      const t = Number(r.total);
      const ratio = t / max;
      const radius = 10 + ratio * 28;
      const color = t === 0 ? '#cbd5e1' : (ratio > 0.66 ? '#b91c1c' : ratio > 0.33 ? '#f4a261' : '#52b788');
      L.circleMarker([r.latitude, r.longitude], {
        radius, color, fillColor: color, fillOpacity: 0.55, weight: 1.5
      }).addTo(map).bindPopup(
        '<strong>' + r.nom + '</strong><br>' + t + ' diagnostic(s)<br>' +
        (Number(r.severes) || 0) + ' cas sévère(s)'
      );
    });
  }
})();
</script>
