-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 25, 2025 at 10:09 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `puninar_logistic`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int UNSIGNED NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `setting_type` enum('string','integer','boolean','json') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'string',
  `setting_group` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'general',
  `setting_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if setting can be accessed by non-admin users',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `setting_group`, `setting_description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'PuninarLogistic', 'string', 'application', 'Application name displayed throughout the system', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(2, 'app_version', '1.0.0', 'string', 'application', 'Current application version', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(3, 'timezone', 'Asia/Jakarta', 'string', 'application', 'System timezone', 0, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(4, 'company_name', 'PT. Puninar Logistik Indonesia', 'string', 'company', 'Company name for reports and documents', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(5, 'company_address', 'Jakarta, Indonesia', 'string', 'company', 'Company address for reports and documents', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(6, 'company_phone', '+62-21-1234567', 'string', 'company', 'Company phone number', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(7, 'date_format', 'd/m/Y', 'string', 'display', 'Default date format for display', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(8, 'items_per_page', '15', 'integer', 'display', 'Number of items to show per page in listings', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(9, 'backup_enabled', '1', 'boolean', 'system', 'Enable automatic database backups', 0, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(10, 'maintenance_mode', '0', 'boolean', 'system', 'Enable maintenance mode', 0, '2025-08-25 16:49:46', '2025-08-25 16:49:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `setting_group` (`setting_group`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
