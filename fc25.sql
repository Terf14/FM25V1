-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 08:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fc25`
--

-- --------------------------------------------------------

--
-- Table structure for table `academy_players`
--

CREATE TABLE `academy_players` (
  `academy_player_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` enum('st','cf','lw','rw','lm','rm','cam','cm','cdm','cb','lb','rb','gk') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academy_players`
--

INSERT INTO `academy_players` (`academy_player_id`, `user_id`, `name`, `position`) VALUES
(20, 1, 'Matti Keller', 'cam'),
(25, 1, 'Leon Field', 'cb'),
(27, 1, 'Alex Miranda', 'cdm'),
(28, 1, 'Guiherme Lopes', 'cdm'),
(29, 1, 'Igor Silva', 'cm'),
(30, 1, 'Miguel Correia', 'cm'),
(32, 1, 'Roman Crook', 'cdm'),
(33, 1, 'Luca Burt', 'cdm'),
(36, 1, 'Kwaw Offei', 'gk'),
(38, 1, 'Jenson Gardner', 'cdm'),
(39, 1, 'Theo Wall', 'lm'),
(40, 2, 'Edward Parr', 'gk'),
(41, 2, 'Bjorn Martens', 'gk'),
(48, 2, 'Darlington Tinubu', 'rw'),
(49, 2, 'Peter Njoku', 'rw'),
(50, 2, 'Solomon Bello', 'rm'),
(51, 2, 'Lucas Howe', 'cm'),
(52, 2, 'Carter Reeves', 'cm'),
(64, 2, 'Gianluga Rimaldi', 'cdm'),
(66, 2, 'Eduardo Gomez', 'rb'),
(67, 2, 'Yeray Bernal', 'cm'),
(68, 3, 'Charles Greaves', 'gk'),
(69, 3, 'Iulian Agafitei', 'gk'),
(70, 3, 'Joel Beard', 'rb'),
(71, 3, 'Jude Davison', 'lm'),
(72, 3, 'Bjorn Olsson', 'lm'),
(73, 3, 'Stanislav Volkov', 'rw'),
(75, 3, 'Jay Dodds', 'cb'),
(79, 3, 'Anton Kramer', 'cam'),
(80, 3, 'Dominic Ludwig', 'cam'),
(82, 4, 'Benjamin Dale', 'gk'),
(98, 4, 'Jayden Bakker', 'cb'),
(99, 4, 'Melvin de Vos', 'st'),
(100, 4, 'Stijn Boogaard', 'st'),
(101, 4, 'Diadie Senghor', 'rm'),
(102, 4, 'Intouch Ruangsak', 'rm'),
(103, 4, 'Phuwadet Sakdinnon', 'cm'),
(104, 4, 'Nantawat Miller', 'cb'),
(105, 4, 'Hiroshi Sato', 'rw'),
(106, 5, 'B. Horton', 'gk'),
(107, 5, 'V. Gassner', 'gk'),
(108, 5, 'J. Clayton', 'rb'),
(109, 5, 'K. McDowell', 'cdm'),
(110, 5, 'A. Abeledo', 'st'),
(111, 5, 'A. Gomes', 'lw'),
(112, 5, 'Rafael Moreira', 'lw'),
(113, 5, 'A. Harvey', 'cb'),
(114, 5, 'L. Best', 'cb');

-- --------------------------------------------------------

--
-- Table structure for table `former_players`
--

