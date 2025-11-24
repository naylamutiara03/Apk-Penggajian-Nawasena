-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
<<<<<<< HEAD
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 11:32 AM
=======
-- Host: localhost:3306
-- Generation Time: Nov 24, 2025 at 05:07 AM
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
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
-- Database: `penggajian`
--

-- --------------------------------------------------------

--
<<<<<<< HEAD
=======
-- Table structure for table `absensi_karyawan`
--

CREATE TABLE `absensi_karyawan` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `tgl_absen` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time NOT NULL,
  `keterangan_telat` varchar(50) NOT NULL DEFAULT 'Tepat Waktu',
  `telat_menit` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_karyawan`
--

INSERT INTO `absensi_karyawan` (`id`, `id_karyawan`, `tgl_absen`, `jam_masuk`, `jam_keluar`, `keterangan_telat`, `telat_menit`) VALUES
(1, 20, '2025-11-21', '09:00:00', '19:00:00', 'Tepat Waktu', 0),
(2, 15, '2025-11-21', '09:00:00', '20:00:00', 'Tepat Waktu', 0),
(6, 19, '2025-11-21', '09:00:00', '19:27:00', 'Tepat Waktu', 0),
(7, 13, '2025-11-03', '09:00:00', '18:15:00', 'Tepat Waktu', 0),
(8, 13, '2025-11-07', '09:00:00', '18:40:00', 'Tepat Waktu', 0),
(9, 13, '2025-11-10', '09:00:00', '18:07:00', 'Tepat Waktu', 0),
(10, 13, '2025-11-14', '09:00:00', '18:05:00', 'Tepat Waktu', 0),
(11, 13, '2025-11-17', '08:53:00', '18:18:00', 'Tepat Waktu', 0),
(12, 13, '2025-11-22', '09:20:00', '18:00:00', 'Tepat Waktu', 0),
(13, 13, '2025-11-20', '10:00:00', '18:00:00', 'Telat', 0),
(14, 13, '2025-10-20', '09:45:00', '19:00:00', 'Telat', 0),
(15, 19, '2025-11-09', '09:30:00', '18:30:00', 'Telat', 0),
(16, 15, '2025-11-24', '10:00:00', '19:00:00', 'Telat', 0),
(17, 14, '2025-11-24', '10:00:00', '18:00:00', 'Telat', 0);

-- --------------------------------------------------------

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- Table structure for table `absensi_tukang`
--

CREATE TABLE `absensi_tukang` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `bulan` varchar(2) DEFAULT NULL,
  `minggu` int(11) DEFAULT NULL,
  `tahun` varchar(4) DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `total_hadir` decimal(3,1) DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
--
-- Dumping data for table `absensi_tukang`
--

INSERT INTO `absensi_tukang` (`id`, `nik`, `id_jabatan`, `bulan`, `minggu`, `tahun`, `jam_masuk`, `jam_keluar`, `total_hadir`, `tanggal_masuk`, `tanggal_keluar`) VALUES
(45, '3318161009780001', NULL, '05', 1, '2025', '09:00:00', '17:00:00', 1.0, '2025-05-01', '2025-05-01'),
(46, '3318161009780001', NULL, '05', 1, '2025', '09:00:00', '17:00:00', 1.0, '2025-05-02', '2025-05-02'),
(47, '3318161009780001', NULL, '05', 1, '2025', '09:00:00', '22:00:00', 2.0, '2025-05-03', '2025-05-03'),
(48, '3318161009780001', NULL, '05', 1, '2025', '09:00:00', '17:00:00', 1.0, '2025-05-04', '2025-05-04'),
(49, '3318161009780001', NULL, '05', 1, '2025', '09:00:00', '07:45:00', 3.0, '2025-05-05', '2025-05-06'),
(50, '3318161009780001', NULL, '05', 1, '2025', '19:48:00', '01:00:00', 1.0, '2025-05-06', '2025-05-07');

=======
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `idadmin` int(5) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `namalengkap` varchar(40) NOT NULL,
  `role` varchar(20) DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idadmin`, `username`, `password`, `namalengkap`, `role`) VALUES
<<<<<<< HEAD
(61, 'admin', '$2y$10$RpDFLG7vjcQ9AVvrX1CYcupZUmhpY/.9zeggjiYd1iPM8FUAdlRBS', 'nawasena1', 'admin'),
(62, 'superadmin', '$2y$10$Z94NTXy1OlQ5PBwLRAiVkOFLMCHoAUDbVqMw3phTtCAlSjdXhyuAS', 'super admin123', 'superadmin'),
(64, 'mutiara', '$2y$10$rbURwjecaHccCfY7Xe6PvuOUSwHdFQ50oiK2XfJ6Ei1AYin20u8tC', 'nayla1', 'admin');
=======
(62, 'superadmin', '$2y$10$Z94NTXy1OlQ5PBwLRAiVkOFLMCHoAUDbVqMw3phTtCAlSjdXhyuAS', 'super admin123', 'superadmin');
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff

