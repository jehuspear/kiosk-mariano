-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2025 at 04:57 PM
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
(18, 2, '2025-01-03 05:22:14', 'Order marked as ready', 'Order #23 status changed to ReadyToClaim');

-- --------------------------------------------------------

--
-- Table structure for table `menuitem`
--

CREATE TABLE `menuitem` (
  `MenuItem_ID` int(11) NOT NULL,
  `MenuItem_Name` varchar(100) NOT NULL,
  `MenuItem_Image` varchar(255) DEFAULT NULL,
  `MenuItem_Description` varchar(255) DEFAULT NULL,
  `MenuItem_Category` varchar(50) DEFAULT NULL,
  `MenuItem_TotalStocks` int(11) NOT NULL,
  `MenuItem_TotalSold` int(11) DEFAULT 0,
  `MenuItem_Availability` enum('Available','Unavailable') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem`
--

INSERT INTO `menuitem` (`MenuItem_ID`, `MenuItem_Name`, `MenuItem_Image`, `MenuItem_Description`, `MenuItem_Category`, `MenuItem_TotalStocks`, `MenuItem_TotalSold`, `MenuItem_Availability`) VALUES
(1, 'Kape Amerikano', 'Images/menu-item/kape-americano.jpg', 'Full-bodied espresso with hot water', 'Coffee', 200, 3, 'Available'),
(2, 'Latte de Kape', 'Images/menu-item/latte-de-kape.jpg', 'Espresso with steamed milk and a thin layer of foam', 'Coffee', 154, 5, 'Available'),
(3, 'Cappuccino', 'Images/menu-item/cappuccino.jpg', 'Espresso with steamed milk and a thick layer of foam', 'Coffee', 120, 0, 'Available'),
(4, 'Latte Espanyol', 'Images/menu-item/latte-espanyol.jpg', 'Espresso combined with milk and a hint of condensed milk', 'Coffee', 154, 7, 'Available'),
(5, 'Kape Dulce Salty Caramelo', 'Images/menu-item/kape-dulce-salty-caramelo.jpg', 'Espresso combined with milk, infused with salted caramel syrup', 'Coffee', 154, 10, 'Available'),
(6, 'Kape con Canela', 'Images/menu-item/kape-con-canela.jpg', 'Espresso combined with steamed milk and infused with honey and cinnamon', 'Coffee', 100, 0, 'Available'),
(7, 'Kape de Cacao', 'Images/menu-item/kape-de-cacao.jpg', 'Espresso combined with steamed milk and rich chocolate syrup', 'Coffee', 2, 0, 'Available'),
(10, 'Espresso (Doppio)', 'Images/menu-items/Doppio_Espresso_Macchiato.jpg', 'Sample Coffee', 'Traditional Coffee', 20, 0, 'Available'),
(11, 'Banana', 'Images/menu-items/banana.jpg', 'Banana', 'Snacks', 35, 0, 'Available'),
(12, 'Blue Punch Mocktail', 'Images/menu-item/blue_mocktail.jpg', 'A vibrant blue mocktail garnished with a cherry and lemon slice, served over crushed ice with a striped straw for a refreshing tropical flair.', 'Mocktail', 5, 0, 'Available'),
(13, 'Matcha Latte', 'Images/menu-item/matcha-latte.jpg', 'A creamy matcha latte with swirling layers of vibrant green tea and silky milk, perfect for a refreshing boost.', 'Non-Coffee', 25, 0, 'Available');

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
(1, 1, 'Uno', 'Hot', 60.00, 3, 50),
(2, 1, 'Dos', 'Hot', 75.00, 0, 40),
(3, 1, 'Tres', 'Iced', 85.00, 0, 30),
(4, 1, 'Quatro', 'Iced', 100.00, 0, 20),
(5, 1, 'Sinco', 'Iced', 110.00, 0, 15),
(6, 2, 'Uno', 'Hot', 80.00, 0, 49),
(7, 2, 'Dos', 'Hot', 105.00, 0, 40),
(8, 2, 'Tres', 'Iced', 90.00, 0, 30),
(9, 2, 'Quatro', 'Iced', 125.00, 0, 20),
(10, 2, 'Sinco', 'Iced', 135.00, 0, 15),
(11, 3, 'Uno', 'Hot', 85.00, 0, 50),
(12, 3, 'Dos', 'Hot', 105.00, 0, 40),
(13, 3, 'Tres', 'Iced', 90.00, 0, 30),
(14, 3, 'Quatro', 'Iced', 125.00, 0, 20),
(15, 3, 'Sinco', 'Iced', 135.00, 0, 15),
(16, 4, 'Uno', '', 90.00, 0, 49),
(17, 4, 'Dos', '', 120.00, 0, 40),
(18, 4, 'Tres', '', 95.00, 0, 30),
(19, 4, 'Quatro', '', 130.00, 0, 20),
(20, 4, 'Sinco', '', 145.00, 0, 15),
(21, 5, 'Uno', '', 95.00, 0, 49),
(22, 5, 'Dos', '', 125.00, 0, 40),
(23, 5, 'Tres', '', 100.00, 0, 30),
(24, 5, 'Quatro', '', 135.00, 0, 20),
(25, 5, 'Sinco', '', 150.00, 0, 15),
(26, 6, 'Uno', '', 100.00, 0, 50),
(27, 6, 'Dos', '', 130.00, 0, 40),
(28, 6, 'Tres', '', 100.00, 0, 30),
(29, 6, 'Quatro', '', 135.00, 0, 20),
(30, 6, 'Sinco', '', 150.00, 0, 15),
(31, 7, 'Uno', '', 100.00, 0, 50),
(32, 7, 'Dos', '', 130.00, 0, 40),
(33, 7, 'Tres', '', 105.00, 0, 30),
(34, 7, 'Quatro', '', 140.00, 0, 20),
(35, 7, 'Sinco', '', 155.00, 0, 15),
(36, 10, 'Uno', 'Hot', 60.00, 0, 10),
(37, 10, 'Dos', 'Hot', 70.00, 0, 2),
(41, 11, 'Small', 'Normal', 50.00, 0, 9),
(42, 11, 'Medium', 'Normal', 60.00, 0, 10),
(44, 11, 'Large', 'Normal', 70.00, 0, 7),
(46, 11, 'Extra-Large', 'Normal', 100.00, 0, 9),
(47, 12, '8oz', 'Iced', 70.00, 0, 2),
(48, 12, '12oz', 'Iced', 100.00, 0, 1),
(49, 12, '16oz', 'Iced', 120.00, 0, 2),
(50, 13, '8oz', 'Hot', 70.00, 0, 10),
(51, 13, '12oz', 'Iced', 100.00, 0, 8),
(52, 13, '16oz', 'Iced', 120.00, 0, 7);

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
(2, NULL, 'Dine-in', 137, '2024-12-28 16:50:45', NULL, 515.00, 2, 'Cash', NULL, 'Preparing'),
(3, NULL, 'Dine-in', 768, '2024-12-28 17:26:29', NULL, 190.00, 3, 'Cash', NULL, 'Completed'),
(4, NULL, 'Take-out', 129, '2024-12-28 17:33:29', NULL, 180.00, 4, 'GCash', NULL, 'Completed'),
(5, NULL, 'Dine-in', 300, '2024-12-28 17:34:18', NULL, 200.00, 5, 'Cash', NULL, 'Completed'),
(6, NULL, 'Dine-in', 572, '2024-12-28 19:06:27', NULL, 200.00, 6, 'Cash', NULL, 'Completed'),
(7, NULL, 'Take-out', 222, '2024-12-28 19:07:34', NULL, 375.00, 7, 'Cash', NULL, 'Pending'),
(8, NULL, 'Take-out', 410, '2024-12-28 19:22:49', NULL, 300.00, 8, 'GCash', NULL, 'Pending'),
(9, NULL, 'Dine-in', 757, '2024-12-28 19:33:06', NULL, 650.00, 9, 'GCash', NULL, 'Pending'),
(10, NULL, 'Dine-in', 924, '2024-12-28 19:36:57', NULL, 580.00, 10, 'GCash', NULL, 'Preparing'),
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
(22, NULL, 'Take-out', 637, '2025-01-03 05:18:34', '2025-01-03 05:19:30', 200.00, 22, 'GCash', NULL, 'ReadyToClaim'),
(23, NULL, 'Dine-in', 233, '2025-01-03 05:21:14', '2025-01-03 05:22:14', 220.00, 23, 'Cash', NULL, 'ReadyToClaim');

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
(36, 23, 11, 'Extra-Large', 1, 100.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Payment_DateTime` datetime NOT NULL,
  `Order_TotalAmount` decimal(10,2) NOT NULL,
  `Payment_DiscountType` varchar(50) DEFAULT NULL,
  `Payment_DiscountAmount` decimal(10,2) DEFAULT 0.00,
  `Payment_TotalAmount` decimal(10,2) NOT NULL,
  `Payment_Status` enum('Pending','Completed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Payment_Method`, `Payment_DateTime`, `Order_TotalAmount`, `Payment_DiscountType`, `Payment_DiscountAmount`, `Payment_TotalAmount`, `Payment_Status`) VALUES
(1, 'Cash', '2024-12-28 16:41:08', 100.00, NULL, 0.00, 100.00, 'Pending'),
(2, 'Cash', '2024-12-28 20:54:01', 515.00, NULL, 0.00, 515.00, 'Completed'),
(3, 'Cash', '2024-12-28 17:26:29', 190.00, NULL, 0.00, 190.00, 'Completed'),
(4, 'GCash', '2024-12-28 22:12:08', 180.00, NULL, 0.00, 180.00, 'Completed'),
(5, 'Cash', '2024-12-28 17:34:18', 200.00, NULL, 0.00, 200.00, 'Pending'),
(6, 'Cash', '2024-12-28 19:57:58', 200.00, NULL, 0.00, 200.00, 'Completed'),
(7, 'Cash', '2024-12-28 20:40:04', 375.00, NULL, 0.00, 375.00, 'Completed'),
(8, 'GCash', '2024-12-28 20:36:12', 300.00, NULL, 0.00, 300.00, 'Completed'),
(9, 'GCash', '2024-12-28 20:32:32', 650.00, NULL, 0.00, 650.00, 'Completed'),
(10, 'GCash', '2024-12-28 20:50:15', 580.00, NULL, 0.00, 580.00, 'Completed'),
(11, 'Cash', '2024-12-28 21:49:33', 120.00, NULL, 0.00, 120.00, 'Cancelled'),
(12, 'Cash', '2024-12-28 21:53:09', 150.00, NULL, 0.00, 150.00, 'Completed'),
(13, 'Cash', '2025-01-02 22:40:22', 280.00, NULL, 0.00, 280.00, 'Completed'),
(14, 'Cash', '2025-01-02 22:54:41', 300.00, NULL, 0.00, 300.00, 'Completed'),
(15, 'GCash', '2025-01-02 22:57:09', 300.00, NULL, 0.00, 300.00, 'Completed'),
(16, 'GCash', '2025-01-02 23:14:20', 510.00, NULL, 0.00, 510.00, 'Completed'),
(17, 'GCash', '2025-01-02 23:17:46', 140.00, NULL, 0.00, 140.00, 'Completed'),
(18, 'GCash', '2025-01-03 00:08:38', 95.00, NULL, 0.00, 95.00, 'Completed'),
(19, 'GCash', '2025-01-03 04:21:09', 340.00, NULL, 0.00, 340.00, 'Completed'),
(20, 'GCash', '2025-01-03 04:29:49', 400.00, NULL, 0.00, 400.00, 'Completed'),
(21, 'Cash', '2025-01-03 05:11:48', 120.00, NULL, 0.00, 120.00, 'Completed'),
(22, 'GCash', '2025-01-03 05:19:01', 200.00, NULL, 0.00, 200.00, 'Completed'),
(23, 'Cash', '2025-01-03 05:21:48', 220.00, NULL, 0.00, 220.00, 'Completed');

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
(2, 'jehu_staff', '$2y$10$DReShyUhE8AT0jgnn3Iph.9FtONWMyjG8U1KodJUcQfaLa02UZm.C', 'Jehu', 'Ferrer', 'Galvez', '09123456789', 'jehu@staff.com', 'Star Apple Street, Barangay 178, Caloocan City', '2001-09-13', 'Admin', 'Active');

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
  MODIFY `Feedback_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `MenuItem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  MODIFY `MenuItemSize_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orderitem`
--
ALTER TABLE `orderitem`
  MODIFY `OrderItem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `Staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
