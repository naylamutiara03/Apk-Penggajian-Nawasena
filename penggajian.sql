-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 11:32 AM
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
(61, 'admin', '$2y$10$RpDFLG7vjcQ9AVvrX1CYcupZUmhpY/.9zeggjiYd1iPM8FUAdlRBS', 'nawasena1', 'admin'),
(62, 'superadmin', '$2y$10$Z94NTXy1OlQ5PBwLRAiVkOFLMCHoAUDbVqMw3phTtCAlSjdXhyuAS', 'super admin123', 'superadmin'),
(64, 'mutiara', '$2y$10$rbURwjecaHccCfY7Xe6PvuOUSwHdFQ50oiK2XfJ6Ei1AYin20u8tC', 'nayla1', 'admin');

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
(35, 'CEO', 15000000, 0, 'karyawan', 15000000),
(36, 'Kepala Tukang', 170000, 0, 'tukang', 170000),
(37, 'Kepala Tukang 2', 160000, 0, 'tukang', 160000),
(38, 'Tukang 145', 145000, 0, 'tukang', 145000),
(39, 'Tukang 140', 140000, 0, 'tukang', 140000),
(40, 'Tukang 130', 130000, 0, 'tukang', 130000),
(41, 'Freelance 140', 140000, 0, 'tukang', 140000),
(42, 'Freelance 120', 120000, 0, 'tukang', 120000);

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
-- Table structure for table `tukang_nws`
--

CREATE TABLE `tukang_nws` (
  `id` int(11) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama_tukang` varchar(50) DEFAULT NULL,
  `jenis_kelamin` varchar(32) NOT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `tgl_masuk` date NOT NULL,
  `status` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tukang_nws`
--

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

--
-- Indexes for dumped tables
--

--
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
-- AUTO_INCREMENT for table `tukang_nws`
--
ALTER TABLE `tukang_nws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
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
-- Constraints for table `tukang_nws`
--
ALTER TABLE `tukang_nws`
  ADD CONSTRAINT `tukang_nws_ibfk_1` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
