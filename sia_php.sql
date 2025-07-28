-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 12:11 PM
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
-- Database: `sia_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(50) DEFAULT NULL COMMENT 'Nomor Induk Pegawai, can be NULL if not applicable',
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` tinyint(1) DEFAULT NULL COMMENT '0 for Perempuan (Female), 1 for Laki-laki (Male)',
  `no_telpon` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `nip`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `no_telpon`) VALUES
(1, '198001012005011001', 'Udin Perkasa', '1980-01-01', 1, '081234567890'),
(2, '198505052008012002', 'Tono Jaya', '1985-05-05', 0, '081234567891'),
(3, NULL, 'Budi Sejahtera', '1990-10-10', 0, '081234567892');

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran`
--

CREATE TABLE `kehadiran` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_tahun_ajaran` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_status`
--

CREATE TABLE `kehadiran_status` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL COMMENT 'e.g., Hadir, Sakit, Izin, Alpha'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kehadiran_status`
--

INSERT INTO `kehadiran_status` (`id`, `nama`) VALUES
(4, 'Alpha'),
(1, 'Hadir'),
(3, 'Izin'),
(2, 'Sakit');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `id_tahun_ajaran` int(11) NOT NULL,
  `id_tingkat` int(11) NOT NULL,
  `id_guru_wali` int(11) DEFAULT NULL COMMENT 'Homeroom teacher, a teacher can only be a homeroom teacher for one class',
  `nama` varchar(100) NOT NULL COMMENT 'e.g., 10-A, 11-IPA-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `id_tahun_ajaran`, `id_tingkat`, `id_guru_wali`, `nama`) VALUES
(1, 1, 1, 1, '10-A'),
(2, 1, 1, 2, '10-B'),
(3, 3, 2, 3, '11-IPA-1');

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int(11) NOT NULL,
  `id_tingkat` int(11) NOT NULL,
  `id_guru` int(11) DEFAULT NULL COMMENT 'Teacher teaching this subject at this grade level, can be NULL if unassigned',
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `id_tingkat`, `id_guru`, `nama`) VALUES
(1, 1, 1, 'Matematika Wajib'),
(2, 1, 2, 'Bahasa Indonesia'),
(3, 1, 3, 'Bahasa Inggris'),
(4, 2, 1, 'Fisika'),
(5, 2, 2, 'Kimia'),
(6, 3, 3, 'Biologi');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_mata_pelajaran` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_tahun_ajaran` int(11) NOT NULL,
  `id_jenis_nilai` int(11) NOT NULL,
  `nilai` float NOT NULL,
  `tanggal_penilaian` date NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_jenis`
--

CREATE TABLE `nilai_jenis` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL COMMENT 'e.g., Tugas Harian, Ulangan Harian, UTS, UAS, Praktikum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_jenis`
--

INSERT INTO `nilai_jenis` (`id`, `nama`) VALUES
(5, 'Praktikum'),
(1, 'Tugas Harian'),
(4, 'UAS'),
(2, 'Ulangan Harian'),
(3, 'UTS');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_spp`
--

CREATE TABLE `pembayaran_spp` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_tahun_ajaran` int(11) NOT NULL,
  `bulan` varchar(20) NOT NULL COMMENT 'e.g., Januari, Februari. Consider using DATE type for first day of month if more precision/sorting is needed.',
  `tanggal_bayar` date NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(50) DEFAULT NULL COMMENT 'Nomor Induk Siswa',
  `nisn` varchar(50) NOT NULL COMMENT 'Nomor Induk Siswa Nasional',
  `nama` varchar(255) NOT NULL,
  `no_kk` varchar(50) DEFAULT NULL COMMENT 'Nomor Kartu Keluarga',
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` tinyint(1) DEFAULT NULL COMMENT '0 for Perempuan (Female), 1 for Laki-laki (Male)',
  `nama_ayah` varchar(255) DEFAULT NULL,
  `nama_ibu` varchar(255) DEFAULT NULL,
  `nik_ayah` varchar(50) DEFAULT NULL,
  `nik_ibu` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nis`, `nisn`, `nama`, `no_kk`, `tanggal_lahir`, `jenis_kelamin`, `nama_ayah`, `nama_ibu`, `nik_ayah`, `nik_ibu`, `alamat`) VALUES
