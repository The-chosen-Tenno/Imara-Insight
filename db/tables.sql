-- Create database
CREATE DATABASE imara_tracker;
    USE imara_tracker;
-- Users table    
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    user_name VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('confirmed', 'declined', 'pending') DEFAULT 'pending',
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Projects table
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_name VARCHAR(150) NOT NULL,
    project_type ENUM('automation', 'coding') DEFAULT NULL,
    status ENUM('finished', 'idle', 'in_progress', 'cancelled') DEFAULT 'in_progress',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    INDEX idx_user_id (user_id) 
);
--  table for project image (optional)
CREATE TABLE project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description  TEXT,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason_type VARCHAR(255) NOT NULL,
    other_reason VARCHAR(255),
    date_off DATE NOT NULL,
    half_day ENUM('first', 'second') DEFAULT NULL
    description TEXT NOT NULL,
    status ENUM('approved', 'denied', 'pending') DEFAULT 'pending',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- updates
ALTER TABLE leave_requests
ADD COLUMN half_day ENUM('first', 'second') DEFAULT NULL;

ALTER TABLE projects
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD INDEX idx_user_id (user_id);

ALTER TABLE projects
ADD COLUMN project_type ENUM('automation', 'coding') DEFAULT NULL AFTER project_name;

-- project sub assignees
CREATE TABLE project_sub_assignees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    sub_assignee_id INT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (sub_assignee_id) REFERENCES users(id)
);