<!-- HERO -->
<section class="page-hero">
  <div class="container">
    <div class="breadcrumb"><i class="fa-solid fa-envelope"></i> Nous contacter</div>
    <h1>Une question ? Parlons-en</h1>
    <p class="lead">Que vous soyez agriculteur, partenaire ou simplement curieux, notre équipe est à votre écoute. Nous répondons sous 48 heures.</p>
  </div>
</section>

<!-- CONTACT GRID -->
<section class="section" style="background:#fff">
  <div class="container">
    <div class="contact-grid">

      <!-- Sidebar infos -->
      <aside class="contact-info">

        <div class="info-card green">
          <div class="icc"><i class="fa-solid fa-location-dot"></i></div>
          <div class="ictx">
            <h4>Notre adresse</h4>
            <p>Quartier Bastos, Yaoundé<br>Centre, Cameroun</p>
          </div>
        </div>

        <div class="info-card gold">
          <div class="icc"><i class="fa-solid fa-phone"></i></div>
          <div class="ictx">
            <h4>Téléphone</h4>
            <a href="tel:+237690123456">+237 690 12 34 56</a>
            <a href="tel:+237680098765">+237 680 09 87 65</a>
          </div>
        </div>

        <div class="info-card info">
          <div class="icc"><i class="fa-solid fa-envelope"></i></div>
          <div class="ictx">
            <h4>Email</h4>
            <a href="mailto:contact@plantdoc.cm">contact@plantdoc.cm</a>
            <a href="mailto:support@plantdoc.cm">support@plantdoc.cm</a>
          </div>
        </div>

        <div class="info-card danger">
          <div class="icc"><i class="fa-regular fa-clock"></i></div>
          <div class="ictx">
            <h4>Heures d'ouverture</h4>
            <p>Lun - Ven : 8h00 - 18h00<br>Sam : 9h00 - 13h00</p>
          </div>
        </div>

        <div class="social-row">
          <h4><i class="fa-solid fa-share-nodes"></i> Suivez-nous</h4>
          <div class="social-icons">
            <a href="#" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="#" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="#" title="X / Twitter"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
          </div>
        </div>
      </aside>

      <!-- Formulaire -->
      <div class="contact-form">
        <h3><i class="fa-solid fa-paper-plane" style="color:var(--leaf)"></i> Envoyez-nous un message</h3>
        <p class="formsubt">Remplissez le formulaire ci-dessous, nous vous répondrons dans les meilleurs délais.</p>

        <form method="post" action="<?= url('/contact') ?>">
          <?= Csrf::field() ?>

          <div class="form-row">
            <div class="form-group">
              <label>Nom complet *</label>
              <div class="input-wrap">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="nom" class="form-control" placeholder="Brayan VINIL" required>
              </div>
            </div>
            <div class="form-group">
              <label>Email *</label>
              <div class="input-wrap">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="vous@exemple.com" required>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Téléphone</label>
              <div class="input-wrap">
                <i class="fa-solid fa-mobile-screen"></i>
                <input type="tel" name="telephone" class="form-control" placeholder="+237 6XX XX XX XX">
              </div>
            </div>
            <div class="form-group">
              <label>Sujet *</label>
              <div class="input-wrap">
                <i class="fa-solid fa-tag"></i>
                <select name="sujet" class="form-control" style="appearance:none" required>
                  <option value="">— Choisir un sujet —</option>
                  <option>Question générale</option>
                  <option>Support technique</option>
                  <option>Partenariat / Coopérative</option>
                  <option>Presse / Médias</option>
                  <option>Rejoindre l'équipe</option>
                  <option>Signaler un bug</option>
                  <option>Autre</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Message *</label>
            <textarea name="message" class="form-control" placeholder="Décrivez votre demande en quelques mots..." required></textarea>
          </div>

          <label style="display:flex;align-items:flex-start;gap:10px;font-size:12px;color:var(--muted);margin-bottom:18px;cursor:pointer">
            <input type="checkbox" required style="accent-color:var(--leaf);margin-top:3px">
            <span>J'accepte que mes données soient utilisées pour traiter ma demande, conformément à la <a href="#" style="color:var(--leaf);font-weight:600">politique de confidentialité</a>.</span>
          </label>

          <button type="submit" class="btn-send">
            <i class="fa-solid fa-paper-plane"></i> Envoyer le message
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section" style="background:var(--bg)">
  <div class="container">
    <div style="text-align:center;max-width:700px;margin:0 auto 40px">
      <div class="eyebrow" style="justify-content:center">FAQ</div>
      <h2>Questions fréquentes</h2>
      <p class="sub" style="margin-left:auto;margin-right:auto">Vous trouverez peut-être votre réponse ci-dessous avant même d'écrire.</p>
    </div>

    <div class="faq-list">
      <details class="faq-item">
        <summary class="faq-q">
          <span>PlantDoc est-il vraiment gratuit ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">Oui, 100% gratuit pour les agriculteurs individuels et les petites coopératives. Aucune carte bancaire requise, aucun abonnement caché. Notre modèle économique repose sur les partenariats institutionnels et les bailleurs de fonds.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">
          <span>L'application fonctionne-t-elle sans internet ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">L'interface est utilisable hors-ligne grâce à la technologie PWA. Cependant, l'analyse IA nécessite une connexion (3G suffit). Les diagnostics sont mis en file d'attente et envoyés dès que vous retrouvez du réseau.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">
          <span>Quelles cultures sont couvertes ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">À ce jour : cacao, café, banane plantain, manioc, maïs, tomate, arachide et mil. Nous ajoutons régulièrement de nouvelles cultures. Si la vôtre n'est pas listée, contactez-nous !</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">
          <span>Quelle est la fiabilité de l'IA ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">Notre précision moyenne est de 87%. Les diagnostics avec une confiance inférieure à 70% sont automatiquement transmis à un agronome humain pour validation, sous 24h.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">
          <span>Comment protégez-vous mes données personnelles ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">Toutes les données sont hébergées au Cameroun, chiffrées en transit (HTTPS) et au repos. Nous ne vendons jamais vos informations à des tiers. Vous pouvez supprimer votre compte à tout moment.</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">
          <span>Puis-je devenir partenaire (coopérative, ONG) ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">Absolument ! Nous accueillons les coopératives agricoles, ONG, instituts de recherche et acteurs publics. Écrivez-nous via le formulaire en choisissant "Partenariat".</div>
      </details>

      <details class="faq-item">
        <summary class="faq-q">
          <span>Comment signaler un diagnostic erroné ?</span>
          <i class="fa-solid fa-chevron-down"></i>
        </summary>
        <div class="faq-a">Sur chaque diagnostic, vous pouvez cliquer sur "Donner un avis" et indiquer si l'analyse était correcte. Vos retours alimentent l'amélioration continue de notre modèle.</div>
      </details>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-final">
  <div class="container">
    <h2>Pas encore inscrit ?</h2>
    <p>Rejoignez plus de 1 200 agriculteurs et commencez à protéger vos cultures gratuitement.</p>
    <a href="<?= url('/register') ?>" class="btn">
      <i class="fa-solid fa-leaf"></i> Créer mon compte gratuit
    </a>
  </div>
</section>
