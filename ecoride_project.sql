-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : dim. 21 sep. 2025 à 16:39
-- Version du serveur : 8.4.6
-- Version de PHP : 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+02:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `car`
--

CREATE TABLE `car` (
  `id` int NOT NULL,
  `model` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `power_engine` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_date_registration` date NOT NULL COMMENT '(DC2Type:date_immutable)',
  `marque_id` int DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `car`
--
-- --------------------------------------------------------

--
-- Structure de la table `carpooling`
--

CREATE TABLE `carpooling` (
  `id` int NOT NULL,
  `start_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `start_place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `end_place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available_seat` int NOT NULL,
  `price_per_person` int NOT NULL,
  `created_by_id` int NOT NULL,
  `car_id` int DEFAULT NULL,
  `start_adress` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `end_adress` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `carpooling`
--
-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250816144010', '2025-09-10 21:18:22', 68),
('DoctrineMigrations\\Version20250817092053', '2025-09-10 21:18:22', 89),
('DoctrineMigrations\\Version20250817092541', '2025-09-10 21:18:22', 24),
('DoctrineMigrations\\Version20250817151658', '2025-09-10 21:18:22', 50),
('DoctrineMigrations\\Version20250817163006', '2025-09-10 21:18:23', 108),
('DoctrineMigrations\\Version20250817163053', '2025-09-10 21:18:23', 56),
('DoctrineMigrations\\Version20250818065525', '2025-09-10 21:18:23', 45),
('DoctrineMigrations\\Version20250823214113', '2025-09-10 21:18:23', 42),
('DoctrineMigrations\\Version20250824091746', '2025-09-10 21:18:23', 49),
('DoctrineMigrations\\Version20250824133431', '2025-09-10 21:18:23', 45),
('DoctrineMigrations\\Version20250824142854', '2025-09-10 21:18:23', 131),
('DoctrineMigrations\\Version20250826205334', '2025-09-10 21:18:23', 47),
('DoctrineMigrations\\Version20250903180904', '2025-09-10 21:18:23', 133),
('DoctrineMigrations\\Version20250903181150', '2025-09-10 21:18:23', 113),
('DoctrineMigrations\\Version20250903185937', '2025-09-10 21:18:23', 344),
('DoctrineMigrations\\Version20250906075355', '2025-09-10 21:18:24', 67),
('DoctrineMigrations\\Version20250906090035', '2025-09-10 21:18:24', 108),
('DoctrineMigrations\\Version20250906091457', '2025-09-10 21:18:24', 53),
('DoctrineMigrations\\Version20250907132316', '2025-09-10 21:18:24', 52),
('DoctrineMigrations\\Version20250907162710', '2025-09-10 21:18:24', 52),
('DoctrineMigrations\\Version20250907165418', '2025-09-10 21:18:24', 62),
('DoctrineMigrations\\Version20250909144143', '2025-09-10 21:18:24', 67),
('DoctrineMigrations\\Version20250909144303', '2025-09-10 21:18:24', 151),
('DoctrineMigrations\\Version20250909144333', '2025-09-10 21:18:24', 131),
('DoctrineMigrations\\Version20250909150340', '2025-09-10 21:18:24', 83),
('DoctrineMigrations\\Version20250910190629', '2025-09-10 21:18:25', 0),
('DoctrineMigrations\\Version20250910192435', '2025-09-10 21:25:12', 144),
('DoctrineMigrations\\Version20250910192500', '2025-09-10 21:26:33', 1),
('DoctrineMigrations\\Version20250910193147', '2025-09-10 21:32:03', 203),
('DoctrineMigrations\\Version20250913211241', '2025-09-13 23:12:55', 63),
('DoctrineMigrations\\Version20250914142213', '2025-09-14 16:22:18', 290),
('DoctrineMigrations\\Version20250914193724', '2025-09-14 21:37:29', 64),
('DoctrineMigrations\\Version20250914195011', '2025-09-14 21:50:15', 139),
('DoctrineMigrations\\Version20250915171309', '2025-09-15 19:13:34', 55),
('DoctrineMigrations\\Version20250915180452', '2025-09-15 20:04:56', 133),
('DoctrineMigrations\\Version20250915181308', '2025-09-15 20:13:11', 41),
('DoctrineMigrations\\Version20250915181358', '2025-09-15 20:15:45', 1),
('DoctrineMigrations\\Version20250915181629', '2025-09-15 20:16:33', 124);

-- --------------------------------------------------------

--
-- Structure de la table `marque`
--

CREATE TABLE `marque` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `marque`
--

INSERT INTO `marque` (`id`, `name`) VALUES
(1, 'Toyota'),
(2, 'Volkswagen'),
(3, 'Ford'),
(4, 'Honda'),
(5, 'Chevrolet'),
(6, 'Mercedes-Benz'),
(7, 'BMW'),
(8, 'Audi'),
(9, 'Hyundai'),
(10, 'Nissan'),
(11, 'Kia'),
(12, 'Peugeot'),
(13, 'Renault'),
(14, 'Fiat'),
(15, 'Opel'),
(16, 'Mazda'),
(17, 'Volvo'),
(18, 'Jaguar'),
(19, 'Land Rover'),
(20, 'Porsche'),
(21, 'Ferrari'),
(22, 'Lamborghini'),
(23, 'Maserati'),
(24, 'Bentley'),
(25, 'Rolls-Royce'),
(26, 'Jeep'),
(27, 'Subaru'),
(28, 'Mitsubishi'),
(29, 'Citroën'),
(30, 'Suzuki');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `carpooling_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `participation`
--

-- --------------------------------------------------------

--
-- Structure de la table `review`
--

CREATE TABLE `review` (
  `id` int NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `review`
--

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL COMMENT '(DC2Type:date_immutable)',
  `photo` longblob,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_adress` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `grade` smallint DEFAULT NULL,
  `ecopiece` int NOT NULL DEFAULT '20',
  `current_car_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--
-- --------------------------------------------------------

--
-- Structure de la table `user_review`
--

CREATE TABLE `user_review` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `review_id` int NOT NULL,
  `affected_user_id` int DEFAULT NULL,
  `carpooling_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_review`
--

INSERT INTO `user_review` (`id`, `user_id`, `review_id`, `affected_user_id`, `carpooling_id`) VALUES
(5, 4, 5, 3, 7);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_773DE69D4827B9B2` (`marque_id`);

--
-- Index pour la table `carpooling`
--
ALTER TABLE `carpooling`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6CC153F1C3C6F69F` (`car_id`),
  ADD KEY `IDX_6CC153F1B03A8386` (`created_by_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `marque`
--
ALTER TABLE `marque`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_carpooling` (`user_id`,`carpooling_id`),
  ADD KEY `IDX_AB55E24FA76ED395` (`user_id`),
  ADD KEY `IDX_AB55E24FAFB2200A` (`carpooling_id`);

--
-- Index pour la table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD KEY `IDX_8D93D649C40F2CF1` (`current_car_id`);

--
-- Index pour la table `user_review`
--
ALTER TABLE `user_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1C119AFBA76ED395` (`user_id`),
  ADD KEY `IDX_1C119AFB3E2E969B` (`review_id`),
  ADD KEY `IDX_1C119AFBEDE70614` (`affected_user_id`),
  ADD KEY `IDX_1C119AFBAFB2200A` (`carpooling_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `car`
--
ALTER TABLE `car`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `carpooling`
--
ALTER TABLE `carpooling`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `marque`
--
ALTER TABLE `marque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `review`
--
ALTER TABLE `review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user_review`
--
ALTER TABLE `user_review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `FK_773DE69D4827B9B2` FOREIGN KEY (`marque_id`) REFERENCES `marque` (`id`);

--
-- Contraintes pour la table `carpooling`
--
ALTER TABLE `carpooling`
  ADD CONSTRAINT `FK_6CC153F1B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_6CC153F1C3C6F69F` FOREIGN KEY (`car_id`) REFERENCES `car` (`id`);

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `FK_AB55E24FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_AB55E24FAFB2200A` FOREIGN KEY (`carpooling_id`) REFERENCES `carpooling` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649C40F2CF1` FOREIGN KEY (`current_car_id`) REFERENCES `car` (`id`);

--
-- Contraintes pour la table `user_review`
--
ALTER TABLE `user_review`
  ADD CONSTRAINT `FK_1C119AFB3E2E969B` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`),
  ADD CONSTRAINT `FK_1C119AFBA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_1C119AFBAFB2200A` FOREIGN KEY (`carpooling_id`) REFERENCES `carpooling` (`id`),
  ADD CONSTRAINT `FK_1C119AFBEDE70614` FOREIGN KEY (`affected_user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
