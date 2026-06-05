-- =====================================================
-- PlantDoc — Schéma de base de données
-- Version 1.0 — Mai 2026
-- SGBD : MySQL 8.0+ / MariaDB 10.5+
-- Encodage : utf8mb4 (support emojis & caractères africains)
-- =====================================================

DROP DATABASE IF EXISTS plantdoc;
CREATE DATABASE plantdoc
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE plantdoc;

-- =====================================================
-- TABLE : roles
-- Rôles utilisateurs (Agriculteur, Expert, Admin)
-- =====================================================
CREATE TABLE roles (
    id              TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(30)  NOT NULL UNIQUE,
    libelle         VARCHAR(60)  NOT NULL,
    permissions     JSON         NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (nom, libelle, permissions) VALUES
('agriculteur', 'Agriculteur',  JSON_OBJECT('diagnostic', true, 'historique', true)),
('expert',      'Expert agronome', JSON_OBJECT('diagnostic', true, 'valider', true, 'forum', true)),
('admin',       'Administrateur',  JSON_OBJECT('all', true));


-- =====================================================
-- TABLE : regions
-- 10 régions du Cameroun
-- =====================================================
CREATE TABLE regions (
    id              TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(50)  NOT NULL UNIQUE,
    chef_lieu       VARCHAR(50)  NOT NULL,
    climat          VARCHAR(50)  NULL,
    latitude        DECIMAL(9,6) NULL,
    longitude       DECIMAL(9,6) NULL
) ENGINE=InnoDB;

INSERT INTO regions (nom, chef_lieu, climat, latitude, longitude) VALUES
('Adamaoua',      'Ngaoundéré', 'Tropical de savane',     7.3167, 13.5833),
('Centre',        'Yaoundé',    'Équatorial guinéen',     3.8480, 11.5021),
('Est',           'Bertoua',    'Équatorial guinéen',     4.5783, 13.6843),
('Extrême-Nord',  'Maroua',     'Sahélien',              10.5912, 14.3158),
('Littoral',      'Douala',     'Équatorial humide',      4.0511, 9.7679),
('Nord',          'Garoua',     'Soudanien',              9.3017, 13.3978),
('Nord-Ouest',    'Bamenda',    'Tropical d''altitude',   5.9631, 10.1591),
('Ouest',         'Bafoussam',  'Tropical d''altitude',   5.4781, 10.4173),
('Sud',           'Ebolowa',    'Équatorial guinéen',     2.9000, 11.1500),
('Sud-Ouest',     'Buéa',       'Équatorial humide',      4.1530, 9.2419);


-- =====================================================
-- TABLE : cultures
-- Cultures agricoles principales
-- =====================================================
CREATE TABLE cultures (
    id              SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(60)  NOT NULL UNIQUE,
    nom_scientifique VARCHAR(100) NULL,
    famille         VARCHAR(60)  NULL,
    saison          VARCHAR(60)  NULL,
    icone           VARCHAR(30)  NULL COMMENT 'Classe FontAwesome',
    couleur         VARCHAR(7)   NULL COMMENT 'Hex color',
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nom (nom)
) ENGINE=InnoDB;

INSERT INTO cultures (nom, nom_scientifique, famille, saison, icone, couleur) VALUES
('Cacao',         'Theobroma cacao',     'Malvacées',     'Toute l''année',   'fa-seedling',  '#774936'),
('Café',          'Coffea arabica',      'Rubiacées',     'Saison sèche',     'fa-mug-hot',   '#6F4E37'),
('Banane plantain','Musa paradisiaca',   'Musacées',      'Toute l''année',   'fa-leaf',      '#FFD166'),
('Manioc',        'Manihot esculenta',   'Euphorbiacées', 'Toute l''année',   'fa-carrot',    '#E9C46A'),
('Maïs',          'Zea mays',            'Poacées',       'Saison des pluies','fa-wheat-awn', '#F4A261'),
('Tomate',        'Solanum lycopersicum','Solanacées',    'Saison sèche',     'fa-apple-whole','#E63946'),
('Arachide',      'Arachis hypogaea',    'Fabacées',      'Saison des pluies','fa-circle',    '#D4A373'),
('Mil',           'Pennisetum glaucum',  'Poacées',       'Saison sèche',     'fa-wheat-awn', '#BC6C25');


-- =====================================================
-- TABLE : maladies
-- Catalogue des maladies/ravageurs
-- =====================================================
CREATE TABLE maladies (
    id                 SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom_commun         VARCHAR(100) NOT NULL,
    nom_scientifique   VARCHAR(120) NULL,
    culture_id         SMALLINT UNSIGNED NOT NULL,
    type_pathologie    ENUM('fongique','bactérienne','virale','ravageur','carence','autre') NOT NULL,
    symptomes          TEXT NOT NULL,
    causes             TEXT NULL,
    traitements_bio    TEXT NULL,
    traitements_chim   TEXT NULL,
    prevention         TEXT NULL,
    image_reference    VARCHAR(255) NULL,
    severite_typique   ENUM('legere','moderee','severe') DEFAULT 'moderee',
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_maladie_culture FOREIGN KEY (culture_id) REFERENCES cultures(id) ON DELETE RESTRICT,
    INDEX idx_culture (culture_id),
    INDEX idx_nom_commun (nom_commun),
    FULLTEXT idx_search (nom_commun, nom_scientifique, symptomes)
) ENGINE=InnoDB;

-- Données initiales (15 maladies courantes au Cameroun)
INSERT INTO maladies (nom_commun, nom_scientifique, culture_id, type_pathologie, symptomes, traitements_bio, traitements_chim, prevention, severite_typique) VALUES
('Pourriture brune du cacao', 'Phytophthora megakarya', 1, 'fongique', 'Taches brunes sur cabosses, pourriture rapide, perte totale du fruit', 'Élimination des cabosses malades, taille sanitaire, paillage', 'Bouillie bordelaise, Métalaxyl', 'Drainage, taille régulière, élimination des cabosses pourries', 'severe'),
('Swollen Shoot du cacao', 'Cocoa swollen shoot virus', 1, 'virale', 'Gonflement des rameaux, jaunissement des feuilles, déclin progressif', 'Arrachage et brûlage des plants atteints', 'Aucun (lutte vectorielle anti-cochenilles)', 'Plants certifiés, contrôle des cochenilles', 'severe'),
('Rouille orangée du caféier', 'Hemileia vastatrix', 2, 'fongique', 'Taches jaune-orange sous les feuilles, chute prématurée', 'Variétés résistantes, fertilisation équilibrée', 'Fongicides cupriques, triazoles', 'Aération, taille, fertilisation', 'severe'),
('Scolyte du caféier', 'Hypothenemus hampei', 2, 'ravageur', 'Trous dans les baies, dégâts internes, baies tombées', 'Pièges à phéromones, ramassage des baies au sol', 'Insecticides (endosulfan interdit)', 'Récolte sanitaire, élimination des résidus', 'moderee'),
('Cercosporiose du bananier', 'Mycosphaerella fijiensis', 3, 'fongique', 'Stries brunes sur feuilles, nécroses, défoliation', 'Effeuillage sanitaire, drainage', 'Fongicides systémiques (propiconazole)', 'Effeuillage régulier, espacement', 'severe'),
('Charançon du bananier', 'Cosmopolites sordidus', 3, 'ravageur', 'Galeries dans le rhizome, chute des plants', 'Pièges à pseudo-tronc, rotation', 'Insecticides du sol', 'Plants sains, hygiène plantation', 'moderee'),
('Mosaïque africaine du manioc', 'African Cassava Mosaic Virus', 4, 'virale', 'Mosaïque jaune-vert sur feuilles, déformation, rabougrissement', 'Variétés résistantes (IITA), élimination plants malades', 'Lutte contre vecteur Bemisia tabaci', 'Boutures saines certifiées', 'severe'),
('Pourriture des racines du manioc', 'Phytophthora drechsleri', 4, 'fongique', 'Pourriture brune des racines, flétrissement', 'Drainage, rotation', 'Fongicides du sol', 'Sols bien drainés, rotation 3 ans', 'moderee'),
('Rouille commune du maïs', 'Puccinia sorghi', 5, 'fongique', 'Pustules brun-rouille sur feuilles, baisse rendement', 'Variétés tolérantes, rotation', 'Fongicides triazoles', 'Rotation, semis précoce', 'moderee'),
('Foreur des tiges du maïs', 'Busseola fusca', 5, 'ravageur', 'Trous dans tiges, chute des plants, dégâts épis', 'Trichogrammes, plantes pièges', 'Insecticides systémiques', 'Destruction résidus, rotation', 'severe'),
('Mildiou de la tomate', 'Phytophthora infestans', 6, 'fongique', 'Taches brun-noir sur feuilles et fruits, moisissure blanche au revers', 'Bouillie bordelaise, décoction de prêle, retrait feuilles malades', 'Mancozèbe, métalaxyl', 'Arrosage au pied, aération, paillage', 'severe'),
('Flétrissement bactérien de la tomate', 'Ralstonia solanacearum', 6, 'bactérienne', 'Flétrissement rapide sans jaunissement, brunissement vasculaire', 'Variétés résistantes, rotation longue', 'Aucun traitement curatif', 'Sols sains, plants certifiés, rotation 4 ans', 'severe'),
('Anthracnose de l''arachide', 'Colletotrichum spp', 7, 'fongique', 'Taches brun-noir sur feuilles et gousses', 'Rotation, variétés tolérantes', 'Fongicides triazoles', 'Semis précoce, densité adaptée', 'moderee'),
('Mildiou du mil', 'Sclerospora graminicola', 8, 'fongique', 'Jaunissement, déformation des épis, hypertrophie', 'Variétés résistantes, traitement des semences', 'Métalaxyl sur semences', 'Semences saines, rotation', 'severe'),
('Carence en azote', NULL, 6, 'carence', 'Feuilles inférieures jaunissantes, croissance réduite', 'Compost, fumier, légumineuses associées', 'Urée, NPK', 'Apport de matière organique régulier', 'legere');


-- =====================================================
-- TABLE : users
-- Utilisateurs (Agriculteurs, Experts, Admins)
-- =====================================================
CREATE TABLE users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(60)  NOT NULL,
    prenom          VARCHAR(60)  NULL,
    email           VARCHAR(120) NOT NULL UNIQUE,
    telephone       VARCHAR(20)  NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role_id         TINYINT UNSIGNED NOT NULL DEFAULT 1,
    region_id       TINYINT UNSIGNED NULL,
    cultures_pref   JSON         NULL COMMENT 'IDs des cultures suivies',
    superficie_ha   DECIMAL(8,2) NULL,
    avatar          VARCHAR(255) NULL,
    langue          VARCHAR(5)   DEFAULT 'fr',
    statut          ENUM('actif','suspendu','supprime') DEFAULT 'actif',
    email_verifie   BOOLEAN      DEFAULT FALSE,
    derniere_connexion TIMESTAMP NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_role   FOREIGN KEY (role_id)   REFERENCES roles(id)   ON DELETE RESTRICT,
    CONSTRAINT fk_user_region FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    INDEX idx_region (region_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- Compte admin par défaut (mot de passe : Admin@2026 — à changer immédiatement)
INSERT INTO users (nom, prenom, email, password_hash, role_id, statut, email_verifie) VALUES
('Admin', 'PlantDoc', 'admin@plantdoc.cm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'actif', TRUE);
-- Note : ce hash correspond à "password" — à régénérer en production


-- =====================================================
-- TABLE : diagnostics
-- Historique des diagnostics IA
-- =====================================================
CREATE TABLE diagnostics (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED NOT NULL,
    maladie_id          SMALLINT UNSIGNED NULL,
    photo_path          VARCHAR(255) NOT NULL,
    photo_thumbnail     VARCHAR(255) NULL,
    maladie_detectee    VARCHAR(150) NULL COMMENT 'Texte brut IA si maladie non trouvée en base',
    nom_scientifique    VARCHAR(150) NULL,
    confiance           DECIMAL(5,2) NULL COMMENT 'Pourcentage 0-100',
    gravite             ENUM('legere','moderee','severe','inconnue') DEFAULT 'inconnue',
    plante_saine        BOOLEAN DEFAULT FALSE,
    traitement_propose  TEXT NULL,
    prevention_proposee TEXT NULL,
    statut              ENUM('en_attente','analyse','valide','a_verifier','rejete','archive') DEFAULT 'en_attente',
    validateur_id       INT UNSIGNED NULL,
    commentaire_expert  TEXT NULL,
    latitude            DECIMAL(9,6) NULL,
    longitude           DECIMAL(9,6) NULL,
    region_id           TINYINT UNSIGNED NULL,
    ia_raw_response     JSON NULL COMMENT 'Réponse brute IA pour audit',
    ia_model            VARCHAR(60) NULL,
    ia_duration_ms      INT UNSIGNED NULL,
    partage_count       INT UNSIGNED DEFAULT 0,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    validated_at        TIMESTAMP NULL,
    CONSTRAINT fk_diag_user       FOREIGN KEY (user_id)       REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_diag_maladie    FOREIGN KEY (maladie_id)    REFERENCES maladies(id) ON DELETE SET NULL,
    CONSTRAINT fk_diag_validateur FOREIGN KEY (validateur_id) REFERENCES users(id)    ON DELETE SET NULL,
    CONSTRAINT fk_diag_region     FOREIGN KEY (region_id)     REFERENCES regions(id)  ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_maladie (maladie_id),
    INDEX idx_statut (statut),
    INDEX idx_created (created_at DESC),
    INDEX idx_region (region_id),
    INDEX idx_geo (latitude, longitude)
) ENGINE=InnoDB;


-- =====================================================
-- TABLE : feedbacks
-- Avis utilisateur sur les diagnostics
-- =====================================================
CREATE TABLE feedbacks (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diagnostic_id   BIGINT UNSIGNED NOT NULL,
    user_id         INT UNSIGNED NOT NULL,
    est_correct     BOOLEAN NOT NULL,
    note            TINYINT UNSIGNED NULL COMMENT '1-5',
    commentaire     TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fb_diag FOREIGN KEY (diagnostic_id) REFERENCES diagnostics(id) ON DELETE CASCADE,
    CONSTRAINT fk_fb_user FOREIGN KEY (user_id)       REFERENCES users(id)       ON DELETE CASCADE,
    UNIQUE KEY uq_diag_user (diagnostic_id, user_id),
    INDEX idx_correct (est_correct)
) ENGINE=InnoDB;


-- =====================================================
-- TABLE : rate_limits
-- Limitation de requêtes (anti-abus)
-- =====================================================
CREATE TABLE rate_limits (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cle             VARCHAR(120) NOT NULL,
    action          VARCHAR(40)  NOT NULL,
    count           INT UNSIGNED DEFAULT 1,
    window_start    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cle_action (cle, action),
    INDEX idx_window (window_start)
) ENGINE=InnoDB;


-- =====================================================
-- TABLE : sessions
-- Gestion sessions (sécurisée)
-- =====================================================
CREATE TABLE sessions (
    id              VARCHAR(128) PRIMARY KEY,
    user_id         INT UNSIGNED NULL,
    ip_address      VARCHAR(45) NULL,
    user_agent      VARCHAR(255) NULL,
    payload         TEXT NULL,
    last_activity   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_session_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_activity (last_activity)
) ENGINE=InnoDB;


-- =====================================================
-- TABLE : logs
-- Journal des actions importantes (audit)
-- =====================================================
CREATE TABLE activity_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED NULL,
    action          VARCHAR(60) NOT NULL,
    cible_type      VARCHAR(40) NULL,
    cible_id        BIGINT UNSIGNED NULL,
    details         JSON NULL,
    ip_address      VARCHAR(45) NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at DESC)
) ENGINE=InnoDB;


-- =====================================================
-- VUES utiles
-- =====================================================

-- Statistiques par utilisateur
CREATE OR REPLACE VIEW v_user_stats AS
SELECT
    u.id,
    u.nom,
    u.prenom,
    u.email,
    COUNT(d.id) AS total_diagnostics,
    SUM(CASE WHEN d.gravite = 'severe' THEN 1 ELSE 0 END) AS diag_severes,
    SUM(CASE WHEN d.plante_saine = TRUE THEN 1 ELSE 0 END) AS plantes_saines,
    MAX(d.created_at) AS dernier_diagnostic
FROM users u
LEFT JOIN diagnostics d ON d.user_id = u.id
GROUP BY u.id;

-- Top maladies détectées
CREATE OR REPLACE VIEW v_top_maladies AS
SELECT
    m.id,
    m.nom_commun,
    c.nom AS culture,
    COUNT(d.id) AS occurrences,
    AVG(d.confiance) AS confiance_moyenne
FROM maladies m
LEFT JOIN diagnostics d ON d.maladie_id = m.id
LEFT JOIN cultures c ON m.culture_id = c.id
GROUP BY m.id
ORDER BY occurrences DESC;


-- =====================================================
-- FIN DU SCHÉMA
-- =====================================================
