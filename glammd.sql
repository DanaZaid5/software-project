SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop tables if they exist (optional, for a clean reset)
DROP TABLE IF EXISTS `Review`;
DROP TABLE IF EXISTS `ListItem`;
DROP TABLE IF EXISTS `List`;
DROP TABLE IF EXISTS `BookingRequest`;
DROP TABLE IF EXISTS `Booking`;
DROP TABLE IF EXISTS `Service`;
DROP TABLE IF EXISTS `Professional`;
DROP TABLE IF EXISTS `Client`;
DROP TABLE IF EXISTS `User`;

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
(1, 'Sarah M.', 'sara@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'professional'),
(2, 'Aljohara Alsultan', 'aljohara@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'professional'),
(3, 'Layan Abdulaziz', 'layan@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'professional'),
(4, 'Noura', 'noura@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'professional'),
(5, 'Aisha', 'aisha@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'client'),
(6, 'Lama', 'lama@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'client'),
(7, 'Reem', 'reem@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'client'),
(8, 'Dana', 'dana@gmail.com', '$2y$10$n34QWRmB5KbE7mr1r43ESuGHZZVWIFg/.UFw3LxAocJLmN5Dlk6P2', 'client');

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

INSERT INTO `Client` (`client_id`) VALUES (5),(6),(7),(8);

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
(1, 'Certified makeup artist specializing in soft glam and bridal looks.', 'pro1.jpg'),
(2, 'Hairstylist focused on natural texture and curly hair.', 'Aljohara.png'),
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

INSERT INTO `Service` (`service_id`,`professional_id`,`category`,`title`,`description`,`duration`,`price`,`tags`) VALUES
(1, 1, 'Makeup',  'Soft Glam',            'Natural soft glam look.',                      60,  300.00, 'soft, glam, oily skin'),
(2, 2, 'Hair',    'Curly Hair Styling',   'Enhances and defines natural curls.',         90,  200.00, 'curly, natural'),
(3, 3, 'Nails',   'Simple Nails',         'Minimalist chic nail design.',                 60,  200.00, 'simple'),
(4, 4, 'Makeup',  'Evening Glam',         'Dramatic evening makeup.',                    120, 1200.00, 'evening, full glam'),
(5, 1, 'Makeup',  'Bridal Makeup',        'Complete bridal makeup package.',             120, 1500.00, 'bridal, wedding'),
(6, 4, 'Makeup',  'Natural Day Makeup',   'Fresh, light, natural daytime look.',          45,  200.00, 'day, natural'),
(7, 2, 'Hair',    'Blowout & Styling',    'Smooth voluminous blowout and styling.',       60,  250.00, 'blowout'),
(8, 2, 'Hair',    'Haircut & Styling',    'Custom haircut and finished style.',           90,  300.00, 'haircut'),
(9, 3, 'Nails',   'Gel Manicure',         'Long-lasting gel manicure.',                   60,  180.00, 'gel'),
(10,3, 'Nails',   'Spa Pedicure',         'Relaxing foot spa and pedicure.',              75,  220.00, 'pedicure, spa'),
(11,4, 'Skincare','Facial Treatment',     'Custom facial treatment.',                     60,  350.00, 'facial'),
(12,1, 'Skincare','Anti-Aging Treatment', 'Targeted anti-aging facial treatment.',        90,  450.00, 'anti-aging'),
(13,4, 'Bodycare','Full Body Massage',    '60-minute relaxing full body massage.',        60,  400.00, 'massage, relax'),
(14,1, 'Bodycare','Body Scrub & Wrap',    'Body scrub followed by body wrap.',            90,  500.00, 'scrub, wrap');

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

INSERT INTO `Booking` (`client_id`,`professional_id`,`service_id`,`time`,`client_notes`,`status`) VALUES
-- Completed bookings (used for reviews)
(5, 1, 1,  '2025-01-10 14:00:00', NULL, 'completed'),
(6, 1, 5,  '2025-01-12 15:00:00', NULL, 'completed'),
(7, 1, 12, '2025-01-15 12:00:00', NULL, 'completed'),
(8, 1, 1,  '2025-01-18 17:00:00', NULL, 'completed'),
(5, 2, 2,  '2025-01-20 13:00:00', NULL, 'completed'),
(6, 2, 7,  '2025-01-22 11:00:00', NULL, 'completed'),
(7, 3, 9,  '2025-01-25 10:00:00', NULL, 'completed'),
(8, 3, 10, '2025-01-26 10:00:00', NULL, 'completed'),
(6, 4, 4,  '2025-01-27 11:30:00', NULL, 'completed'),
(5, 4, 11, '2025-01-29 10:30:00', NULL, 'completed'),
-- Extra bookings to test statuses
(5, 1, 6,  '2025-02-02 09:00:00', 'Day makeup before event.',  'confirmed'),
(7, 2, 8,  '2025-02-05 16:00:00', 'Thinking of a new haircut.', 'cancelled');

-- =============================
-- 6. BOOKING REQUEST TABLE
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

INSERT INTO `BookingRequest` (`client_id`,`professional_id`,`service_id`,`preferred_date`,`preferred_time`,`status`) VALUES
(5, 1, 1,  '2025-02-10', '14:00:00', 'pending'),
(5, 2, 7,  '2025-02-12', '16:00:00', 'accepted'),
(6, 4, 11, '2025-02-15', '11:30:00', 'rejected'),
(7, 3, 9,  '2025-02-18', '10:00:00', 'pending'),
(8, 1, 5,  '2025-02-20', '18:00:00', 'accepted');

-- =============================
-- 7. FAVORITES: LIST + LIST ITEMS
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

-- Boards for each client
INSERT INTO `List` (`list_id`,`client_id`,`name`) VALUES
(1, 5, 'Wedding Looks'),
(2, 5, 'Everyday Glam'),
(3, 6, 'Hair Ideas'),
(4, 7, 'Nail Inspo'),
(5, 8, 'Spa & Relax');

-- Items inside boards (services saved to boards)
INSERT INTO `ListItem` (`item_id`,`list_id`,`service_id`) VALUES
(1, 1, 5),   -- Bridal Makeup
(2, 1, 1),   -- Soft Glam
(3, 1, 12),  -- Anti-Aging Treatment
(4, 2, 6),   -- Natural Day Makeup
(5, 3, 2),   -- Curly Hair Styling
(6, 3, 7),   -- Blowout & Styling
(7, 3, 8),   -- Haircut & Styling
(8, 4, 3),   -- Simple Nails
(9, 4, 9),   -- Gel Manicure
(10,4, 10),  -- Spa Pedicure
(11,5, 11),  -- Facial Treatment
(12,5, 13),  -- Full Body Massage
(13,5, 14);  -- Body Scrub & Wrap

-- =============================
-- 8. REVIEW TABLE
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
  CONSTRAINT `fk_review_booking`
      FOREIGN KEY (`booking_id`) REFERENCES `Booking` (`booking_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `Review`
(`booking_id`,`client_id`,`professional_id`,`service_id`,`rating`,`comment`) VALUES
-- SARAH (Professional 1)
(1, 5, 1, 1, 5, 'Amazing soft glam! Lasted all day.'),
(2, 6, 1, 5, 5, 'Bridal makeup was stunning.'),
(3, 7, 1, 12,4, 'Great facial, very relaxing.'),
(4, 8, 1, 1, 5, 'Perfect natural glam!'),
-- ALJOHARA (Professional 2)
(5, 5, 2, 2, 4, 'Nice curly styling but took a bit long.'),
(6, 6, 2, 7, 5, 'Best blowout ever!'),
-- LAYAN (Professional 3)
(7, 7, 3, 9, 5, 'Gel manicure was perfect.'),
(8, 8, 3, 10,5, 'Spa pedicure was so relaxing.'),
-- NOURA (Professional 4)
(9, 6, 4, 4, 5, 'Evening glam was flawless.'),
(10,5, 4, 11,4, 'Great facial treatment.');

COMMIT;
