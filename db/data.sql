-- for login purpose
CREATE ACCOUNT THAN UPDATE 

    UPDATE Users
    SET Role = 'Admin'
    WHERE Email = 'your@example.com';
AFTER DONE DISABLE create-account.php





-- Insert dummy data into the Users table
INSERT INTO users (full_name, user_name, email, password, role)
VALUES
('admin', 'admin1', 'admin@example.com', SHA2('admin123', 256), 'admin'),
('user', 'user1', 'user@example.com', SHA2('user123', 256), 'user')



-- Insert dummy data into the project table
INSERT INTO projects (user_id, project_name, status)
VALUES
(1, 'Website Redesign', 'in_progress'),
(2, 'Mobile App Launch', 'idle'),
(3, 'Backend API Development', 'finished'),
(1, 'Marketing Campaign', 'in_progress');





