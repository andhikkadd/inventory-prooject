-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2025 at 05:04 AM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id` varchar(6) NOT NULL,
  `id_barang` varchar(5) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang_keluar`
--

INSERT INTO `barang_keluar` (`id`, `id_barang`, `jumlah`, `tanggal`, `user_id`) VALUES
('BK274', 'I054', 50, '2025-06-13 00:00:00', '00687'),
('BK544', 'I508', 40, '2025-06-13 12:02:30', '00687'),
('BK814', 'I742', 50, '2025-06-13 00:00:00', '00687');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` varchar(6) NOT NULL,
  `id_barang` varchar(5) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id`, `id_barang`, `jumlah`, `tanggal`, `user_id`) VALUES
('BM503', 'I508', 30, '2025-06-13 11:58:45', '00687'),
('BM773', 'I508', 30, '2025-06-13 11:56:30', '00687'),
('BM792', 'I742', 30, '2025-06-13 00:00:00', '00687'),
('BM839', 'I054', 30, '2025-06-13 00:00:00', '00687');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` varchar(5) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `distributor` text DEFAULT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `harga` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `nama_barang`, `distributor`, `jumlah`, `harga`) VALUES
('I054', 'Kecap Bango', 'PT Tungtungtung', 50, 29000.00),
('I508', 'Sari Roti Cokelat', 'PT Sinar Jawa', 100, 24000.00),
('I742', 'Mie Sedap', 'PT Indopud', 100, 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL,
  `user_id` varchar(5) NOT NULL,
  `id_barang` varchar(10) NOT NULL,
  `action` text NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `id_barang`, `action`, `tanggal`) VALUES
(1, '00528', '', 'Menambah barang baru ID: 0', '2025-06-13 11:47:57'),
(2, '00528', '', 'Menambah barang baru ID: 0', '2025-06-13 11:51:31'),
(3, '00528', '', 'Menambah barang baru ID: 0', '2025-06-13 11:52:11'),
(4, '00528', '', 'Menambah barang baru ID: 0', '2025-06-13 11:52:39'),
(5, '00528', '', 'Mengubah data barang ID: I664', '2025-06-13 11:53:01'),
(7, '00528', '', 'Menghapus barang dengan ID: 0', '2025-06-13 11:55:22'),
(8, '00528', '', 'Menghapus barang dengan ID: 0', '2025-06-13 11:55:30'),
(9, '00528', '', 'Menghapus barang dengan ID: I664', '2025-06-13 11:55:45'),
(10, '00687', 'I508', 'Menambah barang masuk (ID Barang: I508, Jumlah: 30, Tanggal: 2025-06-13 11:58:45)', '2025-06-13 11:58:45'),
(11, '00687', 'I054', 'Menambah barang masuk (ID Barang: I054, Jumlah: 40, Tanggal: 2025-06-13 11:59:09)', '2025-06-13 11:59:09'),
(12, '00687', 'I054', 'Mengedit barang masuk (ID: BM839, ID Barang: I054, Jumlah: 30, Tanggal: 2025-06-13)', '2025-06-13 11:59:22'),
(13, '00687', 'I742', 'Menambah barang masuk (ID Barang: I742, Jumlah: 40, Tanggal: 2025-06-13 11:59:54)', '2025-06-13 11:59:54'),
(14, '00687', 'I742', 'Mengedit barang masuk (ID: BM792, ID Barang: I742, Jumlah: 30, Tanggal: 2025-06-13)', '2025-06-13 12:00:12'),
(15, '00687', 'I742', 'Mengeluarkan barang keluar (ID Barang: I742, Jumlah: 10, Tanggal: 2025-06-13 12:02:07)', '2025-06-13 12:02:07'),
(16, '00687', 'I508', 'Mengeluarkan barang keluar (ID Barang: I508, Jumlah: 40, Tanggal: 2025-06-13 12:02:30)', '2025-06-13 12:02:30'),
(17, '00687', 'I742', 'Mengedit barang keluar (ID: BK814, ID Barang: I742, Jumlah: 50, Tanggal: 2025-06-13)', '2025-06-13 12:02:48'),
(18, '00687', 'I054', 'Mengeluarkan barang keluar (ID Barang: I054, Jumlah: 100, Tanggal: 2025-06-13 12:03:28)', '2025-06-13 12:03:28'),
(19, '00687', 'I054', 'Mengedit barang keluar (ID: BK274, ID Barang: I054, Jumlah: 50, Tanggal: 2025-06-13)', '2025-06-13 12:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(5) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
('00528', 'admin', '$2y$10$ZHkInCT7odwOnpLDe6djSuQZbjH6jN6Dc0nUhopsDxH8O54YFssAO', 'admin'),
('00687', 'fufufa', '$2y$10$dBMpVSvtJyrT4fvNwM09SeQlvUm393IrQQWcKxQGOWAVDPS7MU6wO', 'petugas'),
('00759', 'petugas', '$2y$10$flqaz5EkmV2hKAZNV7UKcOdpvWJfHtwCindsR1DdnL30dVxKD.v/e', 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `barang_keluar_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `barang_masuk_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
