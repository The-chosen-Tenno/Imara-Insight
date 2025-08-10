-- for login purpose
CREATE ACCOUNT THAN UPDATE 

    UPDATE Users
    SET Role = 'Admin'
    WHERE Email = 'your@example.com';
AFTER DONE DISABLE create-account.php





-- Insert dummy data into the Users table
INSERT INTO users (name, username, email, password_hash, role, photo)
VALUES
('Alice Johnson', 'alicej', 'alice@example.com', SHA2('password123', 256), 'admin', 'uploads/1.png'),
('Bob Smith', 'bobsmith', 'bob@example.com', SHA2('mypassword', 256), 'user', 'uploads/1.png'),
('Charlie Lee', 'charlie', 'charlie@example.com', SHA2('charliepass', 256), 'user', 'uploads/1.png');



-- Insert dummy data into the project table
INSERT INTO projects (user_id, project_name, status)
VALUES
(1, 'Website Redesign', 'in_progress'),
(2, 'Mobile App Launch', 'idle'),
(3, 'Backend API Development', 'finished'),
(1, 'Marketing Campaign', 'in_progress');





