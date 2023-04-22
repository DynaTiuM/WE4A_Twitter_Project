-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 22 avr. 2023 à 17:28
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `we4a_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `adoption`
--

CREATE TABLE `adoption` (
  `id` int(11) NOT NULL,
  `animal_id` varchar(30) NOT NULL,
  `adoptant_username` varchar(30) NOT NULL,
  `date_adoption` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `animal`
--

CREATE TABLE `animal` (
  `id` varchar(30) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `maitre_username` varchar(30) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `sexe` enum('féminin','masculin') DEFAULT NULL,
  `avatar` longblob DEFAULT NULL,
  `caracteristiques` text DEFAULT NULL,
  `espece` varchar(50) DEFAULT NULL,
  `adopter` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `hashtag`
--

CREATE TABLE `hashtag` (
  `id` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `like_message`
--

CREATE TABLE `like_message` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `utilisateur_username` varchar(30) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `auteur_username` varchar(30) NOT NULL,
  `parent_message_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `contenu` text NOT NULL,
  `localisation` varchar(100) DEFAULT NULL,
  `image` longblob DEFAULT NULL,
  `categorie` enum('sauvetage','conseil','evenement') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message_animaux`
--

CREATE TABLE `message_animaux` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `animal_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `utilisateur_username` varchar(30) NOT NULL,
  `date` datetime NOT NULL,
  `vue` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification_adoption`
--

CREATE TABLE `notification_adoption` (
  `notification_id` int(11) NOT NULL,
  `animal_id` varchar(30) NOT NULL,
  `adoptant_username` varchar(30) NOT NULL,
  `etat` enum('en attente','acceptee','refusee') DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification_like`
--

