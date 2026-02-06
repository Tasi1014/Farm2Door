-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 06:51 PM
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
-- Database: `farm2door`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `quantity`, `created_at`) VALUES
(103, 5, 25, 1, '2026-02-06 04:42:28');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `id` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`id`, `Name`, `Email`, `Message`) VALUES
(1, 'Tashi Sherpa', 'sherpajack3@gmail.com', 'Hello i wanted to register for the farmer what are the steps can you please guide me'),
(2, 'Messi', 'messitasi10@gmail.com', 'Hello Php'),
(3, 'Tasi Sherpa', 'nehajack3@gmail.com', 'sf'),
(4, 'Reshma', 'sherpajack3@gmailcom', 'hello'),
(5, 'Neha Mahto', 'neha333@gmail.com', 'Hello '),
(14, 'Neha Mahto', 'mahtoneha2555@gmail.com', 'Hello i wanted to know more about Farm2Door'),
(15, 'Tasi Sherpa', 'pasangbca23@oic.edu.np', 'Hello i wanted to know more about Farm2Door'),
(16, 'Tasi Sherpa', 'mahtoneha2555@gmail.com', 'hello'),
(17, 'Tasi Sherpa', 'mahtoneha2555@gmail.com', 'Heklsfv'),
(18, 'Tasi Sherpa', 'mahtoneha2555@gmail.com', 'Heklsfv'),
(19, 'Tasi Sherpa', 'mahtoneha2555@gmail.com', 'Heklsfv'),
(20, 'Tashi ', 'pasangbca23@oic.edu.np', 'hello'),
(21, 'Neha Mahto', 'mahtoneha2555@gmail.com', 'sdfdg'),
(22, 'Ram Karki', 'pasangbca23@oic.edu.np', 'Improved the SMPT user feedbackdelay by loading the task ni background'),
(23, 'Messi', 'pasangbca23@oic.edu.np', 'Hello i wanted to know more about your project\\r\\n'),
(24, 'Tasi Sherpa', 'pasangbca23@oic.edu.np', 'Hello'),
(25, 'Neha Mahto', 'pasangbca23@oic.edu.np', 'Hello i wanted to know more about you guys'),
(26, 'Tasi Sherpa', 'pasangbca23@oic.edu.np', 'hi'),
(27, 'Tasi Sherpa', 'pasangbca23@oic.edu.np', 'Hi'),
(28, 'Neha Mahto', 'mahtoneha2555@gmail.com', 'hi'),
(29, 'Neha Mahto', 'mahtoneha2555@gmail.com', 'Hi'),
(30, 'Tomatoes', 'sherpajack3@gmailcom', 'hello\\r\\n'),
(31, 'Tomatoes', 'sherpajack3@gmailcom', 'hello\\r\\n'),
(32, 'Tasi Sherpa', 'sherpajack3@gmail.com', 'Hello'),
(33, 'Neha Mahto', 'mahtoneha2555@gmail.com', 'Hello i wanted to know how to register '),
(34, 'anmol jogi', 'sherpajack3@gmail.com', 'hieee chor');

-- --------------------------------------------------------

--
-- Table structure for table `customer_registration`
--

