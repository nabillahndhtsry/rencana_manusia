-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 07:09 PM
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
-- Database: `uas`
--

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE `note` (
  `id_note` int(255) NOT NULL,
  `judul_note` text NOT NULL,
  `deskripsi_note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `note`
--

INSERT INTO `note` (`id_note`, `judul_note`, `deskripsi_note`) VALUES
(5, 'uas web', 'uas s&k:\r\na. Ada Bootstrap (HTML-CSS-Javascript)\r\nb. Ada PHP (Semua Materi Saya)\r\nc. Tabel di database minimal 5\r\nd. Ada Login (session)\r\ne. Minimal 8 halaman\r\nf. Ada Searching di CRUD\r\ng. Hosting Web Kalian \r\n\r\nyang dikumpulkan:\r\na) File PHP (coding) Khusus Praktek\r\nb) MYSQL (hasil Export) Khusus Praktek\r\nc) PDF hasil dokumentasi Khusus Teori dan berikan alasan memilih tema web tersebut');

-- --------------------------------------------------------

--
-- Table structure for table `target`
--

CREATE TABLE `target` (
  `id_target` int(255) NOT NULL,
  `target` text NOT NULL,
  `deskripsi_target` text NOT NULL,
  `tanggal_target` date NOT NULL,
  `status_target` enum('pending','in_progress','done','','') DEFAULT 'pending',
  `fokus_target` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `target`
--

INSERT INTO `target` (`id_target`, `target`, `deskripsi_target`, `tanggal_target`, `status_target`, `fokus_target`) VALUES
(1, 'Mendapatkan nilai A', 'MEmpertahankan IPK', '2025-07-11', 'in_progress', 1);

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id_task` int(255) NOT NULL,
  `judul` text NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal_dl` date NOT NULL,
  `waktu_dl` time NOT NULL,
  `status` enum('pending','done','','') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id_task`, `judul`, `deskripsi`, `tanggal_dl`, `waktu_dl`, `status`) VALUES
(2, 'Project Web', 'https://classroom.google.com/c/NzM4NDE2NjAxNjQ2/m/Nzg1NjQ4OTgyNzAw/details', '2025-07-06', '08:00:00', 'done'),
(5, 'poster jepang', 'membuat poster dan mencetaknya', '2025-07-02', '12:27:00', 'done'),
(6, 'Project Visual', 'Mengumpulkan ', '2025-07-07', '12:12:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `todo`
--

CREATE TABLE `todo` (
  `id_todo` int(255) NOT NULL,
  `hari_todo` enum('senin','selasa','rabu','kamis','jumat','sabtu','minggu') NOT NULL,
  `deskripsi_todo` varchar(255) NOT NULL,
  `status_todo` enum('pending','done') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todo`
--

INSERT INTO `todo` (`id_todo`, `hari_todo`, `deskripsi_todo`, `status_todo`) VALUES
(7, 'senin', 'mengumpulkan tugas', 'done'),
(8, 'jumat', 'uas visual', 'pending'),
(9, 'senin', 'uas web', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_profil` varchar(255) NOT NULL DEFAULT '''user-2.jpg'''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_lengkap`, `tanggal_daftar`, `foto_profil`) VALUES
(1, 'nabillah', '827ccb0eea8a706c4c34a16891f84e7b', 'Nabillah Indah Tsuraya', '2025-06-29 03:04:34', 'profile_1_1751821218.jpg'),
(2, 'fuzzy', '827ccb0eea8a706c4c34a16891f84e7b', 'fuzzy logic', '2025-07-05 22:18:42', '\'user-2.jpg\'');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`id_note`);

--
-- Indexes for table `target`
--
ALTER TABLE `target`
  ADD PRIMARY KEY (`id_target`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id_task`);

--
-- Indexes for table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`id_todo`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `note`
--
ALTER TABLE `note`
  MODIFY `id_note` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `target`
--
ALTER TABLE `target`
  MODIFY `id_target` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id_task` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `todo`
--
ALTER TABLE `todo`
  MODIFY `id_todo` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
