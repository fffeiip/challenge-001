-- SQL Schema for the Stores & Weapons CRUD Application
-- Target: MySQL 

-- Drop tables if they exist to allow for a clean reset.
-- The order is important due to foreign key constraints.
DROP TABLE IF EXISTS `weapons`;
DROP TABLE IF EXISTS `stores`;

--
-- Table structure for table `stores`
--
CREATE TABLE `stores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `address_line1` VARCHAR(255) NOT NULL,
  `address_line2` VARCHAR(255) NULL,
  `city` VARCHAR(100) NOT NULL,
  `state_region` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(50) NULL,
  `email` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `weapons`
--
CREATE TABLE `weapons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `store_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `type` VARCHAR(100) NOT NULL COMMENT 'e.g., rifle, shotgun, handgun',
  `caliber` VARCHAR(50) NOT NULL,
  `serial_number` VARCHAR(255) NOT NULL UNIQUE,
  `price` DECIMAL(10,2) NOT NULL,
  `in_stock` INT NOT NULL DEFAULT 0,
  `status` ENUM('active','discontinued','out_of_stock') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign key constraint
  -- ON DELETE CASCADE: If a store is deleted, all weapons associated with it are also deleted.
  -- This is a design choice for simplicity in this challenge. An alternative would be
  -- ON DELETE RESTRICT, which would prevent a store from being deleted if it still has weapons.
  CONSTRAINT `fk_weapon_store`
    FOREIGN KEY (`store_id`)
    REFERENCES `stores` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;