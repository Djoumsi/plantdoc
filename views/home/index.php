<!-- HERO IMMERSIF avec image agriculteur -->
<section class="landing-hero">
  <div class="container">
    <div>
      <span class="badge-top"><i class="fa-solid fa-wand-magic-sparkles"></i> Propulsé par l'IA · Pour les agriculteurs du Cameroun</span>
      <h1>Sauvez vos récoltes en <span class="accent">5 secondes</span> grâce à l'IA</h1>
      <p class="lead">PlantDoc identifie les maladies de vos cultures — cacao, café, plantain, manioc, tomate — à partir d'une simple photo. Recevez instantanément des traitements adaptés à votre région.</p>

      <div class="cta-row">
        <a href="<?= url('/register') ?>" class="btn-mega">
          <i class="fa-solid fa-leaf"></i> Commencer gratuitement
        </a>
        <a href="#how-it-works" class="btn-ghost">
          <i class="fa-solid fa-play"></i> Voir comment ça marche
        </a>
      </div>

      <div class="trust-row">
        <div class="trust-item"><span class="num">1 247</span><span class="lbl">Agriculteurs actifs</span></div>
        <div class="trust-item"><span class="num">15+</span><span class="lbl">Maladies couvertes</span></div>
        <div class="trust-item"><span class="num">87%</span><span class="lbl">Précision IA</span></div>
        <div class="trust-item"><span class="num">10</span><span class="lbl">Régions couvertes</span></div>
      </div>
    </div>

    <div class="visual-side">
      <div class="floating-card">
        <div class="fc-img"></div>
        <span class="fc-tag"><i class="fa-solid fa-triangle-exclamation"></i> Sévère</span>
        <h4>Pourriture brune du cacao</h4>
        <small style="color:var(--muted);font-style:italic">Phytophthora megakarya</small>
        <div class="meter"><div class="meter-fill"></div></div>
        <div class="fc-meta"><span>Confiance IA</span><strong style="color:var(--success)">87%</strong></div>
      </div>

      <div class="stat-bubble bubble-1">
        <div class="ic"><i class="fa-solid fa-bolt"></i></div>
        <div>
          <div class="b1">4,2s</div>
          <div class="b2">Temps d'analyse</div>
        </div>
      </div>

      <div class="stat-bubble bubble-2">
        <div class="ic" style="background:var(--success-light);color:var(--success)"><i class="fa-solid fa-leaf"></i></div>
        <div>
          <div class="b1">+ 42%</div>
          <div class="b2">Récolte préservée</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section" id="how-it-works">
  <div class="container">
    <div class="how-eyebrow">Comment ça marche</div>
    <h2>Trois étapes pour sauver votre culture</h2>
    <p class="sub">PlantDoc fonctionne sur n'importe quel smartphone Android, même avec une connexion 3G faible.</p>

    <div class="steps-grid">
      <div class="step">
        <div class="num-circle">
          <i class="fa-solid fa-camera-retro"></i>
          <span class="num-badge">1</span>
        </div>
        <h3>Photographier</h3>
        <p>Prenez une photo claire de la feuille ou du fruit suspect avec votre téléphone.</p>
      </div>
      <div class="step">
        <div class="num-circle">
          <i class="fa-solid fa-brain"></i>
          <span class="num-badge">2</span>
        </div>
        <h3>Analyser</h3>
        <p>Notre IA spécialisée en agriculture tropicale africaine identifie la maladie.</p>
      </div>
      <div class="step">
        <div class="num-circle">
          <i class="fa-solid fa-prescription-bottle-medical"></i>
          <span class="num-badge">3</span>
        </div>
        <h3>Traiter</h3>
        <p>Recevez un protocole biologique adapté avec produits disponibles localement.</p>
      </div>
    </div>
  </div>
</section>

<!-- SHOWCASE -->
<section class="showcase">
  <div class="container">
    <div class="showcase-grid">
      <div class="text">
        <div class="how-eyebrow">Pourquoi PlantDoc</div>
        <h2>Pensé pour les réalités du terrain camerounais</h2>
        <p>Contrairement aux applications génériques, PlantDoc est entraîné sur les pathologies tropicales africaines et propose des traitements avec des produits accessibles dans votre village.</p>

        <ul class="checklist">
          <li><i class="fa-solid fa-check"></i> <span><strong>Mode hors-ligne</strong> — fonctionne même sans réseau, synchronise quand vous êtes connecté</span></li>
          <li><i class="fa-solid fa-check"></i> <span><strong>Traitements bio prioritaires</strong> — compost, prêle, bouillie bordelaise plutôt que pesticides coûteux</span></li>
          <li><i class="fa-solid fa-check"></i> <span><strong>Carte épidémiologique</strong> — alertes quand une maladie circule dans votre région</span></li>
          <li><i class="fa-solid fa-check"></i> <span><strong>Validation par experts</strong> — agronomes du MINADER valident les cas douteux</span></li>
          <li><i class="fa-solid fa-check"></i> <span><strong>100% gratuit</strong> — pas d'abonnement, pas de publicité</span></li>
        </ul>
      </div>

      <div class="visual">
        <div class="visual-caption">
          <h4>Reconnaissance précise des pathologies</h4>
          <p>Mildiou tomate, pourriture cacao, mosaïque manioc, rouille caféier… 15+ maladies couvertes</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIAL -->
<section class="testimonials">
  <div class="container">
    <h2>Ce qu'en disent les agriculteurs</h2>
    <div class="quote">
      <p>Grâce à PlantDoc, j'ai détecté la pourriture brune sur mes cabosses avant qu'elle ne se propage à toute la plantation. J'ai sauvé presque 70% de ma récolte de cacao cette saison. C'est devenu un outil indispensable.</p>
      <div class="person">
        <div class="avatar">PN</div>
        <div class="info">
          <strong>Paul Ndongo</strong>
          <small>Producteur de cacao · Mbalmayo, Centre</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<section class="cta-final">
  <div class="container">
    <h2>Prêt à protéger vos cultures ?</h2>
    <p>Rejoignez plus de 1 200 agriculteurs camerounais qui utilisent déjà PlantDoc.</p>
    <a href="<?= url('/register') ?>" class="btn">
      <i class="fa-solid fa-rocket"></i> Créer mon compte gratuit
    </a>
  </div>
</section>
