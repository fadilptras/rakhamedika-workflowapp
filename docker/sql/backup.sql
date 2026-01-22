-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for rakha
CREATE DATABASE IF NOT EXISTS `rakha` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rakha`;

-- Dumping structure for table rakha.absensi
CREATE TABLE IF NOT EXISTS `absensi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('hadir','sakit','izin','cuti','tidak hadir') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan_keluar` text COLLATE utf8mb4_unicode_ci,
  `lampiran_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensi_user_id_tanggal_unique` (`user_id`,`tanggal`),
  CONSTRAINT `absensi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.absensi: ~28 rows (approximately)
INSERT INTO `absensi` (`id`, `user_id`, `tanggal`, `tanggal_keluar`, `jam_masuk`, `jam_keluar`, `status`, `keterangan`, `lampiran`, `latitude`, `longitude`, `keterangan_keluar`, `lampiran_keluar`, `latitude_keluar`, `longitude_keluar`, `created_at`, `updated_at`) VALUES
	(3, 3, '2025-09-11', NULL, '15:10:17', NULL, 'hadir', NULL, 'lampiran_absensi/svKLttmuXrkYMkhTPVP6WQQaKkMTuCjyYPFcXaHv.png', '-6.5994752', '106.807296', NULL, NULL, NULL, NULL, '2025-09-11 08:10:17', '2025-09-11 08:10:17'),
	(8, 3, '2025-09-12', NULL, '09:14:34', '20:19:49', 'hadir', NULL, 'lampiran_absensi/UB65EEXc6vxlTYBRSAFXyTuyhX79SfzDrZ0uHjLp.png', '-6.5741171', '106.8296488', NULL, 'lampiran_absensi_keluar/y8cTASbHhrf6rZPqVf630oKPxySmybQFsiXApAHe.png', '-6.3635456', '106.7941888', '2025-09-12 02:14:34', '2025-09-12 13:19:49'),
	(9, 2, '2025-09-13', NULL, '00:00:00', '07:31:32', 'cuti', 'Cuti tahunan: Liburan', 'lampiran_absensi/u0o3G4lgpNYL1XckcOopm9VZXcwCRvo2aNJS21ID.png', '-6.3504384', '106.7876352', NULL, 'lampiran_absensi_keluar/JxYrtphKH4EGyHNkf2zfYpGFn3VXqDMBOgPeLkuq.png', '-6.3504384', '106.7876352', '2025-09-13 00:31:10', '2025-09-13 16:46:13'),
	(10, 2, '2025-09-14', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan: Liburan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-13 16:46:13', '2025-09-13 16:46:13'),
	(11, 2, '2025-09-15', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan: Liburan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-13 16:46:13', '2025-09-13 16:46:13'),
	(31, 2, '2025-10-03', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-14 13:39:39', '2025-09-14 13:39:39'),
	(32, 2, '2025-10-04', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-14 13:39:39', '2025-09-14 13:39:39'),
	(33, 2, '2025-10-05', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-14 13:39:39', '2025-09-14 13:39:39'),
	(60, 2, '2025-09-17', NULL, '07:45:50', '19:53:09', 'hadir', NULL, 'lampiran_absensi/EjaBqS67TuKCo29V0MaprbHq98BGLo1JKTabNIWH.png', '-6.599795178653564', '106.81117431831485', NULL, 'lampiran_absensi_keluar/SyhU2onZs28MpwXLKTLgKnExvikuWKCqzwvgwU23.png', '-6.613995240674252', '106.8164346526313', '2025-09-17 00:45:50', '2025-09-18 12:53:09'),
	(69, 2, '2025-09-18', NULL, '10:17:16', NULL, 'hadir', 'Terlambat 10 Jam 17 Menit.', 'lampiran_absensi/c7jnt9jWpaXvsxXeVhxoma7N1RikT5TDTKU1g4Ig.png', '-6.5966065', '106.806895', NULL, NULL, NULL, NULL, '2025-09-18 11:17:16', '2025-09-18 11:17:16'),
	(71, 3, '2025-09-18', NULL, '18:18:43', NULL, 'izin', 'Personal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-18 11:18:43', '2025-09-18 11:18:43'),
	(76, 8, '2025-09-29', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-24 01:02:00', '2025-09-24 01:02:00'),
	(77, 8, '2025-09-30', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-24 01:02:00', '2025-09-24 01:02:00'),
	(78, 8, '2025-10-01', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-24 01:02:00', '2025-09-24 01:02:00'),
	(79, 8, '2025-10-02', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-24 01:02:00', '2025-09-24 01:02:00'),
	(80, 8, '2025-10-03', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-24 01:02:00', '2025-09-24 01:02:00'),
	(81, 2, '2025-09-27', NULL, '20:27:46', '20:28:06', 'hadir', 'Terlambat 12 Jam 27 Menit.', 'lampiran_absensi/N3RS7Rh9cpVMG2aInyqNcOU4iCEVVM68etAgFMFh.png', '-6.5965885', '106.8068875', NULL, 'lampiran_absensi_keluar/x9rHMNL69lUDrNOFQOXPBGXb2RDF8uJOuX72DgIf.png', '-6.5965885', '106.8068875', '2025-09-27 13:27:46', '2025-09-27 13:28:06'),
	(82, 2, '2025-12-01', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 14:01:36', '2025-09-28 14:01:36'),
	(83, 2, '2025-12-02', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 14:01:36', '2025-09-28 14:01:36'),
	(84, 2, '2025-12-03', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 14:01:36', '2025-09-28 14:01:36'),
	(85, 9, '2025-09-29', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 16:33:22', '2025-09-28 16:33:22'),
	(86, 9, '2025-09-30', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 16:33:22', '2025-09-28 16:33:22'),
	(87, 9, '2025-10-01', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 16:33:22', '2025-09-28 16:33:22'),
	(88, 9, '2025-10-02', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-28 16:33:22', '2025-09-28 16:33:22'),
	(95, 3, '2025-10-01', NULL, '22:51:31', '22:52:10', 'hadir', 'Terlambat 14 Jam 51 Menit.', 'lampiran_absensi/ngkrqYCPiwV1kJ10EedlOWWHF4t6R9xlpl3CADaF.png', '-6.6139598458904105', '106.81646970776256', NULL, 'lampiran_absensi_keluar/wd9edMgF5XgF2Jbk3jV1IbPpdY9JHom3CTsH3V0k.png', '-6.6139598458904105', '106.81646970776256', '2025-10-01 15:51:31', '2025-10-01 15:52:10'),
	(96, 8, '2025-10-22', NULL, '05:28:21', '05:28:32', 'hadir', NULL, 'lampiran_absensi/buy0aeno72DZBfqLL0bUXsBK8QILgdQpZB3I2kgi.png', '-6.613716126522911', '106.81661493844578', NULL, 'lampiran_absensi_keluar/fzKoe0jyvTzVu2YY6sees7g9rFUn4ZsaG0luaDTI.png', '-6.613716126522911', '106.81661493844578', '2025-10-21 22:28:21', '2025-10-21 22:28:32'),
	(97, 8, '2025-11-05', NULL, '19:16:54', NULL, 'hadir', 'Terlambat 11 Jam 16 Menit.', 'lampiran_absensi/uAV6NmFePrSfbYSa07WVByhOPjdRpOPrPK2EZDr7.png', '-6.3537152', '106.7843584', NULL, NULL, NULL, NULL, '2025-11-05 12:16:54', '2025-11-05 12:16:54'),
	(98, 8, '2025-11-13', NULL, '13:36:35', NULL, 'hadir', 'Terlambat 5 Jam 36 Menit.', 'lampiran_absensi/Q4TDejlitihtgSHaks0lWDIrlyVUEtazlsd9i29J.png', '-6.3635456', '106.774528', NULL, NULL, NULL, NULL, '2025-11-13 06:36:35', '2025-11-13 06:36:35'),
	(99, 2, '2025-11-26', '2025-11-27', '18:18:23', '10:50:17', 'hadir', 'Terlambat 10 Jam 18 Menit.', 'lampiran_absensi/y7yga760Holfuxe6yOmbamdaQNELThSFTEv5LsqT.png', '-6.2029824', '106.9056', NULL, 'lampiran_absensi_keluar/1rMBPe4Wb1yqazunt2os5tePFjs56nWwtNY6MWVl.png', '-6.6001449', '106.8111977', '2025-11-26 11:18:25', '2025-11-27 03:50:17'),
	(101, 8, '2025-11-27', '2025-11-27', '07:28:45', '10:52:28', 'hadir', NULL, 'lampiran_absensi/G8q7QzuHLRd4NaCJI8RZU2Q3t6aiw8PVAZlwu7Et.png', '-6.2029824', '106.9056', NULL, 'lampiran_absensi_keluar/V1rzDSVm8i2hVt26oy8VBQg47rWBeO78VRUtQ6ty.png', '-6.5994752', '106.8040192', '2025-11-27 00:28:45', '2025-11-27 03:52:28'),
	(102, 8, '2025-11-26', '2025-11-26', '21:03:59', '21:04:15', 'hadir', 'Terlambat 13 Jam 3 Menit.', 'lampiran_absensi/Vrt4QNq8xKaZzEsN16WpEuoErT1tbHk8dFoXAz8G.png', '-6.3881941', '106.8384873', NULL, 'lampiran_absensi_keluar/Fazla6KAVHpvmxKzAjbaUx6sXqpMJDKCI88a5IKX.png', '-6.3881941', '106.8384873', '2025-11-26 14:03:59', '2025-11-26 14:04:15'),
	(105, 3, '2025-11-27', '2025-11-27', '22:03:50', '22:04:15', 'hadir', 'Terlambat 14 Jam 3 Menit.', 'lampiran_absensi/EerzXdlWP38pqcxUHfYOnozr4ddfJmJQ7vsF2itr.png', '-6.3209472', '106.7679744', NULL, 'lampiran_absensi_keluar/B4u1AXrgQWZkpFTKN06NkfgWn7F2zi7jQOwL8ucE.png', '-6.3209472', '106.7679744', '2025-11-27 15:03:50', '2025-11-27 15:04:15'),
	(106, 2, '2025-11-27', '2025-11-27', '12:54:21', '12:54:39', 'hadir', 'Terlambat 4 Jam 54 Menit.', 'lampiran_absensi/tu1O1VrNKD4EbwhK3IGTiWz71Xp4MsuP6OS5hqPz.png', '-6.3209472', '106.7679744', NULL, 'lampiran_absensi_keluar/w351teyu8vfGnAgXBFbCm9ROdC2ZH2sZLPp8eChS.png', '-6.3209472', '106.7679744', '2025-11-27 05:54:21', '2025-11-27 05:54:39'),
	(107, 2, '2026-01-01', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-20 07:28:22', '2025-12-20 07:28:22'),
	(108, 2, '2026-01-02', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-20 07:28:22', '2025-12-20 07:28:22'),
	(109, 2, '2026-02-05', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 14:18:33', '2025-12-21 14:18:33'),
	(110, 2, '2026-02-06', NULL, '00:00:00', NULL, 'cuti', 'Cuti tahunan', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-21 14:18:33', '2025-12-21 14:18:33'),
	(111, 2, '2025-12-22', NULL, '08:00:26', NULL, 'hadir', 'Terlambat 0 Jam 0 Menit.', 'lampiran_absensi/21CcKAGpilpCIaszqvj6G15jMkVkj8BcWmTavqMc.png', '-6.3832064', '106.807296', NULL, NULL, NULL, NULL, '2025-12-22 01:00:26', '2025-12-22 01:00:26');

-- Dumping structure for table rakha.admin_users
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rakha.admin_users: ~0 rows (approximately)

-- Dumping structure for table rakha.agendas
CREATE TABLE IF NOT EXISTS `agendas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3788d8',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agendas_user_id_foreign` (`user_id`),
  CONSTRAINT `agendas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.agendas: ~10 rows (approximately)
INSERT INTO `agendas` (`id`, `user_id`, `title`, `start_time`, `end_time`, `description`, `location`, `color`, `created_at`, `updated_at`) VALUES
	(3, 2, 'Rapat Mingguan', '2025-09-26 09:00:00', NULL, 'Test', 'Kantor', '#3b82f6', '2025-09-25 14:10:21', '2025-09-25 14:10:21'),
	(6, 2, 'Kunjungan Promosi ke Bali', '2025-10-03 07:00:00', '2025-10-03 08:00:00', 'Ke RSUD Denpasar', 'Bali', '#f7963b', '2025-09-25 14:41:38', '2025-09-30 01:21:10'),
	(7, 2, 'tes', '2025-09-25 13:00:00', '2025-09-25 14:00:00', 'tes', 'Kantor', '#3b82f6', '2025-09-25 14:46:58', '2025-09-25 14:46:58'),
	(8, 2, 'Ke Jepang', '2025-09-27 04:00:00', '2025-09-27 05:00:00', 'Penelitian', NULL, '#3bf76a', '2025-09-25 15:00:36', '2025-09-25 15:00:36'),
	(9, 2, 'Seminar', '2025-09-26 09:00:00', '2025-09-26 10:00:00', 'Seminar', 'Depok', '#3b82f6', '2025-09-25 15:01:39', '2025-09-25 15:01:39'),
	(11, 1, 'Kunjungan Produksi', '2025-10-02 08:00:00', '2025-10-02 09:00:00', '.', 'Cianjur', '#f59e0b', '2025-09-28 05:58:54', '2025-09-28 05:58:54'),
	(13, 2, 'Eval', '2025-10-01 13:00:00', '2025-10-01 14:00:00', 'Test', 'Kantor', '#3b82f6', '2025-09-30 08:30:48', '2025-09-30 08:30:48'),
	(14, 2, 'Test', '2025-09-30 16:00:00', '2025-09-30 16:30:00', 'Test', 'Kantor', '#3b82f6', '2025-09-30 08:33:09', '2025-09-30 08:33:09'),
	(15, 2, 'Test', '2025-09-30 16:00:00', '2025-09-30 16:30:00', 'Test', 'Kantor', '#3b82f6', '2025-09-30 09:15:47', '2025-09-30 09:15:47'),
	(16, 2, 'p', '2025-10-08 12:00:00', '2025-10-08 13:00:00', '[p', 'Kantor', '#3b82f6', '2025-10-07 23:18:06', '2025-10-10 13:06:03'),
	(17, 2, 'Test', '2025-10-10 12:00:00', '2025-10-10 13:00:00', 'Test', 'Kantor', '#33ff0a', '2025-10-08 06:46:20', '2025-10-08 06:46:20'),
	(18, 8, 'biasa', '2025-10-09 02:15:00', '2025-10-09 03:00:00', 'biasa', 'Kantor', '#3b82f6', '2025-10-08 18:39:57', '2025-10-08 18:39:57'),
	(21, 2, 'o', '2025-10-16 07:00:00', '2025-10-16 09:00:00', 'o', 'Bali', '#3b82f6', '2025-10-14 20:36:39', '2025-10-14 20:36:39');

-- Dumping structure for table rakha.agenda_user
CREATE TABLE IF NOT EXISTS `agenda_user` (
  `agenda_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`agenda_id`,`user_id`),
  KEY `agenda_user_user_id_foreign` (`user_id`),
  CONSTRAINT `agenda_user_agenda_id_foreign` FOREIGN KEY (`agenda_id`) REFERENCES `agendas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `agenda_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.agenda_user: ~25 rows (approximately)
INSERT INTO `agenda_user` (`agenda_id`, `user_id`) VALUES
	(11, 2),
	(18, 2),
	(6, 3),
	(17, 3),
	(21, 3),
	(6, 6),
	(11, 6),
	(3, 8),
	(6, 8),
	(7, 8),
	(8, 8),
	(9, 8),
	(11, 8),
	(13, 8),
	(14, 8),
	(15, 8),
	(16, 8),
	(17, 8),
	(21, 8),
	(13, 9);

-- Dumping structure for table rakha.aktivitas
CREATE TABLE IF NOT EXISTS `aktivitas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lampiran` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aktivitas_user_id_foreign` (`user_id`),
  CONSTRAINT `aktivitas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.aktivitas: ~15 rows (approximately)
INSERT INTO `aktivitas` (`id`, `user_id`, `title`, `keterangan`, `lampiran`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
	(1, 2, 'kepo', 'kepo', 'public/aktivitas/7gJcTHHQiYDk1URyjpOgC5nk3CAOtUoaPttr5pJD.png', '-6.3635456', '106.774528', '2025-11-13 08:29:37', '2025-11-13 08:29:37'),
	(2, 2, 'Test', 'Test Aktivitas', 'public/aktivitas/0moSMXvNoWnsITWRRoaQMJNLT2FdeOd8sNgZtme1.png', '-6.6139423', '106.8162857', '2025-11-13 15:14:47', '2025-11-13 15:14:47'),
	(3, 2, 'Testing Aplikasi Fitur', 'Fitur Aktivitas', 'public/aktivitas/eMIULSfLTr8BuVgpBw2izU3kHmV3aMTfhvJTohc6.png', '-6.3635456', '106.774528', '2025-11-13 15:38:02', '2025-11-13 15:38:02'),
	(4, 2, 'Biasa Ngopi Santai', 'FInishing', 'public/aktivitas/q2QAAeMxcrRFes32g13jNqZOo3aEezXlp8YHffej.png', '-6.3635456', '106.774528', '2025-11-13 15:54:11', '2025-11-13 15:54:11'),
	(5, 2, 'tes', 'tes', 'public/aktivitas/1krm9S7ZQDYu3o3jRMxuNmLWyuYs3KmVARoCiUyV.png', '-6.3635456', '106.774528', '2025-11-13 16:20:59', '2025-11-13 16:20:59'),
	(6, 8, 'Testing', 'Testing Fitur', 'public/aktivitas/1B6QoFU6e7tGeiv2pVZ0lEjUklHYGMAGGV2g10MV.png', '-6.3635456', '106.774528', '2025-11-13 16:41:49', '2025-11-13 16:41:49'),
	(7, 2, 's', 's', 'public/aktivitas/6O6Vp94kMidpUHk3WrsF084rauC4Sj3R1FWQT3kZ.png', '-6.3635456', '106.774528', '2025-11-13 17:09:23', '2025-11-13 17:09:23'),
	(8, 8, 'Testing', 'Testing Fitur', 'public/aktivitas/iQGAIZh9oTGrywjD7trPIyqho564Z6GnGZWf1Y4V.png', '-6.3635456', '106.774528', '2025-11-13 17:25:26', '2025-11-13 17:25:26'),
	(9, 9, 'Testt', 'Testt', 'public/aktivitas/Dawmbab2gbUA8LlM4oyrHw8ZcezUgQIFC0vqlY1d.png', '-6.3635456', '106.774528', '2025-11-13 17:34:08', '2025-11-13 17:34:08'),
	(10, 3, 'Testing Fitur', 'Testing Fitur', 'public/aktivitas/oXljVKzulLTbWmH7c4v9ZNBy6G4aEa36vaTSY002.png', '-6.3209472', '106.7679744', '2025-11-14 15:41:42', '2025-11-14 15:41:42'),
	(11, 3, 'Test Malam', 'Test Malam', 'public/aktivitas/j24cD1izhia0llDs4ow7rUMPKYeTDNNKIZ40ZVoh.png', '-6.3209472', '106.7679744', '2025-11-14 16:01:14', '2025-11-14 16:01:14'),
	(12, 3, 'Test', 'Test', 'public/aktivitas/wn0CP4apVpjKakVO4ayMU6LLzho7XEWgAQ4WOiR7.png', '-6.3209472', '106.7483136', '2025-11-15 02:03:21', '2025-11-15 02:03:21'),
	(13, 3, 'tESTING TERUS', 'tESTING TERUS', 'aktivitas/kK9Gu6hXXx36vhpdGqS1haFftIfsYGJgSSXzw1Ic.png', '-6.5967291', '106.8069639', '2025-11-15 05:24:31', '2025-11-15 05:24:31'),
	(14, 3, 'Makan Siang', 'Makan Siang', 'aktivitas/d6i5u4JtJnXgw0oVnnosHx2bM9ZAA0BzuJkNTWP8.png', '-6.5967084', '106.8069458', '2025-11-15 05:29:29', '2025-11-15 05:29:29'),
	(15, 3, 'Uuuuuuu', 'Uuuuuuu', 'aktivitas/zi4NuMaOfFNgzf2UTrzxTTyyA79DCRtk6qyJs276.png', '-6.9181652', '106.93152', '2025-11-15 05:33:06', '2025-11-15 05:33:06'),
	(16, 2, 'Tes Terus', 'Tes Terus', 'aktivitas/flHokW5ROmb3p1ThXKT73j0w8FUwPA1OeBxEUA3L.png', '-6.9181652', '106.93152', '2025-11-15 06:15:51', '2025-11-15 06:15:51'),
	(17, 8, 'Testing', 'Testing', 'aktivitas/GE1gW8pnQMesWfxEobii53ksLvIz38RTCPJXmUrd.png', '-6.5994752', '106.8040192', '2025-11-22 03:33:01', '2025-11-22 03:33:01'),
	(18, 2, 'RApatt', 'RApatt', 'aktivitas/PcXv5bv7dDDNKPfQ7br02tqAE5BsbZ6ead4jo804.png', '-6.5994752', '106.8040192', '2025-11-22 04:31:37', '2025-11-22 04:31:37'),
	(19, 2, 'ooo', 'ooo', 'aktivitas/RyZshRbmkwjXEl8CkvlS0sx8ui8TPSOw3KoieEvP.png', '-6.422528', '106.7941888', '2025-12-19 13:39:04', '2025-12-19 13:39:04'),
	(20, 2, 'Tes', 'Tes', 'aktivitas/Mg3FTKGyANVWkG8PPiVuNgQv71zbr5KbZ4BqLqZ2.png', '-6.591781679279943', '106.81021664878767', '2026-01-15 10:20:14', '2026-01-15 10:20:14');

-- Dumping structure for table rakha.artikel
CREATE TABLE IF NOT EXISTS `artikel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `isi_konten` text NOT NULL,
  `url_gambar` varchar(255) DEFAULT NULL,
  `keterangan_gambar` varchar(255) DEFAULT NULL,
  `status` enum('draft','diterbitkan') DEFAULT 'draft',
  `waktu_dibuat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `waktu_terbit` timestamp NULL DEFAULT NULL,
  `waktu_diupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rakha.artikel: ~0 rows (approximately)

-- Dumping structure for table rakha.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.cache: ~0 rows (approximately)

-- Dumping structure for table rakha.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.cache_locks: ~0 rows (approximately)

-- Dumping structure for table rakha.clients
CREATE TABLE IF NOT EXISTS `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `nama_user` varchar(255) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `tanggal_berdiri` date DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `no_telpon` varchar(50) DEFAULT NULL,
  `alamat_user` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `alamat_perusahaan` text,
  `nama_di_rekening` varchar(70) DEFAULT NULL,
  `saldo_awal` decimal(15,2) DEFAULT '0.00',
  `bank` varchar(50) DEFAULT NULL,
  `no_rekening` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `hobby_client` text,
  PRIMARY KEY (`id`),
  KEY `clients_user_id_foreign` (`user_id`),
  CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rakha.clients: ~3 rows (approximately)
INSERT INTO `clients` (`id`, `user_id`, `area`, `pic`, `nama_user`, `nama_perusahaan`, `tanggal_berdiri`, `email`, `no_telpon`, `alamat_user`, `alamat_perusahaan`, `nama_di_rekening`, `saldo_awal`, `bank`, `no_rekening`, `created_at`, `updated_at`, `tanggal_lahir`, `jabatan`, `hobby_client`) VALUES
	(4, 2, 'Kota Bogor', 'Fadhillah Putra', 'Akbar', 'Rs PMI Bogor', '2009-02-03', 'pmi@bogor.com', '081289922400', 'Jl Pajajaran Indah 1 No. 79', 'Jakarta Selatan', 'MUHAMAD FADHILLAH PUTRA', 5000000.00, 'Mandiri', '13456789', '2025-12-01 02:46:02', '2025-12-19 14:45:44', '1997-10-14', 'Sales Man', 'Bersepedah'),
	(6, 2, 'Kota Bogor', 'Muhamad Fadhillah Putra SinagA', 'Cristiano Ronaldo 7', 'Al Nasr', NULL, 'cr@gmail.com', '0812312312', 'Arab Saudi', NULL, NULL, 300000000.00, 'Mandiri', '12312312', '2025-12-11 10:54:48', '2026-01-19 14:32:25', '2000-01-19', NULL, NULL),
	(7, 8, 'Jaksel', 'Estevao', 'Emanuel Putra', 'Yasa Tech', '2025-11-04', 'yasatech@gmail.com', '0812827728', 'Jl Pajajaran Indah 1 No.79', NULL, NULL, 8000000.00, 'Mandiri', '12123123', '2025-12-13 04:21:52', '2025-12-13 04:21:52', NULL, NULL, NULL);

-- Dumping structure for table rakha.cutis
CREATE TABLE IF NOT EXISTS `cutis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `jenis_cuti` enum('tahunan','sakit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('diajukan','disetujui','ditolak','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approver_id` bigint unsigned DEFAULT NULL,
  `catatan_approval` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cutis_user_id_foreign` (`user_id`),
  KEY `cutis_approver_id_foreign` (`approver_id`),
  CONSTRAINT `cutis_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`),
  CONSTRAINT `cutis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.cutis: ~17 rows (approximately)
INSERT INTO `cutis` (`id`, `user_id`, `jenis_cuti`, `tanggal_mulai`, `tanggal_selesai`, `alasan`, `lampiran`, `status`, `created_at`, `updated_at`, `approver_id`, `catatan_approval`) VALUES
	(9, 2, 'tahunan', '2025-09-13', '2025-09-15', 'Liburan', NULL, 'disetujui', '2025-09-13 16:35:43', '2025-09-13 16:46:13', NULL, 'jangan lama lama'),
	(10, 2, 'tahunan', '2025-09-13', '2025-09-14', 'sok', NULL, 'ditolak', '2025-09-13 16:51:32', '2025-09-13 16:53:42', NULL, NULL),
	(13, 2, 'tahunan', '2025-10-03', '2025-10-06', 'Nikahan', NULL, 'disetujui', '2025-09-13 17:36:32', '2025-09-14 13:39:39', NULL, NULL),
	(15, 2, 'tahunan', '2025-12-01', '2025-12-03', 'Ke Jepang', NULL, 'disetujui', '2025-09-21 13:36:20', '2025-09-28 14:01:36', NULL, NULL),
	(18, 8, 'tahunan', '2025-09-29', '2025-10-03', 'Liburan', NULL, 'disetujui', '2025-09-24 00:02:12', '2025-09-24 01:02:00', NULL, 'oKE AMAN'),
	(19, 9, 'tahunan', '2025-09-29', '2025-10-02', 'izin', NULL, 'disetujui', '2025-09-28 16:24:54', '2025-09-28 16:33:22', NULL, NULL),
	(20, 8, 'tahunan', '2025-12-24', '2025-12-25', 'Natal', NULL, 'dibatalkan', '2025-10-01 14:12:28', '2025-10-01 14:18:15', NULL, NULL),
	(21, 8, 'tahunan', '2025-10-16', '2025-10-17', 'libur', NULL, 'dibatalkan', '2025-10-01 14:57:58', '2025-10-01 15:01:23', NULL, 'ok'),
	(22, 2, 'tahunan', '2025-10-14', '2025-10-15', 'libur', NULL, 'dibatalkan', '2025-10-01 15:13:24', '2025-10-01 15:15:45', NULL, NULL),
	(23, 8, 'tahunan', '2025-10-30', '2025-10-31', 'Izin', NULL, 'diajukan', '2025-10-18 10:41:02', '2025-10-18 10:41:02', NULL, NULL),
	(24, 8, 'tahunan', '2025-12-22', '2025-12-23', 'Liburan', NULL, 'diajukan', '2025-12-20 05:11:14', '2025-12-20 05:11:14', NULL, NULL),
	(27, 2, 'tahunan', '2025-12-23', '2025-12-25', 'Izinn', NULL, 'diajukan', '2025-12-21 11:57:07', '2025-12-21 11:57:07', NULL, NULL),
	(28, 2, 'tahunan', '2025-12-23', '2025-12-25', 'Izinn', NULL, 'diajukan', '2025-12-21 11:58:05', '2025-12-21 11:58:05', NULL, NULL),
	(29, 8, 'tahunan', '2025-12-18', '2025-12-19', 'izinnn', NULL, 'diajukan', '2025-12-21 12:05:55', '2025-12-21 12:05:55', NULL, NULL),
	(33, 2, 'tahunan', '2026-01-17', '2026-01-17', 'Izinn', NULL, 'diajukan', '2026-01-15 14:21:55', '2026-01-15 14:21:55', NULL, NULL),
	(34, 2, 'tahunan', '2026-01-27', '2026-01-27', 'Izinnn', NULL, 'diajukan', '2026-01-15 14:23:26', '2026-01-15 14:23:26', NULL, NULL),
	(35, 8, 'tahunan', '2026-01-27', '2026-01-27', 'IZINN', NULL, 'diajukan', '2026-01-15 14:42:13', '2026-01-15 14:42:13', NULL, NULL);

-- Dumping structure for table rakha.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.failed_jobs: ~2 rows (approximately)
INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
	(1, 'febe65bd-c6f2-4b1d-a6e5-ad7de05ca62b', 'database', 'default', '{"uuid":"febe65bd-c6f2-4b1d-a6e5-ad7de05ca62b","displayName":"App\\\\Notifications\\\\CutiNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:3;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:34:\\"App\\\\Notifications\\\\CutiNotification\\":2:{s:4:\\"cuti\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\Cuti\\";s:2:\\"id\\";i:7;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"403ff3ad-2337-43ae-8a37-c065dbdcda96\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1757780306,"delay":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\Cuti]. in C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:750\nStack trace:\n#0 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(110): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): Illuminate\\Notifications\\Notification->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(97): Illuminate\\Notifications\\Notification->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: Illuminate\\Notifications\\Notification->__unserialize(Array)\n#4 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(95): unserialize(\'O:48:"Illuminat...\')\n#5 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(62): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(444): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(394): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(180): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(780): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call(Array)\n#18 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Command\\Command.php(318): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Application.php(1092): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Application.php(341): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Application.php(192): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(197): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1234): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 C:\\laragon\\www\\workflow\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2025-09-13 17:03:24'),
	(2, '8a883006-14cc-4af8-934c-d9a80a6ff5a7', 'database', 'default', '{"uuid":"8a883006-14cc-4af8-934c-d9a80a6ff5a7","displayName":"App\\\\Notifications\\\\CutiNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:5;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:34:\\"App\\\\Notifications\\\\CutiNotification\\":2:{s:4:\\"cuti\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\Cuti\\";s:2:\\"id\\";i:7;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:2:\\"id\\";s:36:\\"817a3e5d-e9f9-4c8b-869b-a643c1bc60c0\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1757780306,"delay":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\Cuti]. in C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:750\nStack trace:\n#0 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(110): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): Illuminate\\Notifications\\Notification->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(97): Illuminate\\Notifications\\Notification->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: Illuminate\\Notifications\\Notification->__unserialize(Array)\n#4 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(95): unserialize(\'O:48:"Illuminat...\')\n#5 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(62): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(444): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(394): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(180): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(780): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call(Array)\n#18 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Command\\Command.php(318): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Application.php(1092): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Application.php(341): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 C:\\laragon\\www\\workflow\\vendor\\symfony\\console\\Application.php(192): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(197): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 C:\\laragon\\www\\workflow\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1234): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 C:\\laragon\\www\\workflow\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2025-09-13 17:03:24');

-- Dumping structure for table rakha.holidays
CREATE TABLE IF NOT EXISTS `holidays` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `is_cuti_bersama` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rakha.holidays: ~23 rows (approximately)
INSERT INTO `holidays` (`id`, `tanggal`, `keterangan`, `is_cuti_bersama`, `created_at`, `updated_at`) VALUES
	(1, '2026-01-01', 'Tahun Baru 2026 Masehi', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(2, '2026-01-02', 'Cuti Bersama Tahun Baru 2026', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(3, '2026-01-27', 'Isra Mi\'raj Nabi Muhammad SAW', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(4, '2026-02-16', 'Cuti Bersama Tahun Baru Imlek', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(5, '2026-02-17', 'Tahun Baru Imlek 2577 Kongzili', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(6, '2026-03-19', 'Hari Suci Nyepi Tahun Baru Saka 1948', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(7, '2026-03-18', 'Cuti Bersama Idul Fitri 1447H', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(8, '2026-03-20', 'Hari Raya Idul Fitri 1447H', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(9, '2026-03-21', 'Hari Raya Idul Fitri 1447H', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(10, '2026-03-23', 'Cuti Bersama Idul Fitri 1447H', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(11, '2026-03-24', 'Cuti Bersama Idul Fitri 1447H', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(12, '2026-04-03', 'Wafat Isa Al Masih', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(13, '2026-05-01', 'Hari Buruh Internasional', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(14, '2026-05-14', 'Kenaikan Isa Al Masih', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(15, '2026-05-15', 'Cuti Bersama Kenaikan Isa Al Masih', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(16, '2026-05-27', 'Hari Raya Idul Adha 1447H', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(17, '2026-05-31', 'Hari Raya Waisak 2570 BE', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(18, '2026-06-01', 'Hari Lahir Pancasila', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(19, '2026-06-16', 'Tahun Baru Islam 1448H', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(20, '2026-08-17', 'Hari Kemerdekaan RI ke-81', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(21, '2026-08-25', 'Maulid Nabi Muhammad SAW', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(22, '2026-12-24', 'Cuti Bersama Hari Raya Natal', 1, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(23, '2026-12-25', 'Hari Raya Natal', 0, '2026-01-14 12:24:16', '2026-01-14 12:24:16'),
	(25, '2026-01-19', 'Libur Senin', 0, '2026-01-19 13:48:44', '2026-01-19 13:48:46');

-- Dumping structure for table rakha.interactions
CREATE TABLE IF NOT EXISTS `interactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `peserta` varchar(255) DEFAULT NULL,
  `jenis_transaksi` enum('IN','OUT','ENTERTAIN') NOT NULL,
  `nilai_kontribusi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tanggal_interaksi` date NOT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nilai_sales` int DEFAULT NULL,
  `komisi` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `interactions_client_id_foreign` (`client_id`),
  CONSTRAINT `interactions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table rakha.interactions: ~14 rows (approximately)
INSERT INTO `interactions` (`id`, `client_id`, `user_id`, `nama_produk`, `lokasi`, `peserta`, `jenis_transaksi`, `nilai_kontribusi`, `tanggal_interaksi`, `catatan`, `created_at`, `updated_at`, `nilai_sales`, `komisi`) VALUES
	(9, 4, 0, 'Kassa Besar', NULL, NULL, 'IN', 70000000.00, '2025-09-11', '[Rate:20] Sales September 1', '2025-12-07 12:30:28', '2025-12-07 12:30:28', NULL, NULL),
	(10, 4, 0, 'USAGE : Support', NULL, NULL, 'OUT', 5000000.00, '2025-09-22', '-', '2025-12-07 12:31:09', '2025-12-07 12:31:09', NULL, NULL),
	(11, 7, 0, 'Laptop', NULL, NULL, 'IN', 10000000.00, '2025-12-13', '[Rate:10] Sales 2025', '2025-12-13 04:22:57', '2025-12-13 04:22:57', 10000000, 10),
	(12, 4, 0, 'Activity / Entertain', 'Bogor', 'Staff', 'ENTERTAIN', 200000.00, '2025-12-13', 'Makan Siang', '2025-12-13 11:56:27', '2026-01-11 02:22:22', 0, NULL),
	(13, 4, 0, 'USAGE : Ulang Tahun Perusahaan', NULL, NULL, 'OUT', 2000000.00, '2025-12-02', NULL, '2025-12-13 15:19:10', '2025-12-13 15:19:10', 0, NULL),
	(14, 4, 0, 'Kassa Roll', NULL, NULL, 'IN', 2000000.00, '2025-12-13', '[Rate:10] 2 Bulan Sales', '2025-12-13 15:20:02', '2026-01-11 02:22:33', 2000000, 10),
	(15, 4, 2, 'Kassa Lipat', NULL, NULL, 'IN', 200000000.00, '2026-01-10', '[Rate:2] Sales pertama 2026', '2026-01-10 04:52:14', '2026-01-11 00:05:56', 200000000, 2),
	(17, 4, 2, 'USAGE : Support', NULL, NULL, 'OUT', 200000.00, '2026-01-11', NULL, '2026-01-11 00:19:26', '2026-01-11 00:19:26', 0, NULL),
	(18, 4, 2, 'Kassa Lipat', NULL, NULL, 'IN', 100000000.00, '2026-02-26', '[Rate:20] ', '2026-01-11 00:32:08', '2026-01-11 00:48:37', 100000000, 20),
	(19, 4, 2, 'USAGE : Support', NULL, NULL, 'OUT', 2000000.00, '2026-02-11', NULL, '2026-01-11 00:32:30', '2026-01-11 00:48:50', 0, NULL),
	(20, 4, 2, 'Activity / Entertain', 'Botani Square', NULL, 'ENTERTAIN', 200000.00, '2026-02-12', 'Makan Siang', '2026-01-11 00:35:24', '2026-01-11 01:55:02', 0, NULL),
	(21, 4, 1, 'Kassa Lipat', NULL, NULL, 'IN', 1000000000.00, '2026-01-11', '[Rate:10] ', '2026-01-11 02:29:12', '2026-01-11 02:29:12', 1000000000, 10),
	(22, 4, 1, 'USAGE : Support Event', NULL, NULL, 'OUT', 100000000.00, '2026-01-11', NULL, '2026-01-11 02:29:40', '2026-01-11 02:29:40', 0, NULL),
	(23, 4, 1, 'Activity / Entertain', 'k', NULL, 'ENTERTAIN', 200000.00, '2026-01-11', 'k', '2026-01-11 02:30:01', '2026-01-11 02:30:01', 0, NULL);

-- Dumping structure for table rakha.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.jobs: ~6 rows (approximately)
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
	(26, 'default', '{"uuid":"85353691-f7d2-471c-bf3f-11773ad5c1bb","displayName":"App\\\\Notifications\\\\AgendaNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:2;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:36:\\"App\\\\Notifications\\\\AgendaNotification\\":4:{s:6:\\"agenda\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:17:\\"App\\\\Models\\\\Agenda\\";s:2:\\"id\\";i:18;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:4:\\"tipe\\";s:13:\\"undangan_baru\\";s:10:\\"pengundang\\";s:7:\\"Estevao\\";s:2:\\"id\\";s:36:\\"1e97e140-8fbb-4995-b881-3c98e2a7c5d9\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1759948799,"delay":null}', 0, NULL, 1759948799, 1759948799),
	(27, 'default', '{"uuid":"55a013e8-b4c5-4f7a-8189-9c4f5e108ceb","displayName":"App\\\\Jobs\\\\SendAgendaReminder","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"App\\\\Jobs\\\\SendAgendaReminder","command":"O:27:\\"App\\\\Jobs\\\\SendAgendaReminder\\":3:{s:9:\\"\\u0000*\\u0000agenda\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:17:\\"App\\\\Models\\\\Agenda\\";s:2:\\"id\\";i:18;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:20:\\"\\u0000*\\u0000originalStartTime\\";s:16:\\"2025-10-09 02:15\\";s:5:\\"delay\\";O:13:\\"Carbon\\\\Carbon\\":3:{s:4:\\"date\\";s:26:\\"2025-10-09 01:45:00.000000\\";s:13:\\"timezone_type\\";i:3;s:8:\\"timezone\\";s:12:\\"Asia\\/Jakarta\\";}}"},"createdAt":1759948800,"delay":300}', 0, NULL, 1759949100, 1759948800),
	(28, 'default', '{"uuid":"a29e66de-caba-4041-9dbe-5d4ad930a116","displayName":"App\\\\Notifications\\\\AgendaNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:7;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:36:\\"App\\\\Notifications\\\\AgendaNotification\\":4:{s:6:\\"agenda\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:17:\\"App\\\\Models\\\\Agenda\\";s:2:\\"id\\";i:16;s:9:\\"relations\\";a:1:{i:0;s:7:\\"creator\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:4:\\"tipe\\";s:17:\\"agenda_diperbarui\\";s:10:\\"pengundang\\";s:30:\\"Muhamad Fadhillah Putra Sinaga\\";s:2:\\"id\\";s:36:\\"528ef496-f58d-47b8-a52b-376a835d2577\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1760101563,"delay":null}', 0, NULL, 1760101563, 1760101563),
	(29, 'default', '{"uuid":"8a922921-0aa1-4958-838f-f7b998584640","displayName":"App\\\\Notifications\\\\AgendaNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:8;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:36:\\"App\\\\Notifications\\\\AgendaNotification\\":4:{s:6:\\"agenda\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:17:\\"App\\\\Models\\\\Agenda\\";s:2:\\"id\\";i:16;s:9:\\"relations\\";a:1:{i:0;s:7:\\"creator\\";}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:4:\\"tipe\\";s:17:\\"agenda_diperbarui\\";s:10:\\"pengundang\\";s:30:\\"Muhamad Fadhillah Putra Sinaga\\";s:2:\\"id\\";s:36:\\"bb8af24e-f523-4380-9d0f-57eb0386acf0\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1760101563,"delay":null}', 0, NULL, 1760101563, 1760101563),
	(30, 'default', '{"uuid":"e6b5f6c8-9b69-4452-9143-255a4010ded8","displayName":"App\\\\Notifications\\\\AgendaNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:2;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:36:\\"App\\\\Notifications\\\\AgendaNotification\\":4:{s:6:\\"agenda\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:17:\\"App\\\\Models\\\\Agenda\\";s:2:\\"id\\";i:19;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:4:\\"tipe\\";s:13:\\"undangan_baru\\";s:10:\\"pengundang\\";s:11:\\"Admin Rakha\\";s:2:\\"id\\";s:36:\\"8462e74f-175a-44d3-83e8-09da20f9518a\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1760102715,"delay":null}', 0, NULL, 1760102715, 1760102715),
	(31, 'default', '{"uuid":"d0ff423d-940d-4319-9b89-65b1f5569236","displayName":"App\\\\Notifications\\\\AgendaNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call","maxTries":null,"maxExceptions":null,"failOnTimeout":false,"backoff":null,"timeout":null,"retryUntil":null,"data":{"commandName":"Illuminate\\\\Notifications\\\\SendQueuedNotifications","command":"O:48:\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\":3:{s:11:\\"notifiables\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:15:\\"App\\\\Models\\\\User\\";s:2:\\"id\\";a:1:{i:0;i:8;}s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:12:\\"notification\\";O:36:\\"App\\\\Notifications\\\\AgendaNotification\\":4:{s:6:\\"agenda\\";O:45:\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\":5:{s:5:\\"class\\";s:17:\\"App\\\\Models\\\\Agenda\\";s:2:\\"id\\";i:19;s:9:\\"relations\\";a:0:{}s:10:\\"connection\\";s:5:\\"mysql\\";s:15:\\"collectionClass\\";N;}s:4:\\"tipe\\";s:13:\\"undangan_baru\\";s:10:\\"pengundang\\";s:11:\\"Admin Rakha\\";s:2:\\"id\\";s:36:\\"82e685cc-956a-40d0-a27d-6070035d3f88\\";}s:8:\\"channels\\";a:1:{i:0;s:8:\\"database\\";}}"},"createdAt":1760102715,"delay":null}', 0, NULL, 1760102715, 1760102715);

-- Dumping structure for table rakha.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.job_batches: ~0 rows (approximately)

-- Dumping structure for table rakha.lemburs
CREATE TABLE IF NOT EXISTS `lemburs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk_lembur` time NOT NULL,
  `jam_keluar_lembur` time DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lampiran_masuk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lampiran_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude_masuk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude_masuk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude_keluar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lemburs_user_id_foreign` (`user_id`),
  CONSTRAINT `lemburs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.lemburs: ~5 rows (approximately)
INSERT INTO `lemburs` (`id`, `user_id`, `tanggal`, `jam_masuk_lembur`, `jam_keluar_lembur`, `keterangan`, `lampiran_masuk`, `lampiran_keluar`, `latitude_masuk`, `longitude_masuk`, `latitude_keluar`, `longitude_keluar`, `created_at`, `updated_at`) VALUES
	(1, 2, '2025-09-13', '07:31:56', '07:32:06', 'Lembur', 'lampiran_lembur/Rf9zmET7zW1MQFZ7SphKKhcX2mgWvC3d0rAKSc9S.png', 'lampiran_lembur_keluar/OyGV5vfRj08pnTIEciyuGm1UeBCdbqwdQcV734OX.png', '-6.3504384', '106.7876352', '-6.3504384', '106.7876352', '2025-09-13 00:31:56', '2025-09-13 00:32:06'),
	(2, 3, '2025-09-14', '10:55:11', '10:55:22', 'Lembur', 'lampiran_lembur/xfOfckJU9aAUXr0CsX6YGhYlfEUAgtj5W9EDLHuY.png', 'lampiran_lembur_keluar/pbdI2BI9r7UTtjBK0WLk8wc3ZZCLSTSwMlGesiMT.png', '-6.373376', '106.7614208', '-6.373376', '106.7614208', '2025-09-14 03:55:11', '2025-09-14 03:55:22'),
	(13, 2, '2025-09-23', '22:26:34', '22:26:46', 'Packing', 'lampiran_lembur/4A4Ab9k5N6Jmood9AfudMLcOfnDbfqfyf6xU1o7u.png', 'lampiran_lembur_keluar/VJSjQbX9wBYY3oWrH8OVxHiGMnMrmTJ5K6HgsiWV.png', '-6.614031357665805', '106.81638146339363', '-6.614031357665805', '106.81638146339363', '2025-09-23 15:26:34', '2025-09-23 15:26:46'),
	(14, 8, '2025-10-22', '05:29:04', '05:29:19', 'lembur', 'lampiran_lembur/hRMlgtmxqvv3kclSFtuBFsYmVXdS9Cfhd4ZlfRJ1.png', 'lampiran_lembur_keluar/O6tDCyYRN9mjyZ4K6mLQQfsJqPV5KWLh6vMDkvtH.png', '-6.613716126522911', '106.81661493844578', '-6.613716126522911', '106.81661493844578', '2025-10-21 22:29:04', '2025-10-21 22:29:19'),
	(17, 8, '2025-11-26', '21:49:34', NULL, 'E', 'lampiran_lembur/p85khjIS4TXqBoICEorH0fsjOUDz2nq0ITslsahs.png', NULL, '-6.6139333', '106.8162847', NULL, NULL, '2025-11-26 14:49:34', '2025-11-26 14:49:34'),
	(19, 3, '2025-11-27', '22:14:44', '22:17:47', 'rrrrr', 'lampiran_lembur/lYmH13GsdQeoKNnz6iIVhRONDzNGTW4SyevBlFwj.png', 'lampiran_lembur_keluar/Aph47nI577NMQSN4JBoQ9VkLACMZh1kLkHALc9SO.png', '-6.3209472', '106.7679744', '-6.3209472', '106.7679744', '2025-11-27 15:14:44', '2025-11-27 15:17:47');

-- Dumping structure for table rakha.lokasi_absen
CREATE TABLE IF NOT EXISTS `lokasi_absen` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius` int NOT NULL DEFAULT '50',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.lokasi_absen: ~0 rows (approximately)
INSERT INTO `lokasi_absen` (`id`, `nama`, `latitude`, `longitude`, `radius`, `created_at`, `updated_at`) VALUES
	(1, 'Kantor', -6.61404100, 106.81653900, 100, NULL, '2025-09-13 23:14:55');

-- Dumping structure for table rakha.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.migrations: ~12 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_08_09_025946_add_role_to_users_table', 1),
	(5, '2025_08_13_024400_add_new_columns_to_users_table', 1),
	(6, '2025_08_14_165025_create_absensi_table', 1),
	(7, '2025_08_17_123339_create_cutis_table', 1),
	(8, '2025_08_29_061034_add_divisi_column_to_users_table', 1),
	(9, '2025_08_29_210512_add_keluar_to_absensi_table', 1),
	(10, '2025_09_02_070910_add_location', 1),
	(11, '2025_09_04_192637_add_keluar_details_to_absensi_table', 1),
	(12, '2025_09_11_120130_create_pengajuan_dana_table', 2),
	(13, '2025_09_11_143448_create_notifications_table', 3),
	(14, '2025_09_12_204503_create_lokasi_absen', 4),
	(15, '2025_09_13_065135_create_lemburs_table', 5),
	(16, '2025_09_13_215137_add_approver_id_to_cutis_table', 6),
	(17, '2025_09_14_002313_add_dibatalkan_status_to_cutis_table', 7),
	(18, '2025_09_22_221209_add_is_kepala_divisi_to_users_table', 8),
	(19, '2025_09_24_193306_add_jatah_cuti_to_users_table', 9),
	(20, '2025_09_25_145146_create_agendas_table', 10),
	(21, '2025_09_25_145617_create_agenda_user_table', 10),
	(22, '2025_09_27_194358_create_pengajuan_dokumens_table', 11),
	(23, '2025_10_17_201759_add_approver_ids_to_pengajuan_dana_table', 12),
	(24, '2025_10_18_061151_add_approval_dates_to_pengajuan_danas_table', 13),
	(26, '2025_10_22_214417_add_approvers_to_users_table', 14),
	(27, '2025_10_24_020121_add_manager_keuangan_id_to_users_table', 15),
	(28, '2025_11_13_152511_create_aktivitas_table', 16),
	(29, '2025_11_15_143307_create_clients_table', 17),
	(30, '2025_11_15_143315_create_interactions_table', 17),
	(31, '2025_11_26_174223_add_tanggal_keluar_to_absensi_table', 18),
	(32, '2025_12_07_164925_pengajuan_barang_tabels', 19),
	(33, '2025_12_20_111816_add_fcm_token_to_users_table', 20);

-- Dumping structure for table rakha.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.notifications: ~202 rows (approximately)
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('01357415-9550-49f2-a030-0e51215d1b30', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 13, '{"id":8,"title":"Pengajuan Barang Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan barang baru: \'Penggunaan Kasa\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/8","icon":"fas fa-box","color":"text-blue-600"}', NULL, '2025-12-10 14:45:27', '2025-12-10 14:45:27'),
	('01fec0ac-29eb-4e70-beff-e63fa34fdd48', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 3, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini adalah ulang tahun Widhi. Jangan lupa ucapkan selamat!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:30:24', '2026-01-19 04:30:24'),
	('037f72e2-16e6-4223-a26a-d0574eb73a7b', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:58:01', '2026-01-19 04:58:01'),
	('0573c37b-9a42-4351-adc9-19d5bb4b256b', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:21:17', '2025-10-20 13:27:42'),
	('09450b4c-7974-4e28-a374-3efcbf9f00d0', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":28,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pengadaan Batik Bogor\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/28","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-01 14:38:44', '2025-10-20 13:27:42'),
	('117e6cfd-bd6a-4112-8940-cd6ef6ca89bb', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":13,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Muhamad Fadhillah Putra Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/13","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-16 11:26:43', '2025-09-13 17:36:33', '2025-09-16 11:26:43'),
	('11a5c295-3b86-4a54-a566-c3b14f5c5043', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 14:41:34', '2025-10-20 13:27:42'),
	('132ba42b-6a4b-49bd-8b18-48a5156877d3', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":41,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'a\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/41","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-18 22:37:25', '2025-10-18 22:37:25'),
	('133627e3-abdf-473b-a6d3-8a07f739f9f0', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":3,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Palmer. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-26 09:11:20', '2025-11-26 09:11:20'),
	('13f32a4e-df24-4a3e-a584-9c71ccfdc170', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:19:04', '2025-09-27 12:12:12'),
	('14db7a45-76c3-4d1b-bd2e-1206e4c591a4', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":40,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Alat\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/40","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-18 14:22:33', '2025-10-20 13:27:42'),
	('1577e168-5b2d-4971-929f-4bc06ba8e6ce', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 14:34:35', '2025-09-28 14:34:35'),
	('16b61af1-34da-4b52-bc5b-5cc89405a61c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:30:33', '2025-10-20 13:27:42'),
	('197ebaca-0371-4c9d-90c5-a75f13a46145', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 8, '{"id":7,"title":"Pengajuan Barang Diproses","message":"Pengajuan \'Penggunaan Kasa Roll\' Anda telah disetujui atasan dan diteruskan ke Gudang.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/7","icon":"fas fa-check-double","color":"text-green-500"}', NULL, '2025-12-10 14:11:20', '2025-12-10 14:11:20'),
	('1a53376b-a8b5-4054-95b5-1504ef7b13c4', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":25,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Raimbers\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/25","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 16:21:40', '2025-09-28 16:21:40'),
	('1a54c27e-7457-4be6-a385-5cfdcf99d75f', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":29,"title":"Pengajuan Dana Ditolak","message":"Mohon maaf, pengajuan dana \'Laptop\' Anda ditolak.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/29","icon":"fas fa-times-circle","color":"text-red-600"}', '2025-10-01 14:56:50', '2025-10-01 14:53:45', '2025-10-01 14:56:50'),
	('1b2d192e-8133-4a7a-92e2-e447f85855fe', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":9,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Asep. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-18 01:05:38', '2025-11-18 01:05:38'),
	('1bb2f063-39bf-4803-a303-171fe48ac288', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 2, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:10', '2026-01-19 13:56:10'),
	('1c24e80a-bdd1-4145-a956-21fd0787d25f', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":23,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Rembers\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/23","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 16:00:58', '2025-10-20 13:27:42'),
	('1dd68599-8cb0-45bc-84ea-2eff0492be22', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":45,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan dana baru: \'Alat\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/45","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-26 02:53:52', '2025-10-26 02:53:52'),
	('1ddbb7cd-e37b-4a56-adf3-0fd13ec12f0c', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":21,"title":"Pengajuan Cuti Baru","message":"Estevao mengajukan cuti baru mulai tanggal 16 Oct 2025. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/21","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-10-01 14:57:58', '2025-10-01 14:57:58'),
	('1e3681c7-47a4-43a4-9c5f-b1ceaaeaad82', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:00:12', '2025-09-27 12:12:12'),
	('1f7c2188-06a6-46df-b928-8ac8f11dd1f0', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:13:14', '2025-10-20 13:27:42'),
	('202cb1e9-b93e-448f-b86c-0cc62fe41a61', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":29,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Laptop\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/29","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-01 14:52:41', '2025-10-20 13:27:42'),
	('204bca86-df65-4eb3-bd39-ce941f4af810', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:42:03', '2026-01-19 13:42:03'),
	('21729a45-756f-4fee-b877-8d0437f40f5b', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 2, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun!","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', '2026-01-19 13:49:08', '2026-01-19 13:48:57', '2026-01-19 13:49:08'),
	('2177af10-4304-49ea-8f96-36aa6b835b92', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":31,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Laptop\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/31","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-12 22:51:05', '2025-10-12 22:51:05'),
	('227f6789-0467-4a54-92d9-f18f02c97d33', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 8, '{"id":7,"title":"Pengajuan Barang Disetujui","message":"Kabar baik! Pengajuan barang \'Penggunaan Kasa Roll\' Anda telah disetujui oleh Gudang.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/7","icon":"fas fa-check-circle","color":"text-green-600"}', NULL, '2025-12-10 14:11:50', '2025-12-10 14:11:50'),
	('22bf7257-1dd6-47d7-9a16-5c2c6ea23584', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":17,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian TV\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/17","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-27 12:10:19', '2025-09-27 12:10:19'),
	('240694ab-bc77-4608-b8ed-6ae61ce19935', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":36,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Motor\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/36","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-17 23:28:03', '2025-10-17 23:28:03'),
	('244e7ed2-3653-4469-bc66-6bc286f0c1a0', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":10,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Muhamad Fadhillah Putra Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/10","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 17:04:34', '2025-09-13 17:03:24', '2025-09-13 17:04:34'),
	('27de9383-b8b5-48b1-99e0-fb39278603f1', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":22,"title":"Pengajuan Cuti Dibatalkan","message":"Muhamad Fadhillah Putra Sinaga telah membatalkan pengajuan cutinya untuk tanggal 14 Oct 2025.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/22","icon":"fas fa-ban","color":"text-gray-500"}', NULL, '2025-10-01 15:15:45', '2025-10-01 15:15:45'),
	('28df28e6-f7fa-4336-862a-86717477778a', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":11,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Ridho FIrdhiansyah Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/11","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 17:04:34', '2025-09-13 17:03:24', '2025-09-13 17:04:34'),
	('2968c349-358f-4f74-aa4d-9973cdc1fd2c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":7,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/7","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-12 01:54:18', '2025-09-12 01:52:56', '2025-09-12 01:54:18'),
	('2a41dc42-b67c-4934-bd28-39d13d38d6be', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun!","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:48:59', '2026-01-19 13:48:59'),
	('2a8de55a-ebee-4a59-a07a-f9cad771b064', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":6,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/6","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-12 00:52:58', '2025-09-12 00:49:26', '2025-09-12 00:52:58'),
	('2b064d56-0c22-4521-a595-bdf4cac85f81', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":35,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Motor\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/35","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-17 23:28:02', '2025-10-17 23:28:02'),
	('2baebd3f-897e-4240-a8db-13936e453a18', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":28,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pengadaan Batik Bogor\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/28","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-01 14:39:10', '2025-10-01 14:38:44', '2025-10-01 14:39:10'),
	('2d0bf01c-aea6-45df-bc67-28061c5b9080', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":33,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Baju\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/33","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-16 20:18:35', '2025-10-20 13:27:42'),
	('2d2a42a5-68c7-4af3-88e5-6fdcd3008f5b', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":22,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/22","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:53:45', '2025-10-20 13:27:42'),
	('2f754f51-0477-41b9-9bc2-3573286a5505', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":38,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Tessssssssssssssssssssssssssss...\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/38","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-18 10:37:15', '2025-10-18 10:37:15'),
	('3016cafb-d82a-4877-aab5-21d4b1e3d36e', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:59', '2026-01-19 04:57:59'),
	('32f1151e-721c-4742-afcd-62f55c78ca90', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":2,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Riski telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/localhost\\/admin\\/cuti","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 09:59:10', '2025-09-13 09:58:35', '2025-09-13 09:59:10'),
	('35f5060b-004e-487a-b45a-686498f76ab5', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":3,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Riski telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/localhost\\/cuti\\/3","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 10:56:49', '2025-09-13 10:34:53', '2025-09-13 10:56:49'),
	('37742a4d-2d8a-487b-988a-022f4f4d2a57', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 14:41:40', '2025-10-20 13:27:42'),
	('379067c8-8444-4096-945e-0b3c656eef60', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":26,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Baju\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/26","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 16:23:07', '2025-09-28 16:23:07'),
	('37c524a3-f7cd-4c18-bd2f-ac68f8e86cb0', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 13, '{"id":13,"title":"Selamat Ulang Tahun! \\ue05e\\u8102","message":"Selamat ulang tahun Widhi, semoga hari ini menyenangkan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:42:04', '2026-01-19 13:42:04'),
	('387f437e-02e1-4412-aa5d-7aeada6beb36', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":37,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Baju\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/37","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-17 23:29:03', '2025-10-17 23:29:03'),
	('38c109dd-fbbd-4288-8a69-fa0c623789f7', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini adalah ulang tahun Widhi. Jangan lupa ucapkan selamat!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:30:23', '2026-01-19 04:30:23'),
	('38fc0b4c-aeaa-4db3-a622-562d9006f226', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":28,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pengadaan Batik Bogor\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/28","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-01 14:40:28', '2025-10-01 14:39:54', '2025-10-01 14:40:28'),
	('39af77fd-54e9-426a-a480-cccfeca37bf8', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:18:02', '2025-09-27 12:12:12'),
	('3adfc3e4-9456-493b-ae2f-76b6d2475bd8', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun!","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:48:58', '2026-01-19 13:48:58'),
	('3afb180b-83ee-401b-b574-71729c509804', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 8, '{"id":30,"title":"Cuti Ditolak","message":"Pengajuan cuti tanggal 12 Januari 2026 ditolak.","icon":"fas fa-times-circle","color":"text-red-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/30"}', NULL, '2025-12-21 12:14:54', '2025-12-21 12:14:54'),
	('3c5c6766-7ae2-4a17-8a1d-f92d43f9058c', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:58:01', '2026-01-19 04:58:01'),
	('3cc47d29-6d46-45db-a2ac-2b52cbb0700c', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":19,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Asep telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/19","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-09-28 16:24:54', '2025-09-28 16:24:54'),
	('3d9dc30f-635c-48e5-a0e0-8c561f6ad49c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:14:09', '2025-10-20 13:27:42'),
	('3f432536-cb13-43da-906a-b335ecba021c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 14:42:18', '2025-10-20 13:27:42'),
	('40afda26-2cc1-4b4d-98bd-d174c4cb8da3', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":44,"title":"Pengajuan Dana Ditolak","message":"Mohon maaf, pengajuan dana \'Baju\' Anda ditolak.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/44","icon":"fas fa-times-circle","color":"text-red-600"}', '2025-11-18 01:05:08', '2025-10-24 12:42:11', '2025-11-18 01:05:08'),
	('41f061a8-2384-49ca-b227-0c2de6655c34', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":18,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Estevao telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/18","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-09-24 00:02:12', '2025-09-24 00:02:12'),
	('42aa1ced-d8be-4b20-98ce-c7505f214b30', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:42:04', '2026-01-19 13:42:04'),
	('42b96cfc-97a3-408e-88cf-9c783024b7a2', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:03', '2026-01-19 04:57:03'),
	('46178864-4113-4fd8-b76f-74832b085400', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":36,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Motor\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/36","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-17 23:37:07', '2025-10-20 13:27:42'),
	('4655ac33-7a5b-4d33-a472-3d5f454d99d7', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":40,"title":"Pengajuan Dana Disetujui","message":"Kabar baik! Pengajuan dana \'Alat\' Anda telah disetujui oleh Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/40","icon":"fas fa-check-circle","color":"text-green-600"}', '2025-11-18 01:05:08', '2025-10-18 14:23:03', '2025-11-18 01:05:08'),
	('46d5ed11-ea0b-4616-a46c-e6b56529f4b6', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":3,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/3","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-11 13:29:17', '2025-09-11 13:27:51', '2025-09-11 13:29:17'),
	('47f0687b-06e0-442f-badb-716acd148f69', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":23,"title":"Pengajuan Cuti Baru","message":"Estevao mengajukan cuti baru mulai tanggal 30 Oct 2025. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/23","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-10-18 10:41:02', '2025-10-18 10:41:02'),
	('48456ada-1cc6-41af-82ff-ca1499824c90', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":32,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Pembelian Meja\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/32","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-16 20:00:17', '2025-10-16 20:00:17'),
	('4b6a0a71-b347-4200-8558-5207a0f1e219', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 2, '{"id":26,"title":"Pengajuan Cuti Disetujui","message":"Pengajuan cuti Anda untuk tanggal 01 Jan 2026 telah disetujui. Selamat berlibur!","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/26","icon":"fas fa-check-circle","color":"text-green-600"}', '2026-01-19 13:42:28', '2025-12-20 07:28:22', '2026-01-19 13:42:28'),
	('4ce29cf4-93bf-419d-9df9-65e73b6b6ec0', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":50,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan dana baru: \'Pembelian Perlengkapan Kantor\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/50","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-12-19 12:42:31', '2025-12-19 12:42:31'),
	('4dea1261-ea26-4742-a88d-7a72b45e6a9f', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":33,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Baju\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/33","icon":"fas fa-check-double","color":"text-green-500"}', '2025-11-18 01:05:08', '2025-10-16 20:18:35', '2025-11-18 01:05:08'),
	('4e4aac80-cc63-49d0-9ee7-3c0403c5cc98', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun!","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:48:57', '2026-01-19 13:48:57'),
	('4ec2ef83-b726-4a69-914f-df09063e120d', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini adalah ulang tahun Widhi. Jangan lupa ucapkan selamat!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:30:26', '2026-01-19 04:30:26'),
	('5037b91a-5a72-415d-8cc6-d736f04a4443', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":49,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Pembelian Meja\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/49","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-12-19 11:55:17', '2025-12-19 11:55:17'),
	('531c464a-df1a-4251-8155-6dabf02d7145', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":33,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti tanggal 17 Januari 2026.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/33"}', NULL, '2026-01-15 14:21:55', '2026-01-15 14:21:55'),
	('5439d7fa-7563-44f1-af34-e671591f28fa', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:42:02', '2026-01-19 13:42:02'),
	('5532f5c5-1011-416d-95e4-08cf4f28db0f', 'App\\Notifications\\AgendaNotification', 'App\\Models\\User', 8, '{"title":"Undangan Agenda Baru","message":"Muhamad Fadhillah Putra SinagA mengundang Anda ke agenda \\"Natal\\".","icon":"fas fa-calendar-alt","color":"text-blue-500","url":"http:\\/\\/127.0.0.1:8000\\/dashboard?agenda_id=22"}', NULL, '2025-12-22 00:50:11', '2025-12-22 00:50:11'),
	('554dad58-b1c1-4a33-bbaf-bd595d3b8f58', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":22,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/22","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:51:39', '2025-10-20 13:27:42'),
	('563ac47c-962d-49da-ad23-fb8302745cc2', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":26,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Baju\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/26","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 16:23:44', '2025-10-20 13:27:42'),
	('5af5b2a9-22af-463e-a848-b6628d3806b0', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 13, '{"id":13,"title":"Selamat Ulang Tahun! \\ue05e\\u8102","message":"Selamat ulang tahun Widhi, semoga hari ini menyenangkan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:58:02', '2026-01-19 04:58:02'),
	('5b625756-87e7-4f66-94fd-77728a27a383', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":3,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Palmer. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-26 09:11:20', '2025-11-26 09:11:20'),
	('5f4e5257-541b-4303-949c-63c081758733', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 8, '{"id":5,"title":"Pengajuan Barang Diproses","message":"Pengajuan \'Penggunaan Alat\' Anda telah disetujui atasan dan diteruskan ke Gudang.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/5","icon":"fas fa-check-double","color":"text-green-500"}', NULL, '2025-12-10 13:41:38', '2025-12-10 13:41:38'),
	('5f85375e-669e-494d-83f5-9cce58cbd991', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 6, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:11', '2026-01-19 13:56:11'),
	('6045fbe2-cebe-4bcf-8570-b8bdfce87890', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":14,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Baju\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/14","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-26 20:14:00', '2025-09-26 20:14:00'),
	('6100a496-011e-417a-89dc-b9b72fa1ba5b', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":40,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Alat\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/40","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-18 14:21:56', '2025-10-18 14:21:56'),
	('65e0ef2e-f7e6-49a1-8fbf-f33419bc6319', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":15,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/15","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-26 20:19:22', '2025-09-27 12:12:12'),
	('67471b87-03a9-4783-86bf-dad125315992', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 2, '{"message":"Cuti baru","cuti_id":25,"url":"http:\\/\\/127.0.0.1:8000\\/cuti"}', '2026-01-19 13:42:28', '2025-12-20 05:16:08', '2026-01-19 13:42:28'),
	('676e0496-9ab1-46e4-9c48-48ed5f1706cd', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:34:38', '2025-10-20 13:27:42'),
	('68905d39-c4db-40e2-b3f0-76c475937908', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-27 10:55:13', '2025-09-27 10:55:13'),
	('68bb0a1e-9685-4dad-96fa-32b3a5f8a271', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":42,"title":"Pengajuan Dana Disetujui","message":"Kabar baik! Pengajuan dana \'Tes\' Anda telah disetujui oleh Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/42","icon":"fas fa-check-circle","color":"text-green-600"}', '2025-11-18 01:05:08', '2025-10-24 11:58:17', '2025-11-18 01:05:08'),
	('68f766b2-a172-444d-a4f1-03f51b7b8a7c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":51,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Testing\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/51","icon":"fas fa-check-double","color":"text-green-500"}', NULL, '2025-12-22 00:47:32', '2025-12-22 00:47:32'),
	('69a5c263-11df-4268-9047-b7308d2565f6', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 3, '{"id":51,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Testing\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/51","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-12-22 00:47:33', '2025-12-22 00:47:33'),
	('6a1c64b4-f956-4585-afd2-02fccdf7fe7d', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":29,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Laptop\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/29","icon":"fas fa-check-double","color":"text-green-500"}', '2025-10-01 14:56:50', '2025-10-01 14:52:41', '2025-10-01 14:56:50'),
	('6ac050b5-d86a-48cf-8d8f-513a82ed340a', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 13, '{"id":13,"title":"Selamat Ulang Tahun! \\ue05e\\u8102","message":"Selamat ulang tahun Widhi, semoga hari ini menyenangkan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:03', '2026-01-19 04:57:03'),
	('6ad71cf8-d1bd-44dc-aa70-2962d0d9c3ab', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":39,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Laptop\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/39","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-18 10:40:34', '2025-10-18 10:40:34'),
	('6c781b2e-91d0-4655-aba7-b4461398c98a', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":32,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti tanggal 15 Januari 2026.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/32"}', NULL, '2026-01-15 07:18:15', '2026-01-15 07:18:15'),
	('6cae54a4-32b2-4c93-b019-51a5b442330b', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":36,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Motor\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/36","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-17 23:29:53', '2025-10-20 13:27:42'),
	('6d195e37-b25d-4649-b21d-24486280a9c0', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:06:08', '2025-10-20 13:27:42'),
	('6ef65037-f1a3-4934-ad24-92634c79dccd', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":8,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Ridho FIrdhiansyah Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/8","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 17:04:34', '2025-09-13 17:03:24', '2025-09-13 17:04:34'),
	('6f0a94fa-977c-4322-8e84-9787fab877f6', 'App\\Notifications\\AbsensiNotification', 'App\\Models\\User', 2, '{"title":"Info Absensi","message":"Absen Masuk berhasil dicatat.","icon":"fas fa-sign-in-alt","color":"text-green-500","url":"http:\\/\\/127.0.0.1:8000\\/absen"}', '2026-01-19 13:42:28', '2025-12-22 01:00:26', '2026-01-19 13:42:28'),
	('713645e9-fec5-477d-be66-0be079a535e1', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":34,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Test Pembelian\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/34","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-17 23:08:15', '2025-10-20 13:27:42'),
	('729add29-a2b0-4267-850d-c4902f551a9a', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 8, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:11', '2026-01-19 13:56:11'),
	('734c9ccb-43fd-412a-888b-362243b92501', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":15,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Muhamad Fadhillah Putra Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/15","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-09-22 20:56:04', '2025-09-22 20:56:04'),
	('7351f97d-f32d-4ce7-9849-0dc9947074b9', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:01:28', '2025-10-20 13:27:42'),
	('75059268-dc04-4904-a4a3-91bdedae2bb5', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":9,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian Baju\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/9","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-13 09:50:38', '2025-09-12 02:08:46', '2025-09-13 09:50:38'),
	('7587dfd8-9c39-4926-b7eb-229bf68d35f8', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":27,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti tanggal 23 Desember 2025.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/27"}', NULL, '2025-12-21 11:57:07', '2025-12-21 11:57:07'),
	('759c38f6-28ec-46f9-88ee-361f860435f1', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":28,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pengadaan Batik Bogor\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/28","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-01 14:38:04', '2025-10-01 14:38:04'),
	('75a57429-4c6d-4034-be39-3825b8b5fc72', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 13, '{"id":13,"title":"Selamat Ulang Tahun!","message":"Selamat ulang tahun Widhi, semoga hari ini menyenangkan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:48:59', '2026-01-19 13:48:59'),
	('76ca6a94-4341-4f7f-93d6-6dd87053cf6c', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 2, '{"id":30,"title":"Pengajuan Cuti Baru","message":"Estevao mengajukan cuti tanggal 12 Januari 2026.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/30"}', '2026-01-19 13:42:28', '2025-12-21 12:13:36', '2026-01-19 13:42:28'),
	('7897a4df-67b1-4bbd-8efc-fc88b329eda7', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 10:55:32', '2025-09-27 12:12:12'),
	('79b6e741-6f8d-4ce0-b936-658d335cafba', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":11,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian Meja\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/11","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-22 21:03:21', '2025-09-22 21:03:21'),
	('7a6daeaf-fa81-493d-a6ac-decc2a8e8065', 'App\\Notifications\\AgendaNotification', 'App\\Models\\User', 8, '{"id":21,"title":"Undangan Agenda Baru","message":"Muhamad Fadhillah Putra Sinaga mengundang Anda ke agenda \'o\'.","url":"http:\\/\\/127.0.0.1:8000\\/dashboard?agenda_id=21","icon":"fas fa-calendar-alt","color":"text-purple-600"}', '2025-11-18 01:05:08', '2025-10-14 20:36:43', '2025-11-18 01:05:08'),
	('7c3018fa-4404-4ff3-85be-f1b24e6ad38f', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":14,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Ridho FIrdhiansyah Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/14","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-09-17 14:15:45', '2025-09-17 14:15:45'),
	('7e3cd431-f7a4-46cb-81f3-0fcd21a0f0e0', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 2, '{"id":51,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Testing\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/51","icon":"fas fa-coins","color":"text-blue-600"}', '2026-01-19 13:42:28', '2025-12-22 00:46:21', '2026-01-19 13:42:28'),
	('7e839941-964e-49bc-8c14-b1c455253102', 'App\\Notifications\\AgendaNotification', 'App\\Models\\User', 8, '{"id":19,"title":"Agenda Diperbarui","message":"Agenda \'Rapat\' yang Anda ikuti telah diperbarui oleh Admin Rakha.","url":"http:\\/\\/127.0.0.1:8000\\/dashboard?agenda_id=19","icon":"fas fa-calendar-check","color":"text-yellow-500"}', '2025-11-18 01:05:08', '2025-10-10 13:29:52', '2025-11-18 01:05:08'),
	('7ea48c17-69f2-4c0b-9615-971ccf27a104', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":24,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/24","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 16:15:48', '2025-10-20 13:27:42'),
	('7f9a9996-793f-4eb9-88d2-d1b9266f1e28', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 6, '{"id":11,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian Meja\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/11","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-22 21:25:34', '2025-09-22 21:03:21', '2025-09-22 21:25:34'),
	('80adc1a8-9a14-4566-aca3-33a8ef8ceb22', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":20,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/20","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 14:02:59', '2025-09-28 14:02:59'),
	('80c24727-218e-446f-8355-7e17e241d993', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":48,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan dana baru: \'test\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/48","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-12-10 12:46:01', '2025-12-10 12:46:01'),
	('830d0212-ac9c-48c2-80ed-28653a044fde', 'App\\Notifications\\AgendaNotification', 'App\\Models\\User', 8, '{"id":17,"title":"Undangan Agenda Baru","message":"Muhamad Fadhillah Putra Sinaga mengundang Anda ke agenda \'Test\'.","url":"http:\\/\\/127.0.0.1:8000\\/dashboard","icon":"fas fa-calendar-alt","color":"text-purple-600"}', '2025-10-08 06:48:58', '2025-10-08 06:46:20', '2025-10-08 06:48:58'),
	('841240d9-a3c2-480d-b29a-66c3879b85bf', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":9,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Asep. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-18 01:05:38', '2025-11-18 01:05:38'),
	('861ba896-f0b2-4923-8de7-97d1be8b26c2', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":49,"title":"Dana Telah Ditransfer","message":"Dana untuk pengajuan \'Pembelian Meja\' telah ditransfer. Silakan cek rekening Anda.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/49","icon":"fas fa-receipt","color":"text-indigo-600"}', NULL, '2025-12-19 12:15:08', '2025-12-19 12:15:08'),
	('87c482ed-230d-42a1-aaa5-7f19a0ef589f', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":28,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti tanggal 23 Desember 2025.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/28"}', NULL, '2025-12-21 11:58:05', '2025-12-21 11:58:05'),
	('883821ab-7433-4788-80e5-a4fb22b6cf97', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:13:10', '2025-10-20 13:27:42'),
	('8a28fe91-04ba-4673-a65b-f2453a009cdd', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 13, '{"id":13,"title":"Selamat Ulang Tahun! \\ue05e\\u8102","message":"Selamat ulang tahun Widhi, semoga hari ini menyenangkan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:30:26', '2026-01-19 04:30:26'),
	('8bc7f4a2-25f5-4d8f-97de-87c49f377a76', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":42,"title":"Dana Telah Ditransfer","message":"Dana untuk pengajuan \'Tes\' telah ditransfer. Silakan cek rekening Anda.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/42","icon":"fas fa-receipt","color":"text-indigo-600"}', '2025-11-18 01:05:08', '2025-10-24 12:09:03', '2025-11-18 01:05:08'),
	('8e475bc0-680e-46aa-b372-54aadd879153', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:01', '2026-01-19 04:57:01'),
	('8f5b0122-e5b2-49ae-85c8-7c559676a080', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 3, '{"id":48,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan dana baru: \'test\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/48","icon":"fas fa-coins","color":"text-blue-600"}', '2025-12-19 12:27:43', '2025-12-10 12:45:33', '2025-12-19 12:27:43'),
	('8f9a0e85-c6fc-4d36-a698-210bc9aae059', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 13, '{"id":5,"title":"Pengajuan Barang Baru","message":"Estevao mengajukan barang baru: \'Penggunaan Alat\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/5","icon":"fas fa-box","color":"text-blue-600"}', NULL, '2025-12-10 13:41:38', '2025-12-10 13:41:38'),
	('8fbd95e5-10b4-44cf-aea0-71ab31ca173c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":18,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Instalasi Internet\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/18","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-27 12:19:53', '2025-10-20 13:27:42'),
	('90e4766f-49ba-4972-b53b-04da59b84df6', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":22,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan cuti baru mulai tanggal 14 Oct 2025. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/22","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-10-01 15:13:24', '2025-10-01 15:13:24'),
	('91aaa452-4bc4-4f24-ac24-e6aab9f5f99d', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":15,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/15","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-26 20:19:13', '2025-09-26 20:19:13'),
	('95d10ad9-0b34-4510-800a-eb17927b3313', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:58:02', '2026-01-19 04:58:02'),
	('95e0d713-7597-4692-a7d9-3e01e26650ce', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":8,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian Meja\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/8","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-12 01:54:18', '2025-09-12 01:53:45', '2025-09-12 01:54:18'),
	('963484db-8150-4fb5-b9ff-00756c6bd0b1', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":42,"title":"Dana Telah Ditransfer","message":"Dana untuk pengajuan \'Tes\' telah ditransfer. Silakan cek rekening Anda.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/42","icon":"fas fa-receipt","color":"text-indigo-600"}', '2025-11-18 01:05:08', '2025-10-24 12:08:41', '2025-11-18 01:05:08'),
	('9640348f-0345-483e-8081-583fecceb85c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:32:11', '2025-10-20 13:27:42'),
	('96905093-e08f-48a3-bacd-df763c52bf65', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":31,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti tanggal 05 Februari 2026.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/31"}', NULL, '2025-12-21 14:18:00', '2025-12-21 14:18:00'),
	('9bef2628-9596-4382-8345-87f7661090dc', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 3, '{"id":49,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Pembelian Meja\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/49","icon":"fas fa-coins","color":"text-blue-600"}', '2025-12-19 12:27:43', '2025-12-19 11:54:49', '2025-12-19 12:27:43'),
	('9c3a8d74-14b3-48fc-9301-738bfdf6f038', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":9,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Asep. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', '2025-11-18 01:05:08', '2025-11-18 01:05:38', '2025-11-18 01:05:08'),
	('a080f905-10ed-412b-9a13-1209373435a4', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":27,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/27","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-01 14:14:48', '2025-10-20 13:27:42'),
	('a1166fba-66e9-49a8-a08c-4834ee7290c4', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":33,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Baju\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/33","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-16 20:01:43', '2025-10-16 20:01:43'),
	('a1458041-6fcb-40f1-9ce4-52b320e9d81f', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":4,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Riski telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/localhost\\/cuti\\/4","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 10:56:49', '2025-09-13 10:38:42', '2025-09-13 10:56:49'),
	('a278770d-2ead-4e90-ade1-607802df380f', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":5,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian Meja\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/5","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-12 00:52:58', '2025-09-12 00:46:57', '2025-09-12 00:52:58'),
	('a5502b09-d9de-493c-94b3-9fe802d06ea3', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":26,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti baru mulai tanggal 01 Jan 2026. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/26","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-12-20 07:27:48', '2025-12-20 07:27:48'),
	('a5b139be-e125-43d0-bdd1-e03dba30b473', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 9, '{"id":3,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Palmer. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-26 09:11:20', '2025-11-26 09:11:20'),
	('a7ad1ee8-1343-48f3-b332-81926e9e5233', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 3, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:42:03', '2026-01-19 13:42:03'),
	('a8b9c6ab-2964-4572-abee-9fc58903871d', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":9,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Asep. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-18 01:05:38', '2025-11-18 01:05:38'),
	('a8bf45f6-912f-4c28-b1c2-adbf33e84b5d', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":9,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Muhamad Fadhillah Putra Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/9","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 17:04:34', '2025-09-13 17:03:24', '2025-09-13 17:04:34'),
	('aaee0bfd-2d6b-48c1-920f-ecb08714f76b', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 3, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun!","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:48:58', '2026-01-19 13:48:58'),
	('aced2d03-45bc-4e77-9ad6-f7f2008c2f17', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":44,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Baju\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/44","icon":"fas fa-check-double","color":"text-green-500"}', '2025-11-18 01:05:08', '2025-10-24 12:41:47', '2025-11-18 01:05:08'),
	('ad978aa6-cd90-4395-915a-7ddf1c299709', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 2, '{"id":31,"title":"Cuti Disetujui","message":"Pengajuan cuti tanggal 05 Februari 2026 disetujui.","icon":"fas fa-check-circle","color":"text-green-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/31"}', '2026-01-19 13:42:28', '2025-12-21 14:18:33', '2026-01-19 13:42:28'),
	('af3cba31-fee6-48ba-b881-8eebe0715532', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 2, '{"id":35,"title":"Pengajuan Cuti Baru","message":"Estevao mengajukan cuti tanggal 27 Januari 2026.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/35"}', '2026-01-19 13:42:28', '2026-01-15 14:42:14', '2026-01-19 13:42:28'),
	('b095e89e-cc84-4afc-ae5d-ed9cd3dac20f', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 9, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:11', '2026-01-19 13:56:11'),
	('b096fdee-c10c-4637-b758-752c4d84f79b', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":24,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/24","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 16:15:11', '2025-09-28 16:15:11'),
	('b2a846ba-bf66-4123-a200-62e7107a992e', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 1, '{"id":3,"title":"Hari Ini Ada yang Ulang Tahun! \\ud83c\\udf89","message":"Hari ini adalah ulang tahun Palmer. Jangan lupa ucapkan selamat dan doa terbaik!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2025-11-26 09:11:20', '2025-11-26 09:11:20'),
	('b3582241-7707-46c1-aae7-0b0a4971673d', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 10:58:17', '2025-09-27 12:12:12'),
	('b56d023b-487f-4e3a-b0ee-77f78d323253', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 2, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini adalah ulang tahun Widhi. Jangan lupa ucapkan selamat!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', '2026-01-19 13:42:28', '2026-01-19 04:30:23', '2026-01-19 13:42:28'),
	('b73bbbf3-7799-4600-8253-449513624dd5', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 2, '{"id":29,"title":"Pengajuan Cuti Baru","message":"Estevao mengajukan cuti tanggal 18 Desember 2025.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/29"}', '2026-01-19 13:42:28', '2025-12-21 12:05:55', '2026-01-19 13:42:28'),
	('b83b29b7-486b-4805-a417-80ca7ed521ec', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:02', '2026-01-19 04:57:02'),
	('b9133c80-5130-4727-95ad-d82b15bf049c', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":5,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Muhamad Fadhillah Putra Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/localhost\\/cuti\\/5","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 10:56:49', '2025-09-13 10:47:50', '2025-09-13 10:56:49'),
	('b9f0a481-6085-4375-9128-99b5c264e432', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 2, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', '2026-01-19 13:42:28', '2026-01-19 13:42:02', '2026-01-19 13:42:28'),
	('bbac8e75-28e6-486c-9b2c-ad465c4c0496', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":42,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Tes\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/42","icon":"fas fa-check-double","color":"text-green-500"}', '2025-11-18 01:05:08', '2025-10-24 11:57:25', '2025-11-18 01:05:08'),
	('bd089a7d-a6d2-497e-b6b8-9cc11c288693', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":30,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Perjalanan Dinas\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/30","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-04 00:28:11', '2025-10-04 00:28:11'),
	('bf02341d-205a-4d14-a6fa-5a5909a1d884', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":10,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Kunci\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/10","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-21 14:24:15', '2025-09-21 14:24:15'),
	('bf5235cc-057d-423e-9e36-9d36855d4332', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:01:40', '2025-10-20 13:27:42'),
	('c0d969ce-e12e-4002-b9a9-16ef35d87e95', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":49,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Pembelian Meja\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/49","icon":"fas fa-check-double","color":"text-green-500"}', NULL, '2025-12-19 11:54:49', '2025-12-19 11:54:49'),
	('c1e27e64-2826-4303-945b-9e42ff9128fc', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":17,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari diel telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/17","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-09-22 21:00:33', '2025-09-22 21:00:33'),
	('c26b445a-9811-43e6-8d5d-718f9799839e', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":49,"title":"Pengajuan Dana Disetujui","message":"Kabar baik! Pengajuan dana \'Pembelian Meja\' Anda telah disetujui oleh Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/49","icon":"fas fa-check-circle","color":"text-green-600"}', NULL, '2025-12-19 11:55:17', '2025-12-19 11:55:17'),
	('c28fce72-e819-4fc8-9480-d58483169f77', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 6, '{"id":17,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari diel telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/17","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-22 21:25:34', '2025-09-22 21:00:33', '2025-09-22 21:25:34'),
	('c2d3605f-279c-4b2a-bc6d-492060f15ea6', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 3, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:58:01', '2026-01-19 04:58:01'),
	('c8b8344d-d9d1-40ad-af12-e3e174403b82', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 8, '{"id":5,"title":"Pengajuan Barang Disetujui","message":"Kabar baik! Pengajuan barang \'Penggunaan Alat\' Anda telah disetujui oleh Gudang.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/5","icon":"fas fa-check-circle","color":"text-green-600"}', NULL, '2025-12-10 13:43:05', '2025-12-10 13:43:05'),
	('c98f6a8c-74aa-41d0-bac5-40d3d8aa6492', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":19,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/19","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 13:44:52', '2025-09-28 13:44:52'),
	('cad1a5e0-f4c6-455f-8301-79febc3d7345', 'App\\Notifications\\AgendaNotification', 'App\\Models\\User', 8, '{"title":"Agenda Dibatalkan","message":"Muhamad Fadhillah Putra SinagA telah membatalkan agenda \\"Natal\\".","icon":"fas fa-calendar-times","color":"text-red-500","url":"http:\\/\\/127.0.0.1:8000\\/dashboard?agenda_id=22"}', NULL, '2025-12-22 00:59:49', '2025-12-22 00:59:49'),
	('cc5807a9-99fc-4a6f-964f-cdc4e782c737', 'App\\Notifications\\ClientBirthdayNotification', 'App\\Models\\User', 3, '{"title":"Client Ulang Tahun!","message":"Client Cristiano Ronaldo 7 (Al Nasr) ulang tahun hari ini.","url":"http:\\/\\/127.0.0.1:8000\\/admin\\/crm\\/6","icon":"fas fa-user-tie","color":"text-blue-500"}', NULL, '2026-01-19 14:42:50', '2026-01-19 14:42:50'),
	('ccb93edf-85a9-4e81-987c-1715592ff1cb', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 2, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', '2026-01-19 13:42:28', '2026-01-19 04:57:01', '2026-01-19 13:42:28'),
	('cd90e581-059f-444b-9cab-27c8bc550541', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 13, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:11', '2026-01-19 13:56:11'),
	('ce582246-35e6-449c-935e-f28930988b1e', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 3, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:02', '2026-01-19 04:57:02'),
	('cf3af950-8c5b-410e-aea4-fcdff1c5c72e', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:13:36', '2025-09-27 12:12:12'),
	('d036b8d9-f52f-46bf-b65f-e7122914918a', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":18,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Instalasi Internet\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/18","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-27 12:14:51', '2025-09-27 12:14:51'),
	('d0c810b6-e3bc-43b9-a7c2-885c650738a0', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun!","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:48:58', '2026-01-19 13:48:58'),
	('d16d3c54-6326-4a13-aa17-da03b4c7b4cc', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 8, '{"id":21,"title":"Pengajuan Cuti Disetujui","message":"Pengajuan cuti Anda untuk tanggal 16 Oct 2025 telah disetujui. Selamat berlibur!","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/21","icon":"fas fa-check-circle","color":"text-green-600"}', '2025-10-01 14:59:49', '2025-10-01 14:58:45', '2025-10-01 14:59:49'),
	('d3957d7c-4a4c-4787-88c9-361e2a6cf0a6', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:57:02', '2026-01-19 04:57:02'),
	('d44ee71b-6577-421f-9b5f-a871acb004eb', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":6,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Ridho FIrdhiansyah Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/localhost\\/cuti\\/6","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-13 11:15:30', '2025-09-13 11:07:00', '2025-09-13 11:15:30'),
	('d5320c82-d2a9-4b85-93c0-a9cfdd1f3ddf', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":25,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Raimbers\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/25","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 16:22:13', '2025-10-20 13:27:42'),
	('d5845d92-4e50-488e-a9da-31dce096e4da', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:11:55', '2025-09-27 12:12:12'),
	('d6145e09-b3f4-4e85-b8e0-bff94a0e9d85', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 1, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:10', '2026-01-19 13:56:10'),
	('d70fc5e7-018e-4658-8fd5-b7fbb43c3000', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":12,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Ridho FIrdhiansyah Sinaga telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/12","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-16 11:26:43', '2025-09-13 17:06:31', '2025-09-16 11:26:43'),
	('d737f4ef-05eb-4cd9-9f4a-51b772752b2f', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 8, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini adalah ulang tahun Widhi. Jangan lupa ucapkan selamat!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:30:24', '2026-01-19 04:30:24'),
	('d99feeb6-2f99-4f25-8e26-7eb13bab3989', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":22,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/22","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 15:50:50', '2025-09-28 15:50:50'),
	('db83ac2c-224b-45a9-afde-44e46b1fd023', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 3, '{"id":50,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan dana baru: \'Pembelian Perlengkapan Kantor\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/50","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-12-19 12:41:55', '2025-12-19 12:41:55'),
	('dbecd66b-e44c-41a5-aa06-5381c1856895', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:05:44', '2025-09-27 12:12:12'),
	('dcb20f7c-f668-4db0-ad58-2eb709e1f9b0', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 6, '{"id":16,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari diel telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/16","icon":"fas fa-envelope","color":"text-yellow-500"}', '2025-09-22 21:25:34', '2025-09-22 20:56:04', '2025-09-22 21:25:34'),
	('e3ee007d-8001-44a2-b219-13ca98af0eb6', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":41,"title":"Pengajuan Dibatalkan","message":"Pengajuan dana \'a\' oleh Estevao telah dibatalkan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/41","icon":"fas fa-ban","color":"text-slate-500"}', '2025-10-20 13:27:42', '2025-10-18 22:37:54', '2025-10-20 13:27:42'),
	('e42d21bd-8225-4476-88af-ec5ff8bf9757', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":4,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Pembelian Baju\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/4","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-11 13:35:31', '2025-09-11 13:34:51', '2025-09-11 13:35:31'),
	('e4b506e2-19ce-4164-9fad-8e4a7986397e', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 13:42:03', '2026-01-19 13:42:03'),
	('e5fcb231-344f-45bd-967c-cfdfe558edfb', 'App\\Notifications\\HolidayNotification', 'App\\Models\\User', 3, '{"title":"Hari Ini Libur!","message":"Hari ini libur: Libur Senin. Selamat beristirahat!","url":"#","icon":"fas fa-umbrella-beach","color":"text-green-500"}', NULL, '2026-01-19 13:56:11', '2026-01-19 13:56:11'),
	('e5fd0988-e7c6-4423-baa5-344876c74aa7', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":16,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari diel telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/16","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-09-22 20:56:04', '2025-09-22 20:56:04'),
	('e73d04d6-abc4-4d43-9ece-bbf087f8090a', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:09:03', '2025-09-27 12:12:12'),
	('e7bde8fa-ae23-495e-a3b9-183fb349580d', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 8, '{"id":40,"title":"Pengajuan Dana Diproses","message":"Pengajuan \'Alat\' Anda telah disetujui atasan dan diteruskan ke Finance.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/40","icon":"fas fa-check-double","color":"text-green-500"}', '2025-11-18 01:05:08', '2025-10-18 14:22:33', '2025-11-18 01:05:08'),
	('e7ca9576-982e-40f7-bdc6-bca73ac04c9c', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":23,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Rembers\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/23","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-28 16:00:26', '2025-09-28 16:00:26'),
	('e8895cb3-4bc4-4db6-9d29-5973b14836f0', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 15:11:30', '2025-10-20 13:27:42'),
	('ea2f4bc0-cc09-4aa3-9591-81f5c385a3f7', 'App\\Notifications\\AgendaNotification', 'App\\Models\\User', 8, '{"id":19,"title":"Agenda Dibatalkan","message":"Agenda \'Rapat\' yang dibuat oleh Admin Rakha telah dibatalkan.","url":"http:\\/\\/127.0.0.1:8000\\/dashboard?agenda_id=19","icon":"fas fa-calendar-times","color":"text-slate-500"}', '2025-11-18 01:05:08', '2025-10-10 13:51:13', '2025-11-18 01:05:08'),
	('eac6adce-867d-4d06-92f8-bb0405364b3d', 'App\\Notifications\\PengajuanBarangNotification', 'App\\Models\\User', 13, '{"id":7,"title":"Pengajuan Barang Baru","message":"Estevao mengajukan barang baru: \'Penggunaan Kasa Roll\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-barang\\/7","icon":"fas fa-box","color":"text-blue-600"}', NULL, '2025-12-10 14:11:20', '2025-12-10 14:11:20'),
	('eae5c693-e935-4a7e-aa7a-41a9bfe17c8a', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":12,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Baju\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/12","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-24 01:03:40', '2025-09-24 01:03:40'),
	('f1e50564-2395-43b2-bc87-91456d3a8e65', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":13,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/13","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-09-26 20:03:22', '2025-09-26 20:03:22'),
	('f3c19b34-2e58-4286-b119-838a3e712898', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":31,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Laptop\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/31","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-10-17 13:28:35', '2025-10-20 13:27:42'),
	('f544ea37-e2a5-4874-9b76-5ca3d5a47cd6', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 3, '{"id":34,"title":"Pengajuan Cuti Baru","message":"Muhamad Fadhillah Putra SinagA mengajukan cuti tanggal 27 Januari 2026.","icon":"fas fa-envelope","color":"text-blue-600","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/34"}', NULL, '2026-01-15 14:23:26', '2026-01-15 14:23:26'),
	('f5eff657-9bd4-4d20-bc31-3b714fe9c3cc', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":27,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Perjalanan Dinas\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/27","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-01 14:14:20', '2025-10-01 14:14:20'),
	('f6932566-b5dc-48c9-a9ea-ad6c23fb834f', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":21,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Laptop\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/21","icon":"fas fa-coins","color":"text-blue-600"}', '2025-10-20 13:27:42', '2025-09-28 14:51:17', '2025-10-20 13:27:42'),
	('f7077c90-1caf-4097-865a-da1985562ddf', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 6, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini adalah ulang tahun Widhi. Jangan lupa ucapkan selamat!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', NULL, '2026-01-19 04:30:24', '2026-01-19 04:30:24'),
	('fa56d0be-5b72-489a-b03a-d2eb542f3408', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":29,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Laptop\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/29","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-01 14:51:56', '2025-10-01 14:51:56'),
	('fabb03cd-ff78-4ead-80b9-6eb133b79f8e', 'App\\Notifications\\BirthdayNotification', 'App\\Models\\User', 2, '{"id":13,"title":"Hari Ini Ada yang Ulang Tahun! \\ue05e\\u8102","message":"Hari ini Widhi ulang tahun. Klik untuk kirim ucapan!","url":"#","icon":"fas fa-birthday-cake","color":"text-pink-500"}', '2026-01-19 13:42:28', '2026-01-19 04:58:00', '2026-01-19 13:42:28'),
	('fabc6423-77f7-47cb-b690-53328ff2221c', 'App\\Notifications\\ClientBirthdayNotification', 'App\\Models\\User', 2, '{"title":"Client Ulang Tahun!","message":"Client Cristiano Ronaldo 7 (Al Nasr) ulang tahun hari ini.","url":"http:\\/\\/127.0.0.1:8000\\/admin\\/crm\\/6","icon":"fas fa-user-tie","color":"text-blue-500"}', NULL, '2026-01-19 14:42:50', '2026-01-19 14:42:50'),
	('fb6a800e-9ca0-4e2d-8002-29e189c8ad6a', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":42,"title":"Pengajuan Dana Baru","message":"Estevao mengajukan dana baru: \'Tes\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/42","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-24 11:58:17', '2025-10-24 11:58:17'),
	('fc8bcfbc-a5bc-4f06-b1a9-4c587e3fbad7', 'App\\Notifications\\CutiNotification', 'App\\Models\\User', 5, '{"id":20,"title":"Pengajuan Cuti Baru","message":"Pengajuan cuti dari Estevao telah diajukan dan menunggu persetujuan Anda.","url":"http:\\/\\/127.0.0.1:8000\\/cuti\\/20","icon":"fas fa-envelope","color":"text-yellow-500"}', NULL, '2025-10-01 14:12:29', '2025-10-01 14:12:29'),
	('ffe10e02-7fa3-4e75-8f4f-d08a2fbbf1e3', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 5, '{"id":34,"title":"Pengajuan Dana Baru","message":"Muhamad Fadhillah Putra Sinaga mengajukan dana baru: \'Test Pembelian\'. Mohon direview.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/34","icon":"fas fa-coins","color":"text-blue-600"}', NULL, '2025-10-17 23:07:20', '2025-10-17 23:07:20'),
	('fff7e0ad-59b8-4f46-98b8-a4a04821fadf', 'App\\Notifications\\PengajuanDanaNotification', 'App\\Models\\User', 9, '{"id":16,"title":"Pengajuan Dana Baru","message":"Pengajuan dana dengan judul \\"Alat\\" telah diajukan.","url":"http:\\/\\/127.0.0.1:8000\\/pengajuan-dana\\/16","icon":"fas fa-coins","color":"text-blue-600"}', '2025-09-27 12:12:12', '2025-09-27 11:16:25', '2025-09-27 12:12:12');

-- Dumping structure for table rakha.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table rakha.pengajuan_barang
CREATE TABLE IF NOT EXISTS `pengajuan_barang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `judul_pengajuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `divisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rincian_barang` json NOT NULL,
  `status` enum('diajukan','diproses','selesai','ditolak','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `status_atasan` enum('menunggu','disetujui','ditolak','skipped','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `atasan_id` bigint unsigned DEFAULT NULL,
  `catatan_atasan` text COLLATE utf8mb4_unicode_ci,
  `atasan_approved_at` timestamp NULL DEFAULT NULL,
  `status_gudang` enum('menunggu','disetujui','ditolak','skipped','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `gudang_id` bigint unsigned DEFAULT NULL,
  `catatan_gudang` text COLLATE utf8mb4_unicode_ci,
  `gudang_approved_at` timestamp NULL DEFAULT NULL,
  `status_direktur` enum('menunggu','disetujui','ditolak','skipped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'skipped',
  `catatan_direktur` text COLLATE utf8mb4_unicode_ci,
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `lampiran` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_barang_user_id_foreign` (`user_id`),
  CONSTRAINT `pengajuan_barang_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.pengajuan_barang: ~7 rows (approximately)
INSERT INTO `pengajuan_barang` (`id`, `user_id`, `judul_pengajuan`, `divisi`, `rincian_barang`, `status`, `status_atasan`, `atasan_id`, `catatan_atasan`, `atasan_approved_at`, `status_gudang`, `gudang_id`, `catatan_gudang`, `gudang_approved_at`, `status_direktur`, `catatan_direktur`, `catatan_admin`, `lampiran`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Penggunaan Kasa', 'Tim IT', '[{"jumlah": "2", "deskripsi": "Kassa"}]', 'diajukan', 'skipped', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'skipped', NULL, NULL, '[]', '2025-12-08 04:38:01', '2025-12-08 04:38:01'),
	(2, 2, 'Alat', 'Tim IT', '[{"jumlah": "50", "deskripsi": "Perban"}]', 'diajukan', 'skipped', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'skipped', NULL, NULL, '[]', '2025-12-09 15:13:26', '2025-12-09 15:13:26'),
	(3, 2, 'test', 'Tim IT', '[{"jumlah": "20", "deskripsi": "tes"}]', 'diajukan', 'skipped', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'skipped', NULL, NULL, '[]', '2025-12-10 12:25:46', '2025-12-10 12:25:46'),
	(4, 8, 'Alat', 'Tim IT', '[{"jumlah": "4", "satuan": "Pack", "deskripsi": "Alat Praktek"}, {"jumlah": "20", "satuan": "Box", "deskripsi": "Kassa"}]', 'diajukan', 'menunggu', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'skipped', NULL, NULL, '["lampiran_barang/nrC1cVFms7N0A71qs8TE9g1AuhlzRo6q5h1IUhbz.png"]', '2025-12-10 12:44:13', '2025-12-10 12:44:13'),
	(5, 8, 'Penggunaan Alat', 'Tim IT', '[{"jumlah": "2", "satuan": "Set", "deskripsi": "Alat"}]', 'selesai', 'disetujui', NULL, 'lanjut', NULL, 'disetujui', NULL, 'aman', NULL, 'skipped', NULL, NULL, '["lampiran_barang/8y3Mueb8HYmwvN5Syref6edJKWFV7CtGpG0zK8fS.jpg"]', '2025-12-10 13:35:39', '2025-12-10 13:43:05'),
	(6, 8, 'Penggunaan Kasa Roll', 'Tim IT', '[{"jumlah": "2", "satuan": "Box", "deskripsi": "Kassa Rol Big"}]', 'dibatalkan', 'dibatalkan', NULL, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'skipped', NULL, NULL, '[]', '2025-12-10 14:02:15', '2025-12-10 14:05:06'),
	(7, 8, 'Penggunaan Kasa Roll', 'Tim IT', '[{"jumlah": "5", "satuan": "Roll", "deskripsi": "Big Kassa"}]', 'selesai', 'disetujui', NULL, 'ok', '2025-12-10 14:11:20', 'disetujui', NULL, NULL, NULL, 'skipped', NULL, NULL, '["lampiran_barang/zLU0IuzhuMR8InKVwiRiXxOJ1CFpNNPv2V3d43Cw.jpg"]', '2025-12-10 14:10:50', '2025-12-10 14:11:50'),
	(8, 2, 'Penggunaan Kasa', 'Tim IT', '[{"jumlah": "100", "satuan": "Roll", "deskripsi": "Kassa Rol Big"}]', 'selesai', 'skipped', NULL, NULL, NULL, 'disetujui', 13, 'aman', '2025-12-10 14:46:27', 'skipped', NULL, NULL, '[]', '2025-12-10 14:45:27', '2025-12-10 14:46:27');

-- Dumping structure for table rakha.pengajuan_dana
CREATE TABLE IF NOT EXISTS `pengajuan_dana` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `finance_id` bigint unsigned DEFAULT NULL,
  `finance_processed_at` timestamp NULL DEFAULT NULL,
  `judul_pengajuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `divisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_bank` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rekening` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_dana` bigint NOT NULL DEFAULT (0),
  `lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approver_1_id` bigint unsigned DEFAULT NULL,
  `approver_1_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `approver_1_catatan` text COLLATE utf8mb4_unicode_ci,
  `approver_1_approved_at` timestamp NULL DEFAULT NULL,
  `approver_2_id` bigint unsigned DEFAULT NULL,
  `approver_2_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `payment_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `approver_2_catatan` text COLLATE utf8mb4_unicode_ci,
  `approver_2_approved_at` timestamp NULL DEFAULT NULL,
  `rincian_dana` json NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bukti_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_finance` text COLLATE utf8mb4_unicode_ci,
  `nama_rek` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_dana_user_id_foreign` (`user_id`),
  CONSTRAINT `pengajuan_dana_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.pengajuan_dana: ~45 rows (approximately)
INSERT INTO `pengajuan_dana` (`id`, `user_id`, `finance_id`, `finance_processed_at`, `judul_pengajuan`, `divisi`, `nama_bank`, `no_rekening`, `total_dana`, `lampiran`, `approver_1_id`, `approver_1_status`, `approver_1_catatan`, `approver_1_approved_at`, `approver_2_id`, `approver_2_status`, `payment_status`, `approver_2_catatan`, `approver_2_approved_at`, `rincian_dana`, `status`, `created_at`, `updated_at`, `bukti_transfer`, `invoice`, `catatan_finance`, `nama_rek`) VALUES
	(1, 2, NULL, NULL, 'Pembelian Kabel HDMI', 'Tim IT', 'Mandiri', '111111111111', 50000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "50000", "deskripsi": "Kabel HDMI"}]', 'diajukan', '2025-09-11 05:05:02', '2025-09-11 05:05:02', NULL, NULL, NULL, NULL),
	(2, 3, NULL, NULL, 'Perjalanan Dinas', 'Direktur', 'BRI', '11122122', 90000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "60000", "deskripsi": "Transport"}, {"jumlah": "30000", "deskripsi": "Konsumsi"}]', 'diajukan', '2025-09-11 13:25:25', '2025-09-11 13:25:25', NULL, NULL, NULL, NULL),
	(3, 3, NULL, NULL, 'Perjalanan Dinas', 'Direktur', 'BRI', '11122122', 90000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "60000", "deskripsi": "Transport"}, {"jumlah": "30000", "deskripsi": "Konsumsi"}]', 'diajukan', '2025-09-11 13:27:51', '2025-09-11 13:29:39', NULL, NULL, NULL, NULL),
	(6, 2, NULL, NULL, 'Perjalanan Dinas', 'Tim IT', 'Mandiri', '111111111111', 200000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "200000", "deskripsi": "Transport"}]', 'diajukan', '2025-09-12 00:49:26', '2025-09-12 00:49:26', NULL, NULL, NULL, NULL),
	(9, 6, NULL, NULL, 'Pembelian Baju', 'Operasional', 'Mandiri', '11122122', 70000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "70000", "deskripsi": "Baju"}]', 'diajukan', '2025-09-12 02:08:46', '2025-09-22 21:13:54', NULL, NULL, NULL, NULL),
	(10, 2, NULL, NULL, 'Kunci', 'Tim IT', 'Mandiri', '22323323', 45000, 'lampiran_dana/c4TU5J0vehwEAWpw1K4Y0nltES9EnqPt8RgSyABE.pdf', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "40.000", "deskripsi": "kunci"}, {"jumlah": "5.000", "deskripsi": "gantungan"}]', 'diajukan', '2025-09-21 14:24:15', '2025-09-26 19:54:16', NULL, NULL, NULL, NULL),
	(12, 8, NULL, NULL, 'Baju', 'Tim IT', 'Mandiri', '22323323', 75000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "75.000", "deskripsi": "Baju"}]', 'diajukan', '2025-09-24 01:03:40', '2025-09-24 01:04:23', NULL, NULL, NULL, NULL),
	(13, 2, NULL, NULL, 'Alat', 'Tim IT', 'BNI', '111111111111', 510000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": 40000, "deskripsi": "Kabel HDMI"}, {"jumlah": 440000, "deskripsi": "Hard Disk"}, {"jumlah": 30000, "deskripsi": "Ongkir"}]', 'diajukan', '2025-09-26 20:03:22', '2025-09-26 20:03:43', NULL, NULL, NULL, NULL),
	(14, 2, NULL, NULL, 'Baju', 'Tim IT', 'BNI', '111111111111', 120000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": 120000, "deskripsi": "Baju Kaos Kantor"}]', 'diajukan', '2025-09-26 20:14:00', '2025-09-26 20:14:48', NULL, NULL, NULL, NULL),
	(15, 2, NULL, NULL, 'Perjalanan Dinas', 'Tim IT', 'BRI', '11122122', 700000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": 700000, "deskripsi": "Transport"}]', 'diajukan', '2025-09-26 20:19:13', '2025-09-26 20:19:22', NULL, NULL, NULL, NULL),
	(16, 2, NULL, NULL, 'Alat', 'Tim IT', 'Mandiri', '22323323', 1450000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "1450000", "deskripsi": "Meja Kantor"}]', 'disetujui', '2025-09-27 10:55:13', '2025-09-27 11:42:45', NULL, NULL, NULL, NULL),
	(17, 8, NULL, NULL, 'Pembelian TV', 'Tim IT', 'BNI', '22323323', 13500000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "13500000", "deskripsi": "TV LG 12432"}]', 'diajukan', '2025-09-27 12:10:19', '2025-09-27 12:11:24', NULL, NULL, NULL, NULL),
	(18, 8, NULL, NULL, 'Instalasi Internet', 'Tim IT', 'BCA', '11122122', 3750000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "550000", "deskripsi": "Langganan Provider (1 Tahun)"}, {"jumlah": "3200000", "deskripsi": "Instalasi"}]', 'disetujui', '2025-09-27 12:14:51', '2025-09-27 12:23:19', NULL, NULL, NULL, NULL),
	(19, 2, NULL, NULL, 'Perjalanan Dinas', 'Tim IT', 'BCA', '111111111111', 360000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "300000", "deskripsi": "Transport"}, {"jumlah": "60000", "deskripsi": "Makan"}]', 'diajukan', '2025-09-28 13:44:47', '2025-09-28 13:44:47', NULL, NULL, NULL, NULL),
	(20, 2, NULL, NULL, 'Perjalanan Dinas', 'Tim IT', 'BRI', '111111111111', 430000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "430000", "deskripsi": "Transport"}]', 'diajukan', '2025-09-28 14:02:58', '2025-09-28 14:03:26', NULL, NULL, NULL, NULL),
	(22, 2, NULL, NULL, 'Laptop', 'Tim IT', 'Mandiri', '11122122', 13821999, 'lampiran_dana/HyH7jDZrICF5PCpb50sVNzohgIurjHAR7BWF1NbA.png', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "13799999", "deskripsi": "Asus ROG"}, {"jumlah": "22000", "deskripsi": "ongkir"}]', 'disetujui', '2025-09-28 15:50:50', '2025-09-28 15:56:12', 'bukti_transfer/op4US4Lv891IcJUN6qZJK4oSUzQXWdaHka3m0Su2.png', NULL, NULL, NULL),
	(23, 8, NULL, NULL, 'Rembers', 'Tim IT', 'BNI', '22323323', 456000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "456000", "deskripsi": "Meja"}]', 'disetujui', '2025-09-28 16:00:26', '2025-09-28 16:02:20', 'bukti_transfer/aCd50QIHl5x5Q8oeDBsqB1cHJPeKoTvig8Y5E50Z.png', NULL, NULL, NULL),
	(24, 2, NULL, NULL, 'Alat', 'Tim IT', 'BJB', '11122122', 126000, 'lampiran_dana/9lDFwzJM2srDDr9ddtahow3P6F99W61s3JCurJwX.pdf', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "126000", "deskripsi": "Kabel HDMI"}]', 'disetujui', '2025-09-28 16:15:10', '2025-09-28 16:19:59', 'bukti_transfer/7ml7Z7Uw9y95es0k7sRP8zdJlLicyQWAhhC6xl8O.png', 'invoices/U4GuuQMVw64tIDyNNmGytTeO5TseVa33XPA3YqnC.pdf', NULL, NULL),
	(25, 8, NULL, NULL, 'Raimbers', 'Tim IT', 'BCA', '232323', 75000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "75000", "deskripsi": "Baju Kaos Kantor"}]', 'disetujui', '2025-09-28 16:21:40', '2025-09-28 16:22:40', NULL, NULL, NULL, NULL),
	(26, 9, NULL, NULL, 'Baju', 'Finance dan Gudang', 'Mandiri', '11122122', 88000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "88000", "deskripsi": "Baju"}]', 'disetujui', '2025-09-28 16:23:06', '2025-09-28 16:24:15', NULL, NULL, NULL, NULL),
	(27, 8, NULL, NULL, 'Perjalanan Dinas', 'Tim IT', 'BRI', '11122122', 100000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "100000", "deskripsi": "Transport"}]', 'disetujui', '2025-10-01 14:14:20', '2025-10-01 14:15:42', NULL, NULL, NULL, NULL),
	(28, 8, NULL, NULL, 'Pengadaan Batik Bogor', 'Tim IT', 'Mandiri', '22323323', 5545550, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "5545550", "deskripsi": "Baju Batik Bogor"}]', 'disetujui', '2025-10-01 14:38:04', '2025-10-01 14:39:53', NULL, NULL, NULL, NULL),
	(29, 8, NULL, NULL, 'Laptop', 'Tim IT', 'Mandiri', '111111111111', 11000000, NULL, NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "11000000", "deskripsi": "laptop"}]', 'ditolak', '2025-10-01 14:51:55', '2025-10-01 14:53:45', NULL, NULL, NULL, NULL),
	(30, 2, NULL, NULL, 'Perjalanan Dinas', 'Tim IT', 'BNI', '111111111111', 808000, '["lampiran_dana\\/BfEmF4aHxiXCA2k8YnBMbAmHO6eBuz8kUHdcacK3.png","lampiran_dana\\/dWAQAVc8a27Eee5t5xfz0ksKFx9YIbpAfmmBDh2P.jpg","lampiran_dana\\/KzUVo5Qy75XyF2Iaj6jfADVuphF2HAW206hb27te.pdf"]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "808000", "deskripsi": "Transport"}]', 'dibatalkan', '2025-10-04 00:28:11', '2025-10-04 02:16:41', NULL, NULL, NULL, NULL),
	(31, 2, 9, NULL, 'Laptop', 'Tim IT', 'BCA', '111111111111', 11000000, '[]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "11000000", "deskripsi": "laptop"}]', 'disetujui', '2025-10-12 22:51:04', '2025-10-17 13:29:10', NULL, NULL, NULL, NULL),
	(32, 8, NULL, NULL, 'Pembelian Meja', 'Tim IT', 'BCA', '111111111111', 4200000, '["lampiran_dana\\/YhIY60wpsQDrcZUs4Gqukn0cmftFjgBLC9aAqsCX.pdf"]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "4000000", "deskripsi": "Meja Kantor"}, {"jumlah": "200000", "deskripsi": "Meja"}]', 'diajukan', '2025-10-16 20:00:16', '2025-10-16 20:00:16', NULL, NULL, NULL, NULL),
	(33, 8, NULL, NULL, 'Baju', 'Tim IT', 'BCA', '111111111111', 115000, '["lampiran_dana\\/kzY9FXNvEJ2e19Ot2eDF3xpoKK8IxDFjz6AlAUeC.pdf","lampiran_dana\\/2glQe1mdXrxPz28KcKGpzDwUymP57mvveDMfvzje.pdf","lampiran_dana\\/QXX9Ie0LGXMkXaajdKIAmCc4gtRt6bD1KfrySUDl.png"]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "75000", "deskripsi": "Baju"}, {"jumlah": "40000", "deskripsi": "Baju"}]', 'diproses', '2025-10-16 20:01:43', '2025-10-16 20:18:35', NULL, NULL, NULL, NULL),
	(34, 2, 9, NULL, 'Test Pembelian', 'Tim IT', 'Mandiri', '2099912', 125000, '[]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "45000", "deskripsi": "Test 1"}, {"jumlah": "80000", "deskripsi": "Test 2"}]', 'disetujui', '2025-10-17 23:07:15', '2025-10-17 23:13:05', NULL, NULL, NULL, NULL),
	(35, 2, NULL, NULL, 'Motor', 'Tim IT', 'BNI', '2099912', 12750000, '["lampiran_dana\\/OigRbE94SYqnmytagWkZLzFQNurkylAAlu68kCfA.pdf"]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "12750000", "deskripsi": "Lexi 125"}]', 'diajukan', '2025-10-17 23:28:02', '2025-10-17 23:28:02', NULL, NULL, NULL, NULL),
	(36, 2, NULL, NULL, 'Motor', 'Tim IT', 'BNI', '2099912', 12750000, '["lampiran_dana\\/RLOUfXAKhaSHgH1XBq5Wg59bWfl0875yNzNEXz7B.pdf"]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "12750000", "deskripsi": "Lexi 125"}]', 'diproses', '2025-10-17 23:28:03', '2025-10-17 23:37:07', NULL, NULL, NULL, NULL),
	(37, 2, NULL, NULL, 'Baju', 'Tim IT', 'BCA', '11122122', 200000, '[]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "200000", "deskripsi": "Baju"}]', 'diajukan', '2025-10-17 23:29:03', '2025-10-17 23:29:03', NULL, NULL, NULL, NULL),
	(38, 8, NULL, NULL, 'Tessssssssssssssssssssssssssssssssssssssssssssssssssssss', 'Tim IT', 'Mandiri', '111111111111', 90000, '[]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "40000", "deskripsi": "Tes"}, {"jumlah": "50000", "deskripsi": "Tess"}]', 'diajukan', '2025-10-18 10:37:11', '2025-10-18 10:37:11', NULL, NULL, NULL, NULL),
	(39, 8, NULL, NULL, 'Laptop', 'Tim IT', 'Mandiri', '232323', 4789999, '[]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "4789999", "deskripsi": "Asus Vivobook"}]', 'diajukan', '2025-10-18 10:40:34', '2025-10-18 10:40:34', NULL, NULL, NULL, NULL),
	(40, 8, 9, NULL, 'Alat', 'Tim IT', 'Mandiri', '22323323', 8989999, '[]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "8989999", "deskripsi": "tes"}]', 'disetujui', '2025-10-18 14:21:55', '2025-10-18 14:23:03', NULL, NULL, NULL, NULL),
	(41, 8, NULL, NULL, 'a', 'Tim IT', 'Mandiri', '232323aa', 30000, '["lampiran_dana\\/20lq5Gsipna4tDigJQncKGmiYbhopAgCETeXeSVC.pdf"]', NULL, 'menunggu', NULL, NULL, NULL, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "30000", "deskripsi": "a"}]', 'dibatalkan', '2025-10-18 22:37:17', '2025-10-18 22:37:54', NULL, NULL, NULL, NULL),
	(42, 8, 9, '2025-10-24 12:08:40', 'Tes', 'Tim IT', 'BNI', '111111111111', 400000, '["lampiran_dana\\/7BYfazMEWxJYHlUaSl0sQjkty2E4iQ2SfUh000Np.pdf"]', 2, 'disetujui', 'lanjut terus', '2025-10-24 11:57:25', 3, 'disetujui', 'selesai', 'ok', '2025-10-24 11:58:17', '[{"jumlah": "400000", "deskripsi": "Tes"}]', 'selesai', '2025-10-24 11:49:05', '2025-10-24 12:09:03', 'bukti_transfer/dhfOwPsW1LXQ1L3cvkhbFZfphhaOtnpEF5U5Fr5W.pdf', NULL, NULL, NULL),
	(43, 8, NULL, NULL, 'Laptop', 'Tim IT', 'BCA', '2099912', 4819999, '[]', 2, 'menunggu', NULL, NULL, 3, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "80000", "deskripsi": "Kabel HDMI"}, {"jumlah": "4739999", "deskripsi": "Laptop AXIOO"}]', 'dibatalkan', '2025-10-24 12:33:38', '2025-10-24 12:41:00', NULL, NULL, NULL, NULL),
	(44, 8, NULL, NULL, 'Baju', 'Tim IT', 'BRI', '232323', 110000, '[]', 2, 'disetujui', NULL, '2025-10-24 12:41:47', 3, 'ditolak', 'menunggu', 'no', '2025-10-24 12:42:11', '[{"jumlah": "110000", "deskripsi": "Baju"}]', 'ditolak', '2025-10-24 12:41:22', '2025-10-24 12:42:11', NULL, NULL, NULL, NULL),
	(45, 2, 9, '2025-10-26 03:05:11', 'Alat', 'Tim IT', 'Mandiri', '111111111111', 400000, '[]', 3, 'disetujui', 'ok', '2025-10-26 02:53:52', NULL, 'skipped', 'selesai', NULL, NULL, '[{"jumlah": "400000", "deskripsi": "Alat Praktek"}]', 'selesai', '2025-10-26 02:51:47', '2025-10-26 03:05:30', 'bukti_transfer/FwWKu5LQJg3FKUDN0XBhDAAnS8WCJTrwPRmIoG7D.pdf', NULL, 'okk', NULL),
	(46, 2, NULL, NULL, 'Laptop', 'Tim IT', 'BCA', '111111111111', 11450000, '["lampiran_dana\\/zZlpz0GnXKUGZ5zcqZI3Xq6HEZWQo1mwdXGURTpN.pdf"]', 3, 'menunggu', NULL, NULL, NULL, 'skipped', 'menunggu', NULL, NULL, '[{"jumlah": "11450000", "deskripsi": "Asus ROG"}]', 'diajukan', '2025-10-26 10:37:46', '2025-10-26 10:37:46', NULL, NULL, NULL, NULL),
	(47, 8, NULL, NULL, 'test', 'Tim IT', 'BRI', '2099912', 700000, '["lampiran_dana\\/9zneaDDMtvkXH9jddKMflxf4lv1jaf4bTQuU2f8y.png"]', 2, 'menunggu', NULL, NULL, 3, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "700000", "deskripsi": "tes"}]', 'diajukan', '2025-11-03 23:43:57', '2025-11-03 23:43:57', NULL, NULL, NULL, NULL),
	(48, 2, 9, '2025-12-10 12:46:21', 'test', 'Tim IT', 'BRI', '24342424234', 45555555, '["lampiran_dana\\/1jCWSd3ULIpQWkfOpCug4C0A4tfuue2l3OYvEqPe.jpg"]', 3, 'disetujui', 'Lanjutkan', '2025-12-10 12:46:01', NULL, 'skipped', 'selesai', NULL, NULL, '[{"jumlah": "45555555", "deskripsi": "tes"}]', 'selesai', '2025-12-10 12:45:33', '2025-12-10 12:46:33', 'bukti_transfer/qDpreZW23C6BZVYtHsNAbXAdJ58yETe54RNbSUhx.png', NULL, 'Oke', NULL),
	(49, 8, 1, '2025-12-19 12:15:07', 'Pembelian Meja', 'Tim IT', 'Mandiri', '111111111111', 3000000, '[]', 2, 'disetujui', 'lanjut ya', '2025-12-19 11:54:49', 3, 'disetujui', 'selesai', 'okee', '2025-12-19 11:55:17', '[{"jumlah": "3000000", "deskripsi": "Meja Kantor"}]', 'selesai', '2025-12-19 11:54:03', '2025-12-19 12:15:07', NULL, NULL, 'Diselesaikan oleh Admin (Override)', NULL),
	(50, 2, 1, '2025-12-19 12:43:36', 'Pembelian Perlengkapan Kantor', 'Tim IT', 'Mandiri', '90909219', 465000, '[]', 3, 'disetujui', 'Lanjutkan', '2025-12-19 12:42:31', NULL, 'skipped', 'selesai', NULL, NULL, '[{"jumlah": "320000", "deskripsi": "Meja"}, {"jumlah": "100000", "deskripsi": "Kursi"}, {"jumlah": "45000", "deskripsi": "HDMI"}]', 'selesai', '2025-12-19 12:41:55', '2025-12-19 12:43:37', 'bukti_transfer/oEaLIl2N6lKJfKzo1seJ3fgveGZ95nsKdfXrP4xr.png', NULL, 'Ok done', NULL),
	(51, 8, NULL, NULL, 'Testing', 'Tim IT', 'BRI', '9090888', 450000, '[]', 2, 'disetujui', 'okey', '2025-12-22 00:47:32', 3, 'menunggu', 'menunggu', NULL, NULL, '[{"jumlah": "450000", "deskripsi": "test"}]', 'diproses_appr_2', '2025-12-22 00:46:21', '2025-12-22 00:47:32', NULL, NULL, NULL, 'Testing');

-- Dumping structure for table rakha.pengajuan_dokumens
CREATE TABLE IF NOT EXISTS `pengajuan_dokumens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `jenis_dokumen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `file_pendukung` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('diajukan','diproses','selesai','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `file_hasil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_dokumens_user_id_foreign` (`user_id`),
  CONSTRAINT `pengajuan_dokumens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.pengajuan_dokumens: ~0 rows (approximately)
INSERT INTO `pengajuan_dokumens` (`id`, `user_id`, `jenis_dokumen`, `deskripsi`, `file_pendukung`, `status`, `catatan_admin`, `file_hasil`, `created_at`, `updated_at`) VALUES
	(1, 2, 'Surat Keterangan Kerja', 'KPR', NULL, 'selesai', 'Done', 'dokumen_hasil/1Qxq1h92X2T9yvWOJQCYyQBPkUIr7wy7EUzfBYtP.pdf', '2025-09-27 13:05:06', '2025-09-27 13:10:27'),
	(2, 9, 'Slip Gaji', 'Pengajuan KPR', NULL, 'selesai', NULL, 'dokumen_hasil/s1TEKfsX932LJOUgwkIJXVChz8psxuboPek5Ct2T.docx', '2025-09-28 16:35:32', '2025-09-28 16:36:19');

-- Dumping structure for table rakha.riwayat_pekerjaan
CREATE TABLE IF NOT EXISTS `riwayat_pekerjaan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `nama_perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `posisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `deskripsi_pekerjaan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_pekerjaan_user_id_foreign` (`user_id`),
  CONSTRAINT `riwayat_pekerjaan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.riwayat_pekerjaan: ~0 rows (approximately)

-- Dumping structure for table rakha.riwayat_pendidikan
CREATE TABLE IF NOT EXISTS `riwayat_pendidikan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `jenjang` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_institusi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jurusan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_lulus` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_pendidikan_user_id_foreign` (`user_id`),
  CONSTRAINT `riwayat_pendidikan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.riwayat_pendidikan: ~2 rows (approximately)
INSERT INTO `riwayat_pendidikan` (`id`, `user_id`, `jenjang`, `nama_institusi`, `jurusan`, `tahun_lulus`, `created_at`, `updated_at`) VALUES
	(1, 2, 'SMA/SMK Sederajat', 'SMAN 3 BOGOR', 'IPA', '2023', '2025-10-21 15:35:24', '2025-10-21 15:35:24'),
	(2, 2, 'S1', 'UNIVERSITAS PAKUAN', 'ILMU KOMPUTER', '2027', '2025-10-21 15:35:24', '2025-10-21 15:35:24');

-- Dumping structure for table rakha.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('WFclQagIDQyNGNnC2TnVrKk18P49ZR1CJFKyINQZ', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXpjZlVPazJMdWVacnNDYmZEODR1SGVPTTh3YTdZOEJ0TVZZV2ZKRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9nZXQtdXNlcnMiO3M6NToicm91dGUiO3M6MTY6ImFnZW5kYXMuZ2V0VXNlcnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1768833791);

-- Dumping structure for table rakha.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_karyawan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fcm_token` text COLLATE utf8mb4_unicode_ci,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `divisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_kepala_divisi` tinyint(1) NOT NULL DEFAULT '0',
  `atasan_id` bigint unsigned DEFAULT NULL,
  `lokasi_kerja` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_telepon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_ktp` text COLLATE utf8mb4_unicode_ci COMMENT 'Diubah dari alamat',
  `alamat_domisili` text COLLATE utf8mb4_unicode_ci,
  `tempat_lahir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agama` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `golongan_darah` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pernikahan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_darurat_nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_darurat_nomor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kontak_darurat_hubungan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_bergabung` date DEFAULT NULL,
  `tanggal_mulai_kontrak` date DEFAULT NULL,
  `tanggal_akhir_kontrak` date DEFAULT NULL,
  `tanggal_berhenti` date DEFAULT NULL,
  `npwp` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ptkp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bpjs_kesehatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bpjs_ketenagakerjaan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_bank` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_rekening` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemilik_rekening` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jatah_cuti` int NOT NULL DEFAULT '12',
  `approver_1_id` bigint unsigned DEFAULT NULL,
  `approver_2_id` bigint unsigned DEFAULT NULL,
  `manager_keuangan_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `nip` (`nip`),
  KEY `fk_atasan_id` (`atasan_id`),
  KEY `users_approver_1_id_foreign` (`approver_1_id`),
  KEY `users_approver_2_id_foreign` (`approver_2_id`),
  KEY `users_manager_keuangan_id_foreign` (`manager_keuangan_id`),
  CONSTRAINT `fk_atasan_id` FOREIGN KEY (`atasan_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_approver_1_id_foreign` FOREIGN KEY (`approver_1_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_approver_2_id_foreign` FOREIGN KEY (`approver_2_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_manager_keuangan_id_foreign` FOREIGN KEY (`manager_keuangan_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table rakha.users: ~7 rows (approximately)
INSERT INTO `users` (`id`, `nip`, `status_karyawan`, `name`, `email`, `email_verified_at`, `password`, `fcm_token`, `profile_picture`, `jabatan`, `divisi`, `is_kepala_divisi`, `atasan_id`, `lokasi_kerja`, `nomor_telepon`, `alamat_ktp`, `alamat_domisili`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `golongan_darah`, `status_pernikahan`, `nik`, `kontak_darurat_nama`, `kontak_darurat_nomor`, `kontak_darurat_hubungan`, `tanggal_bergabung`, `tanggal_mulai_kontrak`, `tanggal_akhir_kontrak`, `tanggal_berhenti`, `npwp`, `ptkp`, `bpjs_kesehatan`, `bpjs_ketenagakerjaan`, `nama_bank`, `nomor_rekening`, `pemilik_rekening`, `role`, `remember_token`, `created_at`, `updated_at`, `jatah_cuti`, `approver_1_id`, `approver_2_id`, `manager_keuangan_id`) VALUES
	(1, NULL, NULL, 'Admin Rakha', 'admin@rakha.com', NULL, '$2y$12$aHwuec.Rjao2NZ0qlDK8quYwucAPiMkjfIU0JkNsihxYz9Kchq1.u', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', NULL, '2025-09-11 05:03:13', '2025-09-15 00:57:01', 12, NULL, NULL, NULL),
	(2, NULL, NULL, 'Muhamad Fadhillah Putra SinagA', 'fadhilsinaga3@gmail.com', NULL, '$2y$12$GyadiUpSp5hCmRIF7IwfHes8Ywlv6du6Tc4Xdb3J26./HdhzbG92e', 'c3-yyhCM8-yOdryUyMR0w3:APA91bHqGzAUNSlSLQyNDn3pUiCG3ctoW9Wir3Hv1l24bMd9ZOLqDMYh6befEpvhoY6tPnThkjkfyxCuBtwcVIWjT6DM9sbJZ6pIAEywAxtuXNAklA1sW3s', 'profile-pictures/orXGjVnTaB0oy6JL3UwWtmqWRY1Op8HRvpFeaVNs.jpg', 'Kepala Dev', 'Tim IT', 1, NULL, NULL, '081289922400', 'Jl Pajajaran Indah 1 No. 79', NULL, 'Bogor', '2004-11-18', 'Laki-laki', 'Islam', 'A', 'Belum Menikah', '161', 'Ridho', '0808', NULL, '2025-09-01', NULL, NULL, NULL, '909090', 'TK/0', '707070', '707070', 'Mandiri', '1010101010', 'Muhamad Fadhillah', 'user', NULL, '2025-09-11 05:04:12', '2025-12-21 02:50:06', 12, 3, NULL, 9),
	(3, NULL, NULL, 'Palmer', 'palmer@gmail.com', NULL, '$2y$12$9MabuwPQaqMyAJmkNo55M.RklVXjJpw6Jnyo.zVRjyYL6AxATs/6u', 'c3-yyhCM8-yOdryUyMR0w3:APA91bHqGzAUNSlSLQyNDn3pUiCG3ctoW9Wir3Hv1l24bMd9ZOLqDMYh6befEpvhoY6tPnThkjkfyxCuBtwcVIWjT6DM9sbJZ6pIAEywAxtuXNAklA1sW3s', NULL, 'Direktur', 'Direktur', 1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, '2025-09-11 05:58:17', '2025-12-21 14:18:23', 12, NULL, NULL, NULL),
	(6, NULL, NULL, 'Tiara Alifa Amalia', 'tiara@rakhanusantara.com', NULL, '$2y$12$tgdilzMiZgS3Ej2r8nTt8O4JdLtmj5bIEMk8yV8Q6c0h0b/dTwEze', NULL, NULL, 'Admin Support', 'Operasionall', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, '2025-09-12 02:04:46', '2025-10-21 21:53:07', 12, NULL, NULL, NULL),
	(8, NULL, NULL, 'Estevao', 'estevao@gmail.com', NULL, '$2y$12$3t9YSoEReBenNJ9dK7XFcOYNoO2sej6kE5Ze3Co.1GPuxhqayu9j.', 'c3-yyhCM8-yOdryUyMR0w3:APA91bHqGzAUNSlSLQyNDn3pUiCG3ctoW9Wir3Hv1l24bMd9ZOLqDMYh6befEpvhoY6tPnThkjkfyxCuBtwcVIWjT6DM9sbJZ6pIAEywAxtuXNAklA1sW3s', 'profile-pictures/3IRYka4GMnfj8KjK6YWBeddfDxgB5RNHqh6QSbIl.jpg', 'Support', 'Tim IT', 0, NULL, NULL, '081291550783', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, '2025-09-24 00:00:49', '2025-12-21 02:49:45', 7, 2, 3, 9),
	(9, NULL, NULL, 'Asep', 'asep@gmail.com', NULL, '$2y$12$SK2QvVaYniQdU6U4rX59MeNGauo3mMZl77xG6Ox5Pvu43q4db3UHW', NULL, NULL, 'Kepala Finance', 'Finance dan Gudang', 1, NULL, NULL, NULL, NULL, NULL, NULL, '1999-11-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, '2025-09-26 20:05:19', '2025-11-17 11:55:30', 12, NULL, NULL, 9),
	(13, NULL, NULL, 'Widhi', 'widhi@gmail.com', NULL, '$2y$12$.6lPXP3J84j/dUvQR06yauoO.zGKs6S6kQUUE8VtjvIVS8OYqn0TS', 'c3-yyhCM8-yOdryUyMR0w3:APA91bHqGzAUNSlSLQyNDn3pUiCG3ctoW9Wir3Hv1l24bMd9ZOLqDMYh6befEpvhoY6tPnThkjkfyxCuBtwcVIWjT6DM9sbJZ6pIAEywAxtuXNAklA1sW3s', NULL, 'Admin Gudang', 'Finance dan Gudang', 0, NULL, NULL, '081289922400', NULL, NULL, NULL, '2006-01-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, '2025-12-10 12:43:02', '2026-01-19 04:30:52', 12, NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
