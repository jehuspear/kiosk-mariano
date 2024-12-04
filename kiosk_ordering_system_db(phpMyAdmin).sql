-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2024 at 03:50 PM
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
  `MenuItem_TotalSold` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem`
--

INSERT INTO `menuitem` (`MenuItem_ID`, `MenuItem_Name`, `MenuItem_Image`, `MenuItem_Description`, `MenuItem_Category`, `MenuItem_TotalStocks`, `MenuItem_TotalSold`) VALUES
(1, 'Kape Amerikano', 'Images/menu-item/kape-americano.jpg', 'Full-bodied espresso with hot water', 'Coffee', 200, 3),
(2, 'Latte de Kape', 'Images/menu-item/latte-de-kape.jpg', 'Espresso with steamed milk and a thin layer of foam', 'Coffee', 150, 0),
(3, 'Cappuccino', 'Images/menu-item/cappuccino.jpg', 'Espresso with steamed milk and a thick layer of foam', 'Coffee', 120, 0),
(4, 'Latte Espanyol', 'Images/menu-item/latte-espanyol.jpg', 'Espresso combined with milk and a hint of condensed milk', 'Coffee', 100, 0),
(5, "Kape Dulce\'t Salty Caramelo", 'Images/menu-item/kape-dulce-salty-caramelo.jpg', 'Espresso combined with milk, infused with salted caramel syrup', 'Coffee', 80, 0),
(6, 'Kape con Canela', 'Images/menu-item/kape-con-canela.jpg', 'Espresso combined with steamed milk and infused with honey and cinnamon', 'Coffee', 60, 0),
(7, 'Kape de Cacao', 'Images/menu-item/kape-de-cacao.jpg', 'Espresso combined with steamed milk and rich chocolate syrup', 'Coffee', 40, 0),
(10, 'Espresso (Doppio)', 'Images/menu-items/Doppio_Espresso_Macchiato.jpg', 'Sample Coffee', 'Traditional Coffee', 20, 0);

-- --------------------------------------------------------

--
-- Table structure for table `menuitem_sizes`
--

CREATE TABLE `menuitem_sizes` (
  `MenuItemSize_ID` int(11) NOT NULL,
  `MenuItem_ID` int(11) NOT NULL,
  `MenuItemSize_Size` varchar(10) NOT NULL,
  `MenuItemSize_IsHot` tinyint(1) NOT NULL,
  `MenuItemSize_Price` decimal(10,2) NOT NULL,
  `MenuItemSize_Sold` int(11) NOT NULL,
  `MenuItemSize_Stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem_sizes`
--

INSERT INTO `menuitem_sizes` (`MenuItemSize_ID`, `MenuItem_ID`, `MenuItemSize_Size`, `MenuItemSize_IsHot`, `MenuItemSize_Price`, `MenuItemSize_Sold`, `MenuItemSize_Stock`) VALUES
(1, 1, 'Uno', 1, 60.00, 3, 50),
(2, 1, 'Dos', 1, 75.00, 0, 40),
(3, 1, 'Tres', 0, 85.00, 0, 30),
(4, 1, 'Quatro', 0, 100.00, 0, 20),
(5, 1, 'Sinco', 0, 110.00, 0, 15),
(6, 2, 'Uno', 1, 80.00, 0, 50),
(7, 2, 'Dos', 1, 105.00, 0, 40),
(8, 2, 'Tres', 0, 90.00, 0, 30),
(9, 2, 'Quatro', 0, 125.00, 0, 20),
(10, 2, 'Sinco', 0, 135.00, 0, 15),
(11, 3, 'Uno', 1, 85.00, 0, 50),
(12, 3, 'Dos', 1, 105.00, 0, 40),
(13, 3, 'Tres', 0, 90.00, 0, 30),
(14, 3, 'Quatro', 0, 125.00, 0, 20),
(15, 3, 'Sinco', 0, 135.00, 0, 15),
(16, 4, 'Uno', 1, 90.00, 0, 50),
(17, 4, 'Dos', 1, 120.00, 0, 40),
(18, 4, 'Tres', 0, 95.00, 0, 30),
(19, 4, 'Quatro', 0, 130.00, 0, 20),
(20, 4, 'Sinco', 0, 145.00, 0, 15),
(21, 5, 'Uno', 1, 95.00, 0, 50),
(22, 5, 'Dos', 1, 125.00, 0, 40),
(23, 5, 'Tres', 0, 100.00, 0, 30),
(24, 5, 'Quatro', 0, 135.00, 0, 20),
(25, 5, 'Sinco', 0, 150.00, 0, 15),
(26, 6, 'Uno', 1, 100.00, 0, 50),
(27, 6, 'Dos', 1, 130.00, 0, 40),
(28, 6, 'Tres', 0, 100.00, 0, 30),
(29, 6, 'Quatro', 0, 135.00, 0, 20),
(30, 6, 'Sinco', 0, 150.00, 0, 15),
(31, 7, 'Uno', 1, 100.00, 0, 50),
(32, 7, 'Dos', 1, 130.00, 0, 40),
(33, 7, 'Tres', 0, 105.00, 0, 30),
(34, 7, 'Quatro', 0, 140.00, 0, 20),
(35, 7, 'Sinco', 0, 155.00, 0, 15),
(36, 10, 'Uno', 1, 60.00, 0, 10),
(37, 10, 'Dos', 1, 70.00, 0, 2),
(38, 10, 'Tres', 0, 0.00, 0, 0),
(39, 10, 'Quatro', 0, 0.00, 0, 0),
(40, 10, 'Sinco', 0, 0.00, 0, 0);

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
  `Order_TotalAmount` decimal(10,2) NOT NULL,
  `Payment_ID` int(11) DEFAULT NULL,
  `Payment_Method` varchar(50) DEFAULT NULL,
  `Staff_ID` int(11) DEFAULT NULL,
  `Order_Status` enum('Pending','Completed','Cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

CREATE TABLE `orderitem` (
  `OrderItem_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `MenuItem_ID` int(11) NOT NULL,
  `OrderItem_CupSize` enum('12oz','16oz','22oz') DEFAULT NULL,
  `OrderItem_Quantity` int(11) NOT NULL,
  `OrderItem_Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `Staff_Role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Staff_ID`, `Staff_Username`, `Staff_Password`, `Staff_FirstName`, `Staff_MiddleName`, `Staff_LastName`, `Staff_ContactNumber`, `Staff_Email`, `Staff_Address`, `Staff_BirthDate`, `Staff_Role`) VALUES
(1, 'admin_tan', '$2y$10$Ui7nbTCCCwB18q0DyzTYOegtToXPOy80T/O54Yk5kL84te4S/VWru', 'Clarence', 'Allanson', 'Tan', '09123456789', 'clarence@admin.com', 'Merry Homes, Barangay 178, Caloocan City', '2003-06-12', 'Staff');

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
  ADD UNIQUE KEY `Staff_Username` (`Staff_Username`);

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
  MODIFY `Log_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `MenuItem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menuitem_sizes`
--
ALTER TABLE `menuitem_sizes`
  MODIFY `MenuItemSize_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orderitem`
--
ALTER TABLE `orderitem`
  MODIFY `OrderItem_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `Staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
