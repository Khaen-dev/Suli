-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2026 at 12:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2evi_proba`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `multiplier` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `bonus_points` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `store_id`, `title`, `description`, `multiplier`, `bonus_points`, `starts_at`, `ends_at`, `is_active`, `created_by`, `created_at`) VALUES
(2, 14, 'Dupla pont kedd', 'Dupla pontos kedd', 2, 1, '2026-11-23 19:33:00', '2026-11-23 23:59:00', 1, 2, '2026-11-23 18:34:42'),
(5, 5, 'Dupa kedd', 'Minden kedden dupla pontok szerezhetők', 2, 1, '2026-11-23 23:02:00', '2026-11-27 23:02:00', 1, 2, '2026-11-23 22:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(10) UNSIGNED NOT NULL,
  `storeid` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `storeid`, `userid`, `created_at`) VALUES
(22, 4, 3, '2026-11-23 17:08:23'),
(23, 5, 3, '2026-11-23 17:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_cards`
--

CREATE TABLE `loyalty_cards` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `qr_token` char(32) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `total_earned` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_scan_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `loyalty_cards`
--

INSERT INTO `loyalty_cards` (`id`, `user_id`, `store_id`, `group_id`, `qr_token`, `points`, `total_earned`, `last_scan_at`, `created_at`) VALUES
(4, 2, NULL, 5, 'e785625a154dc3dfc40598491476ec48', 11, 0, NULL, '2026-11-23 20:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED DEFAULT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `origin_store_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `points_required` int(10) UNSIGNED NOT NULL,
  `reward_description` varchar(255) NOT NULL,
  `customer_message` varchar(500) DEFAULT NULL,
  `cashier_message` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `milestones`
--

INSERT INTO `milestones` (`id`, `store_id`, `group_id`, `origin_store_id`, `title`, `points_required`, `reward_description`, `customer_message`, `cashier_message`, `is_active`, `created_at`) VALUES
(1, NULL, 5, 4, 'Ingyen kávé', 10, 'Egy tetszőleges kávé ingyen', 'Gratulálunk {nev}! Elérted a {pont} pontot, a következő kávéd ajándék!', 'Jár egy ingyen kávé a vásárlónak!', 1, '2026-11-23 17:08:23'),
(2, NULL, 5, 4, 'Kávé + sütemény', 20, 'Egy kávé és egy sütemény ingyen', 'Szuper {nev}! Kávé és sütemény a miénk!', 'Kávé + sütemény jár!', 1, '2026-11-23 17:08:23'),
(4, NULL, 5, 4, 'Ingyen kávé', 15, 'Egy tetszőleges kávé', 'Grats', 'Jár egy ingyen ká', 1, '2026-11-23 18:33:47'),
(8, NULL, 5, NULL, 'Ingyen Kalács', 15, 'Egy kalács ingyen válaszott töltelékkel', 'Gratulálok sikeresen elérted a 15. pontot. Beválthatod egy kalácsra', 'A vá', 1, '2026-11-23 22:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED DEFAULT NULL,
  `message` varchar(500) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `point_transactions`
--

CREATE TABLE `point_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `card_id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED DEFAULT NULL,
  `staff_user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` enum('scan','manual','redeem') NOT NULL,
  `points_change` int(11) NOT NULL,
  `balance_after` int(11) NOT NULL,
  `event_id` int(10) UNSIGNED DEFAULT NULL,
  `milestone_id` int(10) UNSIGNED DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `point_transactions`
--

INSERT INTO `point_transactions` (`id`, `card_id`, `store_id`, `staff_user_id`, `type`, `points_change`, `balance_after`, `event_id`, `milestone_id`, `note`, `created_at`) VALUES
(6, 4, 5, 2, 'manual', 11, 11, NULL, NULL, 'Vásárlás', '2026-11-23 23:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `store_count` int(10) UNSIGNED DEFAULT NULL,
  `milestone_limit` int(10) UNSIGNED DEFAULT NULL,
  `staff_limit` int(10) UNSIGNED DEFAULT NULL,
  `allow_shared_db` tinyint(1) NOT NULL DEFAULT 0,
  `allow_events` tinyint(1) NOT NULL DEFAULT 0,
  `allow_export` tinyint(1) NOT NULL DEFAULT 0,
  `allow_priority_support` tinyint(1) NOT NULL DEFAULT 0,
  `tier` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `name`, `price`, `store_count`, `milestone_limit`, `staff_limit`, `allow_shared_db`, `allow_events`, `allow_export`, `allow_priority_support`, `tier`) VALUES
(6, 'Kezdő', 1000, 1, 3, 2, 0, 0, 0, 0, 1),
(7, 'Haladó', 4990, 5, NULL, 10, 1, 1, 0, 0, 2),
(8, 'Üzleti', 12990, NULL, NULL, NULL, 1, 1, 1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_userid` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `type` varchar(60) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_name` varchar(150) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `tel` varchar(30) NOT NULL,
  `points_per_scan` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `scan_cooldown_minutes` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `owner_userid`, `group_id`, `name`, `type`, `address`, `contact_name`, `contact_email`, `tel`, `points_per_scan`, `scan_cooldown_minutes`, `created_at`) VALUES
(4, 2, 5, 'Starbucks', 'Kávézó', 'Budapest, Pesti út 12, 1172', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5, '2026-11-23 17:08:23'),
(5, 2, NULL, 'Lipóti Pékség', 'Pékség', 'Budapest, Ferihegyi út 64, 1173', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5, '2026-11-23 17:08:23'),
(6, 2, NULL, 'Bio Hair', 'Fodrászat', 'Budapest, Budapesti út 24, 1164', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5, '2026-11-23 17:08:23'),
(11, 2, NULL, 'Lipóti Pékség', 'Pékség', 'Budapest, Pesti út 12, 1172', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5, '2026-11-23 17:08:23'),
(14, 2, 5, 'Lipóti Pékség', 'Pékség', 'Budapest, Pesti út 12, 1172', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307', 1, 5, '2026-11-23 17:15:57');

-- --------------------------------------------------------

--
-- Table structure for table `store_groups`
--

CREATE TABLE `store_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_userid` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `store_groups`
--

INSERT INTO `store_groups` (`id`, `owner_userid`, `name`, `created_at`) VALUES
(5, 2, 'Starbucks csoport', '2026-11-23 18:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `store_messages`
--

CREATE TABLE `store_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `trigger_type` enum('first_scan','every_scan') NOT NULL DEFAULT 'every_scan',
  `audience` enum('customer','cashier') NOT NULL DEFAULT 'customer',
  `message` varchar(500) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `store_messages`
--

INSERT INTO `store_messages` (`id`, `store_id`, `trigger_type`, `audience`, `message`, `is_active`, `created_at`) VALUES
(1, 4, 'first_scan', 'customer', 'Üdv a {bolt} hűségprogramjában, {nev}!', 1, '2026-11-23 17:08:23'),
(2, 4, 'first_scan', 'cashier', 'Új törzsvásárló: {nev}', 1, '2026-11-23 17:08:23'),
(4, 14, 'every_scan', 'customer', 'Köszi szia', 1, '2026-11-23 18:35:24'),
(5, 14, 'every_scan', 'customer', 'Köszönjük a vásárlást, {nev}! Most {kapott} pontot kaptál, összesen {pont} pontod van.', 1, '2026-11-23 18:36:34'),
(8, 5, 'every_scan', 'customer', 'Köszönjük a vásárlást', 1, '2026-11-23 22:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `store_staff`
--

CREATE TABLE `store_staff` (
  `id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role` enum('pultos','szerkeszto','menedzser') NOT NULL DEFAULT 'pultos',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `store_staff`
--

INSERT INTO `store_staff` (`id`, `store_id`, `user_id`, `role`, `created_at`) VALUES
(3, 14, 3, 'pultos', '2026-11-23 18:37:17'),
(6, 5, 3, 'szerkeszto', '2026-11-23 22:03:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `last_name`, `first_name`, `password`, `created_at`) VALUES
(2, 'bagoboti@gmail.com', 'Bagó', 'Botond', '$2y$10$jLCM0ibkEfrCzX7w9w/FpehscCC/H0D2Qo46qRr4BiI8i3P553DDq', '2026-11-15 13:49:27'),
(3, 'bkoni2008@gmail.com', 'Bordas', 'Konrad', '$2y$10$Z85TZexkGUWh2G2GmabRReSzCFWNFU2HtPD4vMpSEKrOJZABLJntS', '2026-11-02 20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  `subscription` int(10) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `userid`, `subscription`, `started_at`, `expires`) VALUES
(4, 2, 8, '2026-11-23 19:53:38', '2026-12-23 19:53:38'),
(5, 3, 7, '2026-11-02 19:58:08', '2026-12-02 19:58:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_store_time` (`store_id`,`is_active`,`starts_at`,`ends_at`),
  ADD KEY `fk_events_user` (`created_by`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_favorites_user_store` (`userid`,`storeid`),
  ADD KEY `idx_favorites_store` (`storeid`);

--
-- Indexes for table `loyalty_cards`
--
ALTER TABLE `loyalty_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cards_token` (`qr_token`),
  ADD UNIQUE KEY `uq_cards_user_store` (`user_id`,`store_id`),
  ADD UNIQUE KEY `uq_cards_user_group` (`user_id`,`group_id`),
  ADD KEY `idx_cards_store` (`store_id`),
  ADD KEY `idx_cards_group` (`group_id`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_milestones_store` (`store_id`,`is_active`,`points_required`),
  ADD KEY `idx_milestones_group` (`group_id`,`is_active`,`points_required`),
  ADD KEY `idx_milestones_origin_store` (`origin_store_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_id`,`is_read`,`created_at`),
  ADD KEY `fk_notifications_store` (`store_id`);

--
-- Indexes for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transactions_card` (`card_id`,`created_at`),
  ADD KEY `idx_transactions_store_time` (`store_id`,`created_at`),
  ADD KEY `fk_transactions_staff` (`staff_user_id`),
  ADD KEY `fk_transactions_event` (`event_id`),
  ADD KEY `fk_transactions_milestone` (`milestone_id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pricing_tier` (`tier`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_stores_owner` (`owner_userid`),
  ADD KEY `idx_stores_group` (`group_id`),
  ADD KEY `idx_stores_name` (`name`);

--
-- Indexes for table `store_groups`
--
ALTER TABLE `store_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_groups_owner` (`owner_userid`);

--
-- Indexes for table `store_messages`
--
ALTER TABLE `store_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_store` (`store_id`,`is_active`,`trigger_type`);

--
-- Indexes for table `store_staff`
--
ALTER TABLE `store_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_staff_store_user` (`store_id`,`user_id`),
  ADD KEY `idx_staff_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_subscriptions_user` (`userid`),
  ADD KEY `idx_subscriptions_plan` (`subscription`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `loyalty_cards`
--
ALTER TABLE `loyalty_cards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `point_transactions`
--
ALTER TABLE `point_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_groups`
--
ALTER TABLE `store_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `store_messages`
--
ALTER TABLE `store_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `store_staff`
--
ALTER TABLE `store_staff`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_events_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_store` FOREIGN KEY (`storeid`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loyalty_cards`
--
ALTER TABLE `loyalty_cards`
  ADD CONSTRAINT `fk_cards_group` FOREIGN KEY (`group_id`) REFERENCES `store_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cards_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cards_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `milestones`
--
ALTER TABLE `milestones`
  ADD CONSTRAINT `fk_milestones_group` FOREIGN KEY (`group_id`) REFERENCES `store_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_milestones_origin_store` FOREIGN KEY (`origin_store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_milestones_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD CONSTRAINT `fk_transactions_card` FOREIGN KEY (`card_id`) REFERENCES `loyalty_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transactions_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transactions_milestone` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transactions_staff` FOREIGN KEY (`staff_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transactions_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `fk_stores_group` FOREIGN KEY (`group_id`) REFERENCES `store_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_stores_owner` FOREIGN KEY (`owner_userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_groups`
--
ALTER TABLE `store_groups`
  ADD CONSTRAINT `fk_groups_owner` FOREIGN KEY (`owner_userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_messages`
--
ALTER TABLE `store_messages`
  ADD CONSTRAINT `fk_messages_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `store_staff`
--
ALTER TABLE `store_staff`
  ADD CONSTRAINT `fk_staff_store` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`subscription`) REFERENCES `pricing` (`id`),
  ADD CONSTRAINT `fk_subscriptions_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
