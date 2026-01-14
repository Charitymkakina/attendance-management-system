-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 20, 2024 at 11:45 PM
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
-- Database: `BIDII_school`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `status` enum('Present','Absent') DEFAULT 'Absent',
  `attendance_date` datetime(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `lesson_id`, `status`, `attendance_date`) VALUES
(1, 2000978, 2, 'Present', '2024-11-05 08:01:16.000000'),
(2, 2000978, 1, 'Absent', '2024-11-06 00:01:00.000000'),
(1810, 2000978, 5, 'Present', '2024-11-08 08:43:51.000000'),
(1825, 2000978, 1, 'Absent', '2024-11-07 00:00:00.000000'),
(1826, 2000978, 1, 'Absent', '2024-11-08 00:00:00.000000'),
(1827, 2000978, 1, 'Absent', '2024-11-09 00:00:00.000000'),
(1828, 2000978, 1, 'Absent', '2024-11-10 00:00:00.000000'),
(1829, 2000978, 1, 'Present', '2024-11-11 08:59:09.000000'),
(1830, 2000978, 2, 'Absent', '2024-11-06 00:00:00.000000'),
(1831, 2000978, 2, 'Absent', '2024-11-07 00:00:00.000000'),
(1832, 2000978, 2, 'Absent', '2024-11-08 00:00:00.000000'),
(1833, 2000978, 2, 'Absent', '2024-11-09 00:00:00.000000'),
(1834, 2000978, 2, 'Absent', '2024-11-10 00:00:00.000000'),
(1835, 2000978, 2, 'Present', '2024-11-11 08:59:32.000000'),
(1836, 2000978, 5, 'Absent', '2024-11-09 00:00:00.000000'),
(1837, 2000978, 5, 'Absent', '2024-11-10 00:00:00.000000'),
(1838, 2000978, 5, 'Present', '2024-11-11 09:00:06.000000'),
(1839, 2000978, 5, 'Present', '2024-11-12 13:18:17.000000'),
(1840, 2000978, 1, 'Present', '2024-11-12 13:18:32.000000'),
(1841, 2000978, 2, 'Present', '2024-11-12 13:18:38.000000'),
(1842, 2000978, 3, 'Present', '2024-11-21 00:22:37.000000'),
(1843, 2000978, 5, 'Absent', '2024-11-13 00:00:00.000000'),
(1844, 2000978, 5, 'Absent', '2024-11-14 00:00:00.000000'),
(1845, 2000978, 5, 'Absent', '2024-11-15 00:00:00.000000'),
(1846, 2000978, 5, 'Absent', '2024-11-16 00:00:00.000000'),
(1847, 2000978, 5, 'Absent', '2024-11-17 00:00:00.000000'),
(1848, 2000978, 5, 'Absent', '2024-11-18 00:00:00.000000'),
(1849, 2000978, 5, 'Absent', '2024-11-19 00:00:00.000000'),
(1850, 2000978, 5, 'Present', '2024-11-21 00:22:55.000000'),
(1851, 2000978, 1, 'Absent', '2024-11-13 00:00:00.000000'),
(1852, 2000978, 1, 'Absent', '2024-11-14 00:00:00.000000'),
(1853, 2000978, 1, 'Absent', '2024-11-15 00:00:00.000000'),
(1854, 2000978, 1, 'Absent', '2024-11-16 00:00:00.000000'),
(1855, 2000978, 1, 'Absent', '2024-11-17 00:00:00.000000'),
(1856, 2000978, 1, 'Absent', '2024-11-18 00:00:00.000000'),
(1857, 2000978, 1, 'Absent', '2024-11-19 00:00:00.000000'),
(1858, 2000978, 1, 'Present', '2024-11-21 00:23:18.000000'),
(1859, 2000978, 2, 'Absent', '2024-11-13 00:00:00.000000'),
(1860, 2000978, 2, 'Absent', '2024-11-14 00:00:00.000000'),
(1861, 2000978, 2, 'Absent', '2024-11-15 00:00:00.000000'),
(1862, 2000978, 2, 'Absent', '2024-11-16 00:00:00.000000'),
(1863, 2000978, 2, 'Absent', '2024-11-17 00:00:00.000000'),
(1864, 2000978, 2, 'Absent', '2024-11-18 00:00:00.000000'),
(1865, 2000978, 2, 'Absent', '2024-11-19 00:00:00.000000'),
(1866, 2000978, 2, 'Present', '2024-11-21 00:23:25.000000'),
(1868, 2000978, 12, 'Present', '2024-11-21 01:34:15.000000');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `lesson_id`, `enrollment_date`) VALUES
(1, 2000978, 1, '2024-11-09 16:01:21'),
(3, 2000978, 2, '2024-11-09 16:27:27'),
(4, 2000978, 5, '2024-11-11 05:43:35'),
(5, 2000978, 3, '2024-11-20 21:22:20'),
(6, 2000978, 6, '2024-11-20 21:49:44'),
(12, 2000978, 12, '2024-11-20 22:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `enrollment_key` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `lecturer_id`, `title`, `description`, `enrollment_key`, `created_at`, `updated_at`) VALUES
(1, 9, 'lesson1', 'this is lesson 1', '1234', '2024-11-08 15:43:22', '2024-11-08 15:43:22'),
(2, 9, 'lesson2', 'this is lesson 2', '12345', '2024-11-08 15:43:22', '2024-11-08 15:43:22'),
(3, 9, 'lesson3', 'this is lesson 3', '1234567', '2024-11-08 15:43:22', '2024-11-08 15:43:22'),
(5, 9, 'Cyber security', 'BISF 2022, BSD 2106, BAC 2104.dhshhdhdhdhhdhdhhdhdhhdhdhhdhdhdbhdhdhhdhdhudueudhehdhdhegddhehwhud', 'CYBSEC', '2024-11-10 16:10:34', '2024-11-10 17:18:52'),
(6, 9, 'Networking', 'Bisf3222', '22222', '2024-11-20 21:48:06', '2024-11-20 21:48:06'),
(12, 9, 'ddtftddrdtffd', 'dfghgygtyttyt', '1111', '2024-11-20 22:31:36', '2024-11-20 22:31:36');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `registration_number` varchar(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `course` varchar(255) DEFAULT NULL,
  `phone` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`registration_number`, `username`, `email`, `password`, `course`, `phone`) VALUES
('2000978', 'Ann james', 'ann@gmail.com', '$2y$10$BMkI0Pb1wj0k9kNEG.s6tOelMxkmp3lp1UdHBDX.1hiZliB5aUrRq', 'Bsd', 734567877);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('lecturer','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`, `updated_at`) VALUES
(9, 'Jane Smith', '$2y$10$z2usfhSVojzk0OQ5kJY0HebFAK.p.QV49OZxNj/WbmsOestwnCOwy', 'jane@gmail.com', 'lecturer', '2024-11-08 15:33:34', '2024-11-09 13:03:49'),
(10, 'System Admin', '$2y$10$.vqDPL1pW6HGh4RTrhd6u.tg0JYWM/OTqGAV2OoBGgem/laX3dh7y', 'admin@gmail.com', 'admin', '2024-11-11 20:29:47', '2024-11-11 20:29:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`) USING BTREE,
  ADD KEY `attendance_ibfk_2` (`lesson_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `enrollments_ibfk_1` (`lesson_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`registration_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1869;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
