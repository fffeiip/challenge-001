<?php
use Core\Database;

// Seed database with initial data
$db = Database::getInstance()->pdo();

$seedSql = <<<'SQL'
BEGIN TRANSACTION;

INSERT INTO stores (name, slug, address_line1, city, state_region, country, phone, email, created_at, updated_at) VALUES
('Outdoors Emporium', 'outdoors-emporium', '123 Forest Rd', 'Springfield', 'IL', 'USA', '555-1234', 'contact@outdoors.com', datetime('now'), datetime('now')),
('Hunter''s Haven', 'hunters-haven', '50 Hunt St', 'Nashville', 'TN', 'USA', '555-5678', 'info@hunters.com', datetime('now'), datetime('now')),
('Marksman Supplies', 'marksman-supplies', '77 Target Ave', 'Calgary', 'AB', 'Canada', '555-9988', 'sales@marksman.ca', datetime('now'), datetime('now')),
('Arms Depot', 'arms-depot', '5 Arsenal Blvd', 'Dallas', 'TX', 'USA', '555-7733', 'hello@armsdepot.com', datetime('now'), datetime('now')),
('Sportsman Gear', 'sportsman-gear', '910 Sporty Ln', 'London', '', 'UK', '555-1212', 'support@sportsman.co.uk', datetime('now'), datetime('now'));

INSERT INTO weapons (store_id, name, type, caliber, serial_number, price, in_stock, status, created_at, updated_at) VALUES
(1, 'Ranger Rifle', 'rifle', '5.56mm', 'SN1001', 799.99, 10, 'active', datetime('now'), datetime('now')),
(1, 'Tracker Shotgun', 'shotgun', '12ga', 'SN1002', 499.50, 3, 'active', datetime('now'), datetime('now')),
(2, 'Stealth Pistol', 'handgun', '9mm', 'SN2001', 350.00, 0, 'out_of_stock', datetime('now'), datetime('now')),
(2, 'Vintage Revolver', 'handgun', '.45', 'SN2002', 650.75, 2, 'discontinued', datetime('now'), datetime('now')),
(3, 'Hunter Bow', 'bow', 'N/A', 'SN3001', 299.99, 5, 'active', datetime('now'), datetime('now')),
(3, 'Crossbow Pro', 'crossbow', 'N/A', 'SN3002', 399.99, 1, 'active', datetime('now'), datetime('now')),
(4, 'Longshot Rifle', 'rifle', '7.62mm', 'SN4001', 899.99, 4, 'active', datetime('now'), datetime('now')),
(4, 'Defender Shotgun', 'shotgun', '20ga', 'SN4002', 550.00, 0, 'out_of_stock', datetime('now'), datetime('now')),
(5, 'Quickdraw Pistol', 'handgun', '9mm', 'SN5001', 425.00, 8, 'active', datetime('now'), datetime('now')),
(5, 'Silent Dart', 'airgun', '.177', 'SN5002', 199.99, 10, 'active', datetime('now'), datetime('now')),
(1, 'Marine Rifle', 'rifle', '5.56mm', 'SN1003', 750.00, 6, 'active', datetime('now'), datetime('now')),
(2, 'Guardian Shotgun', 'shotgun', '12ga', 'SN2003', 475.00, 1, 'active', datetime('now'), datetime('now')),
(3, 'Precision Rifle', 'rifle', '.308', 'SN3003', 1050.00, 0, 'out_of_stock', datetime('now'), datetime('now')),
(4, 'Patriot Pistol', 'handgun', '.45', 'SN4003', 575.00, 5, 'active', datetime('now'), datetime('now')),
(5, 'Ranger Bow', 'bow', 'N/A', 'SN5003', 250.00, 2, 'active', datetime('now'), datetime('now')),
(3, 'Elite Crossbow', 'crossbow', 'N/A', 'SN3004', 425.99, 0, 'discontinued', datetime('now'), datetime('now')),
(2, 'Compact Pistol', 'handgun', '9mm', 'SN2004', 315.00, 7, 'active', datetime('now'), datetime('now')),
(4, 'Tactical Rifle', 'rifle', '5.56mm', 'SN4004', 950.00, 3, 'active', datetime('now'), datetime('now')),
(5, 'Sport Shotgun', 'shotgun', '20ga', 'SN5004', 480.00, 6, 'active', datetime('now'), datetime('now')),
(1, 'Classic Revolver', 'handgun', '.357', 'SN1004', 610.00, 1, 'active', datetime('now'), datetime('now'));

INSERT INTO users (store_id, username, password_hash, role, created_at, updated_at) VALUES
(NULL, 'admin', '$2y$12$U7lmzz8VcSCBWZJSWwUNgutUQX1JuiRFxj4en0JnKHQUJ834qPPy2', 'super_admin', datetime('now'), datetime('now')),
(1, 'test_user', '$2y$10$kmpYbIKjZAQVFos2tvVQ0eX0p8LjtUMVTbWcbK97GiyLYX8lqnYBe', 'store_user', datetime('now'), datetime('now'));

COMMIT;
SQL;

$db->exec($seedSql);