-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 21, 2025 at 04:17 PM
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
  `del_no` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `id_kategori` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama`, `satuan`, `del_no`, `id_kategori`, `created_at`, `updated_at`) VALUES
('BRG0001', 'BRAKE SHOE HONDA ASP', 'SATUAN 1', 'Box', 'KTG01', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('BRG0002', 'BRAKE SHOE KHARISMA', 'SATUAN 1', 'Box', 'KTG02', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('BRG0003', 'BRAKE SHOE SUPRA FED', 'SATUAN 1', 'Box', 'KTG01', '2025-08-18 05:01:13', '2025-08-18 05:01:13');

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
(1, 'KRM20160820001', 'BRG0001', 1, '2025-08-18 05:01:14', '2025-08-18 05:01:14'),
(2, 'KRM20160820001', 'BRG0002', 3, '2025-08-18 05:01:14', '2025-08-18 05:01:14');

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
('KTG01', 'KATEGORI 1', 'KATEGORI 1', NULL, NULL),
('KTG02', 'KATEGORI 2', 'KATEGORI 2', NULL, NULL),
('KTG03', 'KATEGORI 3', 'KATEGORI 3', NULL, NULL);

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
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `nama`, `jenis_kelamin`, `telepon`, `alamat`, `password`, `created_at`, `updated_at`) VALUES
('KRR01', 'EKO', 'Laki-Laki', '081385195955', 'TANGERANG', '$argon2id$v=19$m=65536,t=4,p=1$dWN4T1dKeTVkUW8zTkJFQQ$WbcJ0iwRjcEz9zU/SG8iOMEXXvFuxRFbcrXlcBh+lUM', '2025-08-18 05:01:14', '2025-08-18 05:01:14'),
('KRR02', 'ERIK', 'Laki-Laki', '081284959589', 'TANGERANG', '$argon2id$v=19$m=65536,t=4,p=1$ZnpDOTJIYmhqOU1wTFlZaQ$5IdmaqiXjfcVgcdWQplzL8xQVcPvbDoAt6m26vE28qQ', '2025-08-18 05:01:14', '2025-08-18 05:01:14'),
('KRR03', 'TRIBUDI', 'Laki-Laki', '081219900381', 'TANGERANG', '$argon2id$v=19$m=65536,t=4,p=1$Z0NOTGVITUhJWG5za0RKUQ$Wm1jJXcn+ZpmwTMMDXE2LkY06wXnMmFBSUTe8DEr198', '2025-08-18 05:01:14', '2025-08-18 05:01:14');

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
(62, '2024-01-01-000009', 'App\\Database\\Migrations\\AddSecurityIndexes', 'default', 'App', 1755468081, 1);

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
('CST0001', 'ASTRA OTOPART', '021-4603550', 'jakarta', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('CST0002', 'Idemitsu Lube Indonesia', '021-8911 4611', 'JL Permata Raya, Kawasan Industri KIIC, Lot BB/4A, Karawang, Jawa Barat,', '2025-08-18 05:01:13', '2025-08-18 05:01:13'),
('CST0003', 'Federal Karyatama', '021-4613583', 'Jl. Pulobuaran Raya, RW.9, Jatinegara, Cakung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13910', '2025-08-18 05:01:13', '2025-08-18 05:01:13');

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

INSERT INTO `pengiriman` (`id_pengiriman`, `tanggal`, `id_pelanggan`, `id_kurir`, `no_kendaraan`, `no_po`, `keterangan`, `penerima`, `photo`, `status`, `created_at`, `updated_at`) VALUES
('KRM20160820001', '2016-08-20', 'CST0001', 'KRR01', 'B021ZIG', '8732984732984', NULL, NULL, NULL, 1, '2025-08-18 05:01:14', '2025-08-18 05:01:14');

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
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `level`, `created_at`, `updated_at`) VALUES
('USR01', 'adminpuninar', '$argon2id$v=19$m=65536,t=4,p=1$N3gub1d5b01yVUkvU1Mycg$zirbhmTw7GnyY4nEnzYN+wQxAMrBDDbfj2V/jlka8pk', 1, '2025-08-18 12:37:33', '2025-08-18 12:37:33'),
('USR02', 'financepuninar', '$argon2id$v=19$m=65536,t=4,p=1$cUVmU2dKaWM1QW51WS9sdA$PSaSauMee7xMPaOjhM7R37c6aEnQw2F9QLpgMUxXrvA', 2, '2025-08-18 12:37:34', '2025-08-18 12:37:34'),
('USR03', 'gudangpuninar', '$argon2id$v=19$m=65536,t=4,p=1$QmF2ODVWLkM3RllEbGFJLw$jaIdYexnUz1/mcZFV7thmwDDYwOwWDbXsrrupW4sfiM', 3, '2025-08-18 12:37:34', '2025-08-18 12:37:34');

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
  ADD KEY `idx_kurir_nama` (`nama`);

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
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

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
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_id_kurir_foreign` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pengiriman_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
