-- Cleaned & Fixed Glammd Database (Option A – Keep Sample Data)
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- =============================
-- 1. USER TABLE
-- =============================
CREATE TABLE `User` (
  `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('client','professional') NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `User` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Sarah .M', 'sara@gmail.com', 'hi', 'professional'),
(2, 'Aljohara alsultan', 'aljohara@gmail.com', 'hello', 'professional'),
(3, 'Layan Abdulaziz', 'layan@gmail.com', 'hi', 'professional'),
(4, 'Noura', 'noura@gmail.com', 'hi', 'professional');

-- =============================
-- 2. CLIENT TABLE
-- =============================
CREATE TABLE `Client` (
  `client_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`client_id`),
  CONSTRAINT `fk_client_user` FOREIGN KEY (`client_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================
-- 3. PROFESSIONAL TABLE
-- =============================
CREATE TABLE `Professional` (
  `professional_id` int UNSIGNED NOT NULL,
  `bio` text,
  `img` varchar(200) NOT NULL,
  PRIMARY KEY (`professional_id`),
  CONSTRAINT `fk_prof_user` FOREIGN KEY (`professional_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Professional` (`professional_id`, `bio`, `img`) VALUES
(1, 'I’m a certified makeup artist specializing in soft glam, bridal looks, and enhancing natural beauty. With a passion for detail and a love for creativity, I focus on creating flawless, long-lasting looks that make every client feel confident and radiant.', 'pro1.jpg'),
(2, 'Hairstylist focused on natural texture curly hair, soft colors, and lived-in styles. Healthy hair first, beautiful results always. ✨', 'Aljohara.png'),
(3, 'Nail artist obsessed with soft colors, tiny details, and everything cute. Creating pretty, fresh, and unique nail designs that make you smile.', 'layan.png'),
(4, 'Makeup artist creating simple, clean, and glowing looks. Beauty that feels natural and effortless.', 'Noura.png');

-- =============================
-- 4. SERVICE TABLE
-- =============================
CREATE TABLE `Service` (
  `service_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` int UNSIGNED NOT NULL,
  `category` enum('Hair','Makeup','Skincare','Bodycare','Nails') NOT NULL,
  `title` varchar(160) NOT NULL,
  `description` text,
  `duration` smallint UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `tags` text,
  PRIMARY KEY (`service_id`),
  KEY `ix_service_professional` (`professional_id`),
  CONSTRAINT `fk_service_professional` FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Service` (`service_id`, `professional_id`, `category`, `title`, `description`, `duration`, `price`, `tags`) VALUES
(1, 1, 'Makeup', 'SoftGlam', 'A natural makeup look with soft tones.', 60, 300.00, 'oily skin'),
(2, 2, 'Hair', 'Curly Hair Styling', 'Enhances natural texture for flawless curls', 90, 200.00, 'Curly Hair'),
(3, 3, 'Nails', 'Simple nails', 'Minimalist and chic nail designs', 60, 200.00, 'Simple'),
(4, 4, 'Makeup', 'Evening Glam', 'Dramatic evening makeup', 120, 1200.00, 'Dry skin'),
(5, 1, 'Makeup', 'Bridal Makeup', 'Complete bridal makeup package', 120, 1500.00, 'bridal'),
(6, 4, 'Makeup', 'Natural Day Makeup', 'Fresh, natural day look', 45, 200.00, 'natural'),
(7, 2, 'Hair', 'Blowout & Styling', 'Smooth, voluminous blowout', 60, 250.00, 'blowout'),
(8, 2, 'Hair', 'Haircut & Styling', 'Custom haircut', 90, 300.00, 'haircut'),
(9, 3, 'Nails', 'Gel Manicure', 'Long-lasting gel manicure', 60, 180.00, 'gel'),
(10, 3, 'Nails', 'Spa Pedicure', 'Relaxing spa pedicure', 75, 220.00, 'pedicure'),
(11, 4, 'Skincare', 'Facial Treatment', 'Custom facial treatment', 60, 350.00, 'facial'),
(12, 1, 'Skincare', 'Anti-Aging Treatment', 'Targets wrinkles for youthful look', 90, 450.00, 'anti-aging'),
(13, 4, 'Bodycare', 'Full Body Massage', '60-minute relaxing massage', 60, 400.00, 'massage'),
(14, 1, 'Bodycare', 'Body Scrub & Wrap', 'Exfoliating scrub and nourishing wrap', 90, 500.00, 'body scrub');

-- =============================
-- 5. BOOKING TABLE
-- =============================
CREATE TABLE `Booking` (
  `booking_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int UNSIGNED NOT NULL,
  `professional_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `time` datetime NOT NULL,
  `client_notes` text,
  `status` enum('confirmed','completed','cancelled') NOT NULL DEFAULT 'confirmed',
  PRIMARY KEY (`booking_id`),
  KEY `ix_book_client` (`client_id`),
  KEY `ix_book_professional` (`professional_id`),
  KEY `ix_book_service` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================
-- 6. BOOKING REQUEST
-- =============================
CREATE TABLE `BookingRequest` (
  `request_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int UNSIGNED NOT NULL,
  `professional_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` time NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================
-- 7. LIST & LIST ITEMS
-- =============================
CREATE TABLE `List` (
  `list_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ListItem` (
  `item_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `list_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================
-- 8. REVIEW
-- =============================
CREATE TABLE `Review` (
  `review_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text,
  PRIMARY KEY (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================
-- ADD FOREIGN KEYS (AFTER ALL TABLES EXIST)
-- =============================
ALTER TABLE `Service`
  ADD CONSTRAINT `fk_service_professional` FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE;

ALTER TABLE `Booking`
  ADD CONSTRAINT `fk_booking_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_professional` FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE CASCADE;

ALTER TABLE `BookingRequest`
  ADD CONSTRAINT `fk_booking_request_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_request_professional` FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_request_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE CASCADE;

ALTER TABLE `List`
  ADD CONSTRAINT `fk_list_client` FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE;

ALTER TABLE `ListItem`
  ADD CONSTRAINT `fk_list_item_list` FOREIGN KEY (`list_id`) REFERENCES `List` (`list_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_list_item_service` FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE CASCADE;

ALTER TABLE `Review`
  ADD CONSTRAINT `fk_review_booking` FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`booking_id`) ON DELETE CASCADE;

COMMIT;
