-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 25 juil. 2025 à 18:53
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `trainingcentre`
--

-- --------------------------------------------------------

--
-- Structure de la table `choices`
--

CREATE TABLE `choices` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `choice_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL,
  `formation_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `youtube_url` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `title`, `description`, `video_url`, `document_url`, `formation_id`, `created_by`, `created_at`, `youtube_url`, `duration`) VALUES
(1, 'ghdj', 'dfgf', 'uploads/videos/1753210766_WhatsApp Vidéo 2025-05-31 à 11.44.46_2e5bd36d.mp4', '', 1, 3, '2025-07-22 18:59:26', '', 30),
(6, 'langae fransai', 'hello', '', 'uploads/pdfs/1753277031_Soukayna Machraa (9).pdf', 3, 3, '2025-07-23 13:23:51', '', 30),
(7, 'bjkjn', 'knkjk', '', 'uploads/pdfs/1753311063_SOUKAYNA (2).pdf', 3, 3, '2025-07-23 22:51:03', '', 30);

-- --------------------------------------------------------

--
-- Structure de la table `cours_termines`
--

CREATE TABLE `cours_termines` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cours_id` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `niveau` enum('débutant','intermédiaire','avancé') DEFAULT NULL,
  `langue` varchar(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formations`
--

INSERT INTO `formations` (`id`, `title`, `description`, `niveau`, `langue`, `created_by`) VALUES
(1, 'Développement Web', 'Apprenez à créer des sites avec HTML, CSS, JS', 'débutant', 'français', 3),
(2, 'Python pour la Data Science', 'Introduction à Python et aux bibliothèques de data science', 'intermédiaire', 'français', 1),
(3, 'laguage', 'comminication', 'débutant', NULL, 3);

-- --------------------------------------------------------

--
-- Structure de la table `formations_terminees`
--

CREATE TABLE `formations_terminees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `date_terminee` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formations_terminees`
--

INSERT INTO `formations_terminees` (`id`, `user_id`, `formation_id`, `date_terminee`) VALUES
(1, 4, 3, '2025-07-24 21:15:06'),
(2, 4, 1, '2025-07-24 21:15:34');

-- --------------------------------------------------------

--
-- Structure de la table `lecons_consultees`
--

CREATE TABLE `lecons_consultees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cours_id` int(11) NOT NULL,
  `consulted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lecons_consultees`
--

