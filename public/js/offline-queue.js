/**
 * PlantDoc — File d'attente hors-ligne des diagnostics.
 *
 * Permet à l'agriculteur de prendre une photo SANS connexion : le cliché et le
 * jeton CSRF sont stockés dans IndexedDB, puis envoyés automatiquement au
 * serveur dès le retour du réseau (événement 'online') ou au prochain chargement.
 *
 * Expose window.PlantDocOffline.{ enqueue, flush, count }.
 */
(function () {
  const DB_NAME = 'plantdoc-offline';
  const STORE = 'queue';
  const ENDPOINT = (window.PLANTDOC_BASE || '/plantdoc') + '/diagnostic';

  function openDB() {
    return new Promise((resolve, reject) => {
      const req = indexedDB.open(DB_NAME, 1);
      req.onupgradeneeded = () => {
        const db = req.result;
        if (!db.objectStoreNames.contains(STORE)) {
          db.createObjectStore(STORE, { keyPath: 'id', autoIncrement: true });
        }
      };
      req.onsuccess = () => resolve(req.result);
      req.onerror = () => reject(req.error);
    });
  }

  function tx(db, mode) {
    return db.transaction(STORE, mode).objectStore(STORE);
  }

  async function enqueue(file, csrf) {
    const db = await openDB();
    return new Promise((resolve, reject) => {
      const store = tx(db, 'readwrite');
      const rec = { blob: file, filename: file.name || 'photo.jpg', csrf: csrf, createdAt: Date.now() };
      const r = store.add(rec);
      r.onsuccess = () => { updateBadge(); resolve(r.result); };
      r.onerror = () => reject(r.error);
    });
  }

  async function all() {
    const db = await openDB();
    return new Promise((resolve, reject) => {
      const r = tx(db, 'readonly').getAll();
      r.onsuccess = () => resolve(r.result || []);
      r.onerror = () => reject(r.error);
    });
  }

  async function remove(id) {
    const db = await openDB();
    return new Promise((resolve) => {
      const r = tx(db, 'readwrite').delete(id);
      r.onsuccess = () => { updateBadge(); resolve(); };
      r.onerror = () => resolve();
    });
  }

  async function count() {
    try { return (await all()).length; } catch (e) { return 0; }
  }

  let flushing = false;
  async function flush() {
    if (flushing || !navigator.onLine) return;
    flushing = true;
    try {
      const items = await all();
      for (const item of items) {
        try {
          const fd = new FormData();
          fd.append('_csrf', item.csrf);
          fd.append('photo', item.blob, item.filename);
          const res = await fetch(ENDPOINT, { method: 'POST', body: fd, credentials: 'same-origin' });
          if (res.ok || res.redirected) {
            await remove(item.id);
            toast('Un diagnostic hors-ligne a été envoyé ✔');
          }
        } catch (e) {
          // réseau encore instable : on réessaiera plus tard
          break;
        }
      }
    } finally {
      flushing = false;
    }
  }

  // ---- UI : badge compteur + toast ----
  function updateBadge() {
    count().then(n => {
      let badge = document.getElementById('offlineBadge');
      if (n > 0) {
        if (!badge) {
          badge = document.createElement('div');
          badge.id = 'offlineBadge';
          badge.style.cssText = 'position:fixed;bottom:96px;right:16px;z-index:300;background:#f4a261;color:#1b4332;' +
            'padding:10px 16px;border-radius:30px;font:600 13px sans-serif;box-shadow:0 6px 20px rgba(0,0,0,.2);' +
            'display:flex;align-items:center;gap:8px;cursor:pointer';
          badge.onclick = flush;
          document.body.appendChild(badge);
        }
        badge.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> ' + n + ' diagnostic(s) en attente';
      } else if (badge) {
        badge.remove();
      }
    });
  }

  function toast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = 'position:fixed;bottom:150px;right:16px;z-index:301;background:#2d6a4f;color:#fff;' +
      'padding:12px 18px;border-radius:10px;font:600 13px sans-serif;box-shadow:0 6px 20px rgba(0,0,0,.25);opacity:0;transition:.3s';
    document.body.appendChild(t);
    requestAnimationFrame(() => { t.style.opacity = '1'; });
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 400); }, 3500);
  }

  window.PlantDocOffline = { enqueue, flush, count };

  // Auto-flush au retour du réseau et au chargement
  window.addEventListener('online', flush);
  window.addEventListener('load', () => { updateBadge(); flush(); });
})();
