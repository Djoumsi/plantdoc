<div class="page-header">
  <h1><i class="fa-solid fa-camera"></i> Nouveau diagnostic</h1>
  <p class="subtitle">Téléversez une photo nette d'une feuille, fruit ou tige suspecte.</p>
</div>

<div class="card">
  <form method="post" action="<?= url('/diagnostic') ?>" enctype="multipart/form-data" id="diag-form">
    <?= Csrf::field() ?>

    <div class="upload-zone" id="dropzone">
      <i class="fa-solid fa-cloud-arrow-up"></i>
      <h3>Ajoutez une photo de la plante</h3>
      <p>JPG, PNG ou WebP · 5 Mo max</p>
      <img id="preview" class="preview-img" style="display:none">

      <!-- L'unique input envoyé au serveur -->
      <input type="file" name="photo" id="photo"
             accept="image/jpeg,image/png,image/webp"
             class="photo-input" required>

      <div class="photo-actions">
        <button type="button" class="photo-btn photo-btn-camera" id="btnCamera">
          <i class="fa-solid fa-camera"></i>
          <span>Prendre une photo</span>
        </button>

        <button type="button" class="photo-btn photo-btn-gallery" id="btnGallery">
          <i class="fa-solid fa-images"></i>
          <span>Choisir depuis la galerie</span>
        </button>
      </div>
    </div>

    <style>
      .photo-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:18px;justify-content:center}
      .photo-btn{display:inline-flex;align-items:center;gap:10px;padding:14px 22px;border-radius:12px;
        font-size:14px;font-weight:600;cursor:pointer;transition:.2s;border:2px solid transparent;
        text-align:center;min-width:200px;justify-content:center}
      .photo-btn-camera{background:linear-gradient(135deg,var(--leaf,#52b788),var(--leaf-dark,#2d6a4f));color:#fff}
      .photo-btn-camera:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(45,106,79,.3)}
      .photo-btn-gallery{background:#fff;color:var(--leaf-dark,#1b4332);border-color:var(--leaf,#52b788)}
      .photo-btn-gallery:hover{background:var(--leaf-pale,#e8f5e9);transform:translateY(-2px)}
      .photo-btn i{font-size:18px}
      .photo-input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}
      @media(max-width:480px){
        .photo-btn{width:100%;min-width:0}
      }
    </style>

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
const btnCamera = document.getElementById('btnCamera');
const btnGallery = document.getElementById('btnGallery');
const preview = document.getElementById('preview');
const dropzone = document.getElementById('dropzone');
const form = document.getElementById('diag-form');
const offlineNote = document.getElementById('offlineNote');

// Un seul input, deux boutons : on bascule l'attribut capture avant de cliquer.
btnCamera.addEventListener('click', () => {
  photo.setAttribute('capture', 'environment');
  photo.click();
});
btnGallery.addEventListener('click', () => {
  photo.removeAttribute('capture');
  photo.click();
});

photo.addEventListener('change', () => {
  const file = photo.files[0];
  if (!file) return;
  preview.src = URL.createObjectURL(file);
  preview.style.display = 'block';
  dropzone.classList.add('has-image');
  dropzone.querySelectorAll(':scope > i, :scope > h3, :scope > p').forEach(el => el.style.display = 'none');
});

function currentPhoto() { return photo.files[0] || null; }

// Indicateur hors-ligne
function refreshOnline() {
  offlineNote.style.display = navigator.onLine ? 'none' : 'block';
}
window.addEventListener('online', refreshOnline);
window.addEventListener('offline', refreshOnline);
refreshOnline();

// Interception : si hors-ligne, on met la photo en file d'attente locale
form.addEventListener('submit', async function (e) {
  if (navigator.onLine) return;
  e.preventDefault();
  const file = currentPhoto();
  if (!file) return;
  const csrf = form.querySelector('input[name="_csrf"]').value;
  try {
    await window.PlantDocOffline.enqueue(file, csrf);
    form.reset();
    preview.style.display = 'none';
    dropzone.classList.remove('has-image');
    dropzone.querySelectorAll(':scope > i, :scope > h3, :scope > p').forEach(el => el.style.display = '');
    alert('Vous êtes hors-ligne. Votre diagnostic a été enregistré et sera envoyé automatiquement dès le retour de la connexion.');
  } catch (err) {
    alert('Impossible d\'enregistrer le diagnostic hors-ligne.');
  }
});
</script>