-- --------------------------------------------------------

--
-- Table structure for table `gaji_tukang`
--

CREATE TABLE `gaji_tukang` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `bulan` varchar(2) DEFAULT NULL,
  `gapok` int(11) DEFAULT NULL,
  `total_hadir` decimal(3,1) DEFAULT NULL,
  `total_gaji` int(11) DEFAULT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tahun` varchar(4) NOT NULL,
  `minggu` int(11) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int(2) NOT NULL,
  `jabatan` varchar(40) NOT NULL,
  `gapok` int(10) NOT NULL,
  `tunjangan_jabatan` int(10) NOT NULL,
  `jenis` varchar(40) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `jabatan`, `gapok`, `tunjangan_jabatan`, `jenis`, `total`) VALUES
(28, 'Full Stack Developer', 2500000, 0, 'karyawan', 2500000),
(29, 'Consultant Tax', 4900000, 0, 'karyawan', 4900000),
(30, 'Finance', 4500000, 0, 'karyawan', 4500000),
(31, 'General Affair', 3000000, 0, 'karyawan', 3000000),
(32, 'Creative & Sales', 5000000, 0, 'karyawan', 5000000),
(33, 'Head Operational', 5000000, 0, 'karyawan', 5000000),
(34, 'Sales Marketing', 5000000, 0, 'karyawan', 5000000),
<<<<<<< HEAD
(35, 'CEO', 15000000, 0, 'karyawan', 15000000),
(36, 'Kepala Tukang', 170000, 0, 'tukang', 170000),
(37, 'Kepala Tukang 2', 160000, 0, 'tukang', 160000),
(38, 'Tukang 145', 145000, 0, 'tukang', 145000),
(39, 'Tukang 140', 140000, 0, 'tukang', 140000),
(40, 'Tukang 130', 130000, 0, 'tukang', 130000),
(41, 'Freelance 140', 140000, 0, 'tukang', 140000),
(42, 'Freelance 120', 120000, 0, 'tukang', 120000);
=======
(35, 'CEO', 15000000, 0, 'karyawan', 15000000);
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama_karyawan` varchar(32) NOT NULL,
  `jenis_kelamin` varchar(32) NOT NULL,
  `id_jabatan` int(11) NOT NULL,
  `tgl_masuk` date NOT NULL,
  `status` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `nik`, `nama_karyawan`, `jenis_kelamin`, `id_jabatan`, `tgl_masuk`, `status`) VALUES
(13, '3674064301050005', 'Nayla Mutiara Salsabila Bastari', 'Perempuan', 28, '2025-01-13', 'Tidak Tetap'),
(14, '3174062912830005', 'Anjar Lesmana', 'Laki-laki', 29, '2024-12-16', 'Tetap'),
(15, '3174044303820007', 'Evi Yani', 'Perempuan', 30, '2024-07-14', 'Tetap'),
(16, '3507120401850001', 'Mulyo Prabowo', 'Laki-laki', 35, '2023-01-24', 'Tetap'),
(17, '3173012612840005', 'Suratno', 'Laki-laki', 33, '2023-01-24', 'Tetap'),
(18, '3171074904910002', 'Tyas Ramadhianta', 'Perempuan', 34, '2023-01-24', 'Tetap'),
(19, '3276031110990002', 'Muhammad Roy Syahfei', 'Laki-laki', 31, '2024-06-15', 'Tetap'),
(20, '3171072505900006', 'Indra Setiawan', 'Laki-laki', 32, '2024-01-24', 'Tetap');

-- --------------------------------------------------------

--
<<<<<<< HEAD
=======
-- Table structure for table `lembur_tkg`
--

