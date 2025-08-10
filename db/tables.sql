-- Create database
CREATE DATABASE imara_tracker;
    USE imara_tracker;
-- Users table    
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(100) NOT NULL,
    UserName VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_name VARCHAR(150) NOT NULL,
    status ENUM('finished', 'idle', 'in_progress') DEFAULT 'in_progress',
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
--  table for project image (optional)
CREATE TABLE project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);


-- Triggers


-- DELIMITER $$

-- -- Trigger to update the Quantity in the books table after a row is inserted into borrowed_books
-- CREATE TRIGGER decrease_quantity_after_insert
-- AFTER INSERT ON borrowed_books
-- FOR EACH ROW
-- BEGIN
--     UPDATE books
--     SET Quantity = Quantity - 1
--     WHERE BookID = NEW.BookID;
-- END$$

-- DELIMITER ;

-- DELIMITER $$

-- -- Trigger to update the Quantity in the books table after a ReturnDate is inserted or updated
-- CREATE TRIGGER increase_quantity_after_return
-- AFTER INSERT ON borrowed_books
-- FOR EACH ROW
-- BEGIN
--     -- Check if the ReturnDate is not NULL, meaning the book has been returned
--     IF NEW.ReturnDate IS NOT NULL THEN
--         UPDATE books
--         SET Quantity = Quantity + 1
--         WHERE BookID = NEW.BookID;
--     END IF;
-- END$$

-- DELIMITER ;

-- DELIMITER $$

-- -- Trigger to update the Quantity in the books table after a ReturnDate is updated
-- CREATE TRIGGER increase_quantity_after_return_update
-- AFTER UPDATE ON borrowed_books
-- FOR EACH ROW
-- BEGIN
--     -- Check if the ReturnDate has been updated and is not NULL
--     IF NEW.ReturnDate IS NOT NULL AND OLD.ReturnDate IS NULL THEN
--         UPDATE books
--         SET Quantity = Quantity + 1
--         WHERE BookID = NEW.BookID;
--     END IF;
-- END$$

-- DELIMITER ;


-- DELIMITER $$

-- CREATE TRIGGER update_fine_status
-- BEFORE UPDATE ON borrowed_books
-- FOR EACH ROW
-- BEGIN
--     -- If FineStatus is manually updated, do not override it
--     IF OLD.FineStatus != NEW.FineStatus THEN
--         SET NEW.FineStatus = NEW.FineStatus;  -- Allow manual update
--     ELSE
--         -- If the FineStatus is 'Paid', keep it as 'Paid'
--         IF OLD.FineStatus = 'Paid' THEN
--             SET NEW.FineStatus = 'Paid';
--         -- If Fine is not NULL and greater than 0, set FineStatus to 'Not Paid'
--         ELSEIF NEW.Fine IS NOT NULL AND NEW.Fine > 0 THEN
--             SET NEW.FineStatus = 'Not Paid';
--         -- If Fine is 0, set FineStatus to 'No Fine'
--         ELSEIF NEW.Fine = 0 THEN
--             SET NEW.FineStatus = 'No Fine';
--         -- If Fine is NULL, set FineStatus to 'No Fine'
--         ELSEIF NEW.Fine IS NULL THEN
--             SET NEW.FineStatus = 'No Fine';
--         END IF;
--     END IF;
-- END $$

DELIMITER ;