CREATE TABLE `former_players` (
  `player_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('crucial','important','rotation','sporadic','prospect') DEFAULT NULL,
  `position` enum('st','cf','lw','rw','lm','rm','cam','cm','cdm','rb','lb','cb','gk') DEFAULT NULL,
  `jersey_number` int(11) DEFAULT NULL,
  `status` enum('no','sell','for_loan','on_loan') DEFAULT 'no',
  `injured` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `former_players`
--

INSERT INTO `former_players` (`player_id`, `user_id`, `name`, `role`, `position`, `jersey_number`, `status`, `injured`) VALUES
(1, 1, 'Callum Cummings', 'important', 'cam', 8, 'sell', 0),
(2, 1, 'Taylor Clarke', 'sporadic', 'gk', 12, 'sell', 0),
(3, 1, 'Jamie Traynor', 'sporadic', 'st', 22, 'sell', 0),
(4, 1, 'Liam Vaughan', 'rotation', 'lb', 5, 'sell', 1),
(5, 1, 'Reggie Osborne', 'sporadic', 'gk', 22, 'sell', 0),
(6, 1, 'Thomas Middleton', 'important', 'cb', 4, 'sell', 0),
(7, 1, 'Courtney Lewis', 'important', 'cam', 9, 'sell', 0),
(8, 1, 'Riley Humphrey', 'important', 'cdm', 24, 'sell', 0),
(9, 1, 'Elliott Hutchinson', 'sporadic', 'cdm', 6, 'sell', 0),
(10, 1, 'Ollie King', 'rotation', 'rb', 26, 'sell', 0),
(11, 1, 'Cian O\'Leary', 'rotation', 'cm', 18, 'no', 0),
(12, 1, 'Ethan Kane', 'rotation', 'cam', 17, 'sell', 0),
(13, 1, 'Alimu Faye', 'sporadic', 'cb', 13, 'sell', 0),
(14, 1, 'Duane Jacob', 'sporadic', 'gk', 23, 'sell', 0),
(15, 1, 'Nicholas Carter', 'prospect', 'st', 21, 'sell', 0),
(16, 1, 'Adam Franklin', 'sporadic', 'lb', 14, 'sell', 0),
(17, 1, 'Gabriel Brown', 'sporadic', 'rb', 15, 'sell', 0),
(18, 1, 'Vivian Wright', 'prospect', 'rw', 27, 'sell', 0),
(19, 1, 'Benjamin Hahn', 'sporadic', 'lm', 19, 'no', 0),
(26, 1, 'Carney Chukwuemeka', 'crucial', 'cam', 8, '', 0),
(46, 1, 'Abu Klouchi', 'prospect', 'gk', NULL, 'no', 0),
(47, 1, 'Nico Becker', 'prospect', 'lb', NULL, 'no', 0),
(62, 2, 'Gordon Watson', 'crucial', 'cb', 2, 'sell', 0),
(65, 2, 'Taylor Arnold', 'sporadic', 'cb', 5, 'sell', 0),
(66, 2, 'Reggie Myers', 'sporadic', 'cb', 6, 'sell', 0),
(68, 2, 'Louie Bray', 'rotation', 'cdm', 25, 'sell', 0),
(71, 2, 'William Williamson', 'rotation', 'cm', 18, 'sell', 0),
(78, 2, 'Caleb Forrest', 'important', 'st', 9, 'sell', 0),
(126, 4, 'Ethan Morrissey', 'crucial', 'gk', 1, 'sell', 0),
(127, 4, 'Taylor Nicholson', 'important', 'rb', 2, 'sell', 0),
(128, 4, 'Ethan Burt', 'important', 'cb', 3, 'sell', 0),
(129, 4, 'Jesse Seymour', 'important', 'cb', 4, 'sell', 0),
(131, 4, 'Felix Lewis', 'rotation', 'cdm', 22, 'sell', 0),
(133, 4, 'Paul David', 'rotation', 'cm', 24, 'sell', 0),
(134, 4, 'Arthur Hamilton', 'rotation', 'rw', 10, 'sell', 0),
(139, 4, 'Jonathan Charlton', 'rotation', 'lb', 0, 'sell', 0),
(140, 4, 'Billy Frost', 'sporadic', 'rb', 15, 'sell', 0),
(143, 4, 'Ellis Brewer', 'rotation', 'cam', 18, 'no', 0),
(144, 4, 'Jamie Newton', 'prospect', 'lw', 0, 'sell', 0),
(146, 4, 'Damien Cain', 'prospect', 'cam', 0, 'for_loan', 0),
(150, 4, 'Mihaly Kata', 'crucial', 'cm', 8, 'sell', 3),
(151, 4, 'Samson Tovide', 'important', 'st', 25, 'sell', 0),
(158, 4, 'Lewis Koumas', 'crucial', 'rm', 15, '', 0),
(160, 4, 'Darko Gyabi', 'important', 'cdm', 16, 'sell', 0),
(161, 4, 'Marc Bernal', 'important', 'cdm', 20, 'no', 0),
(166, 4, 'Josh Acheampong', 'important', 'rb', 4, 'no', 0),
(169, 4, 'Reggie Parker', 'prospect', 'lb', NULL, 'no', 0),
(170, 4, 'Peter Kerbie', 'prospect', 'rw', NULL, 'no', 0),
(171, 4, 'Manu Vidal', 'prospect', 'st', NULL, 'no', 0),
(172, 4, 'Khaya Smith', 'prospect', 'lm', NULL, 'no', 0),
(173, 4, 'Enzo Gerard', 'prospect', 'gk', NULL, 'no', 0),
(174, 4, 'Simphiwe Booi', 'prospect', 'lb', NULL, 'no', 0),
(175, 4, 'Duma Tyesi', 'prospect', 'lb', NULL, 'no', 0),
(176, 4, 'Dexter Cummings', 'prospect', 'lm', NULL, 'no', 0),
(195, 5, 'E. Schofield', 'sporadic', 'cb', 5, 'sell', 0),
(202, 5, 'D. Coleman', 'important', 'cm', 8, 'sell', 0),
(220, 6, 'Alonso Gomez', 'rotation', 'rw', 11, 'no', 0);

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('crucial','important','rotation','sporadic','prospect') DEFAULT NULL,
  `position` enum('st','cf','lw','rw','lm','rm','cam','cm','cdm','cb','lb','rb','gk') DEFAULT NULL,
  `jersey_number` int(11) DEFAULT NULL,
  `status` enum('no','sell','for_loan','on_loan','in_loan') DEFAULT 'no',
  `injured` int(11) NOT NULL DEFAULT 0,
  `injured_count` int(11) DEFAULT 0,
  `performance_score` int(11) NOT NULL DEFAULT 0,
  `score_change` enum('increase','decrease','neutral') DEFAULT 'neutral',
  `change_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `user_id`, `name`, `role`, `position`, `jersey_number`, `status`, `injured`, `injured_count`, `performance_score`, `score_change`, `change_count`) VALUES
