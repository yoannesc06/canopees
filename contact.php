<?php

// ── config bdd ─────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'canopees');
define('DB_USER', 'root');       
define('DB_PASS', '');           
define('DB_CHARSET', 'utf8mb4');

// mail
define('MAIL_TO', 'contact@canopees.fr');
define('MAIL_SUBJECT_PREFIX', '[Canopées] Nouvelle demande de devis');

// traitement du formulaire
$success = false;
$errors  = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // recup donnés
    $prenom  = trim(htmlspecialchars($_POST['prenom']  ?? '', ENT_QUOTES, 'UTF-8'));
    $nom     = trim(htmlspecialchars($_POST['nom']     ?? '', ENT_QUOTES, 'UTF-8'));
    $email   = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $tel     = trim(htmlspecialchars($_POST['tel']     ?? '', ENT_QUOTES, 'UTF-8'));
    $presta  = trim(htmlspecialchars($_POST['presta']  ?? '', ENT_QUOTES, 'UTF-8'));
    $message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));

    // Validation
    if (empty($prenom))  $errors[] = 'Le prénom est requis.';
    if (empty($nom))     $errors[] = 'Le nom est requis.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                         $errors[] = 'L\'adresse email est invalide.';
    if (empty($message)) $errors[] = 'Le message est requis.';

    if (empty($errors)) {
        // connexion pdo
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // insert en bdd
            $stmt = $pdo->prepare("
                INSERT INTO demandes_contact
                    (prenom, nom, email, telephone, prestation, message, date_envoi, statut)
                VALUES
                    (:prenom, :nom, :email, :telephone, :prestation, :message, NOW(), 'nouveau')
            ");
            $stmt->execute([
                ':prenom'     => $prenom,
                ':nom'        => $nom,
                ':email'      => $email,
                ':telephone'  => $tel,
                ':prestation' => $presta,
                ':message'    => $message,
            ]);

            // envoie mail
            $sujet  = MAIL_SUBJECT_PREFIX . ' — ' . $prenom . ' ' . $nom;
            $corps  = "Nouvelle demande de devis reçue via le site canopees.fr\n\n";
            $corps .= "Prénom   : $prenom\n";
            $corps .= "Nom      : $nom\n";
            $corps .= "Email    : $email\n";
            $corps .= "Téléphone: $tel\n";
            $corps .= "Presta   : $presta\n\n";
            $corps .= "Message  :\n$message\n";
            $headers = "From: noreply@canopees.fr\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
            @mail(MAIL_TO, $sujet, $corps, $headers);

            $success = true;

        } catch (PDOException $e) {
            $errors[] = 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer ou nous appeler directement.';
         
        }
    }

   
    $formData = compact('prenom', 'nom', 'email', 'tel', 'presta', 'message');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact & Devis — Canopées</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <style>
    .alert { padding: 1rem 1.25rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: .9rem; }
    .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-error ul { margin: .5rem 0 0 1rem; padding: 0; }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; display:flex; align-items:center; gap:.75rem; }
    .alert-success svg { flex-shrink:0; width:22px; height:22px; }
  </style>