CREATE TABLE `customer_registration` (
  `id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Phone` int(11) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Terms` tinyint(1) NOT NULL,
  `status` enum('active','blocked') DEFAULT 'active',
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_registration`
--

INSERT INTO `customer_registration` (`id`, `firstName`, `lastName`, `Email`, `Phone`, `Address`, `Password`, `Terms`, `status`, `reset_token`, `reset_expires`) VALUES
(2, 'Tasi', 'Sherpa', 'sherpajack3@gmail.com', 2147483647, 'Kapan, Aakashedhara', '$2y$10$Y87uZKryPSRNLw.SSofOdejl/PL/aoPLsD4rj9GLGck5pQlQLnLEW', 1, 'active', NULL, NULL),
(4, 'Neha', 'Mahto', 'rappid56@gmail.com', 2147483647, 'gatthaghar', '$2y$10$0eKGtHdHTNND4N/pgZPTP.CdJK1H7TnhTMn6uOvPtjZvJrVX4TxXq', 1, 'active', NULL, NULL),
(5, 'Neha', 'Mahto', 'mahtoneha2555@gmail.com', 2147483647, 'Bhaktapur, Gatthaghar', '$2y$10$9lT0Lxeln3PmurUqkzh.E.32UtxJLUES9Yq5gFlv8zZa1eHgbIJga', 1, 'active', NULL, NULL),
(6, 'Pasang', 'Tasi Sherpa', 'pasangbca23@oic.edu.np', 2147483647, 'kapan, Aakashedhara', '$2y$10$/JicFmEOOmABJlfp3lD/DOLT7pcAHSAVwwcZ5l2Chd0awNgBodt22', 1, 'active', NULL, NULL),
(7, 'Namrata', 'Bomjan', 'namratabca23@oic.edu.np', 2147483647, 'Pepsicola, Lalitpur', '$2y$10$GUoIqN/QjHyWhjCy6ne6eecF2uqbTE4irly/qxk7ESXxALY7A8aJi', 1, 'active', NULL, NULL),
(8, 'Bimal', 'Sherpa', 'sherpabimal09@gmail.com', 2147483647, 'kapan, Aakashedhara', '$2y$10$mdAjzgAtVOmNhD.7ORA./Owuj2yjyxZ67oMjI/EISg07Scop/WhNi', 1, 'blocked', NULL, NULL),
(9, 'Ram', 'Khatri', 'ram@gmail.com', 2147483647, 'Kapan, Aakashedhara', '$2y$10$fYHq32zQKbci4Uq6Pv.SnOaMV0R4I47uT3d38Ks0hhHWm23DNH.1C', 1, 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_registration`
--

CREATE TABLE `farmer_registration` (
  `farmer_id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Terms` tinyint(1) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('active','blocked') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmer_registration`
--

INSERT INTO `farmer_registration` (`farmer_id`, `firstName`, `lastName`, `Email`, `Password`, `Phone`, `Address`, `Terms`, `created_at`, `status`) VALUES
(1, 'Tashi', 'Sherpa', 'sherpajack3@gmail.com', '$2y$10$aUVNr5V5LKiB0UBbncctmef4TsLb39dPvhCDIs..MzPL4mZUpkmyq', '9803901467', 'Kapan, Aakashedhara', 1, '2025-11-22 21:51:42', 'blocked'),
(2, 'Neha', 'Mahto', 'mahtoneha2555@gmail.com', '$2y$10$MgNXX8GqHDJpV2QG8xHY/eS5qj0sqEFtfSit90/qjiRG5aRe9xQmO', '9823782211', 'Gatthaghar', 1, '2025-12-15 17:43:08', 'active'),
(4, 'Anmol', 'Jogi', 'anmol11@gmail.com', '$2y$10$AcBtaIQp9KarytV/5/CW8eQlnwogI6qD5J2RFiI53uTDsSLIRTuTe', '9803301576', 'Mandikatar kapan', 1, '2025-12-19 11:10:23', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` enum('Pending','Processing','Dispatched','Received','Ready for Pickup','Fulfilled','Cancelled','Rejected') DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `order_status`, `order_date`, `shipping_name`, `shipping_phone`, `shipping_address`, `shipping_notes`, `rejection_reason`, `cancellation_reason`) VALUES
(13, 2, 255.00, 'Fulfilled', '2025-12-19 17:12:47', 'Tasi Sherpa', '9803901467', 'Kapan, Aakashedhara', 'Call me when reached Nepal Bank', NULL, NULL),
(14, 2, 55.00, 'Cancelled', '2025-12-20 13:42:41', 'Tasi Sherpa', '9823712211', 'Gatthaghar Bus Stop', 'Call me when you reached Neha Suddha sakahari sweet and chat House', NULL, NULL),
(15, 2, 55.00, 'Fulfilled', '2025-12-21 02:18:56', 'Tasi Sherpa', '9803901467', 'Kapan, Aakashedhara', 'Call me when you reach Neha chat house', NULL, NULL),
(16, 5, 120.00, 'Rejected', '2025-12-21 02:29:03', 'Neha Mahto', '2147483647', 'Bhaktapur, Gatthaghar', 'Call me when you reached the location', 'No stock', NULL),
(18, 2, 55.00, 'Fulfilled', '2025-12-21 05:58:04', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(19, 2, 80.00, 'Fulfilled', '2025-12-21 09:39:56', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(20, 2, 120.00, 'Fulfilled', '2025-12-21 10:48:04', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(21, 6, 120.00, 'Fulfilled', '2025-12-21 10:50:07', 'Pasang Tasi Sherpa', '9803901467', 'kapan, Aakashedhara', '', NULL, NULL),
(22, 6, 160.00, 'Ready for Pickup', '2025-12-21 12:39:27', 'Pasang Tasi Sherpa', '9818469725', 'kapan, Aakashedhara', 'Call me when you reached Nabil Bank', NULL, NULL),
(23, 2, 55.00, 'Cancelled', '2025-12-21 16:24:18', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', 'Cancelled by customer', NULL),
(24, 2, 55.00, 'Cancelled', '2025-12-21 16:39:25', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, 'Cancelled by customer'),
(25, 2, 80.00, 'Cancelled', '2025-12-21 16:42:10', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, 'Cancelled by customer'),
(26, 2, 55.00, 'Fulfilled', '2025-12-21 16:43:37', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(27, 2, 55.00, 'Cancelled', '2025-12-21 17:09:28', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', 'Cancelled by customer', NULL),
(28, 2, 165.00, 'Cancelled', '2025-12-21 17:40:43', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, 'Cancelled by customer'),
(29, 2, 95.00, 'Fulfilled', '2025-12-21 17:59:37', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(30, 2, 175.00, 'Rejected', '2025-12-21 18:12:45', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', 'No stock', NULL),
(31, 2, 120.00, 'Rejected', '2025-12-21 18:14:30', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', 'No stock', NULL),
(32, 2, 380.00, 'Cancelled', '2025-12-22 04:59:40', 'Tasi Sherpa', '9803901467', 'Kapan, Aakashedhara', 'Call me when you reached Nabil Bank', NULL, 'Cancelled by customer'),
(33, 2, 190.00, 'Cancelled', '2025-12-22 05:29:16', 'Tasi Sherpa', '9803301576', 'Kapan, Aakashedhara', 'Near Nepal Bank', NULL, 'Cancelled by customer'),
(34, 2, 80.00, 'Fulfilled', '2025-12-22 05:35:38', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(35, 2, 270.00, 'Rejected', '2025-12-22 05:55:56', 'Tasi Sherpa', '9813132648', 'Kapan, Aakashedhara', 'Nepal Bank', 'No Stock', NULL),
(36, 2, 215.00, 'Fulfilled', '2025-12-22 06:25:33', 'Tasi Sherpa', '9803901467', 'Kapan, Aakashedhara', 'Near Nabil Bank', NULL, NULL),
(37, 2, 95.00, 'Cancelled', '2025-12-22 06:36:18', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, 'Cancelled by customer'),
(38, 2, 120.00, 'Ready for Pickup', '2025-12-22 15:47:40', 'Tasi Sherpa', '9803901467', 'Kapan, Aakashedhara', 'Near Prabhu Bank', NULL, NULL),
(39, 2, 150.00, 'Ready for Pickup', '2025-12-24 04:47:10', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(40, 2, 150.00, 'Fulfilled', '2025-12-24 04:58:24', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(41, 2, 200.00, 'Fulfilled', '2025-12-24 14:23:41', 'Tasi Sherpa', '9822334455', 'Kapan, Aakashedhara', 'Near Nabil Bank', NULL, NULL),
(42, 2, 340.00, 'Fulfilled', '2025-12-24 14:37:15', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(52, 2, 160.00, 'Fulfilled', '2025-12-01 04:30:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(53, 2, 215.00, 'Fulfilled', '2025-12-03 05:45:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(54, 2, 320.00, 'Fulfilled', '2025-12-06 04:00:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(55, 2, 275.00, 'Fulfilled', '2025-12-09 08:25:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(56, 2, 320.00, 'Fulfilled', '2025-12-12 10:35:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(57, 2, 275.00, 'Fulfilled', '2025-12-15 06:20:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(58, 2, 400.00, 'Fulfilled', '2025-12-18 12:45:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(59, 2, 330.00, 'Fulfilled', '2025-12-21 05:05:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(60, 2, 320.00, 'Fulfilled', '2025-12-24 09:55:00', 'Test Customer', '9800000000', 'Kathmandu', NULL, NULL, NULL),
(61, 5, 330.00, 'Fulfilled', '2025-12-26 13:06:34', 'Neha Mahto', '9823782211', 'Bhaktapur, Gatthaghar', '', NULL, NULL),
(62, 5, 550.00, 'Fulfilled', '2025-12-26 13:07:14', 'Neha Mahto', '2147483647', 'Bhaktapur, Gatthaghar', '', NULL, NULL),
(63, 6, 600.00, 'Fulfilled', '2025-12-26 13:09:43', 'Pasang Tasi Sherpa', '2147483647', 'kapan, Aakashedhara', '', NULL, NULL),
(64, 6, 600.00, 'Fulfilled', '2025-12-26 13:10:03', 'Pasang Tasi Sherpa', '2147483647', 'kapan, Aakashedhara', '', NULL, NULL),
(65, 7, 670.00, 'Fulfilled', '2025-12-27 07:20:57', 'Namrata Bomjan', '9823623830', 'Pepsicola, Lalitpur', '', NULL, NULL),
(66, 2, 320.00, 'Fulfilled', '2025-12-28 08:08:46', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(68, 2, 280.00, 'Cancelled', '2025-12-28 10:25:39', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, 'Cancelled by customer'),
(69, 2, 500.00, 'Fulfilled', '2025-12-28 14:58:06', 'Tasi Sherpa', '9823782211', 'Kapan, Aakashedhara', 'Call me when reached nehachathouse', NULL, NULL),
(70, 5, 440.00, 'Pending', '2025-12-29 03:10:37', 'Neha Mahto', '9823782211', 'Bhaktapur, Gatthaghar', '', NULL, NULL),
(71, 5, 150.00, 'Fulfilled', '2025-12-29 04:16:53', 'Neha Mahto', '2147483647', 'Bhaktapur, Gatthaghar', '', NULL, NULL),
(72, 8, 200.00, 'Rejected', '2026-01-10 11:50:07', 'Bimal Sherpa', '2147483647', 'kapan, Aakashedhara', '', 'Out of Stock', NULL),
(73, 2, 200.00, 'Cancelled', '2026-02-05 12:08:18', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, 'Cancelled by customer'),
(74, 2, 350.00, 'Fulfilled', '2026-02-05 16:53:34', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL),
(75, 5, 140.00, 'Pending', '2026-02-06 04:20:36', 'Neha Mahto', '2147483647', 'Bhaktapur, Gatthaghar', '', NULL, NULL),
(76, 2, 660.00, 'Pending', '2026-02-06 13:00:18', 'Tasi Sherpa', '2147483647', 'Kapan, Aakashedhara', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `farmer_id`, `quantity`, `price_per_unit`, `subtotal`) VALUES
(13, 13, 2, 1, 1, 80.00, 80.00),
(14, 13, 8, 1, 1, 55.00, 55.00),
(15, 13, 9, 4, 1, 120.00, 120.00),
(16, 14, 8, 1, 1, 55.00, 55.00),
(17, 15, 8, 1, 1, 55.00, 55.00),
(18, 16, 9, 4, 1, 120.00, 120.00),
(20, 18, 8, 1, 1, 55.00, 55.00),
(21, 19, 2, 1, 1, 80.00, 80.00),
(22, 20, 9, 4, 1, 120.00, 120.00),
(23, 21, 9, 4, 1, 120.00, 120.00),
(24, 22, 2, 1, 2, 80.00, 160.00),
(25, 23, 8, 1, 1, 55.00, 55.00),
(26, 24, 8, 1, 1, 55.00, 55.00),
(27, 25, 2, 1, 1, 80.00, 80.00),
(28, 26, 8, 1, 1, 55.00, 55.00),
(29, 27, 8, 1, 1, 55.00, 55.00),
(30, 28, 8, 1, 3, 55.00, 165.00),
(31, 29, 10, 1, 1, 95.00, 95.00),
(32, 30, 8, 1, 1, 55.00, 55.00),
(33, 30, 9, 4, 1, 120.00, 120.00),
(34, 31, 9, 4, 1, 120.00, 120.00),
(35, 32, 10, 1, 4, 95.00, 380.00),
(36, 33, 10, 1, 2, 95.00, 190.00),
(37, 34, 2, 1, 1, 80.00, 80.00),
(38, 35, 10, 1, 2, 95.00, 190.00),
(39, 35, 2, 1, 1, 80.00, 80.00),
(40, 36, 10, 1, 1, 95.00, 95.00),
(41, 36, 9, 4, 1, 120.00, 120.00),
(42, 37, 10, 1, 1, 95.00, 95.00),
(43, 38, 9, 4, 1, 120.00, 120.00),
(44, 39, 26, 2, 1, 150.00, 150.00),
(45, 40, 26, 2, 1, 150.00, 150.00),
(46, 41, 25, 2, 1, 200.00, 200.00),
(47, 42, 25, 2, 1, 200.00, 200.00),
(48, 42, 23, 2, 1, 140.00, 140.00),
(67, 52, 2, 1, 2, 80.00, 160.00),
(68, 53, 2, 1, 1, 80.00, 80.00),
(69, 53, 8, 1, 1, 55.00, 55.00),
(70, 54, 8, 1, 2, 55.00, 110.00),
(71, 54, 9, 1, 1, 120.00, 120.00),
(72, 55, 10, 1, 1, 95.00, 95.00),
(73, 55, 2, 1, 1, 80.00, 80.00),
(74, 56, 11, 1, 1, 150.00, 150.00),
(75, 56, 8, 1, 1, 55.00, 55.00),
(76, 57, 9, 1, 1, 120.00, 120.00),
(77, 57, 8, 1, 1, 55.00, 55.00),
(78, 58, 2, 1, 2, 80.00, 160.00),
(79, 58, 11, 1, 1, 150.00, 150.00),
(80, 59, 2, 1, 1, 80.00, 80.00),
(81, 59, 8, 1, 2, 55.00, 110.00),
(82, 60, 9, 1, 2, 120.00, 240.00),
(83, 60, 8, 1, 1, 55.00, 55.00),
(84, 61, 8, 1, 6, 55.00, 330.00),
(85, 62, 8, 1, 10, 55.00, 550.00),
(86, 63, 20, 4, 5, 120.00, 600.00),
(87, 64, 20, 4, 5, 120.00, 600.00),
(88, 65, 20, 4, 1, 120.00, 120.00),
(89, 65, 21, 2, 2, 220.00, 440.00),
(90, 65, 8, 1, 2, 55.00, 110.00),
(91, 66, 22, 2, 1, 100.00, 100.00),
(92, 66, 21, 2, 1, 220.00, 220.00),
(94, 68, 23, 2, 2, 140.00, 280.00),
(95, 69, 25, 2, 1, 200.00, 200.00),
(96, 69, 26, 2, 2, 150.00, 300.00),
(97, 70, 25, 2, 1, 200.00, 200.00),
(98, 70, 9, 4, 2, 120.00, 240.00),
(99, 71, 26, 2, 1, 150.00, 150.00),
(100, 72, 25, 2, 1, 200.00, 200.00),
(101, 73, 25, 2, 1, 200.00, 200.00),
(102, 74, 26, 2, 1, 150.00, 150.00),
(103, 74, 25, 2, 1, 200.00, 200.00),
(104, 75, 23, 2, 1, 140.00, 140.00),
(105, 76, 26, 2, 1, 150.00, 150.00),
(106, 76, 25, 2, 1, 200.00, 200.00),
(107, 76, 24, 2, 1, 170.00, 170.00),
(108, 76, 23, 2, 1, 140.00, 140.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_logs`
--

CREATE TABLE `order_status_logs` (
  `log_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `actor_type` enum('Farmer','Admin','Customer') NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_logs`
--

INSERT INTO `order_status_logs` (`log_id`, `order_id`, `old_status`, `new_status`, `actor_type`, `actor_id`, `rejection_reason`, `timestamp`) VALUES
(1, 18, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 09:53:35'),
(2, 18, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 10:22:04'),
(3, 18, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:22:39'),
(4, 18, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-21 10:22:56'),
(5, 19, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-21 10:24:37'),
(6, 19, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 10:27:36'),
(7, 19, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 10:27:58'),
(8, 19, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:28:04'),
(9, 19, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-21 10:28:12'),
(10, 13, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 10:33:08'),
(11, 13, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 10:33:44'),
(12, 16, 'Pending', 'Rejected', 'Farmer', 4, 'No stock', '2025-12-21 10:51:28'),
(13, 21, 'Pending', 'Processing', 'Farmer', 4, NULL, '2025-12-21 10:52:25'),
(14, 21, 'Processing', 'Dispatched', 'Farmer', 4, NULL, '2025-12-21 10:52:43'),
(15, 21, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 10:52:50'),
(16, 21, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:55:48'),
(17, 21, 'Ready for Pickup', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:55:54'),
(18, 21, 'Ready for Pickup', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:55:54'),
(19, 21, 'Ready for Pickup', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:55:54'),
(20, 21, 'Ready for Pickup', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 10:55:54'),
(21, 20, 'Pending', 'Processing', 'Farmer', 4, NULL, '2025-12-21 11:03:43'),
(22, 20, 'Processing', 'Dispatched', 'Farmer', 4, NULL, '2025-12-21 11:04:38'),
(23, 20, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 11:05:31'),
(24, 20, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 11:06:06'),
(25, 20, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-21 11:06:38'),
(26, 13, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 11:11:59'),
(27, 14, 'Cancelled', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 49.5 (10% fee applied). Ref: REF-20251221-769C44DC', '2025-12-21 13:10:55'),
(28, 21, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-21 15:45:07'),
(29, 15, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 16:32:30'),
(30, 23, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-21 16:38:39'),
(31, 26, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-21 16:43:55'),
(32, 26, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 16:47:14'),
(33, 26, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 16:48:48'),
(34, 26, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 16:49:25'),
(35, 26, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-21 16:51:09'),
(36, 25, 'Processing', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-21 17:02:10'),
(37, 24, 'Processing', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-21 17:04:04'),
(38, 27, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-21 17:09:52'),
(39, 28, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-21 17:41:22'),
(40, 28, 'Cancelled', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 148.5 (10% fee applied). Ref: REF-20251221-82048250', '2025-12-21 17:46:02'),
(41, 15, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 17:47:09'),
(42, 15, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 17:47:14'),
(43, 22, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 17:50:44'),
(44, 22, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 17:50:55'),
(45, 22, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 17:51:00'),
(46, 29, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-21 18:01:03'),
(47, 29, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-21 18:01:21'),
(48, 29, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-21 18:01:44'),
(49, 29, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-21 18:01:58'),
(50, 31, 'Pending', 'Rejected', 'Farmer', 4, 'No stock', '2025-12-21 18:15:05'),
(51, 31, 'Rejected', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 108 (10% fee applied). Ref: REF-20251221-12CF7ADD', '2025-12-21 18:16:02'),
(52, 30, 'Pending', 'Rejected', 'Farmer', 4, 'No stock', '2025-12-21 18:18:03'),
(53, 32, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-22 05:03:14'),
(54, 32, 'Cancelled', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 342 (10% fee applied). Ref: REF-20251222-2DB615E8', '2025-12-22 05:04:34'),
(55, 33, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-22 05:31:54'),
(56, 33, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-22 05:32:49'),
(57, 34, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-22 05:35:55'),
(58, 34, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-22 05:36:02'),
(59, 34, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-22 05:36:28'),
(60, 34, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-22 05:36:44'),
(61, 29, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-22 05:38:36'),
(62, 35, 'Pending', 'Rejected', 'Farmer', 1, 'No Stock', '2025-12-22 05:56:40'),
(63, 35, 'Rejected', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 243 (10% fee applied). Ref: REF-20251222-9C783DEE', '2025-12-22 05:58:03'),
(64, 36, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-22 06:30:02'),
(65, 36, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-22 06:30:31'),
(66, 36, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-22 06:33:58'),
(67, 36, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-22 06:34:12'),
(68, 37, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-22 06:37:20'),
(69, 37, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-22 06:37:54'),
(70, 37, 'Cancelled', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 85.5 (10% fee applied). Ref: REF-20251222-3166EFFF', '2025-12-22 06:38:52'),
(71, 42, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-24 15:12:31'),
(72, 42, 'Processing', 'Processing', 'Farmer', 2, NULL, '2025-12-24 15:12:36'),
(73, 42, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-24 15:12:58'),
(74, 42, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-24 15:13:16'),
(75, 42, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-24 15:13:18'),
(76, 42, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-24 15:13:36'),
(77, 62, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-26 13:09:01'),
(78, 61, 'Pending', 'Processing', 'Farmer', 1, NULL, '2025-12-26 13:09:06'),
(79, 62, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-26 13:09:11'),
(80, 61, 'Processing', 'Dispatched', 'Farmer', 1, NULL, '2025-12-26 13:09:13'),
(81, 62, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-26 13:10:49'),
(82, 61, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-26 13:10:50'),
(83, 62, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-26 13:10:53'),
(84, 61, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-26 13:10:58'),
(85, 62, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-26 13:11:13'),
(86, 61, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-26 13:11:16'),
(87, 15, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-26 13:11:32'),
(88, 13, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-26 13:11:55'),
(89, 41, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-27 07:22:19'),
(90, 65, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-27 07:22:29'),
(91, 40, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-27 07:22:34'),
(92, 39, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-27 07:22:40'),
(93, 41, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:22:50'),
(94, 65, 'Processing', 'Processing', 'Farmer', 2, NULL, '2025-12-27 07:22:50'),
(95, 65, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:22:59'),
(96, 41, 'Dispatched', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:22:59'),
(97, 41, 'Dispatched', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:22:59'),
(98, 41, 'Dispatched', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:22:59'),
(99, 41, 'Dispatched', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:22:59'),
(100, 40, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:23:00'),
(101, 39, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-27 07:23:02'),
(102, 65, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:23:42'),
(103, 40, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:23:43'),
(104, 41, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:23:44'),
(105, 39, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:23:45'),
(106, 65, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-27 07:23:49'),
(107, 41, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-27 07:23:54'),
(108, 40, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-27 07:23:54'),
(109, 65, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:24:05'),
(110, 41, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:24:12'),
(111, 40, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:24:15'),
(112, 36, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:24:20'),
(113, 34, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:24:24'),
(114, 64, 'Pending', 'Processing', 'Farmer', 4, NULL, '2025-12-27 07:26:36'),
(115, 63, 'Pending', 'Processing', 'Farmer', 4, NULL, '2025-12-27 07:26:45'),
(116, 38, 'Pending', 'Processing', 'Farmer', 4, NULL, '2025-12-27 07:26:59'),
(117, 64, 'Processing', 'Dispatched', 'Farmer', 4, NULL, '2025-12-27 07:27:17'),
(118, 63, 'Processing', 'Dispatched', 'Farmer', 4, NULL, '2025-12-27 07:27:18'),
(119, 38, 'Processing', 'Dispatched', 'Farmer', 4, NULL, '2025-12-27 07:27:20'),
(120, 64, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:27:37'),
(121, 63, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:27:39'),
(122, 38, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-27 07:27:41'),
(123, 64, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-27 07:27:44'),
(124, 63, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-27 07:27:52'),
(125, 64, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:28:05'),
(126, 63, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-27 07:28:07'),
(127, 66, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-28 08:09:53'),
(128, 66, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-28 08:10:06'),
(129, 66, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-28 08:10:13'),
(130, 66, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-28 08:10:17'),
(131, 66, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-28 08:10:27'),
(134, 68, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-28 10:26:34'),
(135, 68, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2025-12-28 10:29:35'),
(136, 68, 'Cancelled', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 252 (10% fee applied). Ref: REF-20251228-7BBD31ED', '2025-12-28 10:30:17'),
(137, 69, 'Pending', 'Processing', 'Farmer', 2, NULL, '2025-12-28 15:00:00'),
(138, 69, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2025-12-28 15:00:08'),
(139, 69, 'Dispatched', 'Received', 'Admin', 1, NULL, '2025-12-28 15:00:34'),
(140, 69, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2025-12-28 15:00:37'),
(141, 69, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2025-12-28 15:00:46'),
(142, 72, 'Pending', 'Rejected', 'Farmer', 2, 'Out of Stock', '2026-01-10 11:53:11'),
(143, 72, 'Rejected', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 180 (10% fee applied). Ref: REF-20260110-B974C164', '2026-01-10 11:54:57'),
(144, 71, 'Pending', 'Processing', 'Farmer', 2, NULL, '2026-01-10 12:01:49'),
(145, 71, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2026-01-10 12:02:34'),
(146, 71, 'Dispatched', 'Received', 'Admin', 1, NULL, '2026-01-10 12:02:56'),
(147, 71, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2026-01-10 12:03:06'),
(148, 71, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2026-01-10 12:03:26'),
(149, 73, 'Pending', 'Processing', 'Farmer', 2, NULL, '2026-02-05 12:09:19'),
(150, 73, '0', 'Cancelled', 'Customer', 2, 'Cancelled by customer', '2026-02-05 12:10:22'),
(151, 73, 'Cancelled', 'Refunded', 'Admin', 1, 'Processed refund of Rs. 180 (10% fee applied). Ref: REF-20260205-C9EB766D', '2026-02-05 12:12:16'),
(152, 39, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2026-02-05 16:43:39'),
(153, 74, 'Pending', 'Processing', 'Farmer', 2, NULL, '2026-02-05 16:54:53'),
(154, 74, 'Processing', 'Dispatched', 'Farmer', 2, NULL, '2026-02-05 16:55:07'),
(155, 74, 'Dispatched', 'Received', 'Admin', 1, NULL, '2026-02-05 16:55:46'),
(156, 74, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2026-02-05 16:55:51'),
(157, 74, 'Ready for Pickup', 'Fulfilled', 'Admin', 1, NULL, '2026-02-05 16:56:07'),
(158, 38, 'Received', 'Ready for Pickup', 'Admin', 1, NULL, '2026-02-06 04:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD',
  `payment_status` enum('Pending','Paid','Failed','Refunded') DEFAULT 'Pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `payment_status`, `transaction_id`, `amount_paid`, `payment_date`) VALUES
(18, 13, 'ONLINE', 'Paid', '000DF4X', 255.00, '2025-12-19 17:12:47'),
(19, 14, 'ONLINE', 'Refunded', '000DF88', 55.00, '2025-12-20 13:42:41'),
(20, 15, 'ONLINE', 'Paid', '000DFAT', 55.00, '2025-12-21 02:18:56'),
(21, 16, 'COD', 'Pending', NULL, 0.00, '2025-12-21 02:29:03'),
(23, 18, 'ONLINE', 'Paid', '000DFBZ', 55.00, '2025-12-21 05:58:04'),
(24, 19, 'COD', 'Paid', NULL, 0.00, '2025-12-21 09:39:56'),
(25, 20, 'COD', 'Paid', NULL, 0.00, '2025-12-21 10:48:04'),
(26, 21, 'COD', 'Paid', NULL, 120.00, '2025-12-21 10:50:07'),
(27, 22, 'ONLINE', 'Paid', '000DFIX', 160.00, '2025-12-21 12:39:27'),
(28, 23, 'COD', 'Pending', NULL, 0.00, '2025-12-21 16:24:18'),
(29, 24, 'ONLINE', 'Refunded', '000DFKL', 55.00, '2025-12-21 16:39:25'),
(30, 25, 'ONLINE', 'Refunded', '000DFKM', 80.00, '2025-12-21 16:42:10'),
(31, 26, 'COD', 'Paid', NULL, 55.00, '2025-12-21 16:43:37'),
(32, 27, 'ONLINE', 'Refunded', '000DFKU', 55.00, '2025-12-21 17:09:28'),
(33, 28, 'ONLINE', 'Refunded', '000DFL4', 165.00, '2025-12-21 17:40:43'),
(34, 29, 'ONLINE', 'Paid', '000DFLS', 95.00, '2025-12-21 17:59:37'),
(35, 30, 'COD', 'Pending', NULL, 0.00, '2025-12-21 18:12:45'),
(36, 31, 'ONLINE', 'Refunded', '000DFLZ', 120.00, '2025-12-21 18:14:30'),
(37, 32, 'ONLINE', 'Refunded', '000DFUN', 380.00, '2025-12-22 04:59:40'),
(38, 33, 'COD', '', NULL, 0.00, '2025-12-22 05:29:16'),
(39, 34, 'COD', 'Paid', NULL, 80.00, '2025-12-22 05:35:38'),
(40, 35, 'ONLINE', 'Refunded', '000DFW3', 270.00, '2025-12-22 05:55:56'),
(41, 36, 'ONLINE', 'Paid', '000DFXA', 215.00, '2025-12-22 06:25:33'),
(42, 37, 'ONLINE', 'Refunded', '000DFXI', 95.00, '2025-12-22 06:36:18'),
(43, 38, 'COD', 'Pending', NULL, 0.00, '2025-12-22 15:47:40'),
(44, 39, 'COD', 'Pending', NULL, 0.00, '2025-12-24 04:47:10'),
(45, 40, 'COD', 'Paid', NULL, 150.00, '2025-12-24 04:58:24'),
(46, 41, 'ONLINE', 'Paid', '000DH3A', 200.00, '2025-12-24 14:23:42'),
(47, 42, 'COD', 'Paid', NULL, 340.00, '2025-12-24 14:37:15'),
(48, 52, 'COD', 'Paid', NULL, 160.00, '2025-12-01 04:30:00'),
(49, 53, 'COD', 'Paid', NULL, 215.00, '2025-12-03 05:45:00'),
(50, 54, 'COD', 'Paid', NULL, 320.00, '2025-12-06 04:00:00'),
(51, 55, 'COD', 'Paid', NULL, 275.00, '2025-12-09 08:25:00'),
(52, 56, 'COD', 'Paid', NULL, 320.00, '2025-12-12 10:35:00'),
(53, 57, 'COD', 'Paid', NULL, 275.00, '2025-12-15 06:20:00'),
(54, 58, 'COD', 'Paid', NULL, 400.00, '2025-12-18 12:45:00'),
(55, 59, 'COD', 'Paid', NULL, 330.00, '2025-12-21 05:05:00'),
(56, 60, 'COD', 'Paid', NULL, 320.00, '2025-12-24 09:55:00'),
(57, 61, 'COD', 'Paid', NULL, 330.00, '2025-12-26 13:06:34'),
(58, 62, 'COD', 'Paid', NULL, 550.00, '2025-12-26 13:07:14'),
(59, 63, 'COD', 'Paid', NULL, 600.00, '2025-12-26 13:09:43'),
(60, 64, 'COD', 'Paid', NULL, 600.00, '2025-12-26 13:10:03'),
(61, 65, 'COD', 'Paid', NULL, 670.00, '2025-12-27 07:20:57'),
(62, 66, 'COD', 'Paid', NULL, 320.00, '2025-12-28 08:08:46'),
(64, 68, 'ONLINE', 'Refunded', 'pi_3SjH9rRwvvwKRc8Q0KWCXW2y', 280.00, '2025-12-28 10:25:39'),
(65, 69, 'ONLINE', 'Paid', 'pi_3SjLPJRwvvwKRc8Q18P9PSWa', 500.00, '2025-12-28 14:58:06'),
(66, 70, 'ONLINE', 'Paid', 'pi_3SjWqMRwvvwKRc8Q057c7Svi', 440.00, '2025-12-29 03:10:37'),
(67, 71, 'ONLINE', 'Paid', 'pi_3SjXsVRwvvwKRc8Q0t22sn2u', 150.00, '2025-12-29 04:16:54'),
(68, 72, 'ONLINE', 'Refunded', 'pi_3So0fDRwvvwKRc8Q1epgknLQ', 200.00, '2026-01-10 11:50:07'),
(69, 73, 'ONLINE', 'Refunded', 'pi_3SxRK6RwvvwKRc8Q02EI6l7d', 200.00, '2026-02-05 12:08:18'),
(70, 74, 'COD', 'Paid', NULL, 350.00, '2026-02-05 16:53:34'),
(71, 75, 'ONLINE', 'Paid', 'pi_3SxgUzRwvvwKRc8Q0uk79Tok', 140.00, '2026-02-06 04:20:36'),
(72, 76, 'COD', 'Pending', NULL, 0.00, '2026-02-06 13:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `farmer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `threshold` int(11) DEFAULT 5,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `farmer_id`, `name`, `category`, `price`, `stock_quantity`, `threshold`, `description`, `image`, `created_at`) VALUES
(2, 1, 'Carrot', 'fruits', 80.00, 30, 5, 'Fresh Carrots', '307093611c273e428a38715e69ce8c70.webp', '2025-12-15 11:32:04'),
(8, 1, 'Tomatoes', 'fruits', 55.00, 17, 20, 'Fresh Organic Tomatoes', '86216febc2c40df4bfc9e5557338bf47.jpg', '2025-12-17 05:37:38'),
(9, 4, 'Potato', 'vegetables', 120.00, 36, 5, 'Fresh local organic Potatoes', 'eaf9b1660a9a20ee074430e0c30fb875.jpg', '2025-12-19 05:27:58'),
(10, 1, 'Cauliflower', 'vegetables', 95.00, 28, 29, 'Fresh Cauliflowers', '4a0a2246dd7f28ad4fe17f87ae5fe884.jpg', '2025-12-21 17:57:31'),
(11, 1, 'Mushroom', 'vegetables', 150.00, 20, 24, 'Fresh Mushrooms', '88a9716545ebcd70f067487af1e2686b.jpg', '2025-12-22 06:29:15'),
(12, 2, 'Mushroom', 'vegetables', 399.00, 20, 5, 'Fresh mushroom home grown', 'ac121f23fe61b66d2c08822e5bfb7c24.jpg', '2025-12-22 16:11:12'),
(13, 2, 'Carrot', 'vegetables', 149.00, 30, 5, 'Fresh Carrots No fertilizers used', '40218539b57e7ee45a83b6ae7a25ff20.jpg', '2025-12-22 16:11:52'),
(14, 2, 'Garlic', 'herbs', 349.00, 30, 5, 'Organic Garlic – Pure flavor, naturally grown.', '66b59fcdea7a30bc4026ec69a94c5a0d.jpg', '2025-12-22 16:13:57'),
(15, 2, 'Peas', 'vegetables', 99.00, 100, 5, 'Organic Peas – Fresh, sweet, and naturally grown.', '4d2ee4e7ac33a1c29b59d6c124b4be97.jpg', '2025-12-22 16:15:16'),
(16, 2, 'Capsicum', 'vegetables', 119.00, 30, 5, 'Organic Capsicum – colorful, and naturally grown', '803345d6c502f03bd3c0ee8a9c99a7fb.jpg', '2025-12-22 16:16:31'),
(17, 2, 'Cabbage', 'vegetables', 99.00, 30, 5, 'Organic Cabbage – Fresh and naturally grown.', '4a6a6763a24014edad5367d3aac5cd77.jpg', '2025-12-22 16:17:30'),
(18, 4, 'Bittergourd', 'vegetables', 70.00, 40, 5, 'Fresh BitterGourd', '5d4dfea9f59924a27603445cc48e188e.jpg', '2025-12-22 16:20:30'),
(19, 4, 'Okra', 'vegetables', 80.00, 50, 5, 'Fresh Okra', 'af20dfdcb91bbf3f0b75d672eceb0305.jpg', '2025-12-22 16:20:58'),
(20, 4, 'Onion', 'vegetables', 120.00, 59, 5, 'Fresh Organic Onion', '093ee64a8b95ac7d86fa3f1c1620cafe.jpg', '2025-12-22 16:21:32'),
(21, 2, 'Ginger', 'vegetables', 220.00, 57, 5, 'Fresh Organic Ginger', 'ff22d08c8a0d18255695140f1df8949d.jpg', '2025-12-22 16:23:29'),
(22, 2, 'Cucumber', 'fruits', 100.00, 49, 5, 'Fresh Organic Cucumber', '70d6f2c2a30ca9066f8021acfabd3713.jpg', '2025-12-22 16:24:35'),
(23, 2, 'Corn', 'fruits', 140.00, 2, 5, 'Organic Corn', '73a9ab3a15ddfd50a57fa067455fc99b.jpg', '2025-12-22 16:25:32'),
(24, 2, 'Broccoli', 'vegetables', 170.00, 49, 5, 'Organic Broccoli', 'c11264d41b23cdc746941311b1e4a49d.jpg', '2025-12-22 16:26:23'),
(25, 2, 'Chilly', 'herbs', 200.00, 33, 5, 'Organic Chilly - Spicyyyyyy', '32a20b9d8ef2f21d80df087dd3db07fd.jpg', '2025-12-22 16:27:06'),
(26, 2, 'Beetroot', 'fruits', 150.00, 33, 5, 'Organic Beetroot – Naturally sweet, rich, and a healthy boost for your blood.', '9056643f88417a2e2f0ff86ab2644119.jpg', '2025-12-22 16:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `refund_transaction_id` varchar(100) NOT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `refund_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`refund_id`, `order_id`, `payment_id`, `refund_transaction_id`, `refund_amount`, `reason`, `refund_date`) VALUES
(2, 14, 19, 'REF-20251221-769C44DC', 49.50, 'Cancelled/Rejected', '2025-12-21 13:10:55'),
(3, 28, 33, 'REF-20251221-82048250', 148.50, 'Cancelled/Rejected', '2025-12-21 17:46:02'),
(4, 31, 36, 'REF-20251221-12CF7ADD', 108.00, 'Cancelled/Rejected', '2025-12-21 18:16:02'),
(5, 32, 37, 'REF-20251222-2DB615E8', 342.00, 'Cancelled/Rejected', '2025-12-22 05:04:34'),
(6, 35, 40, 'REF-20251222-9C783DEE', 243.00, 'Cancelled/Rejected', '2025-12-22 05:58:03'),
(7, 37, 42, 'REF-20251222-3166EFFF', 85.50, 'Cancelled/Rejected', '2025-12-22 06:38:52'),
(8, 68, 64, 'REF-20251228-7BBD31ED', 252.00, 'Cancelled/Rejected', '2025-12-28 10:30:17'),
(9, 72, 68, 'REF-20260110-B974C164', 180.00, 'Cancelled/Rejected', '2026-01-10 11:54:57'),
(10, 73, 69, 'REF-20260205-C9EB766D', 180.00, 'Cancelled/Rejected', '2026-02-05 12:12:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_registration`
--
ALTER TABLE `customer_registration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmer_registration`
--
ALTER TABLE `farmer_registration`
  ADD PRIMARY KEY (`farmer_id`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD UNIQUE KEY `refund_transaction_id` (`refund_transaction_id`),
  ADD KEY `fk_refund_order` (`order_id`),
  ADD KEY `fk_refund_payment` (`payment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `customer_registration`
--
ALTER TABLE `customer_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `farmer_registration`
--
ALTER TABLE `farmer_registration`
  MODIFY `farmer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer_registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer_registration` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_registration` (`farmer_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_logs`
--
ALTER TABLE `order_status_logs`
  ADD CONSTRAINT `order_status_logs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmer_registration` (`farmer_id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `fk_refund_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_refund_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
