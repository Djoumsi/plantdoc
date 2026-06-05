<div class="page-header">
  <h1><i class="fa-solid fa-map-location-dot"></i> Carte épidémiologique du Cameroun</h1>
  <p class="subtitle">Visualisez en temps réel les maladies des cultures détectées dans chaque région.</p>
</div>

<div class="stats-row-pro" style="margin-bottom:24px">
  <div class="stat-pro leaf">
    <div class="ic"><i class="fa-solid fa-microscope"></i></div>
    <div class="lbl">Diagnostics totaux</div>
    <div class="val"><?= number_format($global_stats['total'], 0, ',', ' ') ?></div>
  </div>
  <div class="stat-pro danger">
    <div class="ic"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="lbl">Cas sévères</div>
    <div class="val"><?= $global_stats['severes'] ?></div>
  </div>
  <div class="stat-pro gold">
    <div class="ic"><i class="fa-solid fa-location-dot"></i></div>
    <div class="lbl">Régions touchées</div>
    <div class="val"><?= $global_stats['regions_actives'] ?>/10</div>
  </div>
</div>

<div class="dash-grid">

  <!-- CARTE -->
  <div class="card" style="padding:0;overflow:hidden">
    <div class="card-header" style="padding:18px 24px;border-bottom:1px solid var(--border);margin-bottom:0">
      <h2><i class="fa-solid fa-globe"></i> Carte interactive</h2>
      <div style="display:flex;gap:14px;font-size:11px;color:var(--muted)">
        <span><i class="fa-solid fa-circle" style="color:var(--danger);font-size:8px"></i> Sévère</span>
        <span><i class="fa-solid fa-circle" style="color:var(--gold);font-size:8px"></i> Modéré</span>
        <span><i class="fa-solid fa-circle" style="color:var(--success);font-size:8px"></i> Faible</span>
      </div>
    </div>
    <div id="map" style="height:520px;width:100%"></div>
  </div>

  <!-- SIDEBAR -->
  <div>
    <div class="card">
      <div class="card-header">
        <h2><i class="fa-solid fa-ranking-star"></i> Top maladies</h2>
      </div>
      <?php if (empty($top_maladies)): ?>
        <div class="empty" style="padding:20px"><i class="fa-solid fa-leaf"></i><p>Aucune donnée</p></div>
      <?php else: foreach ($top_maladies as $i => $m): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)">
          <div style="width:30px;height:30px;border-radius:50%;background:<?= $i === 0 ? 'var(--danger-light)' : 'var(--leaf-pale)' ?>;color:<?= $i === 0 ? 'var(--danger)' : 'var(--leaf)' ?>;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0"><?= $i + 1 ?></div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;color:var(--leaf-dark);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= h($m['nom_commun']) ?></div>
            <div style="font-size:11px;color:var(--muted)"><?= h($m['culture'] ?? '—') ?></div>
          </div>
          <strong style="color:var(--leaf-dark);font-size:14px"><?= $m['total'] ?></strong>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <div class="card">
      <div class="card-header"><h2 style="font-size:15px"><i class="fa-solid fa-list"></i> Régions</h2></div>
      <div style="max-height:280px;overflow-y:auto">
        <?php foreach ($regions as $r): ?>
          <a href="#" onclick="zoomToRegion(<?= $r['latitude'] ?>,<?= $r['longitude'] ?>);return false" style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;text-decoration:none;color:inherit;transition:.2s" onmouseover="this.style.background='var(--leaf-pale)'" onmouseout="this.style.background='transparent'">
            <div style="width:8px;height:8px;border-radius:50%;background:<?= $r['cas_severes'] > 0 ? 'var(--danger)' : ($r['total_diagnostics'] > 0 ? 'var(--gold)' : 'var(--success)') ?>"></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:12px;font-weight:600"><?= h($r['nom']) ?></div>
              <div style="font-size:10px;color:var(--muted)"><?= h($r['chef_lieu']) ?></div>
            </div>
            <strong style="font-size:12px;color:var(--leaf-dark)"><?= $r['total_diagnostics'] ?></strong>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const regionsData = <?= json_encode($regions, JSON_UNESCAPED_UNICODE) ?>;

// Centré sur le Cameroun
const map = L.map('map').setView([7.0, 12.0], 6);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap',
  maxZoom: 12,
}).addTo(map);

regionsData.forEach(r => {
  if (!r.latitude || !r.longitude) return;

  const severe = parseInt(r.cas_severes) || 0;
  const total = parseInt(r.total_diagnostics) || 0;

  let color = '#06d6a0'; // vert si aucun cas
  let radius = 12;
  if (total > 0) {
    color = severe > 0 ? '#e63946' : '#f4a261';
    radius = Math.min(12 + total * 3, 40);
  }

  const circle = L.circleMarker([r.latitude, r.longitude], {
    radius: radius,
    fillColor: color,
    color: '#fff',
    weight: 2,
    opacity: 1,
    fillOpacity: 0.7,
  }).addTo(map);

  const popup = `
    <div style="font-family:Inter,sans-serif;min-width:200px">
      <h3 style="margin:0 0 6px;color:#1b4332;font-size:15px">📍 ${r.nom}</h3>
      <p style="margin:0 0 10px;color:#6b7280;font-size:11px">Chef-lieu : ${r.chef_lieu}</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px">
        <div><strong style="color:#2d6a4f">${r.total_diagnostics}</strong> diagnostics</div>
        <div><strong style="color:#e63946">${r.cas_severes}</strong> sévères</div>
        <div><strong style="color:#06d6a0">${r.plantes_saines}</strong> saines</div>
        <div><strong style="color:#f4a261">${r.maladies_distinctes}</strong> maladies</div>
      </div>
      ${r.maladie_top ? `<div style="margin-top:10px;padding:8px;background:#fef3c7;border-radius:6px;font-size:11px"><strong>⚠️ Plus fréquente :</strong><br>${r.maladie_top}</div>` : ''}
    </div>`;
  circle.bindPopup(popup);
});

function zoomToRegion(lat, lng) {
  map.setView([lat, lng], 8, { animate: true });
}
</script>