(1, '1001', '0012345678', 'Joko Pintar', '3201010101000001', '2007-08-17', 1, 'Bambang Perkasa', 'Udin Lestari', '3201010101700001', '3201010101750002', 'Jl. Merdeka No. 1'),
(2, '1002', '0023456789', 'Bambang Ceria', '3201010101000002', '2007-05-20', 0, 'Udin Susilo', 'Tono Marlina', '3201010101680003', '3201010101720004', 'Jl. Pahlawan No. 10'),
(3, '1003', '0034567890', 'Udin Bahagia', '3201010101000003', '2006-11-10', 0, 'Tono Irawan', 'Budi Kartika', '3201010101650005', '3201010101690006', 'Jl. Kemerdekaan No. 5');

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL COMMENT 'e.g., 2023/2024 Ganjil',
  `tahun_mulai` year(4) NOT NULL,
  `tahun_selesai` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`id`, `nama`, `tahun_mulai`, `tahun_selesai`) VALUES
(1, '2023/2024 Ganjil', '2023', '2024'),
(2, '2023/2024 Genap', '2023', '2024'),
(3, '2024/2025 Ganjil', '2024', '2025');

-- --------------------------------------------------------

--
-- Table structure for table `tingkat`
--

CREATE TABLE `tingkat` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL COMMENT 'e.g., Kelas 10, Kelas 11, Kelas 12'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tingkat`
--

INSERT INTO `tingkat` (`id`, `nama`) VALUES
(1, 'Kelas 10'),
(2, 'Kelas 11'),
(3, 'Kelas 12');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Passwords should be hashed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'username', 'password');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indexes for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_tahun_ajaran` (`id_tahun_ajaran`),
  ADD KEY `id_status` (`id_status`);

--
-- Indexes for table `kehadiran_status`
--
ALTER TABLE `kehadiran_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_guru_wali` (`id_guru_wali`),
  ADD KEY `id_tahun_ajaran` (`id_tahun_ajaran`),
  ADD KEY `id_tingkat` (`id_tingkat`);

--
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tingkat` (`id_tingkat`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_mata_pelajaran` (`id_mata_pelajaran`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_tahun_ajaran` (`id_tahun_ajaran`),
  ADD KEY `id_jenis_nilai` (`id_jenis_nilai`);

--
-- Indexes for table `nilai_jenis`
--
ALTER TABLE `nilai_jenis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `pembayaran_spp`
--
ALTER TABLE `pembayaran_spp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_tahun_ajaran` (`id_tahun_ajaran`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nisn` (`nisn`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indexes for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tingkat`
--
ALTER TABLE `tingkat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kehadiran`
--
ALTER TABLE `kehadiran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kehadiran_status`
--
ALTER TABLE `kehadiran_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nilai_jenis`
--
ALTER TABLE `nilai_jenis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pembayaran_spp`
--
ALTER TABLE `pembayaran_spp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tingkat`
--
ALTER TABLE `tingkat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD CONSTRAINT `kehadiran_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_3` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_4` FOREIGN KEY (`id_status`) REFERENCES `kehadiran_status` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_2` FOREIGN KEY (`id_tingkat`) REFERENCES `tingkat` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_ibfk_3` FOREIGN KEY (`id_guru_wali`) REFERENCES `guru` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD CONSTRAINT `mata_pelajaran_ibfk_1` FOREIGN KEY (`id_tingkat`) REFERENCES `tingkat` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mata_pelajaran_ibfk_2` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`id_mata_pelajaran`) REFERENCES `mata_pelajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_3` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_4` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_5` FOREIGN KEY (`id_jenis_nilai`) REFERENCES `nilai_jenis` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran_spp`
--
ALTER TABLE `pembayaran_spp`
  ADD CONSTRAINT `pembayaran_spp_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_spp_ibfk_2` FOREIGN KEY (`id_tahun_ajaran`) REFERENCES `tahun_ajaran` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
