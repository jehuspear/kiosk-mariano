-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2025 at 05:41 PM
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
-- Database: `kiosk_ordering_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `Feedback_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Feedback_CustomerName` varchar(100) NOT NULL,
  `Feedback_DateTime` datetime NOT NULL,
  `Feedback_Rating` int(11) NOT NULL,
  `Feedback_Comments` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`Feedback_ID`, `Order_ID`, `Feedback_CustomerName`, `Feedback_DateTime`, `Feedback_Rating`, `Feedback_Comments`) VALUES
(1, 1, 'Jesus', '2025-01-28 02:59:05', 5, 'Holy sarap');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `Log_ID` int(11) NOT NULL,
  `Staff_ID` int(11) NOT NULL,
  `Log_DateTime` datetime NOT NULL,
  `Log_Action` varchar(255) NOT NULL,
  `Log_Details` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`Log_ID`, `Staff_ID`, `Log_DateTime`, `Log_Action`, `Log_Details`) VALUES
(1, 1, '2024-12-28 20:33:36', 'Order marked as ready', 'Order #3 status changed to ReadyToClaim'),
(2, 1, '2024-12-28 20:33:38', 'Order marked as ready', 'Order #9 status changed to ReadyToClaim'),
(3, 1, '2024-12-28 20:35:47', 'Order cancelled', 'Order #4 status changed to Cancelled'),
(4, 1, '2024-12-28 20:36:12', 'Order marked as ready', 'Order #8 status changed to ReadyToClaim'),
(5, 1, '2024-12-28 20:50:15', 'Order marked as ready', 'Order #10 status changed to ReadyToClaim'),
(6, 1, '2024-12-28 21:53:09', 'Order marked as ready', 'Order #12 status changed to ReadyToClaim'),
(7, 1, '2024-12-28 22:12:08', 'Order marked as ready', 'Order #4 status changed to ReadyToClaim'),
(8, 2, '2025-01-02 22:40:22', 'Order marked as ready', 'Order #13 status changed to ReadyToClaim'),
(9, 2, '2025-01-02 22:54:41', 'Order marked as ready', 'Order #14 status changed to ReadyToClaim'),
(10, 2, '2025-01-02 22:57:09', 'Order marked as ready', 'Order #15 status changed to ReadyToClaim'),
(11, 2, '2025-01-02 23:14:20', 'Order marked as ready', 'Order #16 status changed to ReadyToClaim'),
(12, 2, '2025-01-02 23:22:09', 'Order marked as ready', 'Order #17 status changed to ReadyToClaim'),
(13, 2, '2025-01-03 00:09:44', 'Order marked as ready', 'Order #18 status changed to ReadyToClaim'),
(14, 2, '2025-01-03 04:21:32', 'Order marked as ready', 'Order #19 status changed to ReadyToClaim'),
(15, 2, '2025-01-03 04:30:30', 'Order marked as ready', 'Order #20 status changed to ReadyToClaim'),
(16, 2, '2025-01-03 05:12:08', 'Order marked as ready', 'Order #21 status changed to ReadyToClaim'),
(17, 2, '2025-01-03 05:19:30', 'Order marked as ready', 'Order #22 status changed to ReadyToClaim'),
(18, 2, '2025-01-03 05:22:14', 'Order marked as ready', 'Order #23 status changed to ReadyToClaim'),
(19, 5, '2025-01-06 18:09:19', 'Order marked as ready', 'Order #24 status changed to ReadyToClaim'),
(20, 2, '2025-01-07 10:17:47', 'Order marked as ready', 'Order #25 status changed to ReadyToClaim'),
(21, 2, '2025-01-07 10:22:03', 'Order marked as ready', 'Order #26 status changed to ReadyToClaim'),
(22, 2, '2025-01-07 10:50:30', 'Order marked as ready', 'Order #27 status changed to ReadyToClaim'),
(23, 2, '2025-01-07 10:54:44', 'Order marked as ready', 'Order #28 status changed to ReadyToClaim'),
(24, 2, '2025-01-07 11:00:12', 'Order marked as ready', 'Order #29 status changed to ReadyToClaim'),
(25, 2, '2025-01-07 11:15:07', 'Order marked as ready', 'Order #30 status changed to ReadyToClaim'),
(26, 2, '2025-01-08 16:21:00', 'Order marked as ready', 'Order #35 status changed to ReadyToClaim'),
(27, 2, '2025-01-09 14:15:44', 'Order marked as ready', 'Order #37 status changed to ReadyToClaim'),
(28, 2, '2025-01-24 10:19:11', 'Order marked as ready', 'Order #38 status changed to ReadyToClaim'),
(29, 2, '2025-01-29 19:18:07', 'Order marked as ready', 'Order #39 status changed to ReadyToClaim'),
(30, 2, '2025-01-29 19:21:24', 'Order marked as ready', 'Order #40 status changed to ReadyToClaim'),
(31, 2, '2025-01-29 19:23:08', 'Order marked as ready', 'Order #40 status changed to ReadyToClaim'),
(32, 2, '2025-01-29 19:56:26', 'Order marked as ready', 'Order #41 status changed to ReadyToClaim'),
(33, 2, '2025-01-29 20:40:11', 'Order marked as ready', 'Order #42 status changed to ReadyToClaim'),
(34, 2, '2025-01-29 20:44:46', 'Order marked as ready', 'Order #2 status changed to ReadyToClaim'),
(35, 2, '2025-01-29 20:46:02', 'Order marked as ready', 'Order #43 status changed to ReadyToClaim'),
(36, 2, '2025-01-29 22:14:04', 'Order marked as ready', 'Order #44 status changed to ReadyToClaim'),
(37, 2, '2025-01-29 22:40:56', 'Order cancelled', 'Order #45 status changed to Cancelled'),
(38, 2, '2025-01-29 22:50:39', 'Order cancelled', 'Order #45 status changed to Cancelled'),
(39, 2, '2025-01-29 22:51:34', 'Order marked as ready', 'Order #45 status changed to ReadyToClaim'),
(40, 2, '2025-01-30 00:28:44', 'Order marked as ready', 'Order #46 status changed to ReadyToClaim'),
(41, 2, '2025-01-30 00:55:45', 'Order marked as ready', 'Order #47 status changed to ReadyToClaim'),
(42, 2, '2025-01-30 01:22:15', 'Order marked as ready', 'Order #48 status changed to ReadyToClaim'),
(43, 2, '2025-01-30 04:14:24', 'Order marked as ready', 'Order #50 status changed to ReadyToClaim'),
(44, 2, '2025-01-30 04:38:51', 'Order marked as ready', 'Order #36 status changed to ReadyToClaim'),
(45, 2, '2025-01-30 15:14:46', 'Order marked as ready', 'Order #51 status changed to ReadyToClaim'),
(46, 2, '2025-02-03 17:13:53', 'Order marked as ready', 'Order #53 status changed to ReadyToClaim'),
(47, 2, '2025-02-03 17:17:59', 'Order marked as ready', 'Order #53 status changed to ReadyToClaim'),
(48, 2, '2025-02-03 17:18:04', 'Order marked as ready', 'Order #54 status changed to ReadyToClaim'),
(49, 2, '2025-02-03 17:18:32', 'Order marked as ready', 'Order #51 status changed to ReadyToClaim'),
(50, 2, '2025-02-03 20:26:53', 'Order cancelled', 'Order #54 status changed to Cancelled'),
(51, 2, '2025-02-03 21:02:08', 'Order cancelled', 'Order #54 status changed to Cancelled'),
(52, 2, '2025-02-03 21:30:35', 'Order cancelled', 'Order #55 status changed to Cancelled'),
(53, 2, '2025-02-03 21:30:41', 'Order cancelled', 'Order #54 status changed to Cancelled'),
(54, 2, '2025-02-03 21:42:21', 'Order cancelled', 'Order #54 status changed to Cancelled'),
(55, 2, '2025-02-03 23:33:36', 'Order cancelled', 'Order #53 status changed to Cancelled'),
(56, 2, '2025-02-03 23:33:39', 'Order cancelled', 'Order #51 status changed to Cancelled'),
(57, 2, '2025-02-04 00:19:21', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(58, 2, '2025-02-04 00:31:14', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱160.00, Discount: PWD (20%)'),
(59, 2, '2025-02-04 00:32:08', 'Payment processed', 'Order #51 payment processed. Method: GCash, Amount: ₱475.00, Discount: None'),
(60, 2, '2025-02-04 00:37:26', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(61, 2, '2025-02-04 00:41:02', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(62, 2, '2025-02-04 00:44:28', 'Order cancelled', 'Order #54 status changed to Cancelled'),
(63, 2, '2025-02-04 00:51:59', 'Order cancelled', 'Order #50 status changed to Cancelled'),
(64, 2, '2025-02-04 00:52:23', 'Order cancelled', 'Order #51 status changed to Cancelled'),
(65, 2, '2025-02-04 00:58:09', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱180.00, Discount: Student (10%)'),
(66, 2, '2025-02-04 00:59:27', 'Order cancelled', 'Order #55 status changed to Cancelled'),
(67, 2, '2025-02-04 01:35:06', 'Payment processed', 'Order #10 payment processed. Method: GCash, Amount: ₱580.00, Discount: None'),
(68, 2, '2025-02-04 02:05:26', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱200.00, Discount: None'),
(69, 2, '2025-02-04 02:08:34', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(70, 2, '2025-02-04 02:12:52', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱200.00, Discount: None'),
(71, 2, '2025-02-04 02:16:09', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(72, 2, '2025-02-04 02:16:21', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱200.00, Discount: None'),
(73, 2, '2025-02-04 02:17:06', 'Payment processed', 'Order #50 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(74, 2, '2025-02-04 02:18:26', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(75, 2, '2025-02-04 02:20:24', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱200.00, Discount: None'),
(76, 2, '2025-02-04 02:21:15', 'Payment processed', 'Order #51 payment processed. Method: GCash, Amount: ₱475.00, Discount: None'),
(77, 2, '2025-02-04 02:23:33', 'Payment processed', 'Order #51 payment processed. Method: GCash, Amount: ₱475.00, Discount: None'),
(78, 2, '2025-02-04 02:26:15', 'Payment processed', 'Order #51 payment processed. Method: GCash, Amount: ₱475.00, Discount: None'),
(79, 2, '2025-02-04 02:27:01', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱180.00, Discount: Student (10%)'),
(80, 2, '2025-02-04 02:32:07', 'Payment processed', 'Order #53 payment processed. Method: Cash, Amount: ₱96.00, Discount: PWD (20%)'),
(81, 2, '2025-02-04 02:34:33', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱100.00, Discount: Admin (50%)'),
(82, 2, '2025-02-04 02:39:24', 'Payment processed', 'Order #51 payment processed. Method: Cash, Amount: ₱380.00, Discount: Teacher (20%)'),
(83, 2, '2025-02-04 02:40:23', 'Payment processed', 'Order #51 payment processed. Method: Cash, Amount: ₱418.00, Discount: Teacher (12%)'),
(84, 2, '2025-02-04 02:43:27', 'Payment processed', 'Order #51 payment processed. Method: Cash, Amount: ₱399.00, Discount: Sample (16%)'),
(85, 2, '2025-02-04 02:49:38', 'Payment processed', 'Order #55 payment processed. Method: GCash, Amount: ₱120.00, Discount: None'),
(86, 2, '2025-02-04 02:50:58', 'Payment processed', 'Order #54 payment processed. Method: Cash, Amount: ₱180.00, Discount: Student (10%)'),
(87, 2, '2025-02-04 19:45:23', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱337.50, Discount: Student (10%)'),
(88, 2, '2025-02-04 19:56:15', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱337.50, Discount: Student (10%)'),
(89, 2, '2025-02-04 20:01:46', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(90, 2, '2025-02-04 20:10:18', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(91, 2, '2025-02-04 20:11:34', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱300.00, Discount: PWD (20%)'),
(92, 2, '2025-02-04 20:19:19', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱337.50, Discount: Student (10%)'),
(93, 2, '2025-02-04 20:31:11', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(94, 2, '2025-02-04 20:41:12', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱337.50, Discount: Student (10%)'),
(95, 2, '2025-02-04 20:49:58', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(96, 2, '2025-02-04 21:01:38', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(97, 2, '2025-02-04 21:04:25', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱375.00, Discount: None'),
(98, 2, '2025-02-04 21:06:50', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(99, 2, '2025-02-04 23:13:18', 'Payment processed', 'Order #57 payment processed. Method: GCash, Amount: ₱337.50, Discount: Student (10%)'),
(100, 2, '2025-02-04 23:20:14', 'Payment processed', 'Order #56 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(101, 2, '2025-02-04 23:27:49', 'Payment processed', 'Order #58 payment processed. Method: Cash, Amount: ₱382.50, Discount: Student (10%)'),
(102, 2, '2025-02-05 00:17:25', 'Payment processed', 'Order #58 payment processed. Method: Cash, Amount: ₱382.50, Discount: PWD (10%)'),
(103, 2, '2025-02-05 00:28:38', 'Payment processed', 'Order #58 payment processed. Method: Cash, Amount: ₱382.50, Discount: Student (10%)'),
(104, 2, '2025-02-05 00:34:44', 'Payment processed', 'Order #59 payment processed. Method: Cash, Amount: ₱432.00, Discount: Student (10%)'),
(105, 2, '2025-02-05 00:48:24', 'Payment processed', 'Order #60 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(106, 2, '2025-02-05 01:09:28', 'Order marked as ready', 'Order #60 status changed to ReadyToClaim'),
(107, 2, '2025-02-05 01:09:40', 'Order marked as ready', 'Order #59 status changed to ReadyToClaim'),
(108, 2, '2025-02-05 01:53:49', 'Order marked as ready', 'Order #58 status changed to ReadyToClaim'),
(109, 2, '2025-02-05 02:13:17', 'Payment processed', 'Order #62 payment processed. Method: Cash, Amount: ₱153.00, Discount: Student (10%)'),
(110, 2, '2025-02-05 02:15:08', 'Order marked as ready', 'Order #62 status changed to ReadyToClaim'),
(111, 2, '2025-02-05 02:18:09', 'Payment processed', 'Order #63 payment processed. Method: GCash, Amount: ₱45.00, Discount: None'),
(112, 2, '2025-02-05 02:29:11', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱153.00, Discount: Student (10%)'),
(113, 2, '2025-02-05 02:29:43', 'Order marked as ready', 'Order #64 status changed to ReadyToClaim'),
(114, 2, '2025-02-05 02:39:51', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱153.00, Discount: Partnership (10%)'),
(115, 2, '2025-02-05 02:42:35', 'Payment processed', 'Order #63 payment processed. Method: GCash, Amount: ₱45.00, Discount: None'),
(116, 2, '2025-02-05 02:49:45', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱153.00, Discount: VIP (10%)'),
(117, 2, '2025-02-05 02:52:47', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱153.00, Discount: Teacher (10%)'),
(118, 2, '2025-02-05 03:20:05', 'Payment processed', 'Order #63 payment processed. Method: GCash, Amount: ₱45.00, Discount: None'),
(119, 2, '2025-02-05 03:21:36', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱170.00, Discount: None'),
(120, 2, '2025-02-05 03:23:33', 'Payment processed', 'Order #63 payment processed. Method: GCash, Amount: ₱45.00, Discount: None'),
(121, 2, '2025-02-05 03:38:53', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱153.00, Discount: Student (10%)'),
(122, 2, '2025-02-05 03:44:27', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱161.50, Discount: Partnership (5%)'),
(123, 2, '2025-02-05 03:47:10', 'Payment processed', 'Order #64 payment processed. Method: Cash, Amount: ₱153.00, Discount: Student (10%)'),
(124, 2, '2025-02-06 02:45:52', 'Payment processed', 'Order #66 payment processed. Method: Cash, Amount: ₱261.00, Discount: Teacher (10%)'),
(125, 2, '2025-02-06 02:48:36', 'Order marked as ready', 'Order #66 status changed to ReadyToClaim'),
(126, 2, '2025-02-06 02:51:24', 'Payment processed', 'Order #67 payment processed. Method: GCash, Amount: ₱245.00, Discount: None'),
(127, 2, '2025-02-06 03:05:25', 'Payment processed', 'Order #65 payment processed. Method: Cash, Amount: ₱100.00, Discount: None'),
(128, 2, '2025-02-06 04:29:48', 'Payment processed', 'Order #68 payment processed. Method: Cash, Amount: ₱383.15, Discount: Client (3%)'),
(129, 2, '2025-02-06 04:31:22', 'Order marked as ready', 'Order #68 status changed to ReadyToClaim'),
(130, 2, '2025-02-06 04:35:08', 'Payment processed', 'Order #69 payment processed. Method: Cash, Amount: ₱140.00, Discount: None'),
(131, 2, '2025-02-06 04:46:54', 'Payment processed', 'Order #69 payment processed. Method: Cash, Amount: ₱140.00, Discount: None'),
(132, 2, '2025-02-06 04:48:26', 'Payment processed', 'Order #67 payment processed. Method: GCash, Amount: ₱245.00, Discount: None'),
(133, 2, '2025-02-06 04:49:08', 'Order marked as ready', 'Order #67 status changed to ReadyToClaim'),
(134, 2, '2025-02-06 21:20:34', 'Payment processed', 'Order #70 payment processed. Method: Cash, Amount: ₱306.00, Discount: Student (10%)'),
(135, 2, '2025-02-06 21:23:36', 'Payment processed', 'Order #68 payment processed. Method: Cash, Amount: ₱395.00, Discount: None'),
(136, 2, '2025-02-06 21:29:58', 'Payment processed', 'Order #68 payment processed. Method: Cash, Amount: ₱316.00, Discount: Senior (20%)'),
(137, 2, '2025-02-06 22:23:41', 'Payment processed', 'Order #71 payment processed. Method: Cash, Amount: ₱333.00, Discount: Student (10%)'),
(138, 2, '2025-02-06 22:25:43', 'Order marked as ready', 'Order #71 status changed to ReadyToClaim'),
(139, 2, '2025-02-06 22:39:09', 'Order marked as ready', 'Order #65 status changed to ReadyToClaim'),
(140, 2, '2025-02-06 22:39:12', 'Order marked as ready', 'Order #70 status changed to ReadyToClaim'),
(141, 2, '2025-02-06 22:39:15', 'Order marked as ready', 'Order #68 status changed to ReadyToClaim');

-- --------------------------------------------------------

--
-- Table structure for table `menuitem`
--

CREATE TABLE `menuitem` (
  `MenuItem_ID` int(11) NOT NULL,
  `MenuItem_Name` varchar(100) NOT NULL,
  `MenuItem_Image` varchar(512) DEFAULT NULL,
  `MenuItem_Description` varchar(512) DEFAULT NULL,
  `MenuItem_Category` varchar(50) DEFAULT NULL,
  `MenuItem_TotalStocks` int(11) NOT NULL,
  `MenuItem_TotalSold` int(11) DEFAULT 0,
  `MenuItem_Availability` enum('Available','Unavailable') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem`
