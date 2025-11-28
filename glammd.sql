-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 28, 2025 at 05:32 PM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `glammd`
--

-- --------------------------------------------------------

--
-- Table structure for table `Booking`
--

CREATE TABLE `Booking` (
  `booking_id` int UNSIGNED NOT NULL,
  `client_id` int UNSIGNED NOT NULL,
  `professional_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `time` datetime NOT NULL,
  `client_notes` text,
  `status` enum('confirmed','completed','cancelled') NOT NULL DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `BookingRequest`
--

CREATE TABLE `BookingRequest` (
  `request_id` int UNSIGNED NOT NULL,
  `client_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `requested_time` datetime NOT NULL,
  `status` enum('pending','declined','expired') NOT NULL DEFAULT 'pending',
  `client_notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Client`
--

CREATE TABLE `Client` (
  `client_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `List`
--

CREATE TABLE `List` (
  `list_id` int UNSIGNED NOT NULL,
  `client_id` int UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ListItem`
--

CREATE TABLE `ListItem` (
  `list_item_id` int UNSIGNED NOT NULL,
  `list_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Professional`
--

CREATE TABLE `Professional` (
  `professional_id` int UNSIGNED NOT NULL,
  `bio` text,
  `img` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Professional`
--

INSERT INTO `Professional` (`professional_id`, `bio`, `img`) VALUES
(1, 'I’m a certified makeup artist specializing in soft glam, bridal looks, and enhancing natural beauty. With a passion for detail and a love for creativity, I focus on creating flawless, long-lasting looks that make every client feel confident and radiant. Whether it’s for a special event, photoshoot, or everyday glam, my goal is to bring your vision to life with precision and artistry.', 'pro1.jpg'),
(2, 'Hairstylist focused on natural texture curly hair, soft colors, and lived-in styles.\r\nHealthy hair first, beautiful results always. ✨', 'Aljohara.png'),
(3, 'Nail artist obsessed with soft colors, tiny details, and everything cute.\r\nCreating pretty, fresh, and unique nail designs that make you smile.', 'layan.png'),
(4, 'makeup artist creating simple, clean, and glowing looks. Beauty that feels natural and effortless.', 'Noura.png');

-- --------------------------------------------------------

--
-- Table structure for table `Review`
--

CREATE TABLE `Review` (
  `review_id` int UNSIGNED NOT NULL,
  `booking_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Service`
--

CREATE TABLE `Service` (
  `service_id` int UNSIGNED NOT NULL,
  `professional_id` int UNSIGNED NOT NULL,
  `category` enum('Hair','Makeup','Skincare','Bodycare','Nails') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(160) NOT NULL,
  `description` text,
  `duration` smallint UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tags` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Service`
--

INSERT INTO `Service` (`service_id`, `professional_id`, `category`, `title`, `description`, `duration`, `price`, `tags`) VALUES
(1, 1, 'Makeup', 'SoftGlam', 'A natural, elegant makeup look featuring soft tones, seamless blending, and a radiant finish that enhances your beauty without looking heavy. Perfect for everyday wear or special occasions.', 60, 300.00, 'oily skin'),
(2, 2, 'Hair', 'Curly Hair Styling ', 'Expert curly hair styling that enhances your natural texture, adds volume, and defines your curls for a flawless, bouncy look', 90, 200.00, 'Curly Hair'),
(3, 3, 'Nails', 'Simple nails', 'Minimalist and chic nail designs with clean lines and subtle accents for a stylish everyday look', 60, 200.00, 'Simple'),
(4, 4, 'Makeup', 'Evening Glam', 'Dramatic and sophisticated makeup perfect for evenings, highlighting your features with bold eyes and radiant skin', 120, 1200.00, 'Dry skin');

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','professional') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Sarah .M', 'sara@gmail.com', 'hi', 'professional'),
(2, 'Aljohara alsultan', 'aljohara@gmail.com', 'hello', 'professional'),
(3, 'Layan Abdulaziz', 'layan@gmail.com', 'hi', 'professional'),
(4, 'Noura', 'noura@gmail.com', 'hi', 'professional');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Booking`
--
ALTER TABLE `Booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `ix_book_client` (`client_id`),
  ADD KEY `ix_book_professional` (`professional_id`),
  ADD KEY `ix_book_service` (`service_id`),
  ADD KEY `ix_book_time` (`time`);

--
-- Indexes for table `BookingRequest`
--
ALTER TABLE `BookingRequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `ix_br_client` (`client_id`),
  ADD KEY `ix_br_service` (`service_id`),
  ADD KEY `ix_br_requested_time` (`requested_time`);

--
-- Indexes for table `Client`
--
ALTER TABLE `Client`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `List`
--
ALTER TABLE `List`
  ADD PRIMARY KEY (`list_id`),
  ADD KEY `ix_list_client` (`client_id`);

--
-- Indexes for table `ListItem`
--
ALTER TABLE `ListItem`
  ADD PRIMARY KEY (`list_item_id`),
  ADD UNIQUE KEY `ux_listitem_unique` (`list_id`,`service_id`),
  ADD KEY `ix_item_list` (`list_id`),
  ADD KEY `ix_item_service` (`service_id`);

--
-- Indexes for table `Professional`
--
ALTER TABLE `Professional`
  ADD PRIMARY KEY (`professional_id`);

--
-- Indexes for table `Review`
--
ALTER TABLE `Review`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `ux_review_booking` (`booking_id`);

--
-- Indexes for table `Service`
--
ALTER TABLE `Service`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `ix_service_professional` (`professional_id`),
  ADD KEY `ix_service_category` (`category`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `ux_user_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Booking`
--
ALTER TABLE `Booking`
  MODIFY `booking_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `BookingRequest`
--
ALTER TABLE `BookingRequest`
  MODIFY `request_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `List`
--
ALTER TABLE `List`
  MODIFY `list_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ListItem`
--
ALTER TABLE `ListItem`
  MODIFY `list_item_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Review`
--
ALTER TABLE `Review`
  MODIFY `review_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Service`
--
ALTER TABLE `Service`
  MODIFY `service_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Booking`
--
ALTER TABLE `Booking`
  ADD CONSTRAINT `fk_book_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_book_professional` FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_book_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE RESTRICT;

--
-- Constraints for table `BookingRequest`
--
ALTER TABLE `BookingRequest`
  ADD CONSTRAINT `fk_br_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_br_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `Client`
--
ALTER TABLE `Client`
  ADD CONSTRAINT `fk_client_user` FOREIGN KEY (`client_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `List`
--
ALTER TABLE `List`
  ADD CONSTRAINT `fk_list_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE;

--
-- Constraints for table `ListItem`
--
ALTER TABLE `ListItem`
  ADD CONSTRAINT `fk_listitem_list` FOREIGN KEY (`list_id`) REFERENCES `List` (`list_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_listitem_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE CASCADE;

--
-- Constraints for table `Professional`
--
ALTER TABLE `Professional`
  ADD CONSTRAINT `fk_prof_user` FOREIGN KEY (`professional_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `Review`
--
ALTER TABLE `Review`
  ADD CONSTRAINT `fk_review_booking` FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `Service`
--
ALTER TABLE `Service`
  ADD CONSTRAINT `fk_service_professional` FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
