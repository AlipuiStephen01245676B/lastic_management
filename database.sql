CREATE DATABASE IF NOT EXISTS `project_db`;
USE `project_db`;

-- Users & Points Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `phone` VARCHAR(20) UNIQUE NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `points` INT DEFAULT 0
);

-- Waste Reports Table (Aligned with image upload & district profiles)
CREATE TABLE IF NOT EXISTS `reports` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `region` VARCHAR(50) NOT NULL,
    `district` VARCHAR(100) NOT NULL,
    `waste_type` VARCHAR(50) NOT NULL,
    `description` TEXT,
    `image_path` VARCHAR(255) DEFAULT '',
    `status` VARCHAR(20) DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Waste Pickups Table
CREATE TABLE IF NOT EXISTS `pickups` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `region` VARCHAR(50) NOT NULL,
    `location` TEXT NOT NULL,
    `pickup_date` DATE NOT NULL,
    `waste_type` VARCHAR(50) NOT NULL,
    `status` VARCHAR(20) DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Recycling Centers across all 16 Regions of Ghana
CREATE TABLE IF NOT EXISTS `recycling_centers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `region` VARCHAR(50) NOT NULL,
    `center_name` VARCHAR(100) NOT NULL,
    `location` VARCHAR(100) NOT NULL
);

INSERT INTO `recycling_centers` (`region`, `center_name`, `location`) VALUES
('Greater Accra', 'Accra Eco-Plastics Hub', 'Ring Road Central, Accra'),
('Ashanti', 'Kumasi Circular Recycling', 'Asokwa, Kumasi'),
('Western', 'Takoradi Ocean Clean Initiative', 'Harbour Area, Takoradi'),
('Central', 'Cape Coast Green Drive', 'Abura, Cape Coast'),
('Eastern', 'Koforidua Eco-Collectors', 'Main Market, Koforidua'),
('Volta', 'Ho Clean City Depot', 'Civic Centre, Ho'),
('Northern', 'Tamale Resource Recovery Center', 'Hospital Road, Tamale'),
('Upper East', 'Bolgatanga Waste Hub', 'Zongo, Bolgatanga'),
('Upper West', 'Wa Green Planet', 'Wa Central'),
('Bono', 'Sunyani Plastic Recyclers', 'Fiapre, Sunyani'),
('Bono East', 'Techiman Waste Collectors', 'Market Area, Techiman'),
('Ahafo', 'Goaso Eco-Bins', 'Main Station, Goaso'),
('Oti', 'Dambai Plastic Point', 'Lakeside Road, Dambai'),
('Savannah', 'Damongo Clean Enclave', 'Canteen, Damongo'),
('North East', 'Nalerigu Recovery Base', 'Palace Junction, Nalerigu'),
('Western North', 'Sefwi Wiawso Eco-Center', 'Main Road, Sefwi Wiawso')
ON DUPLICATE KEY UPDATE id=id;