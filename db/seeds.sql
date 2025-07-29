-- Clear existing data to make the script idempotent
-- TRUNCATE is faster than DELETE and resets auto-increment counters.
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `weapons`;
TRUNCATE TABLE `stores`;
SET FOREIGN_KEY_CHECKS = 1;

-- Seed Stores (5+)
INSERT INTO `stores` (`name`, `slug`, `address_line1`, `city`, `state_region`, `country`, `phone`, `email`) VALUES
('Oak Ridge Armory', 'oak-ridge-armory', '123 Patriot Way', 'Knoxville', 'Tennessee', 'USA', '865-555-0101', 'contact@oakridgearmory.com'),
('Alpine Sporting Goods', 'alpine-sporting-goods', '456 Summit Peak', 'Denver', 'Colorado', 'USA', '303-555-0102', 'info@alpinemountain.com'),
('Coastal Defense Solutions', 'coastal-defense-solutions', '789 Ocean Blvd', 'Miami', 'Florida', 'USA', '305-555-0103', 'sales@coastaldefense.net'),
('Lone Star Firearms', 'lone-star-firearms', '101 Ranger Rd', 'Austin', 'Texas', 'USA', '512-555-0104', 'support@lonestarfirearms.com'),
('Northwoods Hunting Supply', 'northwoods-hunting-supply', '212 Maple Leaf Dr', 'Toronto', 'Ontario', 'Canada', '416-555-0105', 'info@northwoodshunting.ca');


-- Seed Weapons (20+)
-- Note: We use subqueries to get the store_id based on the unique slug.
-- This makes the seed script more readable and resilient to changes in auto-incremented IDs.

-- Weapons for Oak Ridge Armory
INSERT INTO `weapons` (`store_id`, `name`, `type`, `caliber`, `serial_number`, `price`, `in_stock`, `status`) VALUES
((SELECT id FROM stores WHERE slug = 'oak-ridge-armory'), 'AR-15 Standard', 'Rifle', '5.56mm', 'SN-AR15-001', 899.99, 15, 'active'),
((SELECT id FROM stores WHERE slug = 'oak-ridge-armory'), 'Glock 19 Gen5', 'Handgun', '9mm', 'SN-G19-001', 549.00, 25, 'active'),
((SELECT id FROM stores WHERE slug = 'oak-ridge-armory'), 'Remington 870', 'Shotgun', '12 Gauge', 'SN-R870-001', 399.50, 10, 'out_of_stock'),
((SELECT id FROM stores WHERE slug = 'oak-ridge-armory'), 'M1 Garand', 'Rifle', '.30-06', 'SN-M1G-001', 1500.00, 2, 'discontinued');

-- Weapons for Alpine Sporting Goods
INSERT INTO `weapons` (`store_id`, `name`, `type`, `caliber`, `serial_number`, `price`, `in_stock`, `status`) VALUES
((SELECT id FROM stores WHERE slug = 'alpine-sporting-goods'), 'Tikka T3x Lite', 'Rifle', '.308 Win', 'SN-T3X-001', 799.00, 8, 'active'),
((SELECT id FROM stores WHERE slug = 'alpine-sporting-goods'), 'Smith & Wesson M&P Shield', 'Handgun', '9mm', 'SN-SWMP-001', 449.00, 30, 'active'),
((SELECT id FROM stores WHERE slug = 'alpine-sporting-goods'), 'Benelli Super Black Eagle 3', 'Shotgun', '12 Gauge', 'SN-BNE-001', 1899.00, 5, 'active'),
((SELECT id FROM stores WHERE slug = 'alpine-sporting-goods'), 'Ruger 10/22 Carbine', 'Rifle', '.22 LR', 'SN-R1022-001', 299.99, 50, 'active');

-- Weapons for Coastal Defense Solutions
INSERT INTO `weapons` (`store_id`, `name`, `type`, `caliber`, `serial_number`, `price`, `in_stock`, `status`) VALUES
((SELECT id FROM stores WHERE slug = 'coastal-defense-solutions'), 'SIG Sauer P320', 'Handgun', '9mm', 'SN-P320-001', 600.00, 18, 'active'),
((SELECT id FROM stores WHERE slug = 'coastal-defense-solutions'), 'Mossberg 590 Shockwave', 'Shotgun', '12 Gauge', 'SN-M590-001', 455.00, 12, 'active'),
((SELECT id FROM stores WHERE slug = 'coastal-defense-solutions'), 'FN SCAR 17S', 'Rifle', '7.62x51mm', 'SN-SCAR17-001', 3500.00, 3, 'active'),
((SELECT id FROM stores WHERE slug = 'coastal-defense-solutions'), 'Beretta 92FS', 'Handgun', '.40 S&W', 'SN-B92FS-001', 699.00, 0, 'out_of_stock');

-- Weapons for Lone Star Firearms
INSERT INTO `weapons` (`store_id`, `name`, `type`, `caliber`, `serial_number`, `price`, `in_stock`, `status`) VALUES
((SELECT id FROM stores WHERE slug = 'lone-star-firearms'), 'Colt 1911 Classic', 'Handgun', '.45 ACP', 'SN-C1911-001', 899.00, 10, 'active'),
((SELECT id FROM stores WHERE slug = 'lone-star-firearms'), 'Daniel Defense DDM4 V7', 'Rifle', '5.56mm', 'SN-DDM4-001', 1750.00, 7, 'active'),
((SELECT id FROM stores WHERE slug = 'lone-star-firearms'), 'Henry Big Boy Classic', 'Rifle', '.44 Magnum', 'SN-HBB-001', 850.00, 9, 'active'),
((SELECT id FROM stores WHERE slug = 'lone-star-firearms'), 'Winchester Model 70', 'Rifle', '.270 Win', 'SN-WM70-001', 1200.00, 4, 'discontinued');

-- Weapons for Northwoods Hunting Supply
INSERT INTO `weapons` (`store_id`, `name`, `type`, `caliber`, `serial_number`, `price`, `in_stock`, `status`) VALUES
((SELECT id FROM stores WHERE slug = 'northwoods-hunting-supply'), 'Savage Arms 110 Hunter', 'Rifle', '6.5 Creedmoor', 'SN-SA110-001', 750.00, 11, 'active'),
((SELECT id FROM stores WHERE slug = 'northwoods-hunting-supply'), 'CZ 457 American', 'Rifle', '.22 LR', 'SN-CZ457-001', 499.00, 20, 'active'),
((SELECT id FROM stores WHERE slug = 'northwoods-hunting-supply'), 'Browning Citori', 'Shotgun', '20 Gauge', 'SN-BCIT-001', 2100.00, 3, 'active'),
((SELECT id FROM stores WHERE slug = 'northwoods-hunting-supply'), 'Ruger GP100', 'Handgun', '.357 Magnum', 'SN-RGP100-001', 729.00, 8, 'active');
