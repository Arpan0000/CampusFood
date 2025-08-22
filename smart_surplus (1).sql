-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 08:40 AM
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
-- Database: `smart_surplus`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_listings`
--

CREATE TABLE `food_listings` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `food_title` varchar(100) NOT NULL,
  `food_description` text DEFAULT NULL,
  `food_type` enum('Veg','Non-Veg') NOT NULL,
  `quantity` int(11) NOT NULL,
  `freshness_status` enum('Fresh','Good','Near Expiry') DEFAULT 'Fresh',
  `pickup_location` varchar(255) NOT NULL,
  `available_until` datetime NOT NULL,
  `status` enum('Active','Expired') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_listings`
--

INSERT INTO `food_listings` (`id`, `provider_id`, `food_title`, `food_description`, `food_type`, `quantity`, `freshness_status`, `pickup_location`, `available_until`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Paneer Sandwich', 'Freshly made paneer sandwich with herbs', 'Veg', 20, 'Fresh', 'Canteen A, Block 5', '2025-08-23 18:00:00', 'Active', '2025-08-20 06:49:45', '2025-08-21 08:06:39'),
(2, 1, 'Chicken Wrap', 'Grilled chicken wrap with salad', 'Non-Veg', 15, 'Good', 'Hostel C, Ground Floor', '2025-08-20 20:00:00', 'Expired', '2025-08-20 06:49:45', '2025-08-22 06:38:34'),
(3, 1, 'Veg Pizza Slice', 'Thin crust pizza slices with veggies', 'Veg', 30, 'Near Expiry', 'Canteen B, Block 3', '2025-08-20 15:00:00', 'Expired', '2025-08-20 06:49:45', '2025-08-22 06:38:34'),
(4, 1, 'Cheese Sandwich', 'Sandwich with cheddar cheese and lettuce', 'Veg', 10, 'Near Expiry', 'Canteen A, Block 5', '2025-08-20 10:00:00', 'Expired', '2025-08-20 06:50:37', '2025-08-20 06:50:37'),
(5, 1, 'Pasta', 'Delicious Pasta', 'Veg', 2, '', 'home', '2025-08-20 17:00:00', 'Expired', '2025-08-20 08:28:11', '2025-08-21 09:58:46'),
(6, 1, 'Biryani', 'Biryani', 'Non-Veg', 10, '', 'home', '2025-08-21 14:07:00', 'Expired', '2025-08-20 08:37:34', '2025-08-22 06:38:34'),
(7, 1, 'roti', 'roti', 'Veg', 10, 'Good', 'home', '2025-08-21 15:07:00', 'Expired', '2025-08-21 08:42:39', '2025-08-22 06:38:34'),
(9, 1, 'Maggi', 'maggi', 'Veg', 2, 'Fresh', 'home', '2025-08-21 16:31:00', 'Expired', '2025-08-21 09:01:48', '2025-08-22 06:38:34'),
(10, 1, 'daal', 'daal', 'Veg', 5, 'Fresh', 'home', '2025-08-21 16:45:00', 'Expired', '2025-08-21 09:15:31', '2025-08-22 06:38:34'),
(11, 1, 'Fried rice', 'Fried rice', 'Non-Veg', 6, 'Good', 'home', '2025-08-21 18:17:00', 'Expired', '2025-08-21 11:47:35', '2025-08-22 06:38:34'),
(12, 4, 'daal', 'daal', 'Veg', 3, 'Fresh', 'Home', '2025-08-21 22:59:00', 'Expired', '2025-08-21 15:29:19', '2025-08-22 06:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('provider','recipient') NOT NULL,
  `Subrole` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `Subrole`, `created_at`) VALUES
(1, 'canteen1', 'canteen1@campus.com', 'canteen123', 'provider', '', '2025-08-20 03:54:04'),
(3, 'tanu', 'tanu@gmail.com', 'Kolkata', 'recipient', '', '2025-08-21 03:21:43'),
(4, 'sb', 'abc@gmail.com', '$2y$10$zB7Cr5iJLyQMIVvWjcIODO8mv0cgmkKeq9MlpK1uyBndPvb7uF2/u', 'provider', '', '2025-08-21 15:22:35'),
(6, 'tb', 'aaa@gmail.com', '$2y$10$eKk8qqb54eGxHuv7oXZrTOXTpnl/KYfMVrp19zk2C0Kvgi7a.lYN6', 'provider', '', '2025-08-21 15:50:22'),
(7, 'mb', 'mb@gmail.com', '$2y$10$i0TlXQocR2g7uwMRJlQON.94V1EuOzHiU/.YtkcErpCMhlG6YzdAG', 'recipient', '', '2025-08-21 15:53:54'),
(8, 'kb', 'kb@gmail.com', 'kb', 'recipient', '', '2025-08-21 15:59:39'),
(10, 'op', 'op@gmail.com', 'op', 'provider', '', '2025-08-21 16:20:56'),
(11, 'lee', 'lee@gmail.com', 'lee', 'recipient', '', '2025-08-21 16:39:05'),
(12, 'sid', 'sid@gmail.com', 'ss', 'recipient', '', '2025-08-21 17:05:07'),
(13, 'sumit', 'ab@gmail.com', '333', 'recipient', '', '2025-08-22 06:01:00'),
(14, 'rim', 'rim@gmail.com', 'rrrr', 'recipient', 'students', '2025-08-22 06:05:16'),
(15, 'mir', 'mir@gmail.com', 'mir', 'provider', 'canteen', '2025-08-22 06:07:42'),
(16, 'amit', 'a@gmail.com', '$2y$10$URZbcbEw1Cu4DTeyWWDSnOFjWNSwmfAaWfczTjZbtOsWEAkf/DSDy', 'recipient', 'staff', '2025-08-22 06:37:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_admin_email` (`email`),
  ADD UNIQUE KEY `unique_admin_username` (`username`);

--
-- Indexes for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `food_listings`
--
ALTER TABLE `food_listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food_listings`
--
ALTER TABLE `food_listings`
  ADD CONSTRAINT `food_listings_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