INSERT INTO `lecons_consultees` (`id`, `user_id`, `cours_id`, `consulted_at`) VALUES
(1, 4, 7, '2025-07-24 19:58:24'),
(2, 4, 6, '2025-07-24 19:58:40'),
(3, 4, 1, '2025-07-24 20:15:32');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(1, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 13:32:02'),
(2, 4, 3, 'ayya  a consulté votre cours : ghdj', 0, '2025-07-24 13:32:45'),
(3, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:11:52'),
(4, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 19:16:43'),
(5, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:26:03'),
(6, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:30:49'),
(7, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:30:59'),
(8, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:34:08'),
(9, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 19:52:48'),
(10, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:56:37'),
(11, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:58:24'),
(12, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 19:58:33'),
(13, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 19:58:40'),
(14, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 19:58:53'),
(15, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 20:14:09'),
(16, 4, 3, 'ayya  a consulté votre cours : bjkjn', 0, '2025-07-24 20:14:58'),
(17, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 20:15:04'),
(18, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 20:15:06'),
(19, 4, 3, 'ayya  a consulté votre cours : langae fransai', 0, '2025-07-24 20:15:12'),
(20, 4, 3, 'ayya  a consulté votre cours : ghdj', 0, '2025-07-24 20:15:32'),
(21, 4, 3, 'ayya  a consulté votre cours : ghdj', 0, '2025-07-24 20:15:34');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 1, '\'jkjnkm,ml\'', 1),
(2, 1, '\'knkjnlk,\'', 0),
(3, 1, '\',nb\'', 0),
(4, 1, '\'hgfd\'', 0),
(5, 2, '\'jhgfd\'', 0),
(6, 2, '\'jhgfd\'', 1),
(7, 2, '\'hgfd\'', 0),
(8, 2, '\'hgtfrd\'', 0),
(9, 3, '\'jkjnkm,ml\'', 1),
(10, 3, '\'knkjnlk,\'', 0),
(11, 3, '\',nb\'', 0),
(12, 3, '\'hgfd\'', 0),
(13, 4, '\'bjbljmkj\'', 1),
(14, 4, '\'n :,:nk,\'', 0),
(15, 4, '\'knkjnmkj\'', 0),
(16, 4, '\'kjnmk\'', 0),
(17, 5, '\'jkjnkm,ml\'', 1),
(18, 5, '\'jkjnkm,ml\'', 0),
(19, 5, '\'jkjnkm,ml\'', 0),
(20, 5, '\'bjbljmkj\'', 0),
(21, 6, '\'jkjnkm,ml\'', 1),
(22, 6, '\'jkjnkm,ml\'', 0),
(23, 6, '\'jkjnkm,ml\'', 0),
(24, 6, '\'bjbljmkj\'', 0),
(25, 7, '\'jkjnkm,ml\'', 1),
(26, 7, '\'jkjnkm,ml\'', 0),
(27, 7, '\'jkjnkm,ml\'', 0),
(28, 7, '\'bjbljmkj\'', 0),
(29, 8, '\'jkjnkm,ml\'', 0),
(30, 8, 'rjejoijeoi', 0),
(31, 8, '\'ejzjijeôi\'', 1),
(32, 8, '\'bjbljmkj\'', 0);

-- --------------------------------------------------------

--
-- Structure de la table `progression`
--

CREATE TABLE `progression` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cours_id` int(11) NOT NULL,
  `statut` enum('non commencé','commencé','terminé') DEFAULT 'non commencé',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `progression`
--

INSERT INTO `progression` (`id`, `user_id`, `cours_id`, `statut`, `updated_at`) VALUES
(1, 4, 7, 'commencé', '2025-07-24 20:30:48'),
(2, 4, 6, 'commencé', '2025-07-24 20:52:48'),
(3, 4, 1, 'commencé', '2025-07-24 21:15:32');

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`) VALUES
(1, 1, '\'nljlkj\''),
(2, 1, '\'jhgfd\''),
(3, 2, '\'nljlkj\''),
(4, 3, '\'bjbljl\''),
(5, 4, '\'nljlkj\''),
(6, 5, '\'nljlkj\''),
(7, 6, '\'hmhjlk\''),
(8, 7, '\'nljlkj\'');

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `budget` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `created_by`, `created_at`, `budget`) VALUES
(1, 1, '\'blkjkj\'', '\'bljljnkjk\'', NULL, '2025-07-25 01:26:56', 0.00),
(2, 1, '\'blkjkj\'', '\'bljljnkjk\'', NULL, '2025-07-25 01:28:17', 0.00),
(3, 1, '\' k!l,\'', '\' n kj\'', NULL, '2025-07-25 01:28:36', 0.00),
(4, 1, '\'b,b:kjk\'', '\'kjytrezq\'', NULL, '2025-07-25 01:29:04', 0.00),
(5, 1, '\'b,b:kjk\'', '\'kjytrezq\'', NULL, '2025-07-25 01:31:00', 0.00),
(6, 1, '\'blkjkj\'', '\'ytreza\'', NULL, '2025-07-25 01:31:36', 0.00),
(7, 1, '\'php\'', '\'lhwlvlkjhmkjfdmieoiqtiroi\'', NULL, '2025-07-25 01:33:46', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `specializations`
--

CREATE TABLE `specializations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `specializations`
--

INSERT INTO `specializations` (`id`, `name`) VALUES
(1, 'Informatique'),
(2, 'Marketing'),
(3, 'Management'),
(4, 'Langues'),
(5, 'Finance');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','formateur','etudiant','stagiaire') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `specialization` varchar(100) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `education` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `previous_experience` text DEFAULT NULL,
  `presentation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `phone`, `created_at`, `specialization`, `experience`, `education`, `certifications`, `previous_experience`, `presentation`) VALUES
(1, 'SOUKAYNA', 'MACHRAA', 'soukaynamachraa1@gmail.com', '$2y$12$n8T9wZDYA.LGnB9uVPMtvOAt5wLVsrd/Gs9F9rEqzzhqbrqLuhthW', 'etudiant', NULL, '2025-07-20 22:36:59', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'ayoub', 'gharaffi', 'ayoub@gmail.com', '$2y$10$UOjzWwOu5ztEJQkX4sYTYezeHJ26.IkGdH2SKOy0aaF2DqsyXdI.G', 'formateur', NULL, '2025-07-21 11:12:32', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'ayoub', 'gharaffi', 'ayoubgharaffi@gmail.com', '$2y$10$QF4IicHLPpZjINdnTPzBHOJzXfLxfx4dO3Fu7jTto2WRbE3Uwv7Qa', 'formateur', '0762521040', '2025-07-21 22:05:34', 'Marketing', 1, 'GHJGKJ', 'JBJLH', 'hghjjg', 'hjgjj'),
(4, 'ayya', '', 'ayyamach@gmail.com', '$2y$12$JMflK1azPAeaUupn0zh1reBtGxw3VeEWZufXl/lRn4TDwluuXKBRG', 'etudiant', NULL, '2025-07-23 20:27:21', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_quizzes`
--

CREATE TABLE `user_quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `completed` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `choices`
--
ALTER TABLE `choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_formation_id` (`formation_id`),
  ADD KEY `fk_created_by` (`created_by`);

--
-- Index pour la table `cours_termines`
--
ALTER TABLE `cours_termines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`cours_id`),
  ADD KEY `cours_id` (`cours_id`);

--
-- Index pour la table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `formations_terminees`
--
ALTER TABLE `formations_terminees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_formation` (`user_id`,`formation_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `lecons_consultees`
--
ALTER TABLE `lecons_consultees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cours_id` (`cours_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `progression`
--
ALTER TABLE `progression`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cours_id` (`cours_id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `choices`
--
ALTER TABLE `choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `cours_termines`
--
ALTER TABLE `cours_termines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `formations_terminees`
--
ALTER TABLE `formations_terminees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `lecons_consultees`
--
ALTER TABLE `lecons_consultees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `progression`
--
ALTER TABLE `progression`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user_quizzes`
--
ALTER TABLE `user_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `choices`
--
ALTER TABLE `choices`
  ADD CONSTRAINT `choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`),
  ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_formation_id` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `cours_termines`
--
ALTER TABLE `cours_termines`
  ADD CONSTRAINT `cours_termines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cours_termines_ibfk_2` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`);

--
-- Contraintes pour la table `formations`
--
ALTER TABLE `formations`
  ADD CONSTRAINT `formations_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `formations_terminees`
--
ALTER TABLE `formations_terminees`
  ADD CONSTRAINT `formations_terminees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `formations_terminees_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`);

--
-- Contraintes pour la table `lecons_consultees`
--
ALTER TABLE `lecons_consultees`
  ADD CONSTRAINT `lecons_consultees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lecons_consultees_ibfk_2` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`);

--
-- Contraintes pour la table `progression`
--
ALTER TABLE `progression`
  ADD CONSTRAINT `progression_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `progression_ibfk_2` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`);

--
-- Contraintes pour la table `user_quizzes`
--
ALTER TABLE `user_quizzes`
  ADD CONSTRAINT `user_quizzes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_quizzes_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
