-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2025 at 11:03 AM
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
-- Database: `drx`
--
CREATE DATABASE IF NOT EXISTS `drx` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `drx`;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `apid` int(11) NOT NULL,
  `patient_Name` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `patient_Num` int(15) NOT NULL,
  `typ` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `appointmentDate` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `appointmentTime` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `postingDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `paid` int(50) DEFAULT NULL,
  `over_paid` int(50) NOT NULL,
  `statue` tinyint(1) NOT NULL DEFAULT 0,
  `commnet` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `adrs` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modality_worklist`
--

CREATE TABLE `modality_worklist` (
  `id` int(11) NOT NULL,
  `PatientID` varchar(64) NOT NULL,
  `PatientName` varchar(255) NOT NULL,
  `Modality` varchar(16) NOT NULL,
  `ScheduledDate` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `ScheduledTime` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `AccessionNumber` varchar(64) DEFAULT NULL,
  `StudyInstanceUID` varchar(128) NOT NULL,
  `RequestedProcedureDescription` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `PatientBirthDate` varchar(20) DEFAULT NULL,
  `ScheduledProcedureStepStartDate` date DEFAULT NULL,
  `ScheduledProcedureStepStartTime` time DEFAULT NULL,
  `StudyDescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modality_worklist`
--

(6, '', 'mo3', 'X-ray', '0000-00-00', '00:00:00', 'ACC68812E85F337E', '', NULL, '2025-07-23 18:48:37', '3', '2025-07-23', '20:46:20', 'asd'),
(7, '', 'Zeyad Yaser', 'X-ray', NULL, NULL, 'ACC68812F350CDEE', '', NULL, '2025-07-23 18:51:33', '3', '2025-07-23', '20:48:39', ''),
(8, '', 'Zeyad', 'X-ray', NULL, NULL, 'ACC68887CFFB7871', '', NULL, '2025-07-29 07:49:19', '3', '2025-07-29', '09:48:59', '588'),
(9, '', 'Zeyad', 'X-ray', NULL, NULL, 'ACC689528970F53E', '', NULL, '2025-08-07 22:28:39', '3', '2025-08-08', '00:28:28', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`apid`);

--
-- Indexes for table `modality_worklist`
--
ALTER TABLE `modality_worklist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `apid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modality_worklist`
--
ALTER TABLE `modality_worklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
