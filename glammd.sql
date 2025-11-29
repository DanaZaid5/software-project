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

-- Professionals (Users 1–4)
INSERT INTO `User` (`user_id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Sarah .M', 'sara@gmail.com', 'hi', 'professional'),
(2, 'Aljohara alsultan', 'aljohara@gmail.com', 'hello', 'professional'),
(3, 'Layan Abdulaziz', 'layan@gmail.com', 'hi', 'professional'),
(4, 'Noura', 'noura@gmail.com', 'hi', 'professional'),

-- Clients (Users 5–8)
(5, 'Aisha', 'aisha@gmail.com', '123', 'client'),
(6, 'Lama', 'lama@gmail.com', '123', 'client'),
(7, 'Reem', 'reem@gmail.com', '123', 'client'),
(8, 'Dana', 'dana@gmail.com', '123', 'client');

-- =============================
-- 2. CLIENT TABLE
-- =============================
CREATE TABLE `Client` (
  `client_id` int UNSIGNED NOT NULL,
  PRIMARY KEY (`client_id`),
  CONSTRAINT `fk_client_user`
      FOREIGN KEY (`client_id`)
      REFERENCES `User` (`user_id`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Client` VALUES (5),(6),(7),(8);

-- =============================
-- 3. PROFESSIONAL TABLE
-- =============================
CREATE TABLE `Professional` (
  `professional_id` int UNSIGNED NOT NULL,
  `bio` text,
  `img` varchar(200) NOT NULL,
  PRIMARY KEY (`professional_id`),
  CONSTRAINT `fk_prof_user`
      FOREIGN KEY (`professional_id`)
      REFERENCES `User` (`user_id`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Professional` (`professional_id`, `bio`, `img`) VALUES
(1, 'Certified makeup artist specializing in soft glam, bridal looks.', 'pro1.jpg'),
(2, 'Hairstylist focused on natural texture curly hair.', 'Aljohara.png'),
(3, 'Nail artist obsessed with cute and simple designs.', 'layan.png'),
(4, 'Makeup artist creating simple, clean glowing looks.', 'Noura.png');

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
  CONSTRAINT `fk_service_professional`
      FOREIGN KEY (`professional_id`)
      REFERENCES `Professional` (`professional_id`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Service` VALUES
(1, 1, 'Makeup', 'SoftGlam', 'Natural soft glam look', 60, 300.00, 'oily skin'),
(2, 2, 'Hair', 'Curly Hair Styling', 'Enhances natural curls', 90, 200.00, 'curly'),
(3, 3, 'Nails', 'Simple nails', 'Minimalist chic nails', 60, 200.00, 'simple'),
(4, 4, 'Makeup', 'Evening Glam', 'Dramatic evening makeup', 120, 1200.00, 'evening'),
(5, 1, 'Makeup', 'Bridal Makeup', 'Complete bridal package', 120, 1500.00, 'bridal'),
(6, 4, 'Makeup', 'Natural Day Makeup', 'Fresh, light natural look', 45, 200.00, 'day'),
(7, 2, 'Hair', 'Blowout & Styling', 'Smooth voluminous blowout', 60, 250.00, 'blowout'),
(8, 2, 'Hair', 'Haircut & Styling', 'Custom haircut and finish', 90, 300.00, 'haircut'),
(9, 3, 'Nails', 'Gel Manicure', 'Long-lasting gel nails', 60, 180.00, 'gel'),
(10, 3, 'Nails', 'Spa Pedicure', 'Relaxing foot spa', 75, 220.00, 'pedicure'),
(11, 4, 'Skincare', 'Facial Treatment', 'Custom facial', 60, 350.00, 'facial'),
(12, 1, 'Skincare', 'Anti-Aging Treatment', 'Wrinkle targeting treatment', 90, 450.00, 'anti-aging'),
(13, 4, 'Bodycare', 'Full Body Massage', '60-minute relaxing massage', 60, 400.00, 'massage'),
(14, 1, 'Bodycare', 'Body Scrub & Wrap', 'Scrub + body wrap', 90, 500.00, 'scrub');

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
  CONSTRAINT `fk_booking_client`
      FOREIGN KEY (`client_id`) REFERENCES `Client` (`client_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_booking_professional`
      FOREIGN KEY (`professional_id`) REFERENCES `Professional` (`professional_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_booking_service`
      FOREIGN KEY (`service_id`) REFERENCES `Service` (`service_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sample bookings so reviews work
INSERT INTO Booking (client_id, professional_id, service_id, time, client_notes, status) VALUES
(5, 1, 1, '2025-01-10 14:00:00', NULL, 'completed'),
(6, 1, 5, '2025-01-12 15:00:00', NULL, 'completed'),
(7, 1, 12, '2025-01-15 12:00:00', NULL, 'completed'),
(8, 1, 1, '2025-01-18 17:00:00', NULL, 'completed'),
(5, 2, 2, '2025-01-20 13:00:00', NULL, 'completed'),
(6, 2, 7, '2025-01-22 11:00:00', NULL, 'completed'),
(7, 3, 9, '2025-01-25 10:00:00', NULL, 'completed'),
(8, 3, 10, '2025-01-26 10:00:00', NULL, 'completed'),
(6, 4, 4, '2025-01-27 11:30:00', NULL, 'completed'),
(5, 4, 11, '2025-01-29 10:30:00', NULL, 'completed');

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
-- 7. LIST + LIST ITEMS
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
-- 8. REVIEW TABLE (FULLY FIXED)
-- =============================
CREATE TABLE `Review` (
  `review_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` int UNSIGNED NOT NULL,
  `client_id` int UNSIGNED NOT NULL,
  `professional_id` int UNSIGNED NOT NULL,
  `service_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment` text,
  `review_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  CONSTRAINT `fk_review_booking` FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`booking_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================
-- 9. SAMPLE REVIEWS (MULTIPLE FOR EACH PROFESSIONAL)
-- =============================

INSERT INTO Review
(booking_id, client_id, professional_id, service_id, rating, comment)
VALUES
-- SARAH (Professional 1)
(1,5,1,1,5,'Amazing soft glam! Lasted all day.'),
(2,6,1,5,5,'Bridal makeup was stunning.'),
(3,7,1,12,4,'Great facial, very relaxing.'),
(4,8,1,1,5,'Perfect natural glam!'),

-- ALJOHARA (Professional 2)
(5,5,2,2,4,'Nice curly styling but took long.'),
(6,6,2,7,5,'Best blowout ever!'),

-- LAYAN (Professional 3)
(7,7,3,9,5,'Gel manicure was perfect.'),
(8,8,3,10,5,'Spa pedicure was so relaxing.'),

-- NOURA (Professional 4)
(9,6,4,4,5,'Evening glam was flawless.'),
(10,5,4,11,4,'Great facial treatment.');

COMMIT;
