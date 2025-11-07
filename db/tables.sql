CREATE DATABASE imara_tracker;

USE imara_tracker;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    user_name VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    user_status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    status ENUM('confirmed', 'declined', 'pending') DEFAULT 'pending',
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NOT NULL,
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
    reason_type ENUM('annual', 'casual', 'medical') NOT NULL,
    leave_note VARCHAR(255) DEFAULT NULL,
    date_off DATE NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    leave_duration ENUM('full','half','multi','short') NOT NULL DEFAULT 'full',
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

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE
);

CREATE TABLE project_tags (
    project_id INT,
    tag_id INT,
    PRIMARY KEY(project_id, tag_id),
    FOREIGN KEY(project_id) REFERENCES projects(id),
    FOREIGN KEY(tag_id) REFERENCES tags(id)
);


--new table
CREATE TABLE leave_limits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    annual_balance INT NOT NULL DEFAULT 7,
    annual_extra INT NOT NULL DEFAULT 0,
    annual_half_day_count INT NOT NULL DEFAULT 0,
    annual_status ENUM('available','exhausted','overused') DEFAULT 'available',

    casual_balance INT NOT NULL DEFAULT 7,
    casual_extra INT NOT NULL DEFAULT 0,
    casual_half_day_count INT NOT NULL DEFAULT 0,
    casual_status ENUM('available','exhausted','overused') DEFAULT 'available',

    medical_balance INT NOT NULL DEFAULT 7,
    medical_extra INT NOT NULL DEFAULT 0,
    medical_half_day_count INT NOT NULL DEFAULT 0,
    medical_status ENUM('available','exhausted','overused') DEFAULT 'available',

    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
