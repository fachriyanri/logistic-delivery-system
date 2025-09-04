-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 02, 2025 at 10:48 AM
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
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `satuan` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `id_kategori` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama`, `satuan`, `id_kategori`, `created_at`, `updated_at`, `harga`) VALUES
('BRG0001', 'BRAKE SHOE HONDA ASP', 'SATUAN 1', 'KTG01', '2025-08-18 05:01:13', '2025-08-18 05:01:13', 10000.00),
('BRG0002', 'BRAKE SHOE KHARISMA', 'SATUAN 1', 'KTG02', '2025-08-18 05:01:13', '2025-08-18 05:01:13', 20000.00),
('BRG0003', 'BRAKE SHOE SUPRA FIT', 'BOX', 'KTG02', '2025-08-18 05:01:13', '2025-08-18 05:01:13', 100000.00),
('BRG0004', 'SHOCKBREAKER KYT', 'BOX', 'KTG01', NULL, NULL, 1000000.00),
('BRG0005', 'SHOCK BREAKER KYT VENOM', 'BOX', 'KTG02', NULL, NULL, 1500000.00),
('BRG0006', 'CSF OE Style Radiator E36 BMW', 'BOX', 'KTG04', NULL, NULL, 3500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pengiriman`
--

CREATE TABLE `detail_pengiriman` (
  `id_detail` int NOT NULL,
  `id_pengiriman` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `id_barang` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `qty` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pengiriman`
--

INSERT INTO `detail_pengiriman` (`id_detail`, `id_pengiriman`, `id_barang`, `qty`, `created_at`, `updated_at`) VALUES
(3, 'KRM20250827001', 'BRG0005', 2, '2025-08-27 00:46:57', '2025-08-27 00:46:57'),
(4, 'KRM20250827002', 'BRG0004', 2, '2025-08-27 00:56:00', '2025-08-27 00:56:00'),
(21, 'KRM20250829001', 'BRG0001', 2, '2025-08-29 19:57:09', '2025-08-29 19:57:09'),
(22, 'KRM20250829001', 'BRG0004', 1, '2025-08-29 19:57:09', '2025-08-29 19:57:09'),
(33, 'KRM20250901001', 'BRG0006', 3, '2025-09-01 16:49:59', '2025-09-01 16:49:59'),
(36, 'KRM20250828001', 'BRG0001', 4, '2025-09-02 15:48:24', '2025-09-02 15:48:24'),
(37, 'KRM20250828001', 'BRG0004', 3, '2025-09-02 15:48:24', '2025-09-02 15:48:24'),
(38, 'KRM20160820001', 'BRG0001', 1, '2025-09-02 15:51:14', '2025-09-02 15:51:14'),
(39, 'KRM20160820001', 'BRG0002', 3, '2025-09-02 15:51:14', '2025-09-02 15:51:14');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama`, `keterangan`, `created_at`, `updated_at`) VALUES
('KTG01', 'Sparepart Motor Honda', NULL, NULL, NULL),
('KTG02', 'Sparepart Motor Yamaha', NULL, NULL, NULL),
('KTG03', 'Sparepart Mobil Toyota', NULL, NULL, NULL),
('KTG04', 'Sparepart mobil', 'mobil baru', '2025-08-25 01:47:07', '2025-08-25 01:47:07'),
('KTG05', 'sparepart ac', 'ac', '2025-08-25 04:42:18', '2025-08-25 04:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_kelamin` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `telepon` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_user` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `nama`, `jenis_kelamin`, `telepon`, `alamat`, `id_user`, `created_at`, `updated_at`) VALUES
('KRR01', 'EKO', 'Laki-Laki', '081385195955', 'TANGERANG SELATAN', NULL, '2025-08-18 05:01:14', '2025-09-01 12:48:42'),
('KRR02', 'ERIK', 'Laki-Laki', '081284959589', 'TANGERANG', NULL, '2025-08-18 05:01:14', '2025-08-18 05:01:14'),
('KRR03', 'TRIBUDI', 'Laki-Laki', '081219900381', 'TANGERANG', NULL, '2025-08-18 05:01:14', '2025-08-18 05:01:14'),
('KRR05', 'AHMAD RIZAL', 'Laki-Laki', '081930100123', 'JAKARTA UTARA', 'USR05', '2025-08-25 23:49:58', '2025-08-25 23:50:13'),
('KRR06', 'TINA', 'Perempuan', '081385195954', 'BEKASI', 'USR06', '2025-09-01 12:49:47', '2025-09-01 12:49:47'),
('KRR07', 'EKI', 'Laki-Laki', '0856901139017', 'DEPOK', 'USR07', '2025-09-01 16:51:33', '2025-09-01 16:51:33');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(54, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateUserTable', 'default', 'App', 1755468068, 1),
(55, '2024-01-01-000002', 'App\\Database\\Migrations\\CreateKategoriTable', 'default', 'App', 1755468069, 1),
(56, '2024-01-01-000003', 'App\\Database\\Migrations\\CreateBarangTable', 'default', 'App', 1755468069, 1),
(57, '2024-01-01-000004', 'App\\Database\\Migrations\\CreateKurirTable', 'default', 'App', 1755468070, 1),
(58, '2024-01-01-000005', 'App\\Database\\Migrations\\CreatePelangganTable', 'default', 'App', 1755468070, 1),
(59, '2024-01-01-000006', 'App\\Database\\Migrations\\CreatePengirimanTable', 'default', 'App', 1755468071, 1),
(60, '2024-01-01-000007', 'App\\Database\\Migrations\\CreateDetailPengirimanTable', 'default', 'App', 1755468072, 1),
(61, '2024-01-01-000008', 'App\\Database\\Migrations\\MigrateExistingData', 'default', 'App', 1755468075, 1),
(62, '2024-01-01-000009', 'App\\Database\\Migrations\\AddSecurityIndexes', 'default', 'App', 1755468081, 1),
(63, '2025-08-25-000002', 'App\\Database\\Migrations\\AddIsActiveToUserTable', 'default', 'App', 1756115241, 2),
(64, '2025-08-25-000003', 'App\\Database\\Migrations\\CreateSettingsTable', 'default', 'App', 1756115386, 3),
(65, '2025-09-01-000001', 'App\\Database\\Migrations\\AddUserRelationToKurir', 'default', 'App', 1756720025, 4);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `telepon` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `telepon`, `alamat`, `created_at`, `updated_at`) VALUES
('CST0001', 'ASTRA OTOPART', '021-4603550', 'Jl. Raya Pegangsaan Dua Km 2.2, Kelapa Gading, Jakarta Utara, Indonesia 14250', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('CST0002', 'Idemitsu Lube Indonesia', '021-8911 4611', 'JL Permata Raya, Kawasan Industri KIIC, Lot BB/4A, Karawang, Jawa Barat,', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('CST0003', 'Federal Karyatama', '021-4613583', 'Jl. Pulobuaran Raya, RW.9, Jatinegara, Cakung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13910', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('CST0004', 'BMW ASTRA CILANDAK', '0217500335', 'Jl. R.A. Kartini No.Kav. 203, RT.11/RW.4, Cilandak Bar., Kec. Cilandak, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12430', NULL, NULL),
('CST0005', 'PT BYD MOTOR INDONESIA', '0219089011', 'Autograph Tower Thamrin Nine, level 50th, Jl. M.H. Thamrin No.10, Kb. Melati, Kecamatan Tanah Abang, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id_pengiriman` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `id_pelanggan` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `id_kurir` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `no_kendaraan` varchar(8) COLLATE utf8mb4_general_ci NOT NULL,
  `no_po` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `detail_location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penerima` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`id_pengiriman`, `tanggal`, `id_pelanggan`, `id_kurir`, `no_kendaraan`, `no_po`, `detail_location`, `keterangan`, `penerima`, `photo`, `status`, `created_at`, `updated_at`) VALUES
('KRM20160820001', '2016-08-20', 'CST0001', 'KRR05', 'B021ZIG', '8732984732984', '', 'barang pecah belah', 'REZA', NULL, 1, '2025-08-18 05:01:14', '2025-09-02 15:51:14'),
('KRM20250827001', '2025-08-27', 'CST0001', 'KRR03', 'B098921', 'PO20250827581', NULL, '', NULL, NULL, 1, NULL, NULL),
('KRM20250827002', '2025-08-27', 'CST0003', 'KRR03', 'B092181', 'PO20250827011', 'kalibata', '', NULL, NULL, 1, NULL, NULL),
('KRM20250828001', '2025-08-28', 'CST0002', 'KRR06', 'B4900QF', 'PO20250828301', 'TRANSIT DEPOK', '', '', NULL, 2, NULL, '2025-09-02 15:49:36'),
('KRM20250829001', '2025-08-29', 'CST0004', 'KRR05', 'B599SBY', 'PO20250829978', '', 'AFTERMARKET', NULL, NULL, 2, NULL, NULL),
('KRM20250901001', '2025-09-01', 'CST0004', 'KRR05', 'B40910QA', 'PO20250901782', 'TRANSIT BEKASI', '', 'AGUS', NULL, 3, '2025-09-01 15:33:01', '2025-09-02 15:44:50');

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
(1, 'app_name', 'PuninarLogistic', 'string', 'application', 'Application name displayed throughout the system', 1, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(2, 'app_version', '1.0.0', 'string', 'application', 'Current application version', 1, '2025-08-25 16:49:46', '2025-08-25 16:49:46'),
(3, 'timezone', 'Asia/Jakarta', 'string', 'application', 'System timezone', 0, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(4, 'company_name', 'PT. Puninar Logistik Indonesia', 'string', 'company', 'Company name for reports and documents', 1, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(5, 'company_address', 'Jakarta, Indonesia', 'string', 'company', 'Company address for reports and documents', 1, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(6, 'company_phone', '+62-21-1234565', 'string', 'company', 'Company phone number', 1, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(7, 'date_format', 'd/m/Y', 'string', 'display', 'Default date format for display', 1, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(8, 'items_per_page', '15', 'integer', 'display', 'Number of items to show per page in listings', 1, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(9, 'backup_enabled', '1', 'boolean', 'system', 'Enable automatic database backups', 0, '2025-08-25 16:49:46', '2025-08-25 18:34:14'),
(10, 'maintenance_mode', '0', 'boolean', 'system', 'Enable maintenance mode', 0, '2025-08-25 16:49:46', '2025-08-25 16:49:46');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `level`, `created_at`, `updated_at`, `is_active`) VALUES
('USR01', 'adminpuninar', '$argon2id$v=19$m=65536,t=4,p=1$ZXNndFVYdk03MHpUc3FtOA$BJfWQeZ7gBiyoqMSMD3c0uRI22LaEsprX0bxVv1gQlg', 1, '2025-08-18 12:37:33', '2025-08-23 18:34:11', 1),
('USR04', 'ahmadkamil', '$argon2id$v=19$m=65536,t=4,p=1$c0Z4dTZyUlBxUUFSWFplaA$O7KIZ1JX3n6p8sfyvOrvrnGvF+OTFU9T41qkGs/uDrY', 2, '2025-08-25 23:47:31', '2025-08-25 23:47:31', 1),
('USR05', 'ahmadrizal', '$argon2id$v=19$m=65536,t=4,p=1$UkZTZXB2eFFJNkV6YjVXRA$Jjv5VJ0Q4KIZMesGY3JElskCWs1Dwgf0+LYzJ6seBDs', 2, '2025-08-25 23:49:58', '2025-08-25 23:49:58', 1),
('USR06', 'tina', '$argon2id$v=19$m=65536,t=4,p=1$MHJ1bFhTZy52L1hDdHZobw$+HdvYXlCY07asAOIAhnGO4r7b5+cIlajHp3ROi2C8fw', 2, '2025-09-01 12:49:47', '2025-09-01 12:49:47', 1),
('USR07', 'eki', '$argon2id$v=19$m=65536,t=4,p=1$eHpOTzVnQ2dNT0JoNzdwZQ$uA+OrjsbWQ0W5rf0vMKInxZkHyKv5TZ43BNyrrqYFP8', 2, '2025-09-01 16:51:33', '2025-09-01 16:51:33', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `idx_barang_kategori` (`id_kategori`),
  ADD KEY `idx_barang_nama` (`nama`);

--
-- Indexes for table `detail_pengiriman`
--
ALTER TABLE `detail_pengiriman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `idx_detail_pengiriman` (`id_pengiriman`),
  ADD KEY `idx_detail_barang` (`id_barang`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `idx_kategori_nama` (`nama`);

--
-- Indexes for table `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD KEY `idx_kurir_nama` (`nama`),
  ADD KEY `kurir_id_user_foreign` (`id_user`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD KEY `idx_pelanggan_nama` (`nama`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `idx_pengiriman_tanggal` (`tanggal`),
  ADD KEY `idx_pengiriman_pelanggan` (`id_pelanggan`),
  ADD KEY `idx_pengiriman_kurir` (`id_kurir`),
  ADD KEY `idx_pengiriman_status` (`status`),
  ADD KEY `idx_pengiriman_date_status` (`tanggal`,`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `setting_group` (`setting_group`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_user_username` (`username`),
  ADD KEY `idx_user_level` (`level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pengiriman`
--
ALTER TABLE `detail_pengiriman`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_pengiriman`
--
ALTER TABLE `detail_pengiriman`
  ADD CONSTRAINT `detail_pengiriman_id_barang_foreign` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_pengiriman_id_pengiriman_foreign` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id_pengiriman`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kurir`
--
ALTER TABLE `kurir`
  ADD CONSTRAINT `kurir_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_id_kurir_foreign` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pengiriman_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
