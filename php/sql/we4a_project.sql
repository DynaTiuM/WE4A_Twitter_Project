-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 15 mars 2023 à 15:11
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
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `email` varchar(40) DEFAULT NULL,
  `username` varchar(30) NOT NULL,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(20) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `mot_de_passe` varchar(25) DEFAULT NULL,
  `avatar` blob DEFAULT NULL,
  `organisation` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`email`, `username`, `nom`, `prenom`, `date_de_naissance`, `mot_de_passe`, `avatar`, `organisation`) VALUES
('aaaaa', 'aaaaa', 'aaaaa', 'aaaaa', '1111-11-11', '0cc175b9c0f1b6a831c399e26', '', 0),
('raph', 'raph', 'raph', 'Raphael', '2002-09-09', '0cc175b9c0f1b6a831c399e26', 0x6e756c6c, 0),
('rapha', 'rapha', 'rapha', 'rapha', '2002-09-09', '0cc175b9c0f1b6a831c399e26', 0x6e756c6c, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