CREATE TABLE `notification_like` (
  `notification_id` int(11) NOT NULL,
  `likeur_username` varchar(30) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification_message`
--

CREATE TABLE `notification_message` (
  `notification_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `utilisateur_username` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification_reponse`
--

CREATE TABLE `notification_reponse` (
  `notification_id` int(11) NOT NULL,
  `repondeur_username` varchar(30) NOT NULL,
  `message_id` int(11) NOT NULL,
  `parent_message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification_suivre`
--

CREATE TABLE `notification_suivre` (
  `notification_id` int(11) NOT NULL,
  `suiveur_username` varchar(30) NOT NULL,
  `suivre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suivre`
--

CREATE TABLE `suivre` (
  `id` int(11) NOT NULL,
  `utilisateur_username` varchar(30) NOT NULL,
  `suivi_type` enum('utilisateur','animal') NOT NULL,
  `suivi_id_utilisateur` varchar(30) DEFAULT NULL,
  `suivi_id_animal` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(30) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `mot_de_passe` varchar(100) DEFAULT NULL,
  `avatar` longblob DEFAULT NULL,
  `organisation` tinyint(1) DEFAULT NULL,
  `bio` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adoption`
--
ALTER TABLE `adoption`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `adoptant_username` (`adoptant_username`);

--
-- Index pour la table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maitre_username` (`maitre_username`);

--
-- Index pour la table `hashtag`
--
ALTER TABLE `hashtag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Index pour la table `like_message`
--
ALTER TABLE `like_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `utilisateur_username` (`utilisateur_username`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auteur_username` (`auteur_username`),
  ADD KEY `parent_message_id` (`parent_message_id`);

--
-- Index pour la table `message_animaux`
--
ALTER TABLE `message_animaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_username` (`utilisateur_username`);

--
-- Index pour la table `notification_adoption`
--
ALTER TABLE `notification_adoption`
  ADD PRIMARY KEY (`notification_id`,`adoptant_username`,`animal_id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `adoptant_username` (`adoptant_username`);

--
-- Index pour la table `notification_like`
--
ALTER TABLE `notification_like`
  ADD PRIMARY KEY (`notification_id`,`likeur_username`,`message_id`),
  ADD KEY `likeur_username` (`likeur_username`),
  ADD KEY `message_id` (`message_id`);

--
-- Index pour la table `notification_message`
--
ALTER TABLE `notification_message`
  ADD PRIMARY KEY (`notification_id`,`utilisateur_username`,`message_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `utilisateur_username` (`utilisateur_username`);

--
-- Index pour la table `notification_reponse`
--
ALTER TABLE `notification_reponse`
  ADD PRIMARY KEY (`notification_id`,`repondeur_username`,`message_id`),
  ADD KEY `repondeur_username` (`repondeur_username`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `parent_message_id` (`parent_message_id`);

--
-- Index pour la table `notification_suivre`
--
ALTER TABLE `notification_suivre`
  ADD PRIMARY KEY (`notification_id`,`suiveur_username`,`suivre_id`),
  ADD KEY `suiveur_username` (`suiveur_username`),
  ADD KEY `suivre_id` (`suivre_id`);

--
-- Index pour la table `suivre`
--
ALTER TABLE `suivre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_username` (`utilisateur_username`),
  ADD KEY `suivi_id_utilisateur` (`suivi_id_utilisateur`),
  ADD KEY `suivi_id_animal` (`suivi_id_animal`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `adoption`
--
ALTER TABLE `adoption`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hashtag`
--
ALTER TABLE `hashtag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `like_message`
--
ALTER TABLE `like_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message_animaux`
--
ALTER TABLE `message_animaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suivre`
--
ALTER TABLE `suivre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adoption`
--
ALTER TABLE `adoption`
  ADD CONSTRAINT `adoption_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`),
  ADD CONSTRAINT `adoption_ibfk_2` FOREIGN KEY (`adoptant_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`maitre_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `hashtag`
--
ALTER TABLE `hashtag`
  ADD CONSTRAINT `hashtag_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `like_message`
--
ALTER TABLE `like_message`
  ADD CONSTRAINT `like_message_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `like_message_ibfk_2` FOREIGN KEY (`utilisateur_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`auteur_username`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`parent_message_id`) REFERENCES `message` (`id`);

--
-- Contraintes pour la table `message_animaux`
--
ALTER TABLE `message_animaux`
  ADD CONSTRAINT `message_animaux_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_animaux_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`utilisateur_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `notification_adoption`
--
ALTER TABLE `notification_adoption`
  ADD CONSTRAINT `notification_adoption_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`),
  ADD CONSTRAINT `notification_adoption_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`),
  ADD CONSTRAINT `notification_adoption_ibfk_3` FOREIGN KEY (`adoptant_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `notification_like`
--
ALTER TABLE `notification_like`
  ADD CONSTRAINT `notification_like_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`),
  ADD CONSTRAINT `notification_like_ibfk_2` FOREIGN KEY (`likeur_username`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `notification_like_ibfk_3` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notification_message`
--
ALTER TABLE `notification_message`
  ADD CONSTRAINT `notification_message_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`),
  ADD CONSTRAINT `notification_message_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_message_ibfk_3` FOREIGN KEY (`utilisateur_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `notification_reponse`
--
ALTER TABLE `notification_reponse`
  ADD CONSTRAINT `notification_reponse_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`),
  ADD CONSTRAINT `notification_reponse_ibfk_2` FOREIGN KEY (`repondeur_username`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `notification_reponse_ibfk_3` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_reponse_ibfk_4` FOREIGN KEY (`parent_message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notification_suivre`
--
ALTER TABLE `notification_suivre`
  ADD CONSTRAINT `notification_suivre_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`),
  ADD CONSTRAINT `notification_suivre_ibfk_2` FOREIGN KEY (`suiveur_username`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `notification_suivre_ibfk_3` FOREIGN KEY (`suivre_id`) REFERENCES `suivre` (`id`);

--
-- Contraintes pour la table `suivre`
--
ALTER TABLE `suivre`
  ADD CONSTRAINT `suivre_ibfk_1` FOREIGN KEY (`utilisateur_username`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `suivre_ibfk_2` FOREIGN KEY (`suivi_id_utilisateur`) REFERENCES `utilisateur` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `suivre_ibfk_3` FOREIGN KEY (`suivi_id_animal`) REFERENCES `animal` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
