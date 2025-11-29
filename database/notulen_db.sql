-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 29, 2025 at 07:25 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `notulen_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `kontak`
--

CREATE TABLE `kontak` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notulis`
--

CREATE TABLE `notulis` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `divisi` varchar(255) NOT NULL,
  `peran` varchar(255) NOT NULL,
  `bergabung_sejak` date NOT NULL,
  `foto_profile` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notulis`
--

INSERT INTO `notulis` (`id`, `nama_lengkap`, `email`, `divisi`, `peran`, `bergabung_sejak`, `foto_profile`) VALUES
(1, 'Umiarti N', 'umiarti.ningsih3@gmail.com', 'Programmer', 'Notulis', '2025-11-15', 'uploads/profile_pics/user_1_1764444213.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `rapat`
--

CREATE TABLE `rapat` (
  `id` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `tempat` varchar(150) NOT NULL,
  `penyelenggara` varchar(150) NOT NULL,
  `notulis` varchar(150) NOT NULL,
  `peserta` text NOT NULL,
  `catatan` text NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rapat`
--

INSERT INTO `rapat` (`id`, `judul`, `tanggal`, `waktu`, `tempat`, `penyelenggara`, `notulis`, `peserta`, `catatan`, `status`) VALUES
(8, 'Weekly Inventory', '2025-10-27', '22:48:00', 'Ruang Diversity', 'Planning', 'Umiarti Ningsih', 'Robbi, Nafilah, Lolly', 'lanjut investigasi', 'Selesai'),
(9, 'Rapat Evaluasi Bulanan', '2025-11-01', '10:20:00', 'Learning Center', 'Production Hydro', 'Umiarti Ningsih', 'rana, askia, hernika, ana, ani, reika, junisa', 'tidak ada', 'Belum Selesai'),
(12, 'Penutupan departemen aocup', '2025-11-30', '16:15:00', 'Learning Center', 'Production Aocup', 'Nafilah Thahirah', 'Umiarti Ningsih, Robbi Akraman, Lolly Carolina', 'resmi tutup', 'Selesai');

-- --------------------------------------------------------

--
-- Table structure for table `rapat_detail`
--

CREATE TABLE `rapat_detail` (
  `id_detail` int NOT NULL,
  `id_rapat` int NOT NULL,
  `topik` text,
  `pembahasan` text,
  `tindak_lanjut` text,
  `pic` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rapat_detail`
--

INSERT INTO `rapat_detail` (`id_detail`, `id_rapat`, `topik`, `pembahasan`, `tindak_lanjut`, `pic`) VALUES
(10, 8, 'Inventory Material', 'melakukan inventory manual, karena telah terjadi mix device di warehouse', 'CAPPA', 'QA'),
(11, 9, 'Evaluasi manpower', 'plan production yang turun di akhir tahun 2025. menyebabkan moving manporwer ke departemen lain yang terus berlanjut.', 'Multiskill', 'Trainner'),
(17, 12, 'asacfsev', 'v sevgrt brfc n ntrjrt m', 'hre5hjswwsbh', 'fqt3t'),
(18, 12, 'sfwgwe', 'bh5hu5', 'hju7kmt', 'et4sd'),
(19, 12, 'wwrhrt', 'jk8o', 'erw35', 'fedgb fn');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notulis`
--
ALTER TABLE `notulis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique` (`email`);

--
-- Indexes for table `rapat`
--
ALTER TABLE `rapat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rapat_detail`
--
ALTER TABLE `rapat_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `index` (`id_rapat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notulis`
--
ALTER TABLE `notulis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rapat`
--
ALTER TABLE `rapat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `rapat_detail`
--
ALTER TABLE `rapat_detail`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rapat_detail`
--
ALTER TABLE `rapat_detail`
  ADD CONSTRAINT `fk_rapat` FOREIGN KEY (`id_rapat`) REFERENCES `rapat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rapat_detail_ibfk_1` FOREIGN KEY (`id_rapat`) REFERENCES `rapat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
