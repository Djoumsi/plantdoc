<div class="page-header">
  <h1><i class="fa-solid fa-camera"></i> Nouveau diagnostic</h1>
  <p class="subtitle">Téléversez une photo nette d'une feuille, fruit ou tige suspecte.</p>
</div>

<div class="card">
  <form method="post" action="<?= url('/diagnostic') ?>" enctype="multipart/form-data" id="diag-form">
    <?= Csrf::field() ?>

    <label for="photo" class="upload-zone" id="dropzone">
      <i class="fa-solid fa-cloud-arrow-up"></i>
      <h3>Choisir une photo</h3>
      <p>JPG, PNG ou WebP · 5 Mo max</p>
      <img id="preview" class="preview-img" style="display:none">
      <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" required>
    </label>

    <div class="alert alert-info" style="margin-top:18px">
      <i class="fa-solid fa-lightbulb" style="color:var(--gold)"></i>
      <div>
        <strong>Conseils pour une bonne photo :</strong>
        cadrage proche, bon éclairage naturel, feuille bien visible, éviter le flou.
      </div>
    </div>

    <div class="alert" id="offlineNote" style="margin-top:14px;display:none;background:#fff3e0;border:1px solid #f4a261;color:#7c4a03;border-radius:10px;padding:12px 14px">
      <i class="fa-solid fa-wifi" style="margin-right:8px"></i>
      <span><strong>Hors-ligne :</strong> votre photo sera enregistrée et analysée automatiquement dès le retour de la connexion.</span>
    </div>

    <button type="submit" class="btn btn-primary btn-block" style="margin-top:18px">
      <i class="fa-solid fa-wand-magic-sparkles"></i> Lancer l'analyse IA
    </button>
  </form>
</div>

<script>
const photo = document.getElementById('photo');
const preview = document.getElementById('preview');
const dropzone = document.getElementById('dropzone');
const form = document.getElementById('diag-form');
const offlineNote = document.getElementById('offlineNote');

photo.addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) return;
  preview.src = URL.createObjectURL(file);
  preview.style.display = 'block';
  dropzone.classList.add('has-image');
  dropzone.querySelectorAll('i,h3,p').forEach(el => el.style.display = 'none');
});

// Indicateur hors-ligne
function refreshOnline() {
  offlineNote.style.display = navigator.onLine ? 'none' : 'block';
}
window.addEventListener('online', refreshOnline);
window.addEventListener('offline', refreshOnline);
refreshOnline();

// Interception : si hors-ligne, on met la photo en file d'attente locale
form.addEventListener('submit', async function (e) {
  if (navigator.onLine) return; // comportement normal : envoi serveur
  e.preventDefault();
  const file = photo.files[0];
  if (!file) return;
  const csrf = form.querySelector('input[name="_csrf"]').value;
  try {
    await window.PlantDocOffline.enqueue(file, csrf);
    form.reset();
    preview.style.display = 'none';
    dropzone.classList.remove('has-image');
    dropzone.querySelectorAll('i,h3,p').forEach(el => el.style.display = '');
    alert('Vous êtes hors-ligne. Votre diagnostic a été enregistré et sera envoyé automatiquement dès le retour de la connexion.');
  } catch (err) {
    alert('Impossible d\'enregistrer le diagnostic hors-ligne.');
  }
});
</script>