--

INSERT INTO `menuitem` (`MenuItem_ID`, `MenuItem_Name`, `MenuItem_Image`, `MenuItem_Description`, `MenuItem_Category`, `MenuItem_TotalStocks`, `MenuItem_TotalSold`, `MenuItem_Availability`) VALUES
(1, 'Kape Amerikano', 'Images/menu-item/kape-americano.jpg', 'Full-bodied espresso with hot water', 'Coffee', 1300, 3, 'Available'),
(2, 'Latte de Kape', 'Images/menu-item/latte-de-kape.jpg', 'Espresso with steamed milk and a thin layer of foam', 'Coffee', 1500, 5, 'Available'),
(3, 'Cappuccino', 'Images/menu-item/cappuccino.jpg', 'Espresso with steamed milk and a thick layer of foam', 'Coffee', 1490, 0, 'Available'),
(4, 'Latte Espanyol', 'Images/menu-item/latte-espanyol.jpg', 'Espresso combined with milk and a hint of condensed milk', 'Coffee', 1495, 7, 'Available'),
(5, 'Kape Dulce Salty Caramelo', 'Images/menu-item/kape-dulce-salty-caramelo.jpg', 'Espresso combined with milk, infused with salted caramel syrup', 'Coffee', 1493, 2, 'Available'),
(6, 'Kape con Canela', 'Images/menu-item/kape-con-canela.jpg', 'Espresso combined with steamed milk and infused with honey and cinnamon', 'Coffee', 1494, 0, 'Available'),
(7, 'Kape de Cacao', 'Images/menu-item/kape-de-cacao.jpg', 'Espresso combined with steamed milk and rich chocolate syrup', 'Coffee', 1490, 0, 'Available'),
(10, 'Espresso (Doppio)', 'Images/menu-items/Doppio_Espresso_Macchiato.jpg', 'Espresso (Doppio) is a rich, concentrated coffee brewed by forcing hot water through finely-ground coffee beans, resulting in a bold, full-bodied flavor. A doppio, meaning \"double\" in Italian, is simply a double shot of espresso, offering twice the intensity and depth in a single serving.', 'Traditional Coffee', 8, 25, 'Available'),
(11, 'Pretzel', 'Images/menu-items/Air-Fryer-Frozen-Pretzels2.jpg', 'Soft and chewy on the inside, crispy and golden on the outside, these Soft Pretzels are a fun snack that bake up in the oven and served with a delicious cheese sauce!\r\n', 'Snacks', 899, 1, 'Available'),
(12, 'Blue Punch Mocktail', 'Images/menu-item/blue_mocktail.jpg', 'A vibrant blue mocktail garnished with a cherry and lemon slice, served over crushed ice with a striped straw for a refreshing tropical flair.', 'Mocktail', 298, 1, 'Available'),
(13, 'Matcha Latte', 'Images/menu-item/matcha-latte.jpg', 'A creamy matcha latte with swirling layers of vibrant green tea and silky milk, perfect for a refreshing boost.', 'Non-Coffee', 888, 8, 'Available'),
(14, 'Black Forest cake ', 'Images/menu-item/black-forest-pastry.jpg', 'A decadent slice of Black Forest cake layered with rich chocolate sponge, whipped cream, cherries, and topped with chocolate shavings and a cherry garnish.', 'Pastries', 3, 4, 'Available'),
(15, 'Strawberry Latte', 'Images/menu-item/sakura-strawberry-latte1.jpg', 'Enjoy a sip of this delicious strawberry flavored drink!', 'Non-Coffee', 1493, 2, 'Available'),
(16, 'Chocolate Latte', 'Images/menu-item/chocolate latte1.jpg', 'A sweet chocolate flavored drink!', 'Non-Coffee', 1490, 4, 'Available'),
(17, 'Shirley Temple', 'Images/menu-item/shirly temple.jpg', 'Shirley Temples are fun, simple, and absolutely refreshing friendly mocktail', 'Mocktail', 298, 1, 'Available'),
(18, 'Strawberry Kiss', 'Images/menu-item/strawberry-lemon-mocktail-5411.jpg', 'A delicious sweet strawberry drink that will mark your lips with a delight!', 'Mocktail', 299, 0, 'Available'),
(19, 'Orange Sunrise', 'Images/menu-item/orange sunrise.jpg', 'Need a fun brunch drink. This sunrise mocktail has you covered. It is eye-catching, tasty, and 100% alcohol-free.', 'Mocktail', 299, 0, 'Available'),
(20, 'Brownies', 'Images/menu-item/brownies.jpg', 'Dense, fudgy, chewy, chocolate brownies! These decadent brownies are a delicious combination of fresh, sweet cherries and chocolate!', 'Pastries', 100, 0, 'Available'),
(21, 'Banana Bread', 'Images/menu-item/banana bread.jpg', 'This Banana Loaf recipe is very moist with just the right sweetness and has that perfect banana flavor.', 'Pastries', 294, 0, 'Available'),
(22, 'Milky Donuts', 'Images/menu-item/milky donuts.jpg', 'These cream-filled doughnuts are what coffee-break dreams are made of. They are soft pillows with a super-easy cream filling. These yeast doughnuts are deep-fried to perfection, and then filling is piped inside for a delightful treat. ', 'Pastries', 297, 0, 'Available'),
(23, 'Air Fried French Fries', 'Images/menu-item/Crispy Air Fried French Fries.jpg', 'These air fryer French fries are just as crispy and crunchy as regular fries. They are easy and delicious! They use far less oil, so as a result, these fries are far healthier than average ones.', 'Snacks', 598, 2, 'Available'),
(24, 'Cafe BonBon', 'Images/menu-item/Bonbon.jpg', 'Discover the delightful cafe bonbon, a sweet and ultra creamy coffee treat.\r\n', 'Traditional Coffee', 2, 15, 'Available'),
(25, 'Traditional Macchiato', 'Images/menu-item/macchiatotraditonal-11.jpg', 'the macchiato is quite a literal drink - it is an espresso marked with a dollop of frothed milk, and nothing else (macchiato means marked in Italian). ', 'Traditional Coffee', 300, 0, 'Available'),
(26, 'Piccolo', 'Images/menu-item/piccolo.jpg', 'a small coffee composed of a single espresso shot and heated milk.', 'Traditional Coffee', 299, 0, 'Available'),
(27, 'Caesar Salad', 'Images/menu-item/Caesar-Salad.jpg', 'This vibrant Caesar salad features crisp romaine lettuce, golden crunchy croutons, and shaved Parmesan cheese, all tossed in a creamy dressing.', 'Snacks', 10, 3, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `menuitem_sizes`
--

CREATE TABLE `menuitem_sizes` (
  `MenuItemSize_ID` int(11) NOT NULL,
  `MenuItem_ID` int(11) NOT NULL,
  `MenuItemSize_SizeName` varchar(25) NOT NULL,
  `MenuItemSize_IsHot` enum('Hot','Iced','Normal') DEFAULT NULL,
  `MenuItemSize_Price` decimal(10,2) NOT NULL,
  `MenuItemSize_Sold` int(11) NOT NULL,
  `MenuItemSize_Stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem_sizes`
--

INSERT INTO `menuitem_sizes` (`MenuItemSize_ID`, `MenuItem_ID`, `MenuItemSize_SizeName`, `MenuItemSize_IsHot`, `MenuItemSize_Price`, `MenuItemSize_Sold`, `MenuItemSize_Stock`) VALUES
(1, 1, 'Uno', 'Hot', 60.00, 3, 300),
(2, 1, 'Dos', 'Hot', 75.00, 0, 300),
(3, 1, 'Tres', 'Iced', 85.00, 0, 300),
(4, 1, 'Quatro', 'Iced', 100.00, 0, 200),
(5, 1, 'Sinco', 'Iced', 110.00, 0, 200),
(6, 2, 'Uno', 'Hot', 80.00, 0, 300),
(7, 2, 'Dos', 'Hot', 105.00, 0, 300),
(8, 2, 'Tres', 'Iced', 90.00, 0, 300),
(9, 2, 'Quatro', 'Iced', 125.00, 0, 300),
(10, 2, 'Sinco', 'Iced', 135.00, 0, 300),
(11, 3, '8oz', 'Hot', 85.00, 0, 299),
(12, 3, '12oz', 'Hot', 105.00, 0, 296),
(13, 3, 'Tres', 'Iced', 90.00, 0, 298),
(14, 3, '16oz', 'Iced', 125.00, 0, 299),
(15, 3, '22oz', 'Iced', 135.00, 0, 298),
(16, 4, 'Uno', 'Hot', 90.00, 0, 299),
(17, 4, 'Dos', 'Hot', 120.00, 0, 299),
(18, 4, 'Tres', 'Iced', 95.00, 0, 299),
(19, 4, 'Quatro', 'Iced', 130.00, 0, 299),
(20, 4, 'Sinco', 'Iced', 145.00, 0, 299),
(21, 5, 'Uno', 'Hot', 95.00, 0, 299),
(22, 5, 'Dos', 'Hot', 125.00, 0, 299),
(23, 5, 'Tres', 'Iced', 100.00, 0, 299),
(24, 5, 'Quatro', 'Iced', 135.00, 0, 299),
(25, 5, 'Sinco', 'Iced', 150.00, 2, 297),
(26, 6, 'Uno', 'Hot', 100.00, 0, 299),
(27, 6, 'Dos', 'Hot', 130.00, 0, 299),
(28, 6, 'Tres', 'Iced', 100.00, 0, 299),
(29, 6, 'Quatro', 'Iced', 135.00, 0, 298),
(30, 6, 'Sinco', 'Iced', 150.00, 0, 299),
(31, 7, 'Uno', 'Hot', 100.00, 0, 299),
(32, 7, 'Dos', 'Hot', 130.00, 0, 299),
(33, 7, 'Tres', 'Iced', 105.00, 0, 299),
(34, 7, 'Quatro', 'Iced', 140.00, 0, 298),
(35, 7, 'Sinco', 'Iced', 155.00, 0, 295),
(36, 10, 'Uno', 'Hot', 60.00, 13, 2),
(37, 10, 'Dos', 'Hot', 70.00, 11, 2),
(41, 11, 'Small', 'Normal', 50.00, 0, 300),
(42, 11, 'Medium', 'Normal', 60.00, 0, 300),
(48, 12, '12oz', 'Iced', 100.00, 1, 298),
(50, 13, '8oz', 'Hot', 70.00, 1, 298),
(51, 13, '12oz', 'Iced', 100.00, 0, 299),
(52, 13, '16oz', 'Iced', 120.00, 7, 291),
(53, 14, '1 Piece', 'Normal', 60.00, 0, 1),
(54, 14, 'Half Slice', 'Normal', 120.00, 3, 1),
(55, 14, '1 Whole', 'Normal', 240.00, 1, 1),
(56, 15, '8oz', 'Hot', 90.00, 0, 299),
(57, 15, '12oz', 'Hot', 95.00, 0, 299),
(58, 15, '12oz', 'Iced', 95.00, 0, 299),
(60, 15, '16oz', 'Iced', 100.00, 2, 297),
(61, 15, '22oz', 'Iced', 105.00, 0, 299),
(62, 16, '8oz', 'Hot', 90.00, 0, 299),
(63, 16, '12oz', 'Hot', 95.00, 2, 297),
(64, 16, '12oz', 'Iced', 95.00, 2, 297),
(65, 16, '16oz', 'Iced', 100.00, 0, 298),
(66, 16, '22oz', 'Iced', 105.00, 0, 299),
(67, 17, '12oz', 'Iced', 100.00, 1, 298),
(68, 18, '12oz', 'Iced', 100.00, 0, 299),
(69, 19, '12oz', 'Iced', 100.00, 0, 299),
(70, 20, 'Per slice', 'Normal', 50.00, 0, 100),
(71, 21, 'Per slice', 'Normal', 50.00, 0, 294),
(72, 22, 'Per piece', 'Normal', 50.00, 0, 297),
(73, 23, 'Medium', 'Normal', 75.00, 1, 299),
(74, 23, 'Large', 'Normal', 115.00, 1, 299),
(75, 24, '8oz', 'Hot', 50.00, 15, 2),
(76, 25, '6oz', 'Hot', 35.00, 0, 300),
(77, 26, '6oz', 'Hot', 45.00, 0, 299),
(78, 11, 'Large', 'Normal', 70.00, 1, 299),
(79, 10, 'Tres', 'Iced', 90.00, 1, 4),
(80, 27, 'Solo 250g', 'Normal', 100.00, 0, 5),
(81, 27, 'Medium 500g', 'Normal', 150.00, 2, 3),
(82, 27, 'Family 1000g', 'Normal', 250.00, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `Order_ID` int(11) NOT NULL,
  `Order_CustomerName` varchar(100) DEFAULT NULL,
  `Order_EatingOption` enum('Dine-in','Take-out') NOT NULL,
  `Order_TicketNumber` int(11) NOT NULL,
  `Order_DateTime` datetime NOT NULL,
  `Order_CompletedTime` datetime DEFAULT NULL,
  `Order_TotalAmount` decimal(10,2) NOT NULL,
  `Payment_ID` int(11) DEFAULT NULL,
  `Payment_Method` varchar(50) DEFAULT NULL,
  `Staff_ID` int(11) DEFAULT NULL,
  `Order_Status` enum('Pending','Preparing','ReadyToClaim','Completed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`Order_ID`, `Order_CustomerName`, `Order_EatingOption`, `Order_TicketNumber`, `Order_DateTime`, `Order_CompletedTime`, `Order_TotalAmount`, `Payment_ID`, `Payment_Method`, `Staff_ID`, `Order_Status`) VALUES
(1, NULL, 'Dine-in', 232, '2024-12-28 16:41:08', NULL, 100.00, 1, 'Cash', NULL, 'Completed'),
(2, NULL, 'Dine-in', 137, '2024-12-28 16:50:45', '2025-01-29 20:44:52', 515.00, 2, 'Cash', NULL, 'Completed'),
(3, NULL, 'Dine-in', 768, '2024-12-28 17:26:29', NULL, 190.00, 3, 'Cash', NULL, 'Completed'),
(4, NULL, 'Take-out', 129, '2024-12-28 17:33:29', NULL, 180.00, 4, 'GCash', NULL, 'Completed'),
(5, NULL, 'Dine-in', 300, '2024-12-28 17:34:18', NULL, 200.00, 5, 'Cash', NULL, 'Completed'),
(6, NULL, 'Dine-in', 572, '2024-12-28 19:06:27', NULL, 200.00, 6, 'Cash', NULL, 'Completed'),
(7, NULL, 'Take-out', 222, '2024-12-28 19:07:34', NULL, 375.00, 7, 'Cash', NULL, 'Pending'),
(8, NULL, 'Take-out', 410, '2024-12-28 19:22:49', NULL, 300.00, 8, 'GCash', NULL, 'Pending'),
(9, NULL, 'Dine-in', 757, '2024-12-28 19:33:06', NULL, 650.00, 9, 'GCash', NULL, 'Pending'),
(10, NULL, 'Dine-in', 6, '2025-02-03 19:36:57', '2025-02-04 01:35:06', 580.00, 10, 'GCash', 2, 'Pending'),
(11, NULL, 'Dine-in', 129, '2024-12-28 21:31:48', NULL, 120.00, 11, 'Cash', NULL, 'Completed'),
(12, NULL, 'Dine-in', 742, '2024-12-28 21:50:30', NULL, 150.00, 12, 'Cash', NULL, 'Completed'),
(13, NULL, 'Take-out', 289, '2024-12-28 22:08:55', '2024-12-28 22:16:19', 280.00, 13, 'Cash', NULL, 'Completed'),
(14, NULL, 'Take-out', 591, '2025-01-02 22:52:35', NULL, 300.00, 14, 'Cash', NULL, 'Completed'),
(15, NULL, 'Dine-in', 168, '2025-01-02 22:55:29', '2025-01-02 22:57:48', 300.00, 15, 'GCash', NULL, 'Completed'),
(16, NULL, 'Dine-in', 971, '2025-01-02 23:13:20', '2025-01-02 23:15:42', 510.00, 16, 'GCash', NULL, 'Completed'),
(17, NULL, 'Dine-in', 516, '2025-01-02 23:16:55', '2025-01-02 23:22:09', 140.00, 17, 'GCash', NULL, 'Completed'),
(18, NULL, 'Dine-in', 444, '2025-01-03 00:08:26', '2025-01-03 00:09:44', 95.00, 18, 'GCash', NULL, 'Completed'),
(19, NULL, 'Dine-in', 273, '2025-01-03 04:20:55', '2025-01-03 04:21:32', 340.00, 19, 'GCash', NULL, 'Completed'),
(20, NULL, 'Dine-in', 413, '2025-01-03 04:29:24', '2025-01-03 04:30:30', 400.00, 20, 'GCash', NULL, 'Completed'),
(21, NULL, 'Dine-in', 225, '2025-01-03 05:10:50', '2025-01-03 05:12:08', 120.00, 21, 'Cash', NULL, 'Completed'),
(22, NULL, 'Take-out', 637, '2025-01-03 05:18:34', '2025-01-03 05:19:30', 200.00, 22, 'GCash', NULL, 'Completed'),
(23, NULL, 'Dine-in', 233, '2025-01-03 05:21:14', '2025-01-03 05:22:14', 220.00, 23, 'Cash', NULL, 'Completed'),
(24, NULL, 'Dine-in', 482, '2025-01-06 18:07:53', '2025-01-06 18:09:19', 220.00, 24, 'Cash', NULL, 'Completed'),
(25, NULL, 'Dine-in', 813, '2025-01-07 10:17:08', '2025-01-07 10:17:47', 120.00, 25, 'Cash', NULL, 'Completed'),
(26, NULL, 'Take-out', 549, '2025-01-07 10:20:46', '2025-01-07 10:22:03', 90.00, 26, 'Cash', NULL, 'Completed'),
(27, NULL, 'Dine-in', 103, '2025-01-07 10:47:52', '2025-01-07 10:50:30', 1130.00, 27, 'Cash', NULL, 'Completed'),
(28, NULL, 'Dine-in', 863, '2025-01-07 10:52:56', '2025-01-07 10:54:44', 200.00, 28, 'GCash', NULL, 'Completed'),
(29, NULL, 'Dine-in', 848, '2025-01-07 10:59:26', '2025-01-07 11:00:12', 120.00, 29, 'Cash', NULL, 'Completed'),
(30, NULL, 'Take-out', 377, '2025-01-07 11:13:06', '2025-01-07 11:15:07', 240.00, 30, 'Cash', NULL, 'Completed'),
(31, NULL, 'Take-out', 998, '2025-01-07 15:10:19', NULL, 125.00, 31, 'Cash', NULL, 'Pending'),
(32, NULL, 'Dine-in', 862, '2025-01-08 15:54:57', NULL, 155.00, 32, 'Cash', NULL, 'Pending'),
(33, NULL, 'Take-out', 813, '2025-01-08 16:12:53', NULL, 190.00, 33, 'GCash', NULL, 'Pending'),
(34, NULL, 'Dine-in', 925, '2025-01-08 16:13:15', NULL, 210.00, 34, 'Cash', NULL, 'Preparing'),
(35, NULL, 'Dine-in', 318, '2025-01-08 16:16:02', '2025-01-08 16:20:59', 400.00, 35, 'GCash', NULL, 'Completed'),
(36, NULL, 'Dine-in', 851, '2025-01-08 16:17:11', '2025-01-30 04:38:51', 90.00, 36, 'GCash', NULL, 'Completed'),
(37, NULL, 'Take-out', 115, '2025-01-09 14:13:59', '2025-01-09 14:15:44', 455.00, 37, 'Cash', NULL, 'Completed'),
(38, NULL, 'Take-out', 833, '2025-01-24 10:18:26', '2025-01-24 10:19:11', 135.00, 38, 'Cash', NULL, 'Completed'),
(39, NULL, 'Dine-in', 146, '2025-01-29 19:17:11', '2025-01-29 19:19:37', 200.00, 39, 'Cash', NULL, 'Completed'),
(40, NULL, 'Dine-in', 980, '2025-01-29 19:20:41', '2025-01-29 19:25:39', 220.00, 40, 'Cash', NULL, 'Completed'),
(41, NULL, 'Dine-in', 867, '2025-01-29 19:55:22', '2025-01-29 19:56:26', 155.00, 41, 'GCash', NULL, 'Completed'),
(42, NULL, 'Dine-in', 137, '2025-01-29 20:38:10', '2025-01-29 20:44:52', 210.00, 42, 'Cash', NULL, 'Completed'),
(43, NULL, 'Dine-in', 448, '2025-01-29 20:45:45', '2025-01-29 20:46:02', 300.00, 43, 'Cash', NULL, 'Completed'),
(44, NULL, 'Dine-in', 454, '2025-01-29 22:12:55', '2025-01-29 22:14:04', 50.00, 44, 'Cash', NULL, 'Completed'),
(45, NULL, 'Dine-in', 819, '2025-01-29 22:40:17', '2025-01-29 23:09:12', 200.00, 45, 'Cash', NULL, 'Completed'),
(46, NULL, 'Dine-in', 816, '2025-01-30 00:28:18', '2025-01-30 00:29:00', 190.00, 46, 'Cash', NULL, 'Completed'),
(47, NULL, 'Dine-in', 133, '2025-01-30 00:55:21', '2025-01-30 00:55:45', 400.00, 47, 'Cash', NULL, 'Completed'),
(48, NULL, 'Take-out', 388, '2025-01-30 01:21:57', '2025-01-30 01:22:15', 300.00, 48, 'Cash', NULL, 'Completed'),
(49, NULL, 'Dine-in', 751, '2025-01-30 03:08:17', NULL, 390.00, 49, 'Cash', NULL, 'Completed'),
(50, NULL, 'Dine-in', 3, '2025-02-03 16:30:19', '2025-02-04 02:17:06', 120.00, 50, 'GCash', 2, 'Pending'),
(51, NULL, 'Dine-in', 2, '2025-02-03 16:28:19', '2025-02-06 02:48:53', 475.00, 51, 'Cash', 2, 'Completed'),
(53, NULL, 'Dine-in', 1, '2025-02-03 16:27:19', '2025-02-04 02:32:07', 120.00, 53, 'Cash', 2, 'Pending'),
(54, NULL, 'Dine-in', 4, '2025-02-03 17:17:27', '2025-02-06 04:31:55', 200.00, 54, 'Cash', 2, 'Completed'),
(55, NULL, 'Dine-in', 5, '2025-02-03 17:21:15', '2025-02-05 01:09:49', 120.00, 55, 'GCash', 2, 'Completed'),
(56, NULL, 'Dine-in', 1, '2025-02-04 19:42:52', '2025-02-04 23:20:14', 340.00, 56, 'Cash', 2, 'Pending'),
(57, NULL, 'Dine-in', 2, '2025-02-04 19:44:24', '2025-02-06 02:48:53', 375.00, 57, 'GCash', 2, 'Completed'),
(58, NULL, 'Dine-in', 3, '2025-02-04 23:27:03', '2025-02-05 01:53:49', 425.00, 58, 'Cash', 2, 'Completed'),
(59, NULL, 'Dine-in', 4, '2025-02-04 23:37:03', '2025-02-06 04:31:55', 480.00, 59, 'Cash', 2, 'Completed'),
(60, NULL, 'Dine-in', 5, '2025-02-04 23:38:03', '2025-02-05 01:09:49', 340.00, 60, 'Cash', 2, 'Completed'),
(62, NULL, 'Dine-in', 1, '2025-02-05 02:12:44', '2025-02-05 02:15:08', 170.00, 62, 'Cash', 2, 'Completed'),
(63, NULL, 'Dine-in', 2, '2025-02-05 02:17:59', '2025-02-06 02:48:53', 45.00, 63, 'GCash', 2, 'Completed'),
(64, NULL, 'Dine-in', 3, '2025-02-05 02:28:25', '2025-02-05 03:47:10', 170.00, 64, 'Cash', 2, 'Preparing'),
(65, NULL, 'Take-out', 1, '2025-02-06 00:54:36', '2025-02-06 22:39:09', 100.00, 65, 'Cash', 2, 'Completed'),
(66, NULL, 'Dine-in', 2, '2025-02-06 02:15:25', '2025-02-06 02:48:53', 290.00, 66, 'Cash', 2, 'Completed'),
(67, NULL, 'Dine-in', 3, '2025-02-06 02:50:44', '2025-02-06 04:49:08', 245.00, 67, 'GCash', 2, 'Completed'),
(68, NULL, 'Dine-in', 4, '2025-02-06 04:27:09', '2025-02-06 22:39:15', 395.00, 68, 'Cash', 2, 'Completed'),
(69, NULL, 'Dine-in', 5, '2025-02-06 04:34:54', '2025-02-06 04:46:54', 140.00, 69, 'Cash', 2, 'Pending'),
(70, NULL, 'Dine-in', 6, '2025-02-06 05:13:03', '2025-02-06 22:39:12', 340.00, 70, 'Cash', 2, 'Completed'),
(71, NULL, 'Dine-in', 7, '2025-02-06 22:23:02', '2025-02-06 22:25:43', 370.00, 71, 'Cash', 2, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

CREATE TABLE `orderitem` (
  `OrderItem_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `MenuItem_ID` int(11) NOT NULL,
  `OrderItem_CupSize` varchar(255) DEFAULT NULL,
  `OrderItem_Quantity` int(11) NOT NULL,
  `OrderItem_Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`OrderItem_ID`, `Order_ID`, `MenuItem_ID`, `OrderItem_CupSize`, `OrderItem_Quantity`, `OrderItem_Price`) VALUES
(1, 1, 6, 'Uno', 1, 100.00),
(2, 2, 3, 'Quatro', 3, 125.00),
(3, 2, 10, 'Dos', 2, 70.00),
(4, 3, 5, 'Uno', 2, 95.00),
(5, 4, 11, 'Dos', 3, 60.00),
(6, 5, 5, 'Tres', 2, 100.00),
(7, 6, 7, 'Uno', 2, 100.00),
(8, 7, 2, 'Quatro', 3, 125.00),
(9, 8, 5, 'Sinco', 2, 150.00),
(10, 9, 6, 'Tres', 2, 100.00),
(11, 9, 5, 'Tres', 3, 100.00),
(12, 9, 11, 'Uno', 3, 50.00),
(13, 10, 7, 'Quatro', 2, 140.00),
(14, 10, 11, 'Uno', 2, 50.00),
(15, 10, 5, 'Tres', 2, 100.00),
(16, 11, 11, 'Dos', 1, 60.00),
(17, 11, 11, 'Dos', 1, 60.00),
(18, 12, 11, 'Uno', 3, 50.00),
(19, 13, 3, 'Uno', 1, 85.00),
(20, 13, 1, 'Uno', 1, 60.00),
(21, 13, 5, 'Quatro', 1, 135.00),
(22, 14, 5, 'Tres', 3, 100.00),
(23, 15, 5, 'Sinco', 2, 150.00),
(24, 16, 2, 'Quatro', 2, 125.00),
(25, 16, 4, 'Quatro', 2, 130.00),
(26, 17, 7, 'Quatro', 1, 140.00),
(27, 18, 5, 'Uno', 1, 95.00),
(28, 19, 11, 'Extra-Large', 1, 100.00),
(29, 19, 12, '16oz', 2, 120.00),
(30, 20, 12, '12oz', 2, 100.00),
(31, 20, 11, 'Extra-Large', 2, 100.00),
(32, 21, 13, '16oz', 1, 120.00),
(33, 22, 13, '12oz', 1, 100.00),
(34, 22, 12, '12oz', 1, 100.00),
(35, 23, 13, '16oz', 1, 120.00),
(36, 23, 11, 'Extra-Large', 1, 100.00),
(37, 24, 14, 'Half Slice', 1, 120.00),
(38, 24, 13, '12oz', 1, 100.00),
(39, 25, 14, '1 Piece', 2, 60.00),
(40, 26, 2, 'Tres', 1, 90.00),
(41, 27, 4, 'Quatro', 1, 130.00),
(42, 27, 11, 'Extra-Large', 10, 100.00),
(43, 28, 12, '12oz', 2, 100.00),
(44, 29, 14, '1 Piece', 2, 60.00),
(45, 30, 14, '1 Whole', 1, 240.00),
(46, 31, 3, 'Quatro', 1, 125.00),
(47, 32, 10, 'Dos', 1, 70.00),
(48, 32, 1, 'Tres', 1, 85.00),
(49, 33, 3, '12oz', 1, 105.00),
(50, 33, 1, 'Tres', 1, 85.00),
(51, 34, 3, '12oz', 2, 105.00),
(52, 35, 1, 'Sinco', 1, 110.00),
(53, 35, 7, 'Sinco', 1, 155.00),
(54, 35, 6, 'Quatro', 1, 135.00),
(55, 36, 3, 'Tres', 1, 90.00),
(56, 37, 1, 'Quatro', 2, 100.00),
(57, 37, 7, 'Sinco', 1, 155.00),
(58, 37, 22, 'Per piece', 2, 50.00),
(59, 38, 3, '22oz', 1, 135.00),
(60, 39, 21, 'Per slice', 2, 50.00),
(61, 39, 16, '16oz', 1, 100.00),
(62, 40, 13, '16oz', 1, 120.00),
(63, 40, 21, 'Per slice', 2, 50.00),
(64, 41, 7, 'Sinco', 1, 155.00),
(65, 42, 10, 'Dos', 3, 70.00),
(66, 43, 10, 'Uno', 5, 60.00),
(67, 44, 24, '8oz', 1, 50.00),
(68, 45, 24, '8oz', 4, 50.00),
(69, 46, 13, '16oz', 1, 120.00),
(70, 46, 11, 'Large', 1, 70.00),
(71, 47, 10, 'Dos', 5, 70.00),
(72, 47, 24, '8oz', 1, 50.00),
(73, 48, 10, 'Uno', 5, 60.00),
(74, 49, 10, 'Uno', 3, 60.00),
(75, 49, 10, 'Dos', 3, 70.00),
(76, 50, 13, '16oz', 1, 120.00),
(77, 51, 13, '16oz', 1, 120.00),
(78, 51, 14, '1 Whole', 1, 240.00),
(79, 51, 23, 'Large', 1, 115.00),
(81, 53, 13, '16oz', 1, 120.00),
(82, 54, 15, '16oz', 2, 100.00),
(83, 55, 14, 'Half Slice', 1, 120.00),
(84, 56, 16, '16oz', 1, 100.00),
(85, 56, 15, '16oz', 1, 100.00),
(86, 56, 7, 'Quatro', 1, 140.00),
(87, 57, 5, 'Sinco', 1, 150.00),
(88, 57, 13, '16oz', 1, 120.00),
(89, 57, 16, '22oz', 1, 105.00),
(90, 58, 16, '22oz', 2, 105.00),
(91, 58, 22, 'Per piece', 2, 50.00),
(92, 58, 23, 'Large', 1, 115.00),
(93, 59, 5, 'Sinco', 1, 150.00),
(94, 59, 26, '6oz', 2, 45.00),
(95, 59, 21, 'Per slice', 2, 50.00),
(96, 59, 11, 'Large', 2, 70.00),
(97, 60, 5, 'Uno', 1, 95.00),
(98, 60, 19, '12oz', 1, 100.00),
(99, 60, 21, 'Per slice', 1, 50.00),
(100, 60, 16, '12oz', 1, 95.00),
(102, 62, 13, '16oz', 1, 120.00),
(103, 62, 21, 'Per slice', 1, 50.00),
(104, 63, 26, '6oz', 1, 45.00),
(105, 64, 13, '16oz', 1, 120.00),
(106, 64, 21, 'Per slice', 1, 50.00),
(107, 65, 12, '12oz', 1, 100.00),
(108, 66, 6, 'Dos', 1, 130.00),
(109, 66, 14, '1 Piece', 1, 60.00),
(110, 66, 12, '12oz', 1, 100.00),
(111, 67, 17, '12oz', 1, 100.00),
(112, 67, 23, 'Medium', 1, 75.00),
(113, 67, 13, '8oz', 1, 70.00),
(114, 68, 5, 'Sinco', 1, 150.00),
(115, 68, 27, 'Medium 500g', 1, 150.00),
(116, 68, 16, '12oz', 1, 95.00),
(117, 69, 10, 'Tres', 1, 90.00),
(118, 69, 24, '8oz', 1, 50.00),
(119, 70, 13, '16oz', 2, 120.00),
(120, 70, 12, '12oz', 1, 100.00),
(121, 71, 27, 'Family 1000g', 1, 250.00),
(122, 71, 13, '16oz', 1, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Payment_ReferenceNumber` varchar(20) DEFAULT NULL,
  `Payment_DateTime` datetime NOT NULL,
  `Order_TotalAmount` decimal(10,2) NOT NULL,
  `Payment_DiscountType` varchar(50) DEFAULT NULL,
  `Payment_DiscountAmount` decimal(10,2) DEFAULT 0.00,
  `Payment_CashPaid` decimal(10,2) DEFAULT NULL,
  `Payment_Change` decimal(10,2) DEFAULT NULL,
  `Payment_TotalAmount` decimal(10,2) NOT NULL,
  `Payment_Status` enum('Pending','Completed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Payment_Method`, `Payment_ReferenceNumber`, `Payment_DateTime`, `Order_TotalAmount`, `Payment_DiscountType`, `Payment_DiscountAmount`, `Payment_CashPaid`, `Payment_Change`, `Payment_TotalAmount`, `Payment_Status`) VALUES
(1, 'Cash', NULL, '2024-12-28 16:41:08', 100.00, NULL, 0.00, NULL, NULL, 100.00, 'Pending'),
(2, 'Cash', NULL, '2024-12-28 20:54:01', 515.00, NULL, 0.00, NULL, NULL, 515.00, 'Completed'),
(3, 'Cash', NULL, '2024-12-28 17:26:29', 190.00, NULL, 0.00, NULL, NULL, 190.00, 'Completed'),
(4, 'GCash', NULL, '2024-12-28 22:12:08', 180.00, NULL, 0.00, NULL, NULL, 180.00, 'Completed'),
(5, 'Cash', NULL, '2024-12-28 17:34:18', 200.00, NULL, 0.00, NULL, NULL, 200.00, 'Pending'),
(6, 'Cash', NULL, '2024-12-28 19:57:58', 200.00, NULL, 0.00, NULL, NULL, 200.00, 'Completed'),
(7, 'Cash', NULL, '2024-12-28 20:40:04', 375.00, NULL, 0.00, NULL, NULL, 375.00, 'Completed'),
(8, 'GCash', NULL, '2024-12-28 20:36:12', 300.00, NULL, 0.00, NULL, NULL, 300.00, 'Completed'),
(9, 'GCash', NULL, '2024-12-28 20:32:32', 650.00, NULL, 0.00, NULL, NULL, 650.00, 'Completed'),
(10, 'GCash', '123123', '2025-02-04 01:35:06', 580.00, '', 0.00, 0.00, 0.00, 580.00, 'Completed'),
(11, 'Cash', NULL, '2024-12-28 21:49:33', 120.00, NULL, 0.00, NULL, NULL, 120.00, 'Cancelled'),
(12, 'Cash', NULL, '2024-12-28 21:53:09', 150.00, NULL, 0.00, NULL, NULL, 150.00, 'Completed'),
(13, 'Cash', NULL, '2025-01-02 22:40:22', 280.00, NULL, 0.00, NULL, NULL, 280.00, 'Completed'),
(14, 'Cash', NULL, '2025-01-02 22:54:41', 300.00, NULL, 0.00, NULL, NULL, 300.00, 'Completed'),
(15, 'GCash', NULL, '2025-01-02 22:57:09', 300.00, NULL, 0.00, NULL, NULL, 300.00, 'Completed'),
(16, 'GCash', NULL, '2025-01-02 23:14:20', 510.00, NULL, 0.00, NULL, NULL, 510.00, 'Completed'),
(17, 'GCash', NULL, '2025-01-02 23:17:46', 140.00, NULL, 0.00, NULL, NULL, 140.00, 'Completed'),
(18, 'GCash', NULL, '2025-01-03 00:08:38', 95.00, NULL, 0.00, NULL, NULL, 95.00, 'Completed'),
(19, 'GCash', NULL, '2025-01-03 04:21:09', 340.00, NULL, 0.00, NULL, NULL, 340.00, 'Completed'),
(20, 'GCash', NULL, '2025-01-03 04:29:49', 400.00, NULL, 0.00, NULL, NULL, 400.00, 'Completed'),
(21, 'Cash', NULL, '2025-01-03 05:11:48', 120.00, NULL, 0.00, NULL, NULL, 120.00, 'Completed'),
(22, 'GCash', NULL, '2025-01-03 05:19:01', 200.00, NULL, 0.00, NULL, NULL, 200.00, 'Completed'),
(23, 'Cash', NULL, '2025-01-03 05:21:48', 220.00, NULL, 0.00, NULL, NULL, 220.00, 'Completed'),
(24, 'Cash', NULL, '2025-01-06 18:08:44', 220.00, NULL, 0.00, NULL, NULL, 220.00, 'Completed'),
(25, 'Cash', NULL, '2025-01-07 10:17:28', 120.00, NULL, 0.00, NULL, NULL, 120.00, 'Completed'),
(26, 'Cash', NULL, '2025-01-07 10:21:09', 90.00, NULL, 0.00, NULL, NULL, 90.00, 'Completed'),
(27, 'Cash', NULL, '2025-01-07 10:49:34', 1130.00, NULL, 0.00, NULL, NULL, 1130.00, 'Completed'),
(28, 'GCash', NULL, '2025-01-07 10:54:12', 200.00, NULL, 0.00, NULL, NULL, 200.00, 'Completed'),
(29, 'Cash', NULL, '2025-01-07 10:59:49', 120.00, NULL, 0.00, NULL, NULL, 120.00, 'Completed'),
(30, 'Cash', NULL, '2025-01-07 11:13:47', 240.00, NULL, 0.00, NULL, NULL, 240.00, 'Completed'),
(31, 'Cash', NULL, '2025-01-07 15:10:19', 125.00, NULL, 0.00, NULL, NULL, 125.00, 'Pending'),
(32, 'Cash', NULL, '2025-01-08 15:54:57', 155.00, NULL, 0.00, NULL, NULL, 155.00, 'Pending'),
(33, 'GCash', NULL, '2025-01-08 16:12:53', 190.00, NULL, 0.00, NULL, NULL, 190.00, 'Pending'),
(34, 'Cash', NULL, '2025-01-08 16:15:58', 210.00, NULL, 0.00, NULL, NULL, 210.00, 'Completed'),
(35, 'GCash', NULL, '2025-01-08 16:16:21', 400.00, NULL, 0.00, NULL, NULL, 400.00, 'Completed'),
(36, 'GCash', NULL, '2025-01-08 16:17:39', 90.00, NULL, 0.00, NULL, NULL, 90.00, 'Completed'),
(37, 'Cash', NULL, '2025-01-09 14:15:14', 455.00, NULL, 0.00, NULL, NULL, 455.00, 'Completed'),
(38, 'Cash', NULL, '2025-01-24 10:18:46', 135.00, NULL, 0.00, NULL, NULL, 135.00, 'Completed'),
(39, 'Cash', NULL, '2025-01-29 19:17:37', 200.00, NULL, 0.00, NULL, NULL, 200.00, 'Completed'),
(40, 'Cash', NULL, '2025-01-29 19:20:52', 220.00, NULL, 0.00, NULL, NULL, 220.00, 'Completed'),
(41, 'GCash', NULL, '2025-01-29 19:55:30', 155.00, NULL, 0.00, NULL, NULL, 155.00, 'Completed'),
(42, 'Cash', NULL, '2025-01-29 20:38:22', 210.00, NULL, 0.00, NULL, NULL, 210.00, 'Completed'),
(43, 'Cash', NULL, '2025-01-29 20:45:50', 300.00, NULL, 0.00, NULL, NULL, 300.00, 'Completed'),
(44, 'Cash', NULL, '2025-01-29 22:13:03', 50.00, NULL, 0.00, NULL, NULL, 50.00, 'Completed'),
(45, 'Cash', NULL, '2025-01-29 22:51:28', 200.00, NULL, 0.00, NULL, NULL, 200.00, 'Completed'),
(46, 'Cash', NULL, '2025-01-30 00:28:29', 190.00, NULL, 0.00, NULL, NULL, 190.00, 'Completed'),
(47, 'Cash', NULL, '2025-01-30 00:55:29', 400.00, NULL, 0.00, NULL, NULL, 400.00, 'Completed'),
(48, 'Cash', NULL, '2025-01-30 01:22:03', 300.00, NULL, 0.00, NULL, NULL, 300.00, 'Completed'),
(49, 'Cash', NULL, '2025-01-30 03:08:21', 390.00, NULL, 0.00, NULL, NULL, 390.00, 'Completed'),
(50, 'GCash', '534512', '2025-02-04 02:17:06', 120.00, '', 0.00, 0.00, 0.00, 120.00, 'Completed'),
(51, 'GCash', '', '2025-02-04 02:43:27', 475.00, 'Sample (16%)', 76.00, 500.00, 101.00, 399.00, 'Completed'),
(53, 'Cash', '', '2025-02-04 02:32:07', 120.00, 'PWD (20%)', 24.00, 100.00, 4.00, 96.00, 'Completed'),
(54, 'GCash', '', '2025-02-04 02:50:58', 200.00, 'Student (10%)', 20.00, 200.00, 20.00, 180.00, 'Completed'),
(55, 'GCash', '543223', '2025-02-04 02:49:38', 120.00, '', 0.00, 0.00, 0.00, 120.00, 'Completed'),
(56, 'Cash', '', '2025-02-04 23:20:14', 340.00, 'Student (10%)', 34.00, 310.00, 4.00, 306.00, 'Completed'),
(57, 'GCash', '123456', '2025-02-04 23:13:18', 375.00, 'Student (10%)', 37.50, 0.00, 0.00, 337.50, 'Completed'),
(58, 'Cash', '', '2025-02-05 00:28:38', 425.00, 'Student (10%)', 42.50, 400.00, 17.50, 382.50, 'Completed'),
(59, 'Cash', '', '2025-02-05 00:34:44', 480.00, 'Student (10%)', 48.00, 440.00, 8.00, 432.00, 'Completed'),
(60, 'Cash', '', '2025-02-05 00:48:24', 340.00, 'Student (10%)', 34.00, 350.00, 44.00, 306.00, 'Completed'),
(62, 'Cash', '', '2025-02-05 02:13:17', 170.00, 'Student (10%)', 17.00, 155.00, 2.00, 153.00, 'Completed'),
(63, 'GCash', '432412', '2025-02-05 03:23:33', 45.00, '', 0.00, 0.00, 0.00, 45.00, 'Completed'),
(64, 'Cash', '', '2025-02-05 03:47:10', 170.00, 'Student (10%)', 17.00, 155.00, 2.00, 153.00, 'Completed'),
(65, 'Cash', '', '2025-02-06 03:05:25', 100.00, '', 0.00, 100.00, 0.00, 100.00, 'Completed'),
(66, 'Cash', '', '2025-02-06 02:45:52', 290.00, 'Teacher (10%)', 29.00, 265.00, 4.00, 261.00, 'Completed'),
(67, 'GCash', '498702', '2025-02-06 04:48:26', 245.00, '', 0.00, 0.00, 0.00, 245.00, 'Completed'),
(68, 'Cash', '', '2025-02-06 21:29:58', 395.00, 'Senior (20%)', 79.00, 320.00, 4.00, 316.00, 'Completed'),
(69, 'Cash', '', '2025-02-06 04:46:54', 140.00, '', 0.00, 150.00, 10.00, 140.00, 'Completed'),
(70, 'Cash', '', '2025-02-06 21:20:34', 340.00, 'Student (10%)', 34.00, 310.00, 4.00, 306.00, 'Completed'),
(71, 'Cash', '', '2025-02-06 22:23:41', 370.00, 'Student (10%)', 37.00, 335.00, 2.00, 333.00, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `Staff_ID` int(11) NOT NULL,
  `Staff_Username` varchar(50) NOT NULL,
  `Staff_Password` varchar(255) NOT NULL,
  `Staff_FirstName` varchar(50) NOT NULL,
  `Staff_MiddleName` varchar(50) NOT NULL,
  `Staff_LastName` varchar(50) NOT NULL,
  `Staff_ContactNumber` varchar(11) NOT NULL,
  `Staff_Email` varchar(100) NOT NULL,
  `Staff_Address` varchar(150) NOT NULL,
  `Staff_BirthDate` date DEFAULT NULL,
  `Staff_Role` enum('Admin','Staff') NOT NULL,
  `Staff_Status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Staff_ID`, `Staff_Username`, `Staff_Password`, `Staff_FirstName`, `Staff_MiddleName`, `Staff_LastName`, `Staff_ContactNumber`, `Staff_Email`, `Staff_Address`, `Staff_BirthDate`, `Staff_Role`, `Staff_Status`) VALUES
(1, 'admin_tan', '$2y$10$Ui7nbTCCCwB18q0DyzTYOegtToXPOy80T/O54Yk5kL84te4S/VWru', 'Clarence', 'Allanson', 'Tan', '09123456789', 'clarence@admin.com', 'Merry Homes, Barangay 178, Caloocan City', '2003-06-12', 'Admin', 'Active'),
(2, 'jehu_staff', '$2y$10$DReShyUhE8AT0jgnn3Iph.9FtONWMyjG8U1KodJUcQfaLa02UZm.C', 'Jehu Vincent', 'Ferrer', 'Galvez', '09123456789', 'jehu@admin.com', 'Star Apple Street, Barangay 178, Caloocan City', '2001-09-13', 'Admin', 'Active'),
(3, 'zuhat_admin', '$2y$10$i80fkexxlrCZ4F5biQ0uHe5xyGw3HKvqXgIXVs2wAFrWf9wOeT5ti', 'Zuhat', 'O', 'Kaplan', '09123654129', 'zuhat@admin.com', 'North Fairview', '2000-01-05', 'Admin', 'Active'),
(4, 'ej_admin', '$2y$10$aMgC9hwb78DyalxYLEkIKOGlUYMu.tP71FKLd6.uJ21UMrTR./d1q', 'Ej Karl', 'B', 'So', '09123654129', 'ej@admin.com', 'SJDM Bulacan', '2000-01-01', 'Admin', 'Active'),
(5, 'juan_staff', '$2y$10$EUF48aOZ.gtVYko//2eXP.x52zAkgJ8ZrmiZf3cyt7o./3ZfroCW6', 'Juan', 'Rizal', 'Dela Cruz', '09123654129', 'juan@staff.com', 'Green Grove Street', '2025-01-01', 'Staff', 'Active'),
(6, 'Dummy', '$2y$10$g/yN3j28csZU4Da484e7HeZDddyYmuEKSmrAiqoXx8Vq3DcFdsf5W', 'Try', 'Dummy', 'Account', '09111111111', 'DummyAccount@gmail.com', 'blkstreetbrngy', '2025-01-01', 'Admin', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`Feedback_ID`),
  ADD KEY `Order_ID` (`Order_ID`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`Log_ID`),
  ADD KEY `Staff_ID` (`Staff_ID`);

--
-- Indexes for table `menuitem`
--
ALTER TABLE `menuitem`
  ADD PRIMARY KEY (`MenuItem_ID`);

--
-- Indexes for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  ADD PRIMARY KEY (`MenuItemSize_ID`),
  ADD KEY `fk_menuitem` (`MenuItem_ID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`Order_ID`),
  ADD KEY `Payment_ID` (`Payment_ID`),
  ADD KEY `Staff_ID` (`Staff_ID`);

--
-- Indexes for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD PRIMARY KEY (`OrderItem_ID`),
  ADD KEY `Order_ID` (`Order_ID`),
  ADD KEY `MenuItem_ID` (`MenuItem_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`Staff_ID`),
  ADD UNIQUE KEY `Staff_Username` (`Staff_Username`),
  ADD UNIQUE KEY `Staff_Email` (`Staff_Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `Feedback_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `MenuItem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  MODIFY `MenuItemSize_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `orderitem`
--
ALTER TABLE `orderitem`
  MODIFY `OrderItem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `Staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `order` (`Order_ID`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`Staff_ID`) REFERENCES `staff` (`Staff_ID`);

--
-- Constraints for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  ADD CONSTRAINT `fk_menuitem` FOREIGN KEY (`MenuItem_ID`) REFERENCES `menuitem` (`MenuItem_ID`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`Payment_ID`) REFERENCES `payment` (`Payment_ID`),
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`Staff_ID`) REFERENCES `staff` (`Staff_ID`);

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `order` (`Order_ID`),
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`MenuItem_ID`) REFERENCES `menuitem` (`MenuItem_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
