-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2024 at 05:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ase230`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `game_id` int(11) NOT NULL,
  `game_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`game_id`, `game_date`) VALUES
(0, '2024-12-06'),
(1, '2024-10-05'),
(3, '2024-10-09'),
(4, '2024-10-03'),
(5, '2024-10-10'),
(6, '2024-10-11'),
(7, '2024-10-12'),
(8, '2024-10-13'),
(9, '2024-10-14');

-- --------------------------------------------------------

--
-- Table structure for table `game_teams`
--

CREATE TABLE `game_teams` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `team_name` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `is_home` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_teams`
--

INSERT INTO `game_teams` (`id`, `game_id`, `team_name`, `score`, `is_home`) VALUES
(1, 3, 'The Giants', 5, 1),
(2, 3, 'The Sharks', 3, 0),
(3, 4, 'The Giants', 5, 1),
(4, 4, 'The Eagles', 2, 0),
(5, 5, 'The Panthers', 6, 1),
(6, 5, 'The Bulls', 4, 0),
(7, 6, 'The Warriors', 7, 1),
(8, 6, 'The Hawks', 5, 0),
(9, 7, 'The Hawks', 3, 1),
(10, 7, 'The Panthers', 2, 0),
(11, 8, 'The Bulls', 8, 1),
(12, 8, 'The Giants', 6, 0),
(13, 9, 'The Eagles', 2, 1),
(14, 9, 'The Warriors', 3, 0),
(17, 0, 'The Giants', 8, 1),
(18, 0, 'The Sharks', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_name` varchar(255) NOT NULL,
  `player_name` varchar(255) NOT NULL,
  `player_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_name`, `player_name`, `player_number`) VALUES
('The Sharks', 'Alice Johnson', 10),
('The Sharks', 'Bob Smith', 11),
('The Sharks', 'Charlie Brown', 12),
('The Eagles', 'David Wilson', 7),
('The Eagles', 'Emma Davis', 8),
('The Eagles', 'Fiona Garcia', 9),
('The Giants', 'Doug', 1),
('The Giants', 'Sean', 2),
('The Giants', 'Cambo', 3),
('The Panthers', 'Rachel Adams', 4),
('The Panthers', 'Michael Thompson', 5),
('The Panthers', 'Olivia Martinez', 6),
('The Bulls', 'James Miller', 13),
('The Bulls', 'Sophia Wilson', 14),
('The Bulls', 'Noah Brown', 15),
('The Warriors', 'Liam Johnson', 16),
('The Warriors', 'Ava Smith', 17),
('The Warriors', 'Mason Garcia', 18),
('The Hawks', 'Isabella Lee', 19),
('The Hawks', 'Ethan Perez', 20),
('The Hawks', 'Chloe Kim', 21),
('The Lions', 'Joe', 22),
('The Lions', 'Bob', 23),
('The Lions', 'John', 24),
('The Giants', 'Mith', 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Douglas Broughton', 'broughtond1@nku.edu', 'douglas1107', 'scorekeeper'),
(2, 'doug', 'dmb@gmail.com', '1234567', 'scorekeeper'),
(3, 'Sean Cancel', 'seancancel21@icloud.com', 'jcpsSC4077', 'scorekeeper'),
(4, 'Sean Cancel', 'seancancel21@outlook.com', 'jcpsSC4077', 'scorekeeper'),
(5, 'Bob', 'bob@gmail.com', '$2y$10$tHPnetPN3Sqd6Utgotgykufd2DCyKhO/eIgGJFArpQBWRsg.ZGZS6', 'viewer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`);

--
-- Indexes for table `game_teams`
--
ALTER TABLE `game_teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

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
-- AUTO_INCREMENT for table `game_teams`
--
ALTER TABLE `game_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_teams`
--
ALTER TABLE `game_teams`
  ADD CONSTRAINT `game_teams_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
