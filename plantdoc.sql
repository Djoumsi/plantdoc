-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 05 juin 2026 à 16:40
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `plantdoc`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED DEFAULT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cible_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cible_id` bigint UNSIGNED DEFAULT NULL,
  `details` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cultures`
--

DROP TABLE IF EXISTS `cultures`;
CREATE TABLE IF NOT EXISTS `cultures` (
  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_scientifique` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `famille` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saison` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe FontAwesome',
  `couleur` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hex color',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  KEY `idx_nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cultures`
--

INSERT INTO `cultures` (`id`, `nom`, `nom_scientifique`, `famille`, `saison`, `icone`, `couleur`, `created_at`) VALUES
(1, 'Cacao', 'Theobroma cacao', 'Malvacées', 'Toute l\'année', 'fa-seedling', '#774936', '2026-05-27 09:35:26'),
(2, 'Café', 'Coffea arabica', 'Rubiacées', 'Saison sèche', 'fa-mug-hot', '#6F4E37', '2026-05-27 09:35:26'),
(3, 'Banane plantain', 'Musa paradisiaca', 'Musacées', 'Toute l\'année', 'fa-leaf', '#FFD166', '2026-05-27 09:35:26'),
(4, 'Manioc', 'Manihot esculenta', 'Euphorbiacées', 'Toute l\'année', 'fa-carrot', '#E9C46A', '2026-05-27 09:35:26'),
(5, 'Maïs', 'Zea mays', 'Poacées', 'Saison des pluies', 'fa-wheat-awn', '#F4A261', '2026-05-27 09:35:26'),
(6, 'Tomate', 'Solanum lycopersicum', 'Solanacées', 'Saison sèche', 'fa-apple-whole', '#E63946', '2026-05-27 09:35:26'),
(7, 'Arachide', 'Arachis hypogaea', 'Fabacées', 'Saison des pluies', 'fa-circle', '#D4A373', '2026-05-27 09:35:26'),
(8, 'Mil', 'Pennisetum glaucum', 'Poacées', 'Saison sèche', 'fa-wheat-awn', '#BC6C25', '2026-05-27 09:35:26');

-- --------------------------------------------------------

--
-- Structure de la table `diagnostics`
--

DROP TABLE IF EXISTS `diagnostics`;
CREATE TABLE IF NOT EXISTS `diagnostics` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `maladie_id` smallint UNSIGNED DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo_thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `maladie_detectee` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Texte brut IA si maladie non trouvée en base',
  `nom_scientifique` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confiance` decimal(5,2) DEFAULT NULL COMMENT 'Pourcentage 0-100',
  `gravite` enum('legere','moderee','severe','inconnue') COLLATE utf8mb4_unicode_ci DEFAULT 'inconnue',
  `plante_saine` tinyint(1) DEFAULT '0',
  `traitement_propose` text COLLATE utf8mb4_unicode_ci,
  `prevention_proposee` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('en_attente','analyse','valide','a_verifier','rejete','archive') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente',
  `validateur_id` int UNSIGNED DEFAULT NULL,
  `commentaire_expert` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `region_id` tinyint UNSIGNED DEFAULT NULL,
  `ia_raw_response` json DEFAULT NULL COMMENT 'Réponse brute IA pour audit',
  `ia_model` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ia_duration_ms` int UNSIGNED DEFAULT NULL,
  `partage_count` int UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `validated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_diag_validateur` (`validateur_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_maladie` (`maladie_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_created` (`created_at` DESC),
  KEY `idx_region` (`region_id`),
  KEY `idx_geo` (`latitude`,`longitude`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `diagnostics`
--

INSERT INTO `diagnostics` (`id`, `user_id`, `maladie_id`, `photo_path`, `photo_thumbnail`, `maladie_detectee`, `nom_scientifique`, `confiance`, `gravite`, `plante_saine`, `traitement_propose`, `prevention_proposee`, `statut`, `validateur_id`, `commentaire_expert`, `latitude`, `longitude`, `region_id`, `ia_raw_response`, `ia_model`, `ia_duration_ms`, `partage_count`, `created_at`, `validated_at`) VALUES
(1, 2, 1, 'uploads/diagnostics/0c9fa03e3750161e810ed057f842bffd.jpg', NULL, 'Pourriture brune du cacao', 'Phytophthora megakarya', 92.00, 'severe', 0, 'Élimination des cabosses, fongicide cuprique', 'Drainage, taille', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"gravite\": \"severe\", \"confiance\": 92, \"prevention\": \"Drainage, taille\", \"traitement\": \"Élimination des cabosses, fongicide cuprique\", \"plante_saine\": false, \"culture_identifiee\": \"Cacao\", \"maladie_nom_commun\": \"Pourriture brune du cacao\", \"symptomes_observes\": \"Cabosses brunâtres\", \"maladie_nom_scientifique\": \"Phytophthora megakarya\"}', 'simulation', 800, 0, '2026-05-29 11:43:50', NULL),
(2, 2, 11, 'uploads/diagnostics/460283d80936e6212cdfdfd880268577.png', NULL, 'Mildiou de la tomate', 'Phytophthora infestans', 87.00, 'severe', 0, 'Bouillie bordelaise, retrait des feuilles atteintes', 'Arrosage au pied, paillage', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"gravite\": \"severe\", \"confiance\": 87, \"prevention\": \"Arrosage au pied, paillage\", \"traitement\": \"Bouillie bordelaise, retrait des feuilles atteintes\", \"plante_saine\": false, \"culture_identifiee\": \"Tomate\", \"maladie_nom_commun\": \"Mildiou de la tomate\", \"symptomes_observes\": \"Taches brun-noir, moisissure blanche au revers\", \"maladie_nom_scientifique\": \"Phytophthora infestans\"}', 'simulation', 800, 0, '2026-05-29 12:14:02', NULL),
(3, 2, 1, 'uploads/diagnostics/2328db03170084ee17f62075c6a8a870.jpg', NULL, 'Pourriture brune du cacao', 'Phytophthora megakarya', 92.00, 'severe', 0, 'Élimination des cabosses, fongicide cuprique', 'Drainage, taille', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"gravite\": \"severe\", \"confiance\": 92, \"prevention\": \"Drainage, taille\", \"traitement\": \"Élimination des cabosses, fongicide cuprique\", \"plante_saine\": false, \"culture_identifiee\": \"Cacao\", \"maladie_nom_commun\": \"Pourriture brune du cacao\", \"symptomes_observes\": \"Cabosses brunâtres\", \"maladie_nom_scientifique\": \"Phytophthora megakarya\"}', 'simulation', 800, 0, '2026-05-29 12:31:05', NULL),
(4, 2, NULL, 'uploads/diagnostics/a0aead60171fb2c7623488143dcf74b3.jpg', NULL, 'Pourriture des cabosses (Frostkrankheit)', 'Phytophthora megakarya', 85.00, 'severe', 0, 'Traitements biologiques : Trichoderma harzianum en pulvérisation, Bacillus subtilis. Traitements chimiques : Métaxyl + Mancozèbe en fongicide de contact, Cuivre (Bouillie bordelaise) en préventif. Élimination immédiate des cabosses infectées et brûlage. Application de fongicides tous les 10-15 jours en période humide.', 'Amélioration du drainage des parcelles, élagage pour aération du cacaoyer, paillage pour limiter éclaboussures du sol, rotation des fongicides, collecte et destruction des cabosses malades, traitement des blessures à la chaux, maintien d\'une ombrage modéré (25-30%), sélection de variétés tolérantes.', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"id\": \"msg_01LZo76ExY1MvS3id3cWPgSH\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 525, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 407, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": false,\\n  \\\"culture_identifiee\\\": \\\"Cacao\\\",\\n  \\\"maladie_nom_commun\\\": \\\"Pourriture des cabosses (Frostkrankheit)\\\",\\n  \\\"maladie_nom_scientifique\\\": \\\"Phytophthora megakarya\\\",\\n  \\\"confiance\\\": 85,\\n  \\\"gravite\\\": \\\"severe\\\",\\n  \\\"symptomes_observes\\\": \\\"Cabosse présentant une pourriture brunâtre à noirâtre avec taches blanches (mycélium) sur la partie supérieure. Pourriture humide et progressive caractéristique de Phytophthora. La partie basale rouge vif indique une nécrose tissulaire avancée.\\\",\\n  \\\"traitement\\\": \\\"Traitements biologiques : Trichoderma harzianum en pulvérisation, Bacillus subtilis. Traitements chimiques : Métaxyl + Mancozèbe en fongicide de contact, Cuivre (Bouillie bordelaise) en préventif. Élimination immédiate des cabosses infectées et brûlage. Application de fongicides tous les 10-15 jours en période humide.\\\",\\n  \\\"prevention\\\": \\\"Amélioration du drainage des parcelles, élagage pour aération du cacaoyer, paillage pour limiter éclaboussures du sol, rotation des fongicides, collecte et destruction des cabosses malades, traitement des blessures à la chaux, maintien d\'une ombrage modéré (25-30%), sélection de variétés tolérantes.\\\"\\n}\\n```\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 6038, 0, '2026-05-29 12:37:19', NULL),
(5, 2, 11, 'uploads/diagnostics/6cca51ac5212973b8492de0837ea2ab6.jpg', NULL, 'mildiou de la tomate', 'Phytophthora infestans', 72.00, 'severe', 0, 'Traitements biologiques : pulvérisation de bouillie bordelaise (1%) ou soufre micronisé; application de Bacillus subtilis ou Trichoderma. Traitements chimiques si nécessaire : fongicides à base de mancozèbe, chlorothalonil ou métallaxyl+mancozèbe en alternance. Élimination immédiate des parties affectées.', 'Rotation des cultures (3-4 ans), élimination des résidus infectés, amélioration de la circulation de l\'air (espacement des plants, élagage), irrigation au goutte-à-goutte sans mouiller le feuillage, utilisation de variétés résistantes, application préventive hebdomadaire de bouillie bordelaise en saison humide', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"id\": \"msg_01HEv7ktB7qsjPS64Chjfuuw\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 1881, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 380, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": false,\\n  \\\"culture_identifiee\\\": \\\"tomate\\\",\\n  \\\"maladie_nom_commun\\\": \\\"mildiou de la tomate\\\",\\n  \\\"maladie_nom_scientifique\\\": \\\"Phytophthora infestans\\\",\\n  \\\"confiance\\\": 72,\\n  \\\"gravite\\\": \\\"severe\\\",\\n  \\\"symptomes_observes\\\": \\\"Nécrose et dessiccation des tiges, noircissement des calices et pédoncules, fruits présentant des taches brunes enfoncées avec halo aqueux, feuillage desséché, présence de lésions nécrotiques progressives sur les rameaux\\\",\\n  \\\"traitement\\\": \\\"Traitements biologiques : pulvérisation de bouillie bordelaise (1%) ou soufre micronisé; application de Bacillus subtilis ou Trichoderma. Traitements chimiques si nécessaire : fongicides à base de mancozèbe, chlorothalonil ou métallaxyl+mancozèbe en alternance. Élimination immédiate des parties affectées.\\\",\\n  \\\"prevention\\\": \\\"Rotation des cultures (3-4 ans), élimination des résidus infectés, amélioration de la circulation de l\'air (espacement des plants, élagage), irrigation au goutte-à-goutte sans mouiller le feuillage, utilisation de variétés résistantes, application préventive hebdomadaire de bouillie bordelaise en saison humide\\\"\\n}\\n```\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 10484, 0, '2026-05-29 12:46:57', NULL),
(6, 2, 11, 'uploads/diagnostics/7667305e90ee8f0798e6ad05b5138e41.jpg', NULL, 'mildiou de la tomate', 'Phytophthora infestans', 75.00, 'severe', 0, 'Traitements biologiques: pulvérisation de bouillie bordelaise (4%) ou sulfate de cuivre (0.5%) à intervalle de 7-10 jours; utilisation de Bacillus subtilis ou Trichoderma. Traitements chimiques: mancozèbe, chlorothalonil ou cymoxanil + mancozèbe en cas d\'infection avancée. Élimination immédiate des parties atteintes.', 'Rotation des cultures (3 ans minimum), élimination des résidus de récolte, amélioration du drainage et réduction de l\'humidité ambiante, espacement adéquat des plants pour aération, irrigation à la base (éviter le feuillage), semences certifiées saines, traitement préventif en saison des pluies', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"id\": \"msg_017iuUpM9jMR2UoheeHjfMq9\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 1881, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 370, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": false,\\n  \\\"culture_identifiee\\\": \\\"tomate\\\",\\n  \\\"maladie_nom_commun\\\": \\\"mildiou de la tomate\\\",\\n  \\\"maladie_nom_scientifique\\\": \\\"Phytophthora infestans\\\",\\n  \\\"confiance\\\": 75,\\n  \\\"gravite\\\": \\\"severe\\\",\\n  \\\"symptomes_observes\\\": \\\"Nécrose foliaire progressive, dessiccation des tiges et calices, fruits présentant des lésions brunes à noires, flétrissement général de la plante, présence de parties mortes importantes\\\",\\n  \\\"traitement\\\": \\\"Traitements biologiques: pulvérisation de bouillie bordelaise (4%) ou sulfate de cuivre (0.5%) à intervalle de 7-10 jours; utilisation de Bacillus subtilis ou Trichoderma. Traitements chimiques: mancozèbe, chlorothalonil ou cymoxanil + mancozèbe en cas d\'infection avancée. Élimination immédiate des parties atteintes.\\\",\\n  \\\"prevention\\\": \\\"Rotation des cultures (3 ans minimum), élimination des résidus de récolte, amélioration du drainage et réduction de l\'humidité ambiante, espacement adéquat des plants pour aération, irrigation à la base (éviter le feuillage), semences certifiées saines, traitement préventif en saison des pluies\\\"\\n}\\n```\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 11027, 0, '2026-05-29 12:47:08', NULL),
(7, 2, 11, 'uploads/diagnostics/a35c4d9263883e6da0e894c553497f8a.jpg', NULL, 'mildiou de la tomate', 'Phytophthora infestans', 75.00, 'severe', 0, 'Biologique : pulvérisation de bouillie bordelaise (sulfate de cuivre) à 1%, application de Bacillus subtilis ou Trichoderma. Chimique : métaxynil, mancozèbe ou chlorothalonil en dernier recours. Élimination des parties atteintes. Chimique : mancozèbe 80% ou cymoxanil', 'Améliorer la ventilation (écartement des plants), éviter l\'arrosage foliaire, rotation des cultures (3-4 ans), destruction des résidus infestés, utiliser des variétés résistantes, maintenir l\'humidité relative basse en serre', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"id\": \"msg_01U5QEc26dWnASvH34psoxt9\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 1881, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 348, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": false,\\n  \\\"culture_identifiee\\\": \\\"tomate\\\",\\n  \\\"maladie_nom_commun\\\": \\\"mildiou de la tomate\\\",\\n  \\\"maladie_nom_scientifique\\\": \\\"Phytophthora infestans\\\",\\n  \\\"confiance\\\": 75,\\n  \\\"gravite\\\": \\\"severe\\\",\\n  \\\"symptomes_observes\\\": \\\"Nécrose et dessèchement progressif des tiges, feuillage dégradé, fruits à différents stades de maturation avec des lésions sombres et desséchées, présence de brunissement des calices et des pédoncules floraux\\\",\\n  \\\"traitement\\\": \\\"Biologique : pulvérisation de bouillie bordelaise (sulfate de cuivre) à 1%, application de Bacillus subtilis ou Trichoderma. Chimique : métaxynil, mancozèbe ou chlorothalonil en dernier recours. Élimination des parties atteintes. Chimique : mancozèbe 80% ou cymoxanil\\\",\\n  \\\"prevention\\\": \\\"Améliorer la ventilation (écartement des plants), éviter l\'arrosage foliaire, rotation des cultures (3-4 ans), destruction des résidus infestés, utiliser des variétés résistantes, maintenir l\'humidité relative basse en serre\\\"\\n}\\n```\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 13330, 0, '2026-05-29 12:47:22', NULL),
(8, 2, 11, 'uploads/diagnostics/31f6431f161793345bf920615c041397.jpg', NULL, 'mildiou de la tomate', 'Phytophthora infestans', 75.00, 'severe', 0, 'Traitements biologiques : pulvérisation de bouillie bordelaise (3%) ou de soufre micronisé en prévention ; Bacillus subtilis ou Trichoderma pour réduire l\'inoculum. Traitements chimiques : fongicides à base de mancozèbe, chlorothalonil ou propamocarbe en cas de forte pression ; alternance des matières actives pour éviter la résistance', 'Éliminer les débris de récolte infectés ; améliorer la circulation de l\'air par l\'élagage ; éviter l\'excès d\'humidité et les arrosages foliaires ; utiliser des variétés tolérantes ; rotation des cultures ; désinfection des outils et du matériel de travail', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"id\": \"msg_014xysBbXJyC8NZqtjfBaGJq\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 1881, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 376, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": false,\\n  \\\"culture_identifiee\\\": \\\"tomate\\\",\\n  \\\"maladie_nom_commun\\\": \\\"mildiou de la tomate\\\",\\n  \\\"maladie_nom_scientifique\\\": \\\"Phytophthora infestans\\\",\\n  \\\"confiance\\\": 75,\\n  \\\"gravite\\\": \\\"severe\\\",\\n  \\\"symptomes_observes\\\": \\\"Nécrose des tiges et des calices, dessèchement progressif des organes floraux et fructifères, brunissement des sepales et pédoncules, dépérissement des grappes, présence de lésions noires caractéristiques sur les structures végétales\\\",\\n  \\\"traitement\\\": \\\"Traitements biologiques : pulvérisation de bouillie bordelaise (3%) ou de soufre micronisé en prévention ; Bacillus subtilis ou Trichoderma pour réduire l\'inoculum. Traitements chimiques : fongicides à base de mancozèbe, chlorothalonil ou propamocarbe en cas de forte pression ; alternance des matières actives pour éviter la résistance\\\",\\n  \\\"prevention\\\": \\\"Éliminer les débris de récolte infectés ; améliorer la circulation de l\'air par l\'élagage ; éviter l\'excès d\'humidité et les arrosages foliaires ; utiliser des variétés tolérantes ; rotation des cultures ; désinfection des outils et du matériel de travail\\\"\\n}\\n```\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 15500, 0, '2026-05-29 12:47:37', NULL),
(9, 2, 11, 'uploads/diagnostics/a459a4ebac5df41b042f86b3ffd52424.webp', NULL, 'Mildiou de la tomate', 'Phytophthora infestans', 78.00, 'severe', 0, 'Traitements biologiques : Pulvérisation de bouillie bordelaise (3-4%) ou cuivre oxychlorure 50% à 10-15 jours d\'intervalle. Extraits de plantes (neem, piment). Traitements chimiques (si biologique insuffisant) : Mancozèbe, Chlorothalonil ou Cymoxanil en alternance pour éviter résistances. Destruction des feuilles gravement atteintes.', 'Éliminer débris végétaux infectés. Assurer bonne aération et drainage du sol. Éviter arrosages par aspersion et mouillage foliaire. Rotation de cultures (3-4 ans). Utiliser variétés résistantes. Espacer les plants. Désinfecter outils de travail. Surveiller météorologie (humidité >85% + températures 15-25°C = conditions favorables)', 'valide', NULL, NULL, NULL, NULL, NULL, '{\"id\": \"msg_01PiJiNxJ9L2NTwsVm8A3mEn\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 463, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 393, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": false,\\n  \\\"culture_identifiee\\\": \\\"Tomate\\\",\\n  \\\"maladie_nom_commun\\\": \\\"Mildiou de la tomate\\\",\\n  \\\"maladie_nom_scientifique\\\": \\\"Phytophthora infestans\\\",\\n  \\\"confiance\\\": 78,\\n  \\\"gravite\\\": \\\"severe\\\",\\n  \\\"symptomes_observes\\\": \\\"Nécrose des feuilles avec bordures brunes/jaunes, dessèchement progressif du feuillage, tiges affectées, aspect général de dépérissement foliaire avancé\\\",\\n  \\\"traitement\\\": \\\"Traitements biologiques : Pulvérisation de bouillie bordelaise (3-4%) ou cuivre oxychlorure 50% à 10-15 jours d\'intervalle. Extraits de plantes (neem, piment). Traitements chimiques (si biologique insuffisant) : Mancozèbe, Chlorothalonil ou Cymoxanil en alternance pour éviter résistances. Destruction des feuilles gravement atteintes.\\\",\\n  \\\"prevention\\\": \\\"Éliminer débris végétaux infectés. Assurer bonne aération et drainage du sol. Éviter arrosages par aspersion et mouillage foliaire. Rotation de cultures (3-4 ans). Utiliser variétés résistantes. Espacer les plants. Désinfecter outils de travail. Surveiller météorologie (humidité >85% + températures 15-25°C = conditions favorables)\\\"\\n}\\n```\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 5234, 0, '2026-05-29 20:50:07', NULL),
(10, 2, NULL, 'uploads/diagnostics/1e7172cfd7a28c58239bc6af8388faf4.webp', NULL, NULL, NULL, 0.00, 'inconnue', 1, NULL, NULL, 'valide', 1, NULL, NULL, NULL, NULL, '{\"id\": \"msg_01TrRs8xsbzRvDLDpuPydqCz\", \"role\": \"assistant\", \"type\": \"message\", \"model\": \"claude-haiku-4-5-20251001\", \"usage\": {\"input_tokens\": 473, \"service_tier\": \"standard\", \"inference_geo\": \"not_available\", \"output_tokens\": 198, \"cache_creation\": {\"ephemeral_1h_input_tokens\": 0, \"ephemeral_5m_input_tokens\": 0}, \"cache_read_input_tokens\": 0, \"cache_creation_input_tokens\": 0}, \"content\": [{\"text\": \"```json\\n{\\n  \\\"plante_saine\\\": true,\\n  \\\"culture_identifiee\\\": null,\\n  \\\"maladie_nom_commun\\\": null,\\n  \\\"maladie_nom_scientifique\\\": null,\\n  \\\"confiance\\\": 0,\\n  \\\"gravite\\\": null,\\n  \\\"symptomes_observes\\\": \\\"Aucune plante présente dans cette image. L\'image montre une personne flottant dans une piscine d\'eau chlorée.\\\",\\n  \\\"traitement\\\": null,\\n  \\\"prevention\\\": null\\n}\\n```\\n\\n**Note** : Cette image ne contient pas de culture agricole. Je suis spécialisé en phytopathologie des cultures camerounaises. Veuillez fournir une image d\'une plante potentiellement malade pour une analyse appropriée.\", \"type\": \"text\"}], \"stop_reason\": \"end_turn\", \"stop_details\": null, \"stop_sequence\": null}', 'claude-haiku-4-5-20251001', 4185, 0, '2026-05-29 20:51:04', '2026-06-04 07:55:34');

-- --------------------------------------------------------

--
-- Structure de la table `feedbacks`
--

DROP TABLE IF EXISTS `feedbacks`;
CREATE TABLE IF NOT EXISTS `feedbacks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `diagnostic_id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `est_correct` tinyint(1) NOT NULL,
  `note` tinyint UNSIGNED DEFAULT NULL COMMENT '1-5',
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_diag_user` (`diagnostic_id`,`user_id`),
  KEY `fk_fb_user` (`user_id`),
  KEY `idx_correct` (`est_correct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `maladies`
--

DROP TABLE IF EXISTS `maladies`;
CREATE TABLE IF NOT EXISTS `maladies` (
  `id` smallint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_commun` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_scientifique` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `culture_id` smallint UNSIGNED NOT NULL,
  `type_pathologie` enum('fongique','bactérienne','virale','ravageur','carence','autre') COLLATE utf8mb4_unicode_ci NOT NULL,
  `symptomes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `causes` text COLLATE utf8mb4_unicode_ci,
  `traitements_bio` text COLLATE utf8mb4_unicode_ci,
  `traitements_chim` text COLLATE utf8mb4_unicode_ci,
  `prevention` text COLLATE utf8mb4_unicode_ci,
  `image_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `severite_typique` enum('legere','moderee','severe') COLLATE utf8mb4_unicode_ci DEFAULT 'moderee',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_culture` (`culture_id`),
  KEY `idx_nom_commun` (`nom_commun`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `maladies`
--

INSERT INTO `maladies` (`id`, `nom_commun`, `nom_scientifique`, `culture_id`, `type_pathologie`, `symptomes`, `causes`, `traitements_bio`, `traitements_chim`, `prevention`, `image_reference`, `severite_typique`, `created_at`, `updated_at`) VALUES
(1, 'Pourriture brune du cacao', 'Phytophthora megakarya', 1, 'fongique', 'Taches brunes sur cabosses, pourriture rapide, perte totale du fruit', NULL, 'Élimination des cabosses malades, taille sanitaire, paillage', 'Bouillie bordelaise, Métalaxyl', 'Drainage, taille régulière, élimination des cabosses pourries', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(2, 'Swollen Shoot du cacao', 'Cocoa swollen shoot virus', 1, 'virale', 'Gonflement des rameaux, jaunissement des feuilles, déclin progressif', NULL, 'Arrachage et brûlage des plants atteints', 'Aucun (lutte vectorielle anti-cochenilles)', 'Plants certifiés, contrôle des cochenilles', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(3, 'Rouille orangée du caféier', 'Hemileia vastatrix', 2, 'fongique', 'Taches jaune-orange sous les feuilles, chute prématurée', NULL, 'Variétés résistantes, fertilisation équilibrée', 'Fongicides cupriques, triazoles', 'Aération, taille, fertilisation', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(4, 'Scolyte du caféier', 'Hypothenemus hampei', 2, 'ravageur', 'Trous dans les baies, dégâts internes, baies tombées', NULL, 'Pièges à phéromones, ramassage des baies au sol', 'Insecticides (endosulfan interdit)', 'Récolte sanitaire, élimination des résidus', NULL, 'moderee', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(5, 'Cercosporiose du bananier', 'Mycosphaerella fijiensis', 3, 'fongique', 'Stries brunes sur feuilles, nécroses, défoliation', NULL, 'Effeuillage sanitaire, drainage', 'Fongicides systémiques (propiconazole)', 'Effeuillage régulier, espacement', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(6, 'Charançon du bananier', 'Cosmopolites sordidus', 3, 'ravageur', 'Galeries dans le rhizome, chute des plants', NULL, 'Pièges à pseudo-tronc, rotation', 'Insecticides du sol', 'Plants sains, hygiène plantation', NULL, 'moderee', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(7, 'Mosaïque africaine du manioc', 'African Cassava Mosaic Virus', 4, 'virale', 'Mosaïque jaune-vert sur feuilles, déformation, rabougrissement', NULL, 'Variétés résistantes (IITA), élimination plants malades', 'Lutte contre vecteur Bemisia tabaci', 'Boutures saines certifiées', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(8, 'Pourriture des racines du manioc', 'Phytophthora drechsleri', 4, 'fongique', 'Pourriture brune des racines, flétrissement', NULL, 'Drainage, rotation', 'Fongicides du sol', 'Sols bien drainés, rotation 3 ans', NULL, 'moderee', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(9, 'Rouille commune du maïs', 'Puccinia sorghi', 5, 'fongique', 'Pustules brun-rouille sur feuilles, baisse rendement', NULL, 'Variétés tolérantes, rotation', 'Fongicides triazoles', 'Rotation, semis précoce', NULL, 'moderee', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(10, 'Foreur des tiges du maïs', 'Busseola fusca', 5, 'ravageur', 'Trous dans tiges, chute des plants, dégâts épis', NULL, 'Trichogrammes, plantes pièges', 'Insecticides systémiques', 'Destruction résidus, rotation', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(11, 'Mildiou de la tomate', 'Phytophthora infestans', 6, 'fongique', 'Taches brun-noir sur feuilles et fruits, moisissure blanche au revers', NULL, 'Bouillie bordelaise, décoction de prêle, retrait feuilles malades', 'Mancozèbe, métalaxyl', 'Arrosage au pied, aération, paillage', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(12, 'Flétrissement bactérien de la tomate', 'Ralstonia solanacearum', 6, 'bactérienne', 'Flétrissement rapide sans jaunissement, brunissement vasculaire', NULL, 'Variétés résistantes, rotation longue', 'Aucun traitement curatif', 'Sols sains, plants certifiés, rotation 4 ans', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(13, 'Anthracnose de l\'arachide', 'Colletotrichum spp', 7, 'fongique', 'Taches brun-noir sur feuilles et gousses', NULL, 'Rotation, variétés tolérantes', 'Fongicides triazoles', 'Semis précoce, densité adaptée', NULL, 'moderee', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(14, 'Mildiou du mil', 'Sclerospora graminicola', 8, 'fongique', 'Jaunissement, déformation des épis, hypertrophie', NULL, 'Variétés résistantes, traitement des semences', 'Métalaxyl sur semences', 'Semences saines, rotation', NULL, 'severe', '2026-05-27 09:35:29', '2026-05-27 09:35:29'),
(15, 'Carence en azote', NULL, 6, 'carence', 'Feuilles inférieures jaunissantes, croissance réduite', NULL, 'Compost, fumier, légumineuses associées', 'Urée, NPK', 'Apport de matière organique régulier', NULL, 'legere', '2026-05-27 09:35:29', '2026-05-27 09:35:29');

-- --------------------------------------------------------

--
-- Structure de la table `rate_limits`
--

DROP TABLE IF EXISTS `rate_limits`;
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cle` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int UNSIGNED DEFAULT '1',
  `window_start` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cle_action` (`cle`,`action`),
  KEY `idx_window` (`window_start`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `cle`, `action`, `count`, `window_start`) VALUES
(31, '::1:test@plantdoc.cm', 'login', 1, '2026-06-05 15:23:19'),
(32, '::1:admin@plantdoc.cm', 'login', 1, '2026-06-05 15:23:37');

-- --------------------------------------------------------

--
-- Structure de la table `regions`
--

DROP TABLE IF EXISTS `regions`;
CREATE TABLE IF NOT EXISTS `regions` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chef_lieu` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `climat` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `regions`
--

INSERT INTO `regions` (`id`, `nom`, `chef_lieu`, `climat`, `latitude`, `longitude`) VALUES
(1, 'Adamaoua', 'Ngaoundéré', 'Tropical de savane', 7.316700, 13.583300),
(2, 'Centre', 'Yaoundé', 'Équatorial guinéen', 3.848000, 11.502100),
(3, 'Est', 'Bertoua', 'Équatorial guinéen', 4.578300, 13.684300),
(4, 'Extrême-Nord', 'Maroua', 'Sahélien', 10.591200, 14.315800),
(5, 'Littoral', 'Douala', 'Équatorial humide', 4.051100, 9.767900),
(6, 'Nord', 'Garoua', 'Soudanien', 9.301700, 13.397800),
(7, 'Nord-Ouest', 'Bamenda', 'Tropical d\'altitude', 5.963100, 10.159100),
(8, 'Ouest', 'Bafoussam', 'Tropical d\'altitude', 5.478100, 10.417300),
(9, 'Sud', 'Ebolowa', 'Équatorial guinéen', 2.900000, 11.150000),
(10, 'Sud-Ouest', 'Buéa', 'Équatorial humide', 4.153000, 9.241900);

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `libelle`, `permissions`, `created_at`) VALUES
(1, 'agriculteur', 'Agriculteur', '{\"diagnostic\": true, \"historique\": true}', '2026-05-27 09:35:24'),
(2, 'expert', 'Expert agronome', '{\"forum\": true, \"valider\": true, \"diagnostic\": true}', '2026-05-27 09:35:24'),
(3, 'admin', 'Administrateur', '{\"all\": true}', '2026-05-27 09:35:24');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci,
  `last_activity` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `region_id` tinyint UNSIGNED DEFAULT NULL,
  `cultures_pref` json DEFAULT NULL COMMENT 'IDs des cultures suivies',
  `superficie_ha` decimal(8,2) DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `langue` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT 'fr',
  `statut` enum('actif','suspendu','supprime') COLLATE utf8mb4_unicode_ci DEFAULT 'actif',
  `email_verifie` tinyint(1) DEFAULT '0',
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role_id`),
  KEY `idx_region` (`region_id`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `telephone`, `password_hash`, `role_id`, `region_id`, `cultures_pref`, `superficie_ha`, `avatar`, `langue`, `statut`, `email_verifie`, `derniere_connexion`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'PlantDoc', 'admin@plantdoc.cm', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, NULL, NULL, NULL, 'fr', 'actif', 1, '2026-06-05 15:23:37', '2026-05-27 09:35:31', '2026-06-05 15:23:37'),
(2, 'Test', 'Brayan', 'test@plantdoc.cm', '237690123456', '$2y$10$4yh7QgMGKSndFoGCn.rIXeINavBeoEfZcxBG9.sZu.EgtO4v9mgDm', 1, 2, NULL, NULL, NULL, 'fr', 'actif', 0, '2026-06-05 15:23:20', '2026-05-27 09:41:37', '2026-06-05 15:23:20'),
(3, 'vinil', 'brayan', 'vinil@gmail.com', '+237655454994', '$2y$10$aFaWYLlIIEXiSoOJD6qmge8ViPMcAF6.D38YYhFYD3uY2iHnZqeWK', 1, 5, NULL, NULL, NULL, 'fr', 'actif', 0, '2026-05-27 13:55:34', '2026-05-27 13:55:10', '2026-05-27 13:55:34'),
(4, 'VINIL', 'Brayan', 'vinilbrayan09@gmail.com', '+237600000000', '$2y$10$h3PZMOgSFW.HowtwN1lnuu7cqTr6Ti1yMakXtjCiAoZN7oJf8RUFu', 1, 5, NULL, NULL, NULL, 'fr', 'actif', 1, '2026-06-04 07:33:32', '2026-06-04 07:07:06', '2026-06-04 07:33:32'),
(5, 'NJOYA', 'Dr. Aminatou', 'expert@plantdoc.cm', '+237699112233', '$2y$10$.qUxJOL8Jr98K8bEgPCTqu6ubQv5wxMWWmx5vdiakpIim8tG95TbS', 2, 2, NULL, NULL, NULL, 'fr', 'actif', 1, '2026-06-04 08:09:58', '2026-06-04 07:07:06', '2026-06-04 08:09:58'),
(6, 'motuo', 'luciole', 'motuoluciole77@gmail.com', '695394923', '$2y$10$/h.pSPh.mCx9kZsT5C3gWeXOZBBLAX7F2ke7Nr9nTPZhQrcbN04Ry', 1, 5, NULL, NULL, NULL, 'fr', 'actif', 0, '2026-06-04 07:37:05', '2026-06-04 07:36:40', '2026-06-04 07:37:05');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_top_maladies`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `v_top_maladies`;
CREATE TABLE IF NOT EXISTS `v_top_maladies` (
`id` smallint unsigned
,`nom_commun` varchar(100)
,`culture` varchar(60)
,`occurrences` bigint
,`confiance_moyenne` decimal(9,6)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_user_stats`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `v_user_stats`;
CREATE TABLE IF NOT EXISTS `v_user_stats` (
`id` int unsigned
,`nom` varchar(60)
,`prenom` varchar(60)
,`email` varchar(120)
,`total_diagnostics` bigint
,`diag_severes` decimal(23,0)
,`plantes_saines` decimal(23,0)
,`dernier_diagnostic` timestamp
);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `maladies`
--
ALTER TABLE `maladies` ADD FULLTEXT KEY `idx_search` (`nom_commun`,`nom_scientifique`,`symptomes`);

-- --------------------------------------------------------

--
-- Structure de la vue `v_top_maladies`
--
DROP TABLE IF EXISTS `v_top_maladies`;

DROP VIEW IF EXISTS `v_top_maladies`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_top_maladies`  AS SELECT `m`.`id` AS `id`, `m`.`nom_commun` AS `nom_commun`, `c`.`nom` AS `culture`, count(`d`.`id`) AS `occurrences`, avg(`d`.`confiance`) AS `confiance_moyenne` FROM ((`maladies` `m` left join `diagnostics` `d` on((`d`.`maladie_id` = `m`.`id`))) left join `cultures` `c` on((`m`.`culture_id` = `c`.`id`))) GROUP BY `m`.`id` ORDER BY `occurrences` DESC ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_user_stats`
--
DROP TABLE IF EXISTS `v_user_stats`;

DROP VIEW IF EXISTS `v_user_stats`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_user_stats`  AS SELECT `u`.`id` AS `id`, `u`.`nom` AS `nom`, `u`.`prenom` AS `prenom`, `u`.`email` AS `email`, count(`d`.`id`) AS `total_diagnostics`, sum((case when (`d`.`gravite` = 'severe') then 1 else 0 end)) AS `diag_severes`, sum((case when (`d`.`plante_saine` = true) then 1 else 0 end)) AS `plantes_saines`, max(`d`.`created_at`) AS `dernier_diagnostic` FROM (`users` `u` left join `diagnostics` `d` on((`d`.`user_id` = `u`.`id`))) GROUP BY `u`.`id` ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `diagnostics`
--
ALTER TABLE `diagnostics`
  ADD CONSTRAINT `fk_diag_maladie` FOREIGN KEY (`maladie_id`) REFERENCES `maladies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_diag_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_diag_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_diag_validateur` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `fk_fb_diag` FOREIGN KEY (`diagnostic_id`) REFERENCES `diagnostics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `maladies`
--
ALTER TABLE `maladies`
  ADD CONSTRAINT `fk_maladie_culture` FOREIGN KEY (`culture_id`) REFERENCES `cultures` (`id`) ON DELETE RESTRICT;

--
-- Contraintes pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
