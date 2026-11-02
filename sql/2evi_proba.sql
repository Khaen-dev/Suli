-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2026 at 09:15 PM
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
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `storeid`, `userid`) VALUES
(22, 4, 2),
(23, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `store_count` int(11) DEFAULT NULL,
  `milestone_limit` int(11) DEFAULT NULL,
  `allow_shared_db` tinyint(1) DEFAULT 0,
  `allow_events` tinyint(1) DEFAULT 0,
  `allow_export` tinyint(1) DEFAULT 0,
  `allow_priority_support` tinyint(1) DEFAULT 0,
  `tier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `name`, `price`, `store_count`, `milestone_limit`, `allow_shared_db`, `allow_events`, `allow_export`, `allow_priority_support`, `tier`) VALUES
(6, 'Kezdő', 1000, 1, 3, 0, 0, 0, 0, 1),
(7, 'Haladó', 4990, 5, NULL, 1, 1, 0, 0, 2),
(8, 'Üzleti', 12990, NULL, NULL, 1, 1, 1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `type` varchar(1000) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_name` varchar(1000) NOT NULL,
  `contact_email` varchar(1000) NOT NULL,
  `tel` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `userid`, `name`, `type`, `address`, `contact_name`, `contact_email`, `tel`) VALUES
(4, 1, 'Starbucks', 'Kávézó', 'Budapest, Pesti út 12, 1172', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307'),
(5, 1, 'Lipóti Pékség', 'Pékség', 'Budapest, Ferihegyi út 64, 1173', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307'),
(6, 1, 'Bio Hair', 'Fodrászat', 'Budapest, Budapesti út 24, 1164', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307'),
(8, 1, 'Lipóti Pékség', 'Kávézó', 'Budapest, Pesti út 12, 1172', 'Bagó Botond Dominik', 'bagoboti@gmail.com', '+36309122307'),
(10, 1, 'Domi Pizzázója', 'Pizzázó', 'Budapest, Budapesti út 24, 1164', 'Kertész Dominik', 'keridomi@gmail.com', '+36309122307');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `last_name` varchar(1000) NOT NULL,
  `first_name` varchar(1000) NOT NULL,
  `password` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `last_name`, `first_name`, `password`) VALUES
(1, 'bagoboti@gmail.com', 'Bagó', 'Botond', '$2y$10$g9PMO9pJ/j2KJsGGacA4vODtvWReSVYKZvHqxTsDBFbhuRH2ZiLDa'),
(2, 'bkoni2008@gmail.com', 'Bordas', 'Konrad', '$2y$10$Z85TZexkGUWh2G2GmabRReSzCFWNFU2HtPD4vMpSEKrOJZABLJntS');

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `expires` datetime NOT NULL,
  `subscription` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `userid`, `expires`, `subscription`) VALUES
(4, 1, '2026-12-02 19:36:31', 7),
(5, 2, '2026-12-02 19:58:08', 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
