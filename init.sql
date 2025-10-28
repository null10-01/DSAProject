CREATE DATABASE IF NOT EXISTS flight_booking;
USE flight_booking;


-- users: passengers and admin
CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
email VARCHAR(150) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
is_admin TINYINT(1) DEFAULT 0,
priority_level INT DEFAULT 1, -- 1 (low) .. 5 (high)
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- flights (UPDATED with flight_type and arrival_date)
CREATE TABLE flights (
id INT AUTO_INCREMENT PRIMARY KEY,
flight_code VARCHAR(30) UNIQUE NOT NULL,
source VARCHAR(100) NOT NULL,
destination VARCHAR(100) NOT NULL,
flight_type VARCHAR(20) NOT NULL DEFAULT 'Domestic',
flight_date DATE NOT NULL,
departure_time TIME,
arrival_time TIME,
arrival_date DATE NOT NULL,
seats_total INT DEFAULT 100,
seats_booked INT DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- bookings
CREATE TABLE bookings (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
flight_id INT NOT NULL,
status ENUM('CONFIRMED','WAITLISTED','CANCELLED') DEFAULT 'WAITLISTED',
priority INT NOT NULL DEFAULT 1,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (flight_id) REFERENCES flights(id) ON DELETE CASCADE
);


-- sample admin (password: admin123)
INSERT INTO users (name,email,password,is_admin,priority_level)
VALUES ('Admin','admin@example.com', '$2y$12$0sbUHf1ayPB7cHIkdfCLD.7xSGCnVpL9whQ9U4.CJGh488GG/N5lm', 1, 5);


-- sample passengers (password: password123) - All with priority level 1
INSERT INTO users (name,email,password,is_admin,priority_level)
VALUES ('Alice','alice@example.com','$2y$12$MoibA99UY2f68kyNl446ROvzdioJO37uLNlLoEeD/at2K9APXkzuC',0,1),
('Bob','bob@example.com','$2y$12$MoibA99UY2f68kyNl446ROvzdioJO37uLNlLoEeD/at2K9APXkzuC',0,1);


-- 200 new flights with varied codes and 'flight_type' column
INSERT INTO flights (flight_code,source,destination,flight_type,flight_date,departure_time,arrival_time,arrival_date,seats_total)
VALUES
    ('AI374', 'Delhi', 'Mumbai', 'Domestic', '2026-02-12', '12:00:00', '14:20:00', '2026-02-12', 151),
    ('EK3836', 'Mumbai', 'Bangkok', 'International', '2026-02-09', '11:05:00', '16:15:00', '2026-02-09', 246),
    ('AI570', 'Mumbai', 'Delhi', 'Domestic', '2026-02-27', '04:00:00', '06:20:00', '2026-02-27', 123),
    ('AI745', 'Kolkata', 'Delhi', 'Domestic', '2026-01-20', '00:05:00', '02:45:00', '2026-01-20', 178),
    ('AI627', 'Bengaluru', 'Delhi', 'Domestic', '2026-02-15', '16:15:00', '19:25:00', '2026-02-15', 151),
    ('AI122', 'Delhi', 'Mumbai', 'Domestic', '2025-12-16', '13:30:00', '15:50:00', '2025-12-16', 155),
    ('6E454', 'Pune', 'Delhi', 'Domestic', '2025-12-19', '16:55:00', '19:35:00', '2025-12-19', 140),
    ('AI613', 'Delhi', 'Mumbai', 'Domestic', '2026-01-28', '19:25:00', '21:55:00', '2026-01-28', 122),
    ('UK661', 'Delhi', 'Lucknow', 'Domestic', '2026-02-28', '04:55:00', '06:30:00', '2026-02-28', 113),
    ('SQ9716', 'Delhi', 'Dubai', 'International', '2026-01-19', '02:00:00', '06:40:00', '2026-01-19', 314),
    ('FL881', 'Delhi', 'Mumbai', 'Domestic', '2026-01-16', '06:05:00', '08:35:00', '2026-01-16', 121),
    ('FL129', 'Ahmedabad', 'Delhi', 'Domestic', '2025-12-12', '12:00:00', '13:50:00', '2025-12-12', 152),
    ('AI972', 'Delhi', 'Kolkata', 'Domestic', '2026-02-26', '14:55:00', '17:35:00', '2026-02-26', 160),
    ('AF9224', 'Delhi', 'New York', 'International', '2026-02-10', '00:15:00', '16:25:00', '2026-02-10', 338),
    ('QR9546', 'Delhi', 'Toronto', 'International', '2025-12-07', '02:50:00', '18:50:00', '2025-12-07', 315),
    ('AI625', 'Lucknow', 'Delhi', 'Domestic', '2026-01-09', '14:40:00', '16:15:00', '2026-01-09', 149),
    ('QR2312', 'Delhi', 'Toronto', 'International', '2025-12-25', '06:20:00', '21:30:00', '2025-12-25', 281),
    ('UK189', 'Kolkata', 'Delhi', 'Domestic', '2025-12-29', '17:25:00', '20:15:00', '2025-12-29', 110),
    ('SG190', 'Delhi', 'Ahmedabad', 'Domestic', '2026-01-18', '03:40:00', '05:30:00', '2026-01-18', 127),
    ('TG4936', 'Delhi', 'Toronto', 'International', '2025-12-30', '10:15:00', '01:25:00', '2025-12-31', 334),
    ('AI364', 'Delhi', 'Pune', 'Domestic', '2026-02-09', '02:50:00', '05:30:00', '2026-02-09', 170),
    ('AI893', 'Bengaluru', 'Delhi', 'Domestic', '2025-12-17', '18:50:00', '22:10:00', '2025-12-17', 151),
    ('AI467', 'Kochi', 'Bengaluru', 'Domestic', '2025-12-28', '11:15:00', '12:45:00', '2025-12-28', 113),
    ('TG4970', 'Mumbai', 'Bangkok', 'International', '2026-02-25', '19:40:00', '00:50:00', '2026-02-26', 308),
    ('AI460', 'Goa', 'Mumbai', 'Domestic', '2025-12-08', '11:50:00', '13:10:00', '2025-12-08', 145),
    ('FL254', 'Mumbai', 'Delhi', 'Domestic', '2026-02-07', '14:10:00', '16:40:00', '2026-02-07', 160),
    ('LH8200', 'Delhi', 'Tokyo', 'International', '2025-12-12', '14:20:00', '23:40:00', '2025-12-12', 315),
    ('FL125', 'Delhi', 'Lucknow', 'Domestic', '2026-02-24', '15:25:00', '16:40:00', '2026-02-24', 163),
    ('AI765', 'Kolkata', 'Delhi', 'Domestic', '2025-12-19', '14:25:00', '16:55:00', '2025-12-19', 118),
    ('AI305', 'Chennai', 'Hyderabad', 'Domestic', '2025-12-08', '11:15:00', '12:55:00', '2025-12-08', 105),
    ('AF9809', 'Mumbai', 'Bangkok', 'International', '2025-12-25', '16:30:00', '22:10:00', '2025-12-25', 255),
    ('AI361', 'Goa', 'Mumbai', 'Domestic', '2025-12-21', '10:45:00', '12:05:00', '2025-12-21', 145),
    ('SG157', 'Mumbai', 'Delhi', 'Domestic', '2026-01-20', '02:05:00', '04:35:00', '2026-01-20', 165),
    ('AF2284', 'Delhi', 'Dubai', 'International', '2026-01-23', '06:15:00', '10:45:00', '2026-01-23', 242),
    ('FL836', 'Bengaluru', 'Delhi', 'Domestic', '2026-01-25', '15:25:00', '18:35:00', '2026-01-25', 158),
    ('QR6963', 'London', 'Bengaluru', 'International', '2026-02-02', '19:40:00', '05:40:00', '2026-02-03', 342),
    ('QR8111', 'Delhi', 'Dubai', 'International', '2026-02-01', '06:40:00', '11:10:00', '2026-02-01', 342),
    ('6E479', 'Delhi', 'Ahmedabad', 'Domestic', '2026-01-29', '02:10:00', '04:10:00', '2026-01-29', 133),
    ('FL179', 'Mumbai', 'Bengaluru', 'Domestic', '2025-12-11', '15:05:00', '17:10:00', '2025-12-11', 142),
    ('6E154', 'Pune', 'Delhi', 'Domestic', '2026-02-23', '06:05:00', '08:55:00', '2026-02-23', 105),
    ('TG4976', 'Delhi', 'Toronto', 'International', '2025-12-22', '16:05:00', '07:15:00', '2025-12-23', 305),
    ('AF8767', 'Kuala Lumpur', 'Chennai', 'International', '2026-02-27', '04:45:00', '09:35:00', '2026-02-27', 260),
    ('AF9499', 'Delhi', 'Dubai', 'International', '2026-01-08', '02:30:00', '07:00:00', '2026-01-08', 345),
    ('BA8072', 'Kuala Lumpur', 'Chennai', 'International', '2025-12-30', '07:45:00', '12:25:00', '2025-12-30', 256),
    ('AF8597', 'Singapore', 'Mumbai', 'International', '2026-01-20', '07:35:00', '14:05:00', '2026-01-20', 315),
    ('FL253', 'Mumbai', 'Bengaluru', 'Domestic', '2026-01-23', '08:50:00', '10:55:00', '2026-01-23', 133),
    ('QR2315', 'Bengaluru', 'London', 'International', '2026-02-26', '13:50:00', '00:30:00', '2026-02-27', 265),
    ('AF6663', 'Mumbai', 'Bangkok', 'International', '2026-01-14', '19:50:00', '01:00:00', '2026-01-15', 314),
    ('AI725', 'Delhi', 'Ahmedabad', 'Domestic', '2025-12-18', '08:35:00', '10:15:00', '2025-12-18', 123),
    ('FL171', 'Jaipur', 'Mumbai', 'Domestic', '2026-02-12', '12:25:00', '14:25:00', '2026-02-12', 156);

INSERT INTO flights (flight_code,source,destination,flight_type,flight_date,departure_time,arrival_time,arrival_date,seats_total)
VALUES
    ('AI960', 'Goa', 'Mumbai', 'Domestic', '2026-02-09', '18:30:00', '19:50:00', '2026-02-09', 110),
    ('AI620', 'Pune', 'Delhi', 'Domestic', '2026-01-11', '03:10:00', '05:50:00', '2026-01-11', 119),
    ('AI628', 'Goa', 'Mumbai', 'Domestic', '2026-01-08', '09:20:00', '10:50:00', '2026-01-08', 151),
    ('AF7700', 'Delhi', 'Dubai', 'International', '2025-12-14', '04:10:00', '08:50:00', '2025-12-14', 332),
    ('UK636', 'Mumbai', 'Delhi', 'Domestic', '2026-02-10', '10:50:00', '13:20:00', '2026-02-10', 101),
    ('SG161', 'Kochi', 'Bengaluru', 'Domestic', '2026-01-12', '14:40:00', '16:20:00', '2026-01-12', 170),
    ('FL427', 'Mumbai', 'Bengaluru', 'Domestic', '2026-02-05', '03:40:00', '05:35:00', '2026-02-05', 103),
    ('EK3852', 'New York', 'Delhi', 'International', '2026-02-05', '17:15:00', '08:25:00', '2026-02-06', 228),
    ('SG876', 'Delhi', 'Kolkata', 'Domestic', '2026-01-13', '01:50:00', '04:20:00', '2026-01-13', 173),
    ('UK373', 'Delhi', 'Kolkata', 'Domestic', '2026-01-05', '07:15:00', '09:55:00', '2026-01-05', 155),
    ('AF4891', 'Delhi', 'New York', 'International', '2026-01-13', '06:30:00', '22:10:00', '2026-01-13', 315),
    ('SG135', 'Delhi', 'Pune', 'Domestic', '2026-01-09', '21:05:00', '23:35:00', '2026-01-09', 113),
    ('AI7687', 'Delhi', 'New York', 'International', '2026-02-03', '09:10:00', '00:20:00', '2026-02-04', 315),
    ('AF8598', 'Toronto', 'Delhi', 'International', '2026-02-23', '05:55:00', '19:45:00', '2026-02-23', 305),
    ('6E911', 'Delhi', 'Pune', 'Domestic', '2026-02-21', '19:05:00', '21:45:00', '2026-02-21', 113),
    ('BA8462', 'Mumbai', 'Bangkok', 'International', '2026-01-01', '06:50:00', '12:30:00', '2026-01-01', 288),
    ('AI767', 'Delhi', 'Mumbai', 'Domestic', '2026-02-01', '02:20:00', '04:50:00', '2026-02-01', 171),
    ('AF8163', 'Mumbai', 'Paris', 'International', '2026-02-12', '12:55:00', '22:35:00', '2026-02-12', 325),
    ('BA8051', 'New York', 'Delhi', 'International', '2026-02-09', '18:00:00', '09:10:00', '2026-02-10', 309),
    ('AI840', 'Delhi', 'Pune', 'Domestic', '2025-12-16', '12:00:00', '14:40:00', '2025-12-16', 165),
    ('AI583', 'Pune', 'Delhi', 'Domestic', '2026-02-01', '17:40:00', '20:10:00', '2026-02-01', 177),
    ('FL174', 'Kolkata', 'Delhi', 'Domestic', '2026-01-09', '11:55:00', '14:45:00', '2026-01-09', 145),
    ('AI950', 'Delhi', 'Mumbai', 'Domestic', '2026-01-27', '04:55:00', '07:25:00', '2026-01-27', 175),
    ('QR6883', 'New York', 'Delhi', 'International', '2026-01-09', '02:00:00', '16:50:00', '2026-01-09', 242),
    ('6E433', 'Delhi', 'Mumbai', 'Domestic', '2026-01-13', '03:15:00', '05:45:00', '2026-01-13', 145),
    ('FL190', 'Mumbai', 'Goa', 'Domestic', '2025-12-23', '10:05:00', '11:35:00', '2025-12-23', 175),
    ('AI768', 'Mumbai', 'Goa', 'Domestic', '2025-12-07', '18:10:00', '19:40:00', '2025-12-07', 158),
    ('AI407', 'Kolkata', 'Delhi', 'Domestic', '2026-02-05', '03:15:00', '05:45:00', '2026-02-05', 133),
    ('FL922', 'Delhi', 'Lucknow', 'Domestic', '2026-01-08', '07:25:00', '08:40:00', '2026-01-08', 121),
    ('AF9638', 'New York', 'Delhi', 'International', '2025-12-14', '04:45:00', '19:35:00', '2025-12-14', 270),
    ('AI564', 'Bengaluru', 'Kochi', 'Domestic', '2026-01-13', '13:00:00', '14:40:00', '2026-01-13', 151),
    ('SG609', 'Delhi', 'Mumbai', 'Domestic', '2026-02-23', '06:15:00', '08:45:00', '2026-02-23', 160),
    ('AI753', 'Jaipur', 'Mumbai', 'Domestic', '2026-01-06', '06:30:00', '08:30:00', '2026-01-06', 151),
    ('FL172', 'Bengaluru', 'Mumbai', 'Domestic', '2025-12-18', '05:40:00', '07:45:00', '2025-12-18', 133),
    ('QR2481', 'Delhi', 'Tokyo', 'International', '2025-12-11', '19:35:00', '05:05:00', '2025-12-12', 307),
    ('AI490', 'Mumbai', 'Bengaluru', 'Domestic', '2026-01-20', '06:55:00', '08:50:00', '2026-01-20', 91),
    ('AI359', 'Ahmedabad', 'Delhi', 'Domestic', '2025-12-25', '16:05:00', '17:55:00', '2025-12-25', 93),
    ('6E315', 'Pune', 'Delhi', 'Domestic', '2026-02-28', '21:00:00', '23:40:00', '2026-02-28', 113),
    ('AF3667', 'Delhi', 'New York', 'International', '2026-01-25', '14:15:00', '05:45:00', '2026-01-26', 315),
    ('FL914', 'Delhi', 'Lucknow', 'Domestic', '2025-12-16', '08:40:00', '09:55:00', '2025-12-16', 151),
    ('AI629', 'Bengaluru', 'Delhi', 'Domestic', '2026-01-06', '01:50:00', '05:00:00', '2026-01-06', 105),
    ('AI624', 'Delhi', 'Mumbai', 'Domestic', '2026-02-18', '23:40:00', '02:10:00', '2026-02-19', 170),
    ('FL308', 'Mumbai', 'Goa', 'Domestic', '2026-01-03', '03:10:00', '04:30:00', '2026-01-03', 137),
    ('EK3317', 'Mumbai', 'Singapore', 'International', '2026-01-07', '03:00:00', '09:10:00', '2026-01-07', 315),
    ('UK350', 'Delhi', 'Kolkata', 'Domestic', '2025-12-27', '07:35:00', '10:05:00', '2025-12-27', 160),
    ('AI722', 'Kolkata', 'Delhi', 'Domestic', '2026-01-03', '10:15:00', '12:45:00', '2026-01-03', 142),
    ('FL977', 'Hyderabad', 'Chennai', 'Domestic', '2026-02-21', '16:15:00', '17:55:00', '2026-02-21', 115),
    ('FL394', 'Chennai', 'Hyderabad', 'Domestic', '2026-01-19', '11:15:00', '12:55:00', '2026-01-19', 151),
    ('AI333', 'Delhi', 'Lucknow', 'Domestic', '2026-01-13', '06:05:00', '07:20:00', '2026-01-13', 101),
    ('AI824', 'Mumbai', 'Bengaluru', 'Domestic', '2026-01-17', '07:05:00', '09:10:00', '2026-01-17', 133);

INSERT INTO flights (flight_code,source,destination,flight_type,flight_date,departure_time,arrival_time,arrival_date,seats_total)
VALUES
    ('AI175', 'Delhi', 'Mumbai', 'Domestic', '2026-02-10', '03:30:00', '06:10:00', '2026-02-10', 160),
    ('AF3217', 'New York', 'Delhi', 'International', '2025-12-14', '04:20:00', '19:10:00', '2025-12-14', 285),
    ('AI6277', 'Delhi', 'New York', 'International', '2026-01-10', '10:45:00', '02:35:00', '2026-01-11', 255),
    ('AI419', 'Delhi', 'Ahmedabad', 'Domestic', '2025-12-12', '12:15:00', '13:55:00', '2025-12-12', 151),
    ('AI981', 'Delhi', 'Mumbai', 'Domestic', '2026-01-20', '05:05:00', '07:25:00', '2026-01-20', 147),
    ('LH5845', 'New York', 'Delhi', 'International', '2026-01-16', '13:50:00', '04:00:00', '2026-01-17', 345),
    ('BA8050', 'Bengaluru', 'London', 'International', '2025-12-10', '07:35:00', '18:15:00', '2025-12-10', 325),
    ('AI672', 'Kochi', 'Bengaluru', 'Domestic', '2026-02-13', '03:00:00', '04:40:00', '2026-02-13', 113),
    ('AF6622', 'Delhi', 'New York', 'International', '2026-01-26', '14:20:00', '05:50:00', '2026-01-27', 335),
    ('EK3830', 'Delhi', 'Dubai', 'International', '2026-01-18', '03:35:00', '08:05:00', '2026-01-18', 343),
    ('AF5091', 'New York', 'Delhi', 'International', '2026-01-08', '04:40:00', '19:30:00', '2026-01-08', 315),
    ('TG4978', 'Delhi', 'Toronto', 'International', '2026-01-22', '03:05:00', '18:15:00', '2026-01-22', 320),
    ('FL435', 'Delhi', 'Mumbai', 'Domestic', '2026-01-10', '16:05:00', '18:45:00', '2026-01-10', 170),
    ('AF7914', 'Delhi', 'New York', 'International', '2026-02-15', '06:05:00', '21:45:00', '2026-02-15', 315),
    ('AI331', 'Mumbai', 'Jaipur', 'Domestic', '2026-02-10', '19:40:00', '21:40:00', '2026-02-10', 173),
    ('QR6828', 'Toronto', 'Delhi', 'International', '2026-02-10', '08:35:00', '22:25:00', '2026-02-10', 255),
    ('TG4987', 'Toronto', 'Delhi', 'International', '2026-01-13', '02:00:00', '15:50:00', '2026-01-13', 305),
    ('SG607', 'Mumbai', 'Bengaluru', 'Domestic', '2025-12-14', '04:40:00', '06:45:00', '2025-12-14', 160),
    ('AI555', 'Delhi', 'Mumbai', 'Domestic', '2025-12-10', '14:40:00', '17:10:00', '2025-1Two-10', 151),
    ('SG456', 'Mumbai', 'Jaipur', 'Domestic', '2025-12-25', '06:30:00', '08:30:00', '2025-12-25', 133),
    ('AI682', 'Delhi', 'Mumbai', 'Domestic', '2025-12-09', '18:45:00', '21:15:00', '2025-12-09', 147),
    ('SG175', 'Delhi', 'Mumbai', 'Domestic', '2026-02-19', '14:50:00', '17:20:00', '2026-02-19', 158),
    ('AF2882', 'Delhi', 'New York', 'International', '2026-01-26', '13:00:00', '04:10:00', '2026-01-27', 332),
    ('AF3037', 'Mumbai', 'Bangkok', 'International', '2026-01-23', '03:10:00', '08:20:00', '2026-01-23', 290),
    ('LH3660', 'Delhi', 'New York', 'International', '2025-12-07', '03:55:00', '19:25:00', '2025-12-07', 288),
    ('FL186', 'Mumbai', 'Goa', 'Domestic', '2025-12-18', '21:05:00', '22:35:00', '2025-12-18', 113),
    ('FL431', 'Hyderabad', 'Chennai', 'Domestic', '2026-02-09', '21:55:00', '23:35:00', '2026-02-09', 170),
    ('AI225', 'Delhi', 'Mumbai', 'Domestic', '2025-12-08', '02:00:00', '04:30:00', '2025-12-08', 110),
    ('FL600', 'Bengaluru', 'Delhi', 'Domestic', '2026-02-23', '08:35:00', '11:45:00', '2026-02-23', 142),
    ('AI842', 'Mumbai', 'Bengaluru', 'Domestic', '2025-12-21', '06:05:00', '08:10:00', '2025-12-21', 177),
    ('EK3973', 'Delhi', 'Dubai', 'International', '2026-02-19', '02:30:00', '07:10:00', '2026-02-19', 315),
    ('UK619', 'Delhi', 'Kolkata', 'Domestic', '2025-12-09', '18:50:00', '21:30:00', '2025-12-09', 110),
    ('AI8591', 'Delhi', 'Tokyo', 'International', '2026-02-13', '06:10:00', '15:40:00', '2026-02-13', 255),
    ('AI661', 'Delhi', 'Pune', 'Domestic', '2026-01-09', '18:50:00', '21:30:00', '2026-01-09', 170),
    ('AI970', 'Bengaluru', 'Kochi', 'Domestic', '2025-12-27', '21:05:00', '22:35:00', '2025-12-27', 151),
    ('EK2817', 'Mumbai', 'Paris', 'International', '2025-12-26', '14:35:00', '00:05:00', '2025-12-27', 309),
    ('TG4938', 'Delhi', 'New York', 'International', '2026-01-29', '12:00:00', '03:10:00', '2026-01-30', 253),
    ('FL601', 'Delhi', 'Mumbai', 'Domestic', '2025-12-10', '04:00:00', '06:30:00', '2025-12-10', 177),
    ('AI161', 'Mumbai', 'Goa', 'Domestic', '2026-01-08', '08:50:00', '10:20:00', '2026-01-08', 160),
    ('EK2343', 'Dubai', 'Delhi', 'International', '2025-12-24', '18:25:00', '22:15:00', '2025-12-24', 338),
    ('AI334', 'Goa', 'Mumbai', 'Domestic', '2026-01-13', '10:30:00', '12:00:00', '2026-01-13', 147),
    ('FL314', 'Ahmedabad', 'Delhi', 'Domestic', '2026-01-08', '09:20:00', '11:10:00', '2026-01-08', 151),
    ('6E452', 'Delhi', 'Mumbai', 'Domestic', '2026-01-13', '08:45:00', '11:15:00', '2026-01-13', 140),
    ('AI556', 'Kolkata', 'Delhi', 'Domestic', '2026-01-02', '12:45:00', '15:25:00', '2026-01-02', 151),
    ('QR7859', 'Delhi', 'New York', 'International', '2026-02-18', '09:20:00', '00:30:00', '2026-02-19', 302),
    ('SG140', 'Delhi', 'Mumbai', 'Domestic', '2026-02-12', '17:35:00', '20:15:00', '2026-02-12', 145),
    ('BA1737', 'Chennai', 'Kuala Lumpur', 'International', '2026-01-28', '21:05:00', '01:35:00', '2026-01-29', 270),
    ('QR4509', 'Mumbai', 'Singapore', 'International', '2026-02-17', '18:40:00', '00:50:00', '2026-02-18', 295),
    ('6E227', 'Kolkata', 'Delhi', 'Domestic', '2026-02-09', '18:55:00', '21:35:00', '2026-0Two-09', 160);