</head>
<body>

  <!-- NAV -->
  <nav id="navbar">
    <div class="nav-logo">
      <a href="index.html" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;color:inherit;">
        <img src="images/logo.png" alt="Canopées" style="height:48px;width:auto;" />
      </a>
    </div>
    <button class="burger" id="burger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
    <ul class="nav-links" id="nav-links">
      <li><a href="index.html">Accueil</a></li>
      <li><a href="qui-sommes-nous.html">Qui sommes-nous</a></li>
      <li><a href="prestations.html">Prestations</a></li>
      <li><a href="tarifs.html">Tarifs</a></li>
      <li><a href="contact.php" class="nav-cta active">Contact & Devis</a></li>
    </ul>
  </nav>

  <main>

    <div class="page-hero short">
      <div class="page-hero-bg" style="background-image:url('https://images.unsplash.com/photo-1599598425947-5202edd56bdb?w=1600&q=80')"></div>
      <div class="page-hero-content">
        <span class="eyebrow light">Parlons-en</span>
        <h1>Contact & Devis</h1>
      </div>
    </div>

    <div class="container section-pad">
      <div class="contact-grid">

        <!-- Formulaire -->
        <div class="contact-form-col reveal">
          <h2>Demande de devis</h2>
          <p>Décrivez votre projet, nous vous recontactons sous 48h avec un devis gratuit.</p>

          <?php if ($success): ?>
            <div class="alert alert-success">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Message envoyé ! Nous vous recontactons sous 48h.</span>
            </div>
          <?php elseif (!empty($errors)): ?>
            <div class="alert alert-error">
              <strong>Veuillez corriger les erreurs suivantes :</strong>
              <ul>
                <?php foreach ($errors as $err): ?>
                  <li><?= $err ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!$success): ?>
          <div class="form-wrapper">
            <form method="POST" action="contact.php" novalidate>
              <div class="form-row">
                <div class="form-group">
                  <label for="prenom">Prénom <span style="color:var(--green)">*</span></label>
                  <input type="text" id="prenom" name="prenom" placeholder="Jean"
                        value="<?= htmlspecialchars($formData['prenom'] ?? '') ?>" required />
                </div>
                <div class="form-group">
                  <label for="nom">Nom <span style="color:var(--green)">*</span></label>
                  <input type="text" id="nom" name="nom" placeholder="Dupont"
                        value="<?= htmlspecialchars($formData['nom'] ?? '') ?>" required />
                </div>
              </div>
              <div class="form-group">
                <label for="email">Email <span style="color:var(--green)">*</span></label>
                <input type="email" id="email" name="email" placeholder="jean@exemple.fr"
                      value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required />
              </div>
              <div class="form-group">
                <label for="tel">Téléphone</label>
                <input type="tel" id="tel" name="tel" placeholder="06 00 00 00 00"
                      value="<?= htmlspecialchars($formData['tel'] ?? '') ?>" />
              </div>
              <div class="form-group">
                <label for="presta">Type de prestation</label>
                <select id="presta" name="presta">
                  <option value="">Choisir une prestation</option>
                  <?php
                    $options = [
                      'Conception & réalisation d\'espace vert',
                      'Entretien des espaces verts',
                      'Taille des haies',
                      'Élagage & abattage d\'arbres',
                      'Valorisation des déchets verts',
                      'Autre',
                    ];
                    foreach ($options as $opt):
                      $selected = (($formData['presta'] ?? '') === $opt) ? 'selected' : '';
                  ?>
                    <option value="<?= htmlspecialchars($opt) ?>" <?= $selected ?>><?= htmlspecialchars($opt) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message <span style="color:var(--green)">*</span></label>
                <textarea id="message" name="message" rows="5"
                          placeholder="Décrivez votre projet, la superficie, vos attentes..." required><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
              </div>
              <button type="submit" class="btn-primary full">Envoyer ma demande</button>
            </form>
          </div>
          <?php endif; ?>
        </div>

        <!-- Coordonnées -->
        <div class="contact-info-col reveal">
          <h2>Nos coordonnées</h2>
          <div class="contact-items">
            <div class="contact-item">
              <div class="contact-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
              <div><strong>Adresse</strong><p>12 Allée des Jardins<br/>31000 Toulouse</p></div>
            </div>
            <div class="contact-item">
              <div class="contact-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.7A2 2 0 012.18 1h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.91 8.27A16 16 0 0015.54 16.9l1.63-1.63a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg></div>
              <div><strong>Téléphone</strong><p>05 61 00 00 00</p></div>
            </div>
            <div class="contact-item">
              <div class="contact-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
              <div><strong>Email</strong><p>contact@canopees.fr</p></div>
            </div>
            <div class="contact-item">
              <div class="contact-item-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
              <div><strong>Horaires</strong><p>Lun – Ven : 8h – 18h<br/>Sam : 9h – 12h</p></div>
            </div>
          </div>
          <div class="map-placeholder">
            <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=1.4079%2C43.5965%2C1.4679%2C43.6265&layer=mapnik&marker=43.6047%2C1.4442"
                    width="100%" height="260"
                    style="border:0;border-radius:4px;"
                    loading="lazy"
                    title="Plan d'accès Canopées"></iframe>
          </div>
        </div>

      </div>
    </div>

  </main>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="footer-top">
      <div class="container footer-grid">
        <div class="footer-brand">
          <div class="footer-logo">
            <img src="images/logo.png" alt="Canopées" style="height:52px;width:auto;" />
          </div>
          <p>Espaces verts créés avec passion depuis 2020.</p>
          <div class="footer-socials">
            <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
            <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
            <a href="#" aria-label="LinkedIn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Navigation</h4>
          <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="qui-sommes-nous.html">Qui sommes-nous</a></li>
            <li><a href="prestations.html">Prestations</a></li>
            <li><a href="tarifs.html">Tarifs</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Prestations</h4>
          <ul>
            <li><a href="prestations.html">Conception espaces verts</a></li>
            <li><a href="prestations.html">Entretien jardins</a></li>
            <li><a href="prestations.html">Taille des haies</a></li>
            <li><a href="prestations.html">Élagage & abattage</a></li>
            <li><a href="prestations.html">Valorisation déchets verts</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Contact</h4>
          <address>
            <p>12 Allée des Jardins</p>
            <p>31000 Toulouse</p>
            <p><a href="tel:0561000000">05 61 00 00 00</a></p>
            <p><a href="mailto:contact@canopees.fr">contact@canopees.fr</a></p>
          </address>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container footer-bottom-inner">
        <p>© 2026 Canopées. Tous droits réservés.</p>
        <div class="footer-legal">
          <a href="mentions-legales.html">Mentions légales</a>
          <a href="cgu.html">CGU</a>
          <a href="cgv.html">CGV</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
