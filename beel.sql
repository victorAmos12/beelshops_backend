-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 08 fév. 2026 à 16:05
-- Version du serveur : 9.1.0
-- Version de PHP : 8.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `beelshops`
--
-- --------------------------------------------------------

--
-- Structure de la table `article_commande`
--

DROP TABLE IF EXISTS `article_commande`;
CREATE TABLE IF NOT EXISTS `article_commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantite` int NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `commande_id` int NOT NULL,
  `produit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3B02521682EA2E54` (`commande_id`),
  KEY `IDX_3B025216F347EFB` (`produit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avis_client`
--

DROP TABLE IF EXISTS `avis_client`;
CREATE TABLE IF NOT EXISTS `avis_client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rating` int NOT NULL,
  `commentaire` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id_produit_id` int DEFAULT NULL,
  `id_utilisateur_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_708E90EFAABEFE2C` (`id_produit_id`),
  KEY `IDX_708E90EFC6EE5C49` (`id_utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(30) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `num_ordre` varchar(100) NOT NULL,
  `prix_total` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `adresse_livraison` varchar(255) DEFAULT NULL,
  `adresse_appartement` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `utilisateur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_35D4282CFB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260208032020', '2026-02-08 03:20:57', 565),
('DoctrineMigrations\\Version20260208033246', '2026-02-08 03:32:57', 882);

-- --------------------------------------------------------

--
-- Structure de la table `liste_souhaits`
--

DROP TABLE IF EXISTS `liste_souhaits`;
CREATE TABLE IF NOT EXISTS `liste_souhaits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `added_at` datetime NOT NULL,
  `id_utilisateur_id` int NOT NULL,
  `id_produit_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_92B072AFC6EE5C49` (`id_utilisateur_id`),
  KEY `IDX_92B072AFAABEFE2C` (`id_produit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

DROP TABLE IF EXISTS `panier`;
CREATE TABLE IF NOT EXISTS `panier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `utilisateur_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_24CC0DF2FB88E14F` (`utilisateur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panier_articles`
--

DROP TABLE IF EXISTS `panier_articles`;
CREATE TABLE IF NOT EXISTS `panier_articles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quantite` int NOT NULL,
  `added_at` datetime NOT NULL,
  `panier_id_id` int NOT NULL,
  `produit_id_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2598381A5669B1EA` (`panier_id_id`),
  KEY `IDX_2598381A4FD8F9C3` (`produit_id_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `slug` varchar(30) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int DEFAULT NULL,
  `weight` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_29A5EC2712469DE2` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produit_images`
--

DROP TABLE IF EXISTS `produit_images`;
CREATE TABLE IF NOT EXISTS `produit_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT NULL,
  `alt_text` varchar(50) DEFAULT NULL,
  `position` int NOT NULL,
  `produit_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_174D7550F347EFB` (`produit_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `is_active` tinyint NOT NULL,
  `roles` json NOT NULL COMMENT '(DC2Type:json)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `email`, `password`, `nom`, `prenom`, `phone`, `created_at`, `updated_at`, `is_active`, `roles`) VALUES
(1, 'admin@beelshop.com', '$2y$12$QLuUEwxGgggCTGvly5rlMer5wAPFPYhY0b9V7r5XkPYaBkH4FPhHG', 'Admin', 'BeelShop', '+33612345678', '2026-02-08 16:10:15', '2026-02-08 16:10:15', 1, '[\"ROLE_ADMIN\", \"ROLE_CLIENT\"]'),
(2, 'client@beelshop.com', '$2y$12$QLuUEwxGgggCTGvly5rlMer5wAPFPYhY0b9V7r5XkPYaBkH4FPhHG', 'Client', 'Test', '+33687654321', '2026-02-08 16:10:15', '2026-02-08 16:10:15', 1, '[\"ROLE_CLIENT\"]');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
