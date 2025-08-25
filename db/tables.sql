CREATE DATABASE imara_tracker;
USE imara_tracker;

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

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_name VARCHAR(150) NOT NULL,
    project_type ENUM('automation', 'coding') DEFAULT NULL,
    status ENUM('finished', 'idle', 'in_progress', 'cancelled') DEFAULT 'in_progress',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

CREATE TABLE project_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason_type VARCHAR(255) NOT NULL,
    other_reason VARCHAR(255) DEFAULT NULL,
    date_off DATE NOT NULL,
    leave_duration ENUM('full','half') NOT NULL DEFAULT 'full',
    half_day ENUM('first','second') DEFAULT NULL,
    description TEXT NOT NULL,
    status ENUM('approved','denied','pending') DEFAULT 'pending',
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE project_sub_assignees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    sub_assignee_id INT NOT NULL,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id),
    FOREIGN KEY (sub_assignee_id) REFERENCES users(id)
);


--updates
ALTER TABLE leave_requests
ADD COLUMN leave_duration ENUM('full','half') NOT NULL DEFAULT 'full' AFTER date_off;

-- 2. Add the updated_at timestamp column to track edits
ALTER TABLE leave_requests
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER uploaded_at;

ALTER TABLE projects
ADD COLUMN project_description TEXT DEFAULT NULL AFTER project_name;