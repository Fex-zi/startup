-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 12:20 AM
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
(20, 'Consumer Products', 'consumer-products', 'Consumer goods and services', 1, '2025-07-01 18:18:03', '2025-07-01 18:18:03'),
(24, 'Education', 'education', 'EdTech and learning platforms', 1, '2025-07-15 17:26:42', '2025-07-15 17:26:42');

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
  `profile_picture_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investors`
--

INSERT INTO `investors` (`id`, `user_id`, `investor_type`, `company_name`, `bio`, `preferred_industries`, `investment_stages`, `investment_range_min`, `investment_range_max`, `location`, `portfolio_companies`, `availability_status`, `profile_picture_url`, `linkedin_url`, `website`, `avatar_url`, `created_at`, `updated_at`) VALUES
(1, 2, 'corporate', 'Kokolet Investor', 'I\'m a billionaire investor mehn', '[\"7\",\"17\",\"13\"]', '[\"idea\",\"prototype\",\"mvp\",\"early_revenue\",\"growth\"]', 10000.00, 50000.00, 'Utah', NULL, 'actively_investing', NULL, '', '', NULL, '2025-07-15 03:06:44', '2025-07-15 18:20:29'),
(5, 11, '', 'TechVentures Capital', 'Early-stage venture capital fund focused on technology startups', '[1,3]', '[\"seed\",\"series_a\"]', 100000.00, 5000000.00, 'New York, NY', NULL, 'actively_investing', NULL, NULL, 'https://techventures.com', NULL, '2025-07-15 18:11:01', '2025-07-15 18:11:01'),
(6, 12, 'angel', 'Angel Syndicate Boston', 'Angel investor network specializing in healthcare and education technology', '[2,24]', '[\"pre_seed\",\"seed\"]', 25000.00, 500000.00, 'Boston, MA', NULL, 'actively_investing', NULL, NULL, 'https://angelsyndicateboston.com', NULL, '2025-07-15 18:11:01', '2025-07-15 18:11:01');

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
  `business_plan_url` varchar(255) DEFAULT NULL,
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

INSERT INTO `startups` (`id`, `user_id`, `company_name`, `slug`, `description`, `industry_id`, `stage`, `employee_count`, `website`, `business_plan_url`, `logo_url`, `pitch_deck_url`, `funding_goal`, `funding_type`, `location`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fexzi tech', 'fexzi-tech', 'I\'m Just a chill guy', 6, 'idea', '1', 'https://fexzihub.com', 'documents/business-plans/Fundraising_Website_2025-2026_689bbc9ab65cb6.56667554.docx', 'images/logos/profile-pix_689bbc9ab01ad9.56438764.png', 'documents/pitch-decks/Connection_to_your_Student_689bbc9ab15142.88097191.pdf', 26000.00, 'seed', 'utah', 0, '2025-07-15 20:40:31', '2025-08-12 22:13:46'),
(5, 9, 'TechNova Solutions', 'technova-solutions', 'AI-powered business automation platform for small businesses', 1, 'idea', NULL, NULL, NULL, NULL, NULL, 500000.00, NULL, 'San Francisco, CA', 0, '2025-07-15 18:11:01', '2025-07-15 18:11:01'),
(6, 10, 'HealthTrack Pro', 'healthtrack-pro', 'Digital health platform for chronic disease management', 2, 'idea', NULL, NULL, NULL, NULL, NULL, 2000000.00, NULL, 'Austin, TX', 0, '2025-07-15 18:11:01', '2025-07-15 18:11:01');

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
(1, 'the.emmanuel.ifeanyi@gmail.com', '$2y$10$rd9apX6hoPIwnhGxedkX0uX2eeS0EKgAx7j9fBwQnf0vvlRbjB3MW', 'startup', 'Ifeanyi', 'Ojukwu', '', '', NULL, '04a833e0bf5a89d4eaf54da02a5c8c3e', 1, 1, '2025-07-02 00:10:42', '2025-07-31 16:32:03'),
(2, 'fexzi87@gmail.com', '$2y$10$rd9apX6hoPIwnhGxedkX0uX2eeS0EKgAx7j9fBwQnf0vvlRbjB3MW', 'investor', 'Mr Dimanco', 'investor', NULL, NULL, NULL, '04a833e0bf5a89d4eaf54da02a5c8c3e', 1, 1, '2025-07-15 03:04:45', '2025-07-31 16:32:05'),
(9, 'founder1@testcompany.com', '$2y$10$kz/yczj6wIJhW/hOHW./xusxGbdSCDRXaGGTTfyZt.jhg.hq7VIma', 'startup', 'John', 'Smith', NULL, 'San Francisco, CA', NULL, NULL, 1, 1, '2025-07-15 18:11:01', '2025-07-15 18:11:01'),
(10, 'founder2@techstartup.com', '$2y$10$gCK5Cv6Gx4/5X2Q0rJ4tTuxs0KMd0d8ld5rbyXqEUNvGI3z6h2FA6', 'startup', 'Sarah', 'Johnson', NULL, 'Austin, TX', NULL, NULL, 1, 1, '2025-07-15 18:11:01', '2025-07-15 18:11:01'),
(11, 'investor1@vcfund.com', '$2y$10$UKV2ktCP98CMWzft3TsCTeORkD4ecTO/eLcQ7T0cT5..qwWHX43Au', 'investor', 'Michael', 'Brown', NULL, 'New York, NY', NULL, NULL, 1, 1, '2025-07-15 18:11:01', '2025-07-15 18:11:01'),
(12, 'investor2@angelgroup.com', '$2y$10$v7KR/aK51kur7TNffBlRiO7OH0ErMi12wdDW6bJqojMMYJEBxmDPi', 'investor', 'Emily', 'Davis', NULL, 'Boston, MA', NULL, NULL, 1, 1, '2025-07-15 18:11:01', '2025-07-15 18:11:01');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `investors`
--
ALTER TABLE `investors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `startups`
--
ALTER TABLE `startups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
