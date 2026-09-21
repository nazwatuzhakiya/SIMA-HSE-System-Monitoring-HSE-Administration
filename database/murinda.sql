-- =======================================================================
-- SKEMA DATABASE MYSQL & SEED DATA UNTUK SISTEM INFORMASI HSE
-- PT MURINDA IRON STEEL - PLANT KALIJATI SUBANG
-- DATABASE NAME: MURINDA
-- =======================================================================

CREATE DATABASE IF NOT EXISTS `MURINDA` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `MURINDA`;

-- 1. Tabel Users (Petugas HSE)
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed User HSE
INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'Petugas HSE Kalijati', 'hse.kalijati@murinda.com', '$2y$12$e/9G6xT..yvU9eXf.Yk.r.9qgX.S9s9k0Y6a6q9k0Y6a6q9k0Y6a6')
ON DUPLICATE KEY UPDATE `name`=`name`;

-- 2. Tabel Laporan Bahaya Publik
CREATE TABLE IF NOT EXISTS `laporan_bahayas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `nama_pelapor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Anonim',
  `area_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat_risiko` enum('Rendah','Sedang','Tinggi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sedang',
  `status` enum('Menunggu','Diproses','Selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `tindak_lanjut` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Subcont Personnel (DKP, SUM AGS, PKN, CM, NC, BM, SBL)
CREATE TABLE IF NOT EXISTS `subcont_personnels` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_code` enum('DKP','SUM AGS','PKN','CM','NC','BM','SBL') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DKP',
  `tgl_masuk` date NOT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `posisi` enum('Foreman','Admin','Painter','Blaster','Helper') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Helper',
  `status` enum('Aktif','Non-Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `domisili_alamat` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domisili_desa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domisili_kec` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `domisili_kota` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Master List of Manpower
CREATE TABLE IF NOT EXISTS `manpowers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_induction` date NOT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Aktif','Non-Aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `apd_helmet_issued` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Absensi SMT & TBM
CREATE TABLE IF NOT EXISTS `absensis` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jenis_kegiatan` enum('SMT','TBM') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SMT',
  `tanggal` date NOT NULL,
  `departemen_subcont` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manpower_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_personel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_kehadiran` enum('Hadir','Izin','Sakit','Telat','Alpha') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Hadir',
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`manpower_id`) REFERENCES `manpowers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Logistik APD
CREATE TABLE IF NOT EXISTS `logistik_apds` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manpower_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_personel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departemen_subcont` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pengambilan` date NOT NULL,
  `jenis_apd` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`manpower_id`) REFERENCES `manpowers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel Klinik & Pengambilan Obat
CREATE TABLE IF NOT EXISTS `klinik_obats` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keluhan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_obat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_obat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_pengambilan` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Mobile Audit 5R
CREATE TABLE IF NOT EXISTS `audit_5rs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `auditor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `score_ringkas` int(11) NOT NULL DEFAULT 8,
  `score_rapi` int(11) NOT NULL DEFAULT 8,
  `score_resik` int(11) NOT NULL DEFAULT 8,
  `score_rawat` int(11) NOT NULL DEFAULT 8,
  `score_rajin` int(11) NOT NULL DEFAULT 8,
  `total_percentage` decimal(5,2) NOT NULL DEFAULT 80.00,
  `catatan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel Laporan Insiden K3
CREATE TABLE IF NOT EXISTS `insiden_k3s` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_insiden` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `tanggal` date NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi_kejadian` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('Near-Miss','First Aid','LTI') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Near-Miss',
  `tindak_lanjut` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Open','Investigasi','Closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Open',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
