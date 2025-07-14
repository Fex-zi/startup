-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2025 at 11:00 PM
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
-- Database: `startup_investor_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `industries`
--

CREATE TABLE `industries` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `industries`
--

INSERT INTO `industries` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Technology', 'technology', 'Software, hardware, and tech-enabled services', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(2, 'Healthcare', 'healthcare', 'Medical devices, pharmaceuticals, and health services', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(3, 'Fintech', 'fintech', 'Financial technology and services', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(4, 'E-commerce', 'ecommerce', 'Online retail and marketplace platforms', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(5, 'SaaS', 'saas', 'Software as a Service platforms', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(6, 'Artificial Intelligence', 'ai', 'AI and machine learning solutions', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(7, 'Blockchain', 'blockchain', 'Cryptocurrency and blockchain technology', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(8, 'Clean Energy', 'clean-energy', 'Renewable energy and sustainability', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(9, 'EdTech', 'edtech', 'Educational technology and learning platforms', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(10, 'Food & Beverage', 'food-beverage', 'Food production, restaurants, and beverage', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(11, 'Real Estate', 'real-estate', 'Property technology and real estate services', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(12, 'Transportation', 'transportation', 'Logistics, mobility, and transportation', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(13, 'Entertainment', 'entertainment', 'Media, gaming, and entertainment', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(14, 'Agriculture', 'agriculture', 'AgTech and agricultural innovation', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(15, 'Manufacturing', 'manufacturing', 'Industrial and manufacturing technology', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(16, 'Retail', 'retail', 'Physical and omnichannel retail', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(17, 'Cybersecurity', 'cybersecurity', 'Information security and privacy', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(18, 'IoT', 'iot', 'Internet of Things and connected devices', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(19, 'Biotech', 'biotech', 'Biotechnology and life sciences', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(20, 'Consumer Products', 'consumer-products', 'Consumer goods and services', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03');

-- --------------------------------------------------------

--
-- Table structure for table `investors`
--

CREATE TABLE `investors` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `investor_type` enum('angel','vc_firm','corporate','family_office') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `preferred_industries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferred_industries`)),
  `investment_stages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`investment_stages`)),
  `investment_range_min` decimal(15,2) DEFAULT NULL,
  `investment_range_max` decimal(15,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `portfolio_companies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`portfolio_companies`)),
  `availability_status` enum('actively_investing','selective','not_investing') DEFAULT 'actively_investing',
  `linkedin_url` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(10) UNSIGNED NOT NULL,
  `startup_id` int(10) UNSIGNED NOT NULL,
  `investor_id` int(10) UNSIGNED NOT NULL,
  `match_score` int(11) NOT NULL,
  `match_reasons` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`match_reasons`)),
  `startup_interested` tinyint(1) DEFAULT NULL,
  `investor_interested` tinyint(1) DEFAULT NULL,
  `status` enum('pending','mutual_interest','startup_declined','investor_declined','expired') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `startups`
--

CREATE TABLE `startups` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `industry_id` int(10) UNSIGNED DEFAULT NULL,
  `stage` enum('idea','prototype','mvp','early_revenue','growth') NOT NULL,
  `employee_count` enum('1','2-5','6-10','11-25','26-50','51+') DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `pitch_deck_url` varchar(255) DEFAULT NULL,
  `funding_goal` decimal(15,2) DEFAULT NULL,
  `funding_type` enum('seed','series_a','series_b','debt','grant') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `startups`
--

INSERT INTO `startups` (`id`, `user_id`, `company_name`, `slug`, `description`, `industry_id`, `stage`, `employee_count`, `website`, `logo_url`, `pitch_deck_url`, `funding_goal`, `funding_type`, `location`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fexzi tech', 'fexzi-tech', 'sgghdhfhtjhtjtj', 7, 'early_revenue', '1', '', NULL, NULL, 20000.00, 'grant', 'usa', 0, '2025-07-01 18:33:56', '2025-07-01 18:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('startup','investor') NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `profile_completed` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `user_type`, `first_name`, `last_name`, `phone`, `location`, `email_verified_at`, `email_verification_token`, `profile_completed`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'the.emmanuel.ifeanyi@gmail.com', '$2y$10$ij850VIS/1nqwUyNLtiKYuaxXL9v2Djl3ONpKdcPAkbaJIUboYw.G', 'startup', 'Ifeanyi', 'Ojukwu', NULL, NULL, NULL, '524ca353def78ab9cc150fb55a88b49b', 1, 1, '2025-07-01 18:10:42', '2025-07-14 16:31:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `industries`
--
ALTER TABLE `industries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `investors`
--
ALTER TABLE `investors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_investor_type` (`investor_type`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_investment_range` (`investment_range_min`,`investment_range_max`),
  ADD KEY `idx_availability` (`availability_status`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_match` (`startup_id`,`investor_id`),
  ADD KEY `idx_startup` (`startup_id`),
  ADD KEY `idx_investor` (`investor_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_score` (`match_score`);

--
-- Indexes for table `startups`
--
ALTER TABLE `startups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_industry` (`industry_id`),
  ADD KEY `idx_stage` (`stage`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_funding_goal` (`funding_goal`),
  ADD KEY `idx_slug` (`slug`);
ALTER TABLE `startups` ADD FULLTEXT KEY `idx_search` (`company_name`,`description`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_email_verification` (`email_verification_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `industries`
--
ALTER TABLE `industries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `investors`
--
ALTER TABLE `investors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `startups`
--
ALTER TABLE `startups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `investors`
--
ALTER TABLE `investors`
  ADD CONSTRAINT `investors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`startup_id`) REFERENCES `startups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`investor_id`) REFERENCES `investors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `startups`
--
ALTER TABLE `startups`
  ADD CONSTRAINT `startups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `startups_ibfk_2` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
