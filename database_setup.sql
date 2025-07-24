-- Create the users table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a sample user.
-- The password is 'password123'
-- The PHP password_hash() function generates a different hash each time,
-- but any of them will be valid for the same password.
-- This hash was generated with: echo password_hash('password123', PASSWORD_DEFAULT);
INSERT INTO `users` (`email`, `password`) VALUES
('test@example.com', '$2y$10$Y.aVMyv9ohhHh2yC4YyLHeC0S49hOCa4f/XQjO8aYp3JBIiVp8vB2');

