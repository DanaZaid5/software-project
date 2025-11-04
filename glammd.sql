-- ==========================================================
-- GLAMMD - MVP Schema (MySQL, INT-only, Inheritance)
-- Inheritance: Client.client_id  = User.user_id
--              Professional.professional_id = User.user_id
-- ==========================================================

-- Safety first (optional):
-- SET FOREIGN_KEY_CHECKS = 0;
-- DROP TABLE IF EXISTS ListItem, List, Review, Booking, BookingRequest, Service, Professional, Client, User;
-- SET FOREIGN_KEY_CHECKS = 1;

-- -------------------------------
-- USER (parent)
-- -------------------------------
CREATE TABLE `User` (
  `user_id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(255) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('client','professional') NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `ux_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- CLIENT (child of User)
-- PK is also FK to User(user_id)
-- -------------------------------
CREATE TABLE `Client` (
  `client_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`client_id`),
  CONSTRAINT `fk_client_user`
    FOREIGN KEY (`client_id`) REFERENCES `User`(`user_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- PROFESSIONAL (child of User)
-- PK is also FK to User(user_id)
-- -------------------------------
CREATE TABLE `Professional` (
  `professional_id` INT UNSIGNED NOT NULL,
  `bio`             TEXT NULL,
  PRIMARY KEY (`professional_id`),
  CONSTRAINT `fk_prof_user`
    FOREIGN KEY (`professional_id`) REFERENCES `User`(`user_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- SERVICE (offered by Professional)
-- -------------------------------
CREATE TABLE `Service` (
  `service_id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id`  INT UNSIGNED NOT NULL,
  `category`         ENUM('Hair','Makeup','Skincare','Bodycare') NOT NULL,
  `title`            VARCHAR(160) NOT NULL,
  `description`      TEXT NULL,
  `duration`         SMALLINT UNSIGNED NOT NULL,  -- minutes
  `price`            DECIMAL(10,2) NOT NULL,      -- SAR
  `tags`             TEXT NULL,                   -- simple CSV for MVP
  PRIMARY KEY (`service_id`),
  KEY `ix_service_professional` (`professional_id`),
  KEY `ix_service_category` (`category`),
  CONSTRAINT `fk_service_professional`
    FOREIGN KEY (`professional_id`) REFERENCES `Professional`(`professional_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- BOOKING REQUEST (pending/declined/expired)
-- "Offers" tab
-- -------------------------------
CREATE TABLE `BookingRequest` (
  `request_id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`      INT UNSIGNED NOT NULL,
  `service_id`     INT UNSIGNED NOT NULL,
  `requested_time` DATETIME NOT NULL,
  `status`         ENUM('pending','declined','expired') NOT NULL DEFAULT 'pending',
  `client_notes`   TEXT NULL,
  PRIMARY KEY (`request_id`),
  KEY `ix_br_client` (`client_id`),
  KEY `ix_br_service` (`service_id`),
  KEY `ix_br_requested_time` (`requested_time`),
  CONSTRAINT `fk_br_client`
    FOREIGN KEY (`client_id`) REFERENCES `Client`(`client_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_br_service`
    FOREIGN KEY (`service_id`) REFERENCES `Service`(`service_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- BOOKING (accepted/confirmed bookings)
-- -------------------------------
CREATE TABLE `Booking` (
  `booking_id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id`        INT UNSIGNED NOT NULL,
  `professional_id`  INT UNSIGNED NOT NULL,
  `service_id`       INT UNSIGNED NOT NULL,
  `time`             DATETIME NOT NULL,
  `client_notes`     TEXT NULL,
  `status`           ENUM('confirmed','completed','cancelled') NOT NULL DEFAULT 'confirmed',
  PRIMARY KEY (`booking_id`),
  KEY `ix_book_client` (`client_id`),
  KEY `ix_book_professional` (`professional_id`),
  KEY `ix_book_service` (`service_id`),
  KEY `ix_book_time` (`time`),
  CONSTRAINT `fk_book_client`
    FOREIGN KEY (`client_id`) REFERENCES `Client`(`client_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_book_professional`
    FOREIGN KEY (`professional_id`) REFERENCES `Professional`(`professional_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_book_service`
    FOREIGN KEY (`service_id`) REFERENCES `Service`(`service_id`)
    ON DELETE RESTRICT   -- keep history even if a service is removed
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- REVIEW (one per booking)
-- normalized: only booking_id
-- -------------------------------
CREATE TABLE `Review` (
  `review_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id`  INT UNSIGNED NOT NULL,
  `rating`      TINYINT UNSIGNED NOT NULL,  -- 1..5 (validate in app or CHECK)
  `comment`     TEXT NULL,
  PRIMARY KEY (`review_id`),
  UNIQUE KEY `ux_review_booking` (`booking_id`),
  CONSTRAINT `fk_review_booking`
    FOREIGN KEY (`booking_id`) REFERENCES `Booking`(`booking_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- LIST (client-curated sets of services)
-- -------------------------------
CREATE TABLE `List` (
  `list_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` INT UNSIGNED NOT NULL,
  `name`      VARCHAR(120) NOT NULL,
  PRIMARY KEY (`list_id`),
  KEY `ix_list_client` (`client_id`),
  CONSTRAINT `fk_list_client`
    FOREIGN KEY (`client_id`) REFERENCES `Client`(`client_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------
-- LIST ITEM (services within a list)
-- prevent duplicates per list
-- -------------------------------
CREATE TABLE `ListItem` (
  `list_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `list_id`      INT UNSIGNED NOT NULL,
  `service_id`   INT UNSIGNED NOT NULL,
  PRIMARY KEY (`list_item_id`),
  UNIQUE KEY `ux_listitem_unique` (`list_id`, `service_id`),
  KEY `ix_item_list` (`list_id`),
  KEY `ix_item_service` (`service_id`),
  CONSTRAINT `fk_listitem_list`
    FOREIGN KEY (`list_id`) REFERENCES `List`(`list_id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_listitem_service`
    FOREIGN KEY (`service_id`) REFERENCES `Service`(`service_id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