(1, 1, 'Henry White', 'rotation', 'cam', 10, 'no', 3, 0, 16, 'increase', 18),
(2, 1, 'Edward Buckley', 'important', 'st', 11, 'in_loan', 1, 0, 0, 'neutral', 0),
(3, 1, 'Ezra Hall', 'rotation', 'cb', 33, 'sell', 0, 0, 0, 'neutral', 0),
(20, 1, 'Alfie Turner', 'sporadic', 'cdm', 16, 'for_loan', 0, 0, 0, 'neutral', 0),
(21, 1, 'Raul Rangel', 'crucial', 'gk', 1, 'no', 0, 0, 0, 'neutral', 0),
(22, 1, 'Marcel Ruiz', 'crucial', 'cm', 7, 'no', 2, 0, -1, 'decrease', 1),
(23, 1, 'Prateep Inpi', 'important', 'lw', 25, 'no', 0, 0, 0, 'neutral', 0),
(24, 1, 'Peeranut Iadthongkam', 'important', 'rw', 20, 'sell', 0, 0, 0, 'neutral', 0),
(25, 1, 'Homan Ahmed', 'crucial', 'lb', 2, 'sell', 0, 0, 0, 'neutral', 0),
(30, 1, 'Anwar Hacini', 'prospect', 'lm', 18, 'on_loan', 0, 0, 0, 'neutral', 0),
(31, 1, 'Jesus Angulo', 'crucial', 'cb', 5, 'no', 2, 0, 0, 'neutral', 0),
(32, 1, 'Juan Gonzalez', 'prospect', 'gk', 12, 'no', 0, 0, 0, 'neutral', 0),
(33, 1, 'Gustav Bengtsson', 'sporadic', 'cb', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(34, 1, 'Romero Radu', 'sporadic', 'lb', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(35, 1, 'Kirk Reid', 'sporadic', 'cm', 25, 'no', 0, 0, 1, 'increase', 1),
(36, 1, 'Gustave Germain', 'sporadic', 'cb', 28, 'no', 1, 0, 0, 'neutral', 0),
(37, 1, 'Fran Flores', 'rotation', 'rb', 29, 'no', 1, 0, 0, 'neutral', 0),
(38, 1, 'Brandon Cover', 'rotation', 'cdm', 30, 'no', 0, 0, 0, 'neutral', 0),
(39, 1, 'Cristian Marin', 'important', 'st', 17, 'no', 1, 0, 4, 'increase', 4),
(40, 1, 'Godson Akenzua', 'sporadic', 'cdm', 21, 'no', 0, 0, 0, 'neutral', 0),
(41, 1, 'Lucas Seymour', 'sporadic', 'cb', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(42, 1, 'Jasper Cunningham', 'sporadic', 'cam', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(43, 1, 'Taylor Welsh', 'sporadic', 'cam', 24, 'no', 0, 0, 2, 'increase', 2),
(44, 1, 'Nils Stief', 'sporadic', 'lw', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(45, 1, 'Carter Venness', 'prospect', 'gk', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(48, 1, 'Sebastian Gunther', 'prospect', 'cdm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(49, 1, 'Dennis Diekmann', 'sporadic', 'lm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(50, 1, 'Damjan Jelavic', 'crucial', 'lw', 15, 'for_loan', 0, 0, 1, 'increase', 1),
(51, 1, 'Dominic Morley', 'crucial', 'rb', 3, 'no', 0, 0, 0, 'neutral', 0),
(52, 1, 'Clarence Dieng', 'rotation', 'lb', 27, 'no', 0, 0, 0, 'neutral', 0),
(53, 1, 'Eduardo Santos', 'sporadic', 'cam', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(54, 1, 'Antonio Kovacevic', 'rotation', 'cb', 4, 'no', 0, 0, 0, 'neutral', 0),
(55, 1, 'David Harding', 'sporadic', 'cm', 99, 'in_loan', 0, 0, 0, 'neutral', 0),
(57, 1, 'Frederick Hicks', 'prospect', 'cdm', 31, 'no', 0, 0, 0, 'neutral', 0),
(58, 2, 'Archie Barlow', 'important', 'gk', 1, 'no', 0, 0, 0, 'increase', 1),
(59, 2, 'Edward Johnson', 'important', 'gk', 12, 'no', 0, 0, 0, 'increase', 1),
(60, 2, 'Dylan Park', 'sporadic', 'gk', 23, 'no', 0, 0, 0, 'neutral', 0),
(61, 2, 'Ross Boyce', 'prospect', 'lb', 16, 'no', 0, 0, 0, 'increase', 1),
(63, 2, 'Freddie Brown', 'crucial', 'cb', 3, 'no', 0, 0, 0, 'increase', 1),
(64, 2, 'Jim McCabe', 'crucial', 'cb', 4, 'no', 0, 0, 0, 'increase', 1),
(67, 2, 'Eamon Boyd', 'important', 'cdm', 26, 'sell', 0, 0, 0, 'increase', 1),
(69, 2, 'Tommy Harper', 'important', 'lm', 14, 'no', 0, 0, -1, 'decrease', 2),
(70, 2, 'Joel Chambers', 'prospect', 'cm', 17, 'no', 0, 0, 0, 'decrease', 1),
(72, 2, 'Jay Little', 'crucial', 'cam', 15, 'sell', 0, 0, 0, 'decrease', 2),
(73, 2, 'Jordan McAllister', 'sporadic', 'cam', 19, 'sell', 0, 0, 0, 'decrease', 1),
(74, 2, 'Robert Morrow', 'prospect', 'cam', 21, 'on_loan', 0, 0, 0, 'neutral', 0),
(75, 2, 'Nathan John', 'important', 'rm', 13, 'no', 0, 0, -3, 'decrease', 3),
(76, 2, 'Charlie Palmer', 'prospect', 'rm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(77, 2, 'Joshua Randall', 'prospect', 'lw', 11, 'sell', 0, 0, 0, 'increase', 1),
(79, 2, 'Albert Thompson', 'crucial', 'st', 10, 'no', 0, 0, 4, 'increase', 13),
(80, 2, 'Leo Rhodes', 'sporadic', 'st', 22, 'no', 0, 0, 3, 'increase', 5),
(81, 2, 'Brendan Garvey', 'prospect', 'rb', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(83, 2, 'Simon Olanrewaju', 'important', 'lm', 7, 'no', 0, 0, 0, 'increase', 7),
(84, 2, 'Aaron Horne', 'important', 'cm', 28, 'no', 0, 0, 0, 'decrease', 1),
(85, 2, 'Hugo de Oliveira', 'important', 'cdm', 27, 'no', 0, 0, 0, 'increase', 5),
(86, 2, 'Eric Herrmann', 'crucial', 'cm', 8, 'no', 0, 0, 0, 'increase', 9),
(87, 2, 'Hugo Santos', 'important', 'cm', 29, 'no', 0, 0, 0, 'increase', 8),
(88, 2, 'Pedro Alves', 'important', 'cm', 30, 'no', 0, 0, 0, 'decrease', 1),
(89, 2, 'Christopher Zimmermann', 'important', 'cam', 31, 'no', 0, 0, -2, 'decrease', 2),
(90, 2, 'Gianmarco Poli', 'crucial', 'rb', 34, 'no', 0, 0, 0, 'increase', 1),
(91, 2, 'Alessandro D\'Angelo', 'crucial', 'lb', 6, 'no', 0, 0, 0, 'increase', 1),
(92, 2, 'Xabi Carrasco', 'important', 'cb', 33, 'no', 0, 0, 0, 'decrease', 1),
(93, 2, 'Inki Gil', 'crucial', 'cb', 5, 'no', 0, 0, 0, 'increase', 4),
(94, 2, 'Ivan Campos', 'crucial', 'cb', 2, 'no', 0, 0, 0, 'increase', 2),
(95, 3, 'Mason Bullock', 'crucial', 'gk', 1, 'no', 0, 0, 7, 'increase', 8),
(96, 3, 'Joel Sadler', 'sporadic', 'gk', 12, 'no', 0, 0, -1, 'increase', 1),
(97, 3, 'Connor Lancaster', 'sporadic', 'gk', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(98, 3, 'Michael Morris', 'crucial', 'lb', 14, 'no', 0, 0, -1, 'increase', 2),
(99, 3, 'Saikou Jatta', 'sporadic', 'lb', 16, 'no', 0, 0, 2, 'increase', 3),
(100, 3, 'Jack Evans', 'important', 'cb', 5, 'no', 0, 0, -2, 'decrease', 2),
(101, 3, 'Jesse Willis', 'rotation', 'cb', 4, 'no', 0, 0, -2, 'decrease', 2),
(102, 3, 'Bradley Brookes', 'rotation', 'cb', 6, 'no', 0, 0, 0, 'increase', 1),
(103, 3, 'Dwight Nelson', 'crucial', 'rb', 2, 'on_loan', 0, 0, -2, 'decrease', 2),
(104, 3, 'Lewis Buchanan', 'important', 'rb', 17, 'no', 0, 0, -2, 'decrease', 2),
(105, 3, 'Rhodri Griffiths', 'rotation', 'cdm', 25, 'no', 0, 0, -1, 'increase', 1),
(106, 3, 'Aron McGarry', 'crucial', 'cdm', 8, 'no', 0, 0, 3, 'increase', 2),
(107, 3, 'Edi Brahimi', 'prospect', 'cdm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(108, 3, 'Elliot Barnard', 'sporadic', 'lm', 21, 'no', 0, 0, 4, 'increase', 4),
(109, 3, 'Carter Coates', 'sporadic', 'cm', 19, 'no', 0, 0, -1, 'increase', 1),
(110, 3, 'Harrack Baker-Whiting', 'sporadic', 'cm', 20, 'no', 0, 0, -1, 'decrease', 1),
(111, 3, 'Tyler Graham', 'important', 'cam', 13, 'no', 0, 0, -2, 'increase', 2),
(112, 3, 'Vasilis Papadopoulos', 'crucial', 'cam', 10, 'no', 0, 0, 3, 'decrease', 1),
(113, 3, 'Barry McFarlane', 'crucial', 'cam', 15, 'no', 0, 0, -4, 'decrease', 5),
(114, 3, 'Benjamin Holmes', 'sporadic', 'rw', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(115, 3, 'Gavin Byrne', 'important', 'st', 9, 'no', 0, 0, 1, 'increase', 1),
(116, 3, 'Stanley Cairns', 'sporadic', 'st', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(117, 3, 'Cameron Parkinson', 'sporadic', 'st', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(118, 3, 'Shumaira Mheuka', 'crucial', 'st', 11, 'in_loan', 0, 0, 8, 'increase', 7),
(119, 3, 'Donay O\'Brien-Brady', 'important', 'cam', 7, 'no', 0, 0, -2, 'increase', 1),
(120, 3, 'Joel Webber', 'important', 'cb', 22, 'no', 0, 0, 4, 'increase', 1),
(121, 3, 'Aidan Goddard', 'crucial', 'cb', 3, 'no', 0, 0, 7, 'increase', 7),
(122, 3, 'Sam Matthews', 'crucial', 'rb', 18, 'no', 0, 0, 4, 'increase', 1),
(123, 3, 'Izan Merino', 'crucial', 'cdm', 23, 'no', 0, 0, 4, 'increase', 4),
(124, 3, 'Mohammed Kuhn', 'prospect', 'rm', NULL, 'no', 0, 0, 3, 'increase', 3),
(125, 3, 'Theodor Voigt', 'prospect', 'cm', NULL, 'no', 0, 0, -1, 'decrease', 1),
(130, 4, 'Mike McCann', 'sporadic', 'lb', 0, 'on_loan', 0, 0, 0, 'increase', 1),
(132, 4, 'Lewis McNeill', 'crucial', 'cm', 7, 'no', 0, 0, 18, 'increase', 54),
(135, 4, 'Barry McKinney', 'crucial', 'st', 9, 'no', 0, 0, 24, 'increase', 39),
(136, 4, 'Tony Reid', 'crucial', 'lw', 11, 'no', 0, 0, 21, 'increase', 21),
(137, 4, 'Jonathan Evans', 'crucial', 'gk', 1, 'no', 0, 0, 8, 'decrease', 1),
(138, 4, 'Aaron Charlton', 'rotation', 'cb', 13, 'no', 0, 0, 13, 'increase', 49),
(141, 4, 'Benjamin Watts', 'prospect', 'cdm', 29, 'for_loan', 1, 0, 8, 'increase', 12),
(142, 4, 'Curtis Ellis', 'sporadic', 'cm', 17, 'no', 3, 0, 6, 'increase', 10),
(145, 4, 'Robert McFadden', 'sporadic', 'rm', 0, 'on_loan', 0, 0, 0, 'increase', 2),
(147, 4, 'Noah Booth', 'prospect', 'st', 14, 'no', 0, 0, 7, 'increase', 4),
(148, 4, 'Connor Ball', 'sporadic', 'gk', 0, 'on_loan', 0, 0, 0, 'decrease', 1),
(149, 4, 'Said Said', 'crucial', 'cb', 6, 'no', 0, 0, 9, 'increase', 3),
(152, 4, 'Edney', 'important', 'rb', 26, 'no', 3, 0, 11, 'decrease', 2),
(153, 4, 'Samu Cortes', 'sporadic', 'lw', 19, 'no', 0, 0, 3, 'decrease', 1),
(154, 4, 'Tomas Silva', 'rotation', 'lb', 21, 'no', 0, 0, 8, 'increase', 5),
(155, 4, 'Theo Rigby', 'crucial', 'rw', 10, 'no', 2, 0, 20, 'increase', 39),
(156, 4, 'Samson Baidoo', 'important', 'cb', 5, 'no', 3, 0, 13, 'increase', 27),
(157, 4, 'Joshua David', 'sporadic', 'cm', 0, 'on_loan', 3, 0, 0, 'increase', 2),
(159, 4, 'Martin Hurtado', 'rotation', 'rb', 2, 'no', 0, 0, 9, 'increase', 9),
(162, 4, 'Diego Fernandes', 'important', 'cb', 0, 'on_loan', 0, 0, 0, 'increase', 1),
(163, 4, 'Liam Mohamed', 'sporadic', 'gk', 12, 'no', 0, 0, 15, 'increase', 8),
(164, 4, 'Otto Hartmann', 'important', 'cam', 0, 'on_loan', 1, 0, 0, 'increase', 1),
(165, 4, 'Daniel Christensen', 'crucial', 'cm', 23, 'no', 0, 0, 13, 'increase', 29),
(167, 4, 'Archie Turner', 'sporadic', 'rw', 15, 'no', 0, 0, 9, 'increase', 1),
(168, 4, 'Matha Homhuan', 'sporadic', 'cdm', 0, 'on_loan', 0, 0, 0, 'increase', 1),
(177, 4, 'Myles Lewis-Skelly', 'crucial', 'lb', 3, 'no', 0, 0, 12, 'increase', 2),
(178, 4, 'Cedric de Leeuw', 'important', 'cb', 0, 'on_loan', 0, 0, 1, 'increase', 1),
(179, 4, 'Javi Correa', 'prospect', 'cm', 0, 'on_loan', 0, 0, 1, 'increase', 1),
(180, 4, 'Dario Essugo', 'crucial', 'cdm', 18, 'no', 0, 0, 13, 'increase', 13),
(181, 4, 'Sebastian Torres', 'sporadic', 'cdm', 16, 'no', 0, 0, 2, 'decrease', 1),
(182, 4, 'Raheem AI Muwallad', 'sporadic', 'lm', 20, 'no', 0, 0, 14, 'increase', 13),
(183, 4, 'Mikel Rodriguez', 'sporadic', 'cm', 22, 'no', 0, 0, 13, 'increase', 13),
(184, 4, 'Jonathan Richter', 'prospect', 'cm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(185, 4, 'Emillio Morillo', 'rotation', 'cb', 4, 'no', 0, 0, 10, 'increase', 10),
(186, 4, 'Finley Payne', 'rotation', 'rb', 25, 'no', 0, 0, -5, 'decrease', 7),
(187, 4, 'Jayson Bakker', 'sporadic', 'cm', 27, 'no', 0, 0, 6, 'increase', 4),
(188, 4, 'Ramiro Mansilla', 'prospect', 'cm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(189, 5, 'E. Terry', 'important', 'gk', 1, 'no', 0, 0, 0, 'neutral', 0),
(190, 5, 'T. Elliott', 'rotation', 'gk', 12, 'no', 0, 0, 0, 'decrease', 1),
(191, 5, 'O. Bowden', 'sporadic', 'gk', 23, 'no', 0, 0, 0, 'decrease', 1),
(192, 5, 'S. Tremblay', 'crucial', 'cb', 2, 'no', 0, 0, 1, 'increase', 2),
(193, 5, 'J. Bentley', 'crucial', 'cb', 3, 'no', 0, 0, 5, 'increase', 5),
(194, 5, 'O. Lyons', 'crucial', 'cb', 4, 'no', 0, 0, 3, 'increase', 1),
(196, 5, 'J. Wilson', 'sporadic', 'cb', 6, 'no', 0, 0, -3, 'decrease', 3),
(197, 5, 'J. Woolley', 'prospect', 'cb', 24, 'no', 0, 0, -4, 'decrease', 4),
(198, 5, 'N. Proctor', 'sporadic', 'rb', 15, 'sell', 0, 0, -6, 'decrease', 5),
(199, 5, 'A. Small', 'prospect', 'cdm', 16, 'no', 0, 0, -3, 'decrease', 4),
(200, 5, 'J. Dodds', 'important', 'lm', 14, 'no', 0, 0, 2, 'increase', 2),
(201, 5, 'D. Austin', 'rotation', 'lm', 0, 'on_loan', 0, 0, 0, 'neutral', 0),
(203, 5, 'CM T. Bradley', 'crucial', 'cm', 13, 'no', 0, 0, 0, 'decrease', 1),
(204, 5, 'R. Seymour', 'sporadic', 'cm', 17, 'no', 0, 0, 6, 'increase', 2),
(205, 5, 'O. Young', 'important', 'rm', 7, 'no', 0, 0, 6, 'increase', 6),
(206, 5, 'L. Kent', 'sporadic', 'rm', 0, 'on_loan', 0, 0, -1, 'decrease', 1),
(207, 5, 'T. Parker', 'crucial', 'lw', 11, 'sell', 0, 0, -3, 'decrease', 3),
(208, 5, 'A. Scott', 'sporadic', 'lw', 22, 'no', 0, 0, -2, 'decrease', 2),
(209, 5, 'l. Russell', 'crucial', 'rw', 9, 'sell', 0, 0, 1, 'decrease', 1),
(210, 5, 'J. Kennedy', 'prospect', 'rw', 21, 'no', 0, 0, 1, 'increase', 1),
(211, 5, 'J. Graham', 'rotation', 'st', 10, 'sell', 0, 0, 4, 'increase', 4),
(212, 5, 'K. Richards', 'prospect', 'st', 20, 'no', 0, 0, -1, 'decrease', 2),
(213, 5, 'Jimmy-Jay Morgan', 'important', 'st', 18, 'no', 0, 0, 2, 'increase', 2),
(214, 5, 'Luke Bolton', 'important', 'rb', 25, 'no', 0, 0, -4, 'decrease', 4),
(215, 5, 'Regan Hendry', 'important', 'cm', 8, 'no', 0, 0, 4, 'increase', 4),
(216, 5, 'Clarke Oduor', 'important', 'cm', 19, 'no', 0, 0, 3, 'increase', 3),
(217, 5, 'Yair Arismendi', 'crucial', 'lb', 5, 'no', 0, 0, 0, 'increase', 1),
(218, 5, 'O. Bishop', 'prospect', 'lm', NULL, 'no', 0, 0, 0, 'neutral', 0);

-- --------------------------------------------------------

--
-- Table structure for table `player_statistics`
--

CREATE TABLE `player_statistics` (
  `stat_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `year` varchar(9) NOT NULL,
  `appearances` int(11) DEFAULT 0,
  `goals` int(11) DEFAULT 0,
  `assists` int(11) DEFAULT 0,
  `clean_sheets` int(11) DEFAULT 0,
  `yellow_cards` int(11) DEFAULT 0,
  `red_cards` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `player_statistics`
--

INSERT INTO `player_statistics` (`stat_id`, `player_id`, `user_id`, `year`, `appearances`, `goals`, `assists`, `clean_sheets`, `yellow_cards`, `red_cards`, `rating`, `created_at`) VALUES
(3, 20, 1, '24/25', 2, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(4, 54, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(5, 30, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(6, 38, 1, '24/25', 2, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(7, 45, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(8, 52, 1, '24/25', 4, 1, 520, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(9, 39, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(10, 50, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(11, 55, 1, '24/25', 4, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(12, 49, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(13, 51, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(14, 53, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(15, 2, 1, '24/25', 0, 145, 0, 0, 12, 0, 0.00, '2025-04-26 03:25:01'),
(16, 3, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(17, 37, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(18, 57, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(19, 40, 1, '24/25', 0, 2, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(20, 33, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(21, 36, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(22, 1, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(23, 25, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(24, 42, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(25, 31, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(26, 32, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(27, 35, 1, '24/25', 0, 0, 0, 4, 0, 0, 0.00, '2025-04-26 03:25:01'),
(28, 41, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(29, 22, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(30, 44, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(31, 24, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(32, 23, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(33, 21, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(34, 34, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(35, 48, 1, '24/25', 0, 7, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01'),
(36, 43, 1, '24/25', 0, 0, 0, 0, 0, 0, 0.00, '2025-04-26 03:25:01');

-- --------------------------------------------------------

--
-- Table structure for table `team_trophies`
--

CREATE TABLE `team_trophies` (
  `trophy_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `season` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `trophy_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_trophies`
--

INSERT INTO `team_trophies` (`trophy_id`, `user_id`, `title`, `season`, `description`, `trophy_date`, `created_at`) VALUES
(1, 6, 'Premier League', '24/25', 'ชนะ 21 แพ้ 1 เสมอ 10 ยิง 120 เสีย 20', '2025-04-26', '2025-04-26 03:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'acl', '$2y$10$DWG44fCE3LZ7AVZ8nc5tTuGWPxNBcWzR3qkmx35achk9q02ONX0y.'),
(2, 'can', '$2y$10$n4dSsC0Uu2GBZiin6WeWiuQ8UGewKlnCs0GeDKOtQQO9Rnp8Rrg3G'),
(3, 'Albion', '$2y$10$yywuZ9LZ9huHOdjG8epGS.3wsQd9ahQ4DR6TzjMD51p4aRCHdU2Vq'),
(4, 'elr', '$2y$10$5zykrCwWpztx4aUpnmympe4qHoMEZHEH.u8HK3E.LmYkjBOSF4mxS'),
(5, 'Cowden', '$2y$10$lGQDzonK8CAuNpklC1B6gOeCRmAOa7APBuAout6PWI9Fk1p9cUX1K'),
(6, 'ter', '$2y$10$6Vp1yL2fTAyhLQVf4hY18elPVV20jONLx81W6lYLWseBIREyJNjJ6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academy_players`
--
ALTER TABLE `academy_players`
  ADD PRIMARY KEY (`academy_player_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `former_players`
--
ALTER TABLE `former_players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `player_statistics`
--
ALTER TABLE `player_statistics`
  ADD PRIMARY KEY (`stat_id`);

--
-- Indexes for table `team_trophies`
--
ALTER TABLE `team_trophies`
  ADD PRIMARY KEY (`trophy_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academy_players`
--
ALTER TABLE `academy_players`
  MODIFY `academy_player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `former_players`
--
ALTER TABLE `former_players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `player_statistics`
--
ALTER TABLE `player_statistics`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `team_trophies`
--
ALTER TABLE `team_trophies`
  MODIFY `trophy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academy_players`
--
ALTER TABLE `academy_players`
  ADD CONSTRAINT `academy_players_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `former_players`
--
ALTER TABLE `former_players`
  ADD CONSTRAINT `former_players_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
