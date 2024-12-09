-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 08:25 AM
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
-- Database: `clinic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `check_up`
--

CREATE TABLE `check_up` (
  `u_id` int(50) NOT NULL,
  `c_pd` date NOT NULL,
  `c_pt` varchar(50) NOT NULL,
  `c_rc` varchar(50) NOT NULL,
  `c_urgent` enum('urgent','unurgent') NOT NULL,
  `c_lc` date NOT NULL,
  `c_nc` date NOT NULL,
  `c_id` int(11) NOT NULL,
  `c_status` enum('completed','pending') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_up`
--

INSERT INTO `check_up` (`u_id`, `c_pd`, `c_pt`, `c_rc`, `c_urgent`, `c_lc`, `c_nc`, `c_id`, `c_status`) VALUES
(34, '2024-12-12', '03:0', 'a', 'unurgent', '2024-11-26', '0000-00-00', 4, 'pending'),
(34, '2024-12-12', '10:3', 'yawa', 'unurgent', '2024-11-26', '0000-00-00', 5, 'pending'),
(34, '2024-12-12', '11:0', '12', 'unurgent', '2024-11-26', '0000-00-00', 6, 'pending'),
(34, '2024-12-12', '01:0', 'a', 'unurgent', '2024-11-26', '0000-00-00', 7, 'pending'),
(34, '2024-12-12', '11:00AM', 'a', 'unurgent', '2024-11-26', '0000-00-00', 8, 'pending'),
(34, '2024-12-12', '1:00PM', 'asd', 'unurgent', '2024-11-26', '0000-00-00', 9, 'pending'),
(34, '2024-12-12', '10:30AM', 'dasd', 'unurgent', '2024-11-26', '0000-00-00', 10, 'pending'),
(32, '2024-12-12', '1:30PM', 'yawa naa koy sakit', 'unurgent', '2024-11-27', '0000-00-00', 11, 'completed'),
(32, '2024-12-12', '10:30AM', 'urget ni bai', 'urgent', '2024-11-27', '0000-00-00', 12, 'completed'),
(32, '2024-12-12', '10:30AM', 'urgent jud kaayo yawa oy animal', 'urgent', '2024-11-27', '0000-00-00', 13, 'completed'),
(32, '2024-12-12', '1:30PM', 'dili mani urgent yawa bago diay ni hehe\r\n', 'unurgent', '2024-11-27', '0000-00-00', 14, 'completed'),
(32, '2024-12-12', '10:30AM', 'kayasa urgent kayni do', 'urgent', '2024-11-28', '0000-00-00', 15, 'pending'),
(32, '2024-12-12', '1:00PM', 'yawa', 'urgent', '2024-11-28', '0000-00-00', 16, 'completed'),
(38, '2024-12-12', '10:30AM', 'urgetn', 'urgent', '2024-11-29', '0000-00-00', 17, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `u_id` int(11) NOT NULL,
  `u_fn` varchar(50) NOT NULL,
  `u_email` varchar(50) NOT NULL,
  `u_bt` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `u_password` varchar(150) NOT NULL,
  `u_type` enum('student','doctor') NOT NULL,
  `u_grade` varchar(50) NOT NULL,
  `u_hs` varchar(50) NOT NULL,
  `u_h` varchar(50) NOT NULL,
  `u_gender` enum('male','female','others') NOT NULL,
  `u_allergy` varchar(50) NOT NULL,
  `u_age` int(50) NOT NULL,
  `u_pc` varchar(50) NOT NULL,
  `u_sc` varchar(50) NOT NULL,
  `u_pcn` varchar(50) NOT NULL,
  `u_scn` varchar(50) NOT NULL,
  `u_image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`u_id`, `u_fn`, `u_email`, `u_bt`, `u_password`, `u_type`, `u_grade`, `u_hs`, `u_h`, `u_gender`, `u_allergy`, `u_age`, `u_pc`, `u_sc`, `u_pcn`, `u_scn`, `u_image`) VALUES
(32, 'My Full Name', 'a', 'A+', '$2y$10$o/i.1PfnivWNtLkZpqAM8O8.ud3Z8E4EbSUI/EVHiCbGNjIaAIr6e', 'student', 'College', 'Healthier', '1', 'female', 'updated nga allergy', 10, 'nanay ko', 'tatay ko', '09288487123123', '09585489478678', '/uploads/profiles/profile_32_1732700173.jfif'),
(33, 't', 't', 'AB-', '$2y$10$Rz62yow8w552pi1bJvER7.sNQYMET2Is2KTK7AShkqimGEETi52I6', 'student', 'N/A', 'Healthy', 'N/A', '', 'None', 0, '', '', '0', '0', ''),
(34, 'samot', 'samot', 'O+', '$2y$10$u9e1iGHJb2XzImhbjOIhk.ux5HIITiEzjOcXeeGA5nk7sPx51ReOK', 'doctor', 'N/A', 'Healthy', 'N/A', 'others', 'None', 0, '', '', '0', '0', '/uploads/profiles/profile_34_1732681389.jpg'),
(35, 'asd', 'sad', 'AB+', '$2y$10$aV7oxeTYntxvzhkHYfmcWeDXwu/V8MwI1dC3gQPMoA/MrfXB4r4dy', 'doctor', 'N/A', 'Healthy', 'N/A', 'others', 'None', 0, 'N/A', 'N/A', 'N/A', 'N/A', '/uploads/profiles/profile_35_1732678858.png'),
(36, '3333', '3333', 'B+', '$2y$10$8hkx19spxc38gSsb9DQGbeNHfO51KptByQz2gpGvxgtOWuikMBmO2', 'doctor', 'N/A', 'Healthy', 'N/A', 'others', 'None', 0, 'N/A', 'N/A', 'N/A', 'N/A', './src/'),
(37, 'samotan', 'samotan', 'A-', '$2y$10$ydQ8JKPwE57LbefjlxJGLuJnHAehYoAYYZ5n4O0ns4jAoJVjay6pS', 'student', 'N/A', 'Healthy', 'N/A', 'others', 'None', 0, 'N/A', 'N/A', 'N/A', 'N/A', './src/'),
(38, 'asd', 'asd', 'B-', '$2y$10$n4Dt7TUc8hpa.mUa4EbJKu/p5Zrmy5YUYsVnzcw72nUA6r9JHsplW', 'student', '2nd Year', 'Healthy', 'N/A', 'others', 'None', 0, 'N/A', 'N/A', 'N/A', 'N/A', '/uploads/profiles/profile_38_1732864692.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `check_up`
--
ALTER TABLE `check_up`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`u_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `check_up`
--
ALTER TABLE `check_up`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
