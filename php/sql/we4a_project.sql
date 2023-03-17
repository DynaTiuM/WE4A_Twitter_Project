-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 17 mars 2023 à 10:12
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 8.2.0

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
-- Structure de la table `animal`
--

CREATE TABLE `animal` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `maitre_username` varchar(30) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `sexe` enum('féminin','masculin') DEFAULT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  `caracteristiques` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `animaux`
--

CREATE TABLE `animaux` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `maitre_id` varchar(30) NOT NULL
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

--
-- Déchargement des données de la table `hashtag`
--

INSERT INTO `hashtag` (`id`, `tag`, `message_id`) VALUES
(1, 'hashtagduturfu', 5),
(2, 'deuxiemehashtag', 6),
(3, 'ehoui', 7),
(4, 'hashtagduturfu', 8),
(5, 'trois', 9),
(6, 'hastags', 9),
(7, 'enmemetemps', 9);

-- --------------------------------------------------------

--
-- Structure de la table `liste_messages`
--

CREATE TABLE `liste_messages` (
  `id` int(11) NOT NULL,
  `utilisateur_username` varchar(30) NOT NULL,
  `message_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `auteur_username` varchar(30) NOT NULL,
  `date` datetime NOT NULL,
  `contenu` text NOT NULL,
  `localisation` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `auteur_username`, `date`, `contenu`, `localisation`, `image`) VALUES
(1, 'raphael_prrn', '2023-03-16 21:13:05', 'a', NULL, NULL),
(2, 'raphael_prrn', '2023-03-16 22:19:12', 'b', 'null', 'null'),
(3, 'raphael_prrn', '2023-03-16 22:19:28', 'test', 'null', 'null'),
(4, 'raphael_prrn', '2023-03-16 22:19:56', 'coucou', 'null', 'null'),
(5, 'raphael_prrn', '2023-03-16 22:20:18', 'petit message avec un #hashtagduturfu eh oui !', 'null', 'null'),
(6, 'raphael_prrn', '2023-03-16 22:23:04', 'voici le #deuxiemehashtag', 'null', 'null'),
(7, 'raphael_prrn', '2023-03-16 22:23:12', '#ehoui!', 'null', 'null'),
(8, 'raphael_prrn', '2023-03-16 22:26:52', '#hashtagduturfu oeoeoe', 'null', 'null'),
(9, 'raphael_prrn', '2023-03-16 22:36:55', '#trois #hastags #enmemetemps', 'null', 'null'),
(10, 'raphael_prrn', '2023-03-17 08:06:55', '', 'null', 'null');

-- --------------------------------------------------------

--
-- Structure de la table `message_animaux`
--

CREATE TABLE `message_animaux` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `username` varchar(30) NOT NULL,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(30) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `mot_de_passe` varchar(100) DEFAULT NULL,
  `avatar` blob DEFAULT NULL,
  `organisation` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`username`, `nom`, `prenom`, `date_de_naissance`, `mot_de_passe`, `avatar`, `organisation`) VALUES
('raphael_prrn', 'PERRIN', 'Raphael', '2002-09-09', '0cc175b9c0f1b6a831c399e269772661', 0x6e756c6c, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maitre_username` (`maitre_username`);

--
-- Index pour la table `animaux`
--
ALTER TABLE `animaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `maitre_id` (`maitre_id`);

--
-- Index pour la table `hashtag`
--
ALTER TABLE `hashtag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Index pour la table `liste_messages`
--
ALTER TABLE `liste_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_username` (`utilisateur_username`),
  ADD KEY `message_id` (`message_id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auteur_username` (`auteur_username`);

--
-- Index pour la table `message_animaux`
--
ALTER TABLE `message_animaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animal`
--
ALTER TABLE `animal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `animaux`
--
ALTER TABLE `animaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hashtag`
--
ALTER TABLE `hashtag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `liste_messages`
--
ALTER TABLE `liste_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `message_animaux`
--
ALTER TABLE `message_animaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`maitre_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `animaux`
--
ALTER TABLE `animaux`
  ADD CONSTRAINT `animaux_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`),
  ADD CONSTRAINT `animaux_ibfk_2` FOREIGN KEY (`maitre_id`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `hashtag`
--
ALTER TABLE `hashtag`
  ADD CONSTRAINT `hashtag_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`);

--
-- Contraintes pour la table `liste_messages`
--
ALTER TABLE `liste_messages`
  ADD CONSTRAINT `liste_messages_ibfk_1` FOREIGN KEY (`utilisateur_username`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `liste_messages_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`auteur_username`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `message_animaux`
--
ALTER TABLE `message_animaux`
  ADD CONSTRAINT `message_animaux_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`),
  ADD CONSTRAINT `message_animaux_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
