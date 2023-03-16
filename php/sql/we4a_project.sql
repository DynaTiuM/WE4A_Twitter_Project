-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 16 mars 2023 à 20:11
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
  `nom` varchar(30) DEFAULT NULL,
  `maitre` varchar(30) DEFAULT NULL,
  `age` int(200) DEFAULT NULL,
  `sexe` enum('feminin','masculin') DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `caracteristiques` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `animaux`
--

CREATE TABLE `animaux` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) DEFAULT NULL,
  `maitre` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `liste_messages`
--

CREATE TABLE `liste_messages` (
  `id` int(11) NOT NULL,
  `maitre_id` varchar(30) DEFAULT NULL,
  `message_id` int(11) DEFAULT NULL,
  `animaux_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `owner` varchar(30) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `contenu` varchar(240) DEFAULT NULL,
  `liste_hashtags` int(11) DEFAULT NULL,
  `localisation` varchar(50) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `animaux` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `owner`, `date`, `contenu`, `liste_hashtags`, `localisation`, `image`, `animaux`) VALUES
(13, 'raph', '2023-03-16 11:18:05', 'Je aime les chats !', NULL, NULL, NULL, NULL),
(14, 'raph', '2023-03-16 11:18:13', 'Bonjour', NULL, NULL, NULL, NULL),
(15, 'raph', '2023-03-16 11:18:18', 'blablabla', NULL, NULL, NULL, NULL),
(16, 'raph', '2023-03-16 11:19:53', 'J&#039;aime aussi les chats !!!', NULL, NULL, NULL, NULL),
(17, 'raph', '2023-03-16 11:39:01', 'coucou', NULL, NULL, NULL, NULL),
(18, 'raph', '2023-03-16 12:54:12', 'Rebonjour', NULL, NULL, NULL, NULL),
(19, 'raph2', '2023-03-16 14:19:56', 'Coucou Raphael', NULL, NULL, NULL, NULL),
(20, 'raph2', '2023-03-16 14:21:50', 'jj', NULL, NULL, NULL, NULL),
(21, 'raph2', '2023-03-16 14:23:04', 'Looooooooooooooooooooooong texte pour essayer de regarder combien de caractères maximal on peut mettre et s&#039;il n&#039;y a pas de problemes de disposition lorsque le texte sera envoyé, wow 240 caractères c&#039;est quand même pas mal!!\r', NULL, NULL, NULL, NULL),
(22, 'raph2', '2023-03-16 14:37:09', 'Bonjour !!', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `email` varchar(40) DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(20) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `mot_de_passe` varchar(40) DEFAULT NULL,
  `avatar` blob DEFAULT NULL,
  `organisation` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`email`, `username`, `nom`, `prenom`, `date_de_naissance`, `mot_de_passe`, `avatar`, `organisation`) VALUES
('raphael.perrin@utbm.fr', 'raph', 'PERRIN', 'Raphael', '2002-09-09', 'ab4f63f9ac65152575886860dde480a1', 0x6e756c6c, 0),
('r', 'raph2', 'PERRIN2', 'Raphael2', '2002-09-09', '0cc175b9c0f1b6a831c399e269772661', 0x6e756c6c, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maitre` (`maitre`);

--
-- Index pour la table `animaux`
--
ALTER TABLE `animaux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`),
  ADD KEY `maitre` (`maitre`);

--
-- Index pour la table `liste_messages`
--
ALTER TABLE `liste_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `maitre_id` (`maitre_id`),
  ADD KEY `animaux_id` (`animaux_id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `animaux_id_unique` (`animaux`),
  ADD KEY `owner_index` (`owner`);

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
-- AUTO_INCREMENT pour la table `liste_messages`
--
ALTER TABLE `liste_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `animal_ibfk_1` FOREIGN KEY (`maitre`) REFERENCES `utilisateur` (`username`);

--
-- Contraintes pour la table `animaux`
--
ALTER TABLE `animaux`
  ADD CONSTRAINT `animaux_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`),
  ADD CONSTRAINT `animaux_ibfk_2` FOREIGN KEY (`maitre`) REFERENCES `animal` (`maitre`);

--
-- Contraintes pour la table `liste_messages`
--
ALTER TABLE `liste_messages`
  ADD CONSTRAINT `liste_messages_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`),
  ADD CONSTRAINT `liste_messages_ibfk_3` FOREIGN KEY (`maitre_id`) REFERENCES `utilisateur` (`username`),
  ADD CONSTRAINT `liste_messages_ibfk_4` FOREIGN KEY (`animaux_id`) REFERENCES `message` (`animaux`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `utilisateur` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