CREATE TABLE `lembur_tkg` (
  `id` int(11) NOT NULL,
  `id_tukang` int(11) NOT NULL,
  `tgl_lembur` date NOT NULL,
  `minggu_ke` tinyint(1) DEFAULT NULL,
  `shift` varchar(10) DEFAULT NULL,
  `harga_lembur` int(11) NOT NULL,
  `detail_shifts` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lembur_tkg`
--

INSERT INTO `lembur_tkg` (`id`, `id_tukang`, `tgl_lembur`, `minggu_ke`, `shift`, `harga_lembur`, `detail_shifts`, `created_at`) VALUES
(7, 37, '2025-10-08', NULL, 'shift1', 270000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000},{\"shift\":\"shift3\",\"multiplier\":0.5,\"harga\":60000}]', '2025-11-17 08:26:47'),
(18, 35, '2025-11-26', 5, '', 330000, '[{\"shift\":\"1\",\"harga\":100000},{\"shift\":\"2\",\"harga\":110000},{\"shift\":\"3\",\"harga\":120000}]', '2025-11-21 04:00:12'),
(24, 43, '2025-11-21', 4, '', 270000, '[{\"shift\":\"1\",\"harga\":100000,\"multiplier\":1},{\"shift\":\"2\",\"harga\":110000,\"multiplier\":1},{\"shift\":\"3\",\"harga\":60000,\"multiplier\":0.5}]', '2025-11-21 04:42:35'),
(25, 36, '2025-11-12', 5, '', 155000, '[{\"shift\":\"1\",\"harga\":100000,\"multiplier\":1},{\"shift\":\"2\",\"harga\":55000,\"multiplier\":0.5}]', '2025-11-21 04:48:09'),
(26, 42, '2025-11-05', 1, '', 210000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000}]', '2025-11-21 07:29:30'),
(27, 42, '2025-11-06', 1, '', 210000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000}]', '2025-11-21 07:29:55'),
(28, 42, '2025-11-07', 1, '', 210000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000}]', '2025-11-21 07:31:02'),
(29, 42, '2025-11-08', 1, '', 210000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000}]', '2025-11-21 07:31:31'),
(30, 42, '2025-11-09', 1, '', 270000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000},{\"shift\":\"shift3\",\"multiplier\":0.5,\"harga\":60000}]', '2025-11-21 07:32:26'),
(31, 42, '2025-11-10', 1, '', 270000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000},{\"shift\":\"shift3\",\"multiplier\":0.5,\"harga\":60000}]', '2025-11-21 07:32:50'),
(32, 42, '2025-11-11', 1, '', 330000, '[{\"shift\":\"shift1\",\"multiplier\":1,\"harga\":100000},{\"shift\":\"shift2\",\"multiplier\":1,\"harga\":110000},{\"shift\":\"shift3\",\"multiplier\":1,\"harga\":120000}]', '2025-11-21 07:33:11'),
(33, 42, '2025-11-21', 4, '', 210000, '[{\"shift\":\"1\",\"harga\":100000,\"multiplier\":1},{\"shift\":\"2\",\"harga\":110000,\"multiplier\":1}]', '2025-11-21 07:51:13');

-- --------------------------------------------------------

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- Table structure for table `tukang_nws`
--

CREATE TABLE `tukang_nws` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama_tukang` varchar(50) DEFAULT NULL,
  `jenis_kelamin` varchar(32) NOT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `tgl_masuk` date NOT NULL,
<<<<<<< HEAD
  `status` varchar(32) NOT NULL
=======
  `status` varchar(32) NOT NULL,
  `harga_shift_1` decimal(10,0) DEFAULT NULL,
  `harga_shift_2` decimal(10,0) DEFAULT NULL,
  `harga_shift_3` decimal(10,0) DEFAULT NULL
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tukang_nws`
--

<<<<<<< HEAD
INSERT INTO `tukang_nws` (`id`, `nik`, `nama_tukang`, `jenis_kelamin`, `id_jabatan`, `tgl_masuk`, `status`) VALUES
(10, '3318161009780001', 'Sukamto', 'Laki-laki', 36, '2024-01-24', 'Tetap'),
(11, '3313102003920002', 'Sukino', 'Laki-laki', 37, '2024-01-24', 'Tetap'),
(12, '3328082507800003', 'Chaeroni', 'Laki-laki', 39, '2024-01-24', 'Tetap'),
(13, '3320092412830002', 'Minto', 'Laki-laki', 38, '2024-01-24', 'Tetap'),
(14, '3320090408060003', 'Fendi Zulianto', 'Laki-laki', 40, '2024-01-24', 'Tetap'),
(15, '1508061104990001', 'Dicky Adi Pratama', 'Laki-laki', 40, '2024-01-24', 'Tetap'),
(16, '3315101712920002', 'Rinto Softian Adisaputro', 'Laki-laki', 38, '2024-01-24', 'Tetap'),
(17, '3320140507960001', 'Muhamad Muinuddin', 'Laki-laki', 40, '2025-06-16', 'Tetap'),
(18, '3316122512800002', 'Warsiman', 'Laki-laki', 41, '2024-06-14', 'Tidak Tetap');
=======
INSERT INTO `tukang_nws` (`id`, `nik`, `nama_tukang`, `jenis_kelamin`, `id_jabatan`, `tgl_masuk`, `status`, `harga_shift_1`, `harga_shift_2`, `harga_shift_3`) VALUES
(35, '3315101712920002', 'Rinto Softian Adisaputro', 'Laki-laki', NULL, '2023-08-08', 'Aktif', 100000, 110000, 120000),
(36, '1508061104990001', 'Dicky Adi Pratama', 'Laki-laki', NULL, '2023-11-15', 'Aktif', 100000, 110000, 120000),
(37, '3316122512800002', 'Wasiman', 'Laki-laki', NULL, '2023-12-03', 'Aktif', 100000, 110000, 120000),
(39, '3403030606680002', 'Muslih', 'Laki-laki', NULL, '2025-11-17', 'Aktif', 100000, 110000, 120000),
(40, '3320092412830002', 'Minto', 'Laki-laki', NULL, '2023-03-13', 'Aktif', 100000, 130000, 150000),
(41, '3318181904860001', 'Anton Sujarwo', 'Laki-laki', NULL, '2025-10-05', 'Aktif', 100000, 130000, 150000),
(42, '3320090408060003', 'Fendi Zulianto', 'Laki-laki', NULL, '2023-11-10', 'Aktif', 100000, 110000, 120000),
(43, '3320140507960001', 'Muhammad Muinuddin', 'Laki-laki', NULL, '2025-03-19', 'Aktif', 100000, 110000, 120000),
(44, '3328082507800003', 'Chaeroni', 'Laki-laki', NULL, '2023-01-14', 'Aktif', 100000, 110000, 120000),
(45, '3318161009780001', 'Sukamto', 'Laki-laki', NULL, '2023-01-24', 'Aktif', 130000, 150000, 170000);
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff

--
-- Indexes for dumped tables
--

--
<<<<<<< HEAD
=======
-- Indexes for table `absensi_karyawan`
--
ALTER TABLE `absensi_karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- Indexes for table `absensi_tukang`
--
ALTER TABLE `absensi_tukang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nik` (`nik`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idadmin`);

--
-- Indexes for table `gaji_tukang`
--
ALTER TABLE `gaji_tukang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_gaji_per_minggu` (`nik`,`bulan`,`tahun`,`minggu`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jabatan` (`jabatan`,`jenis`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
<<<<<<< HEAD
=======
-- Indexes for table `lembur_tkg`
--
ALTER TABLE `lembur_tkg`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tukang` (`id_tukang`);

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- Indexes for table `tukang_nws`
--
ALTER TABLE `tukang_nws`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
<<<<<<< HEAD
=======
-- AUTO_INCREMENT for table `absensi_karyawan`
--
ALTER TABLE `absensi_karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- AUTO_INCREMENT for table `absensi_tukang`
--
ALTER TABLE `absensi_tukang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `idadmin` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `gaji_tukang`
--
ALTER TABLE `gaji_tukang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
<<<<<<< HEAD
-- AUTO_INCREMENT for table `tukang_nws`
--
ALTER TABLE `tukang_nws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
=======
-- AUTO_INCREMENT for table `lembur_tkg`
--
ALTER TABLE `lembur_tkg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tukang_nws`
--
ALTER TABLE `tukang_nws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff

--
-- Constraints for dumped tables
--

--
<<<<<<< HEAD
=======
-- Constraints for table `absensi_karyawan`
--
ALTER TABLE `absensi_karyawan`
  ADD CONSTRAINT `absensi_karyawan_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`);

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- Constraints for table `absensi_tukang`
--
ALTER TABLE `absensi_tukang`
  ADD CONSTRAINT `absensi_tukang_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `tukang_nws` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `absensi_tukang_ibfk_2` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `gaji_tukang`
--
ALTER TABLE `gaji_tukang`
  ADD CONSTRAINT `gaji_tukang_ibfk_1` FOREIGN KEY (`nik`) REFERENCES `tukang_nws` (`nik`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gaji_tukang_ibfk_2` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD CONSTRAINT `karyawan_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
<<<<<<< HEAD
=======
-- Constraints for table `lembur_tkg`
--
ALTER TABLE `lembur_tkg`
  ADD CONSTRAINT `lembur_tkg_ibfk_1` FOREIGN KEY (`id_tukang`) REFERENCES `tukang_nws` (`id`) ON DELETE CASCADE;

--
>>>>>>> 66cdde3f9703df955595e0520c06a9bfdf262cff
-- Constraints for table `tukang_nws`
--
ALTER TABLE `tukang_nws`
  ADD CONSTRAINT `tukang_nws_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
