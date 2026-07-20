CREATE DATABASE school_db;
USE school_db;

-- Students Table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Loans Table
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    loan_amount DECIMAL(10,2) NOT NULL,
    loan_type ENUM('Tuition', 'Books', 'Living Expenses') NOT NULL,
    status ENUM('Pending', 'Approved', 'Disbursed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Payments Table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('Cash', 'Bank Transfer', 'Online Payment') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id)
        REFERENCES loans(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Sample data (optional, remove if you don't want test rows)
INSERT INTO students (name, email, course) VALUES
('Juan Dela Cruz', 'juan@example.com', 'BS Computer Science'),
('Maria Santos', 'maria@example.com', 'BS Accountancy');

INSERT INTO loans (student_id, loan_amount, loan_type, status) VALUES
(1, 15000.00, 'Tuition', 'Approved'),
(1, 3000.00, 'Books', 'Pending'),
(2, 20000.00, 'Living Expenses', 'Disbursed');

INSERT INTO payments (loan_id, payment_amount, payment_date, payment_method) VALUES
(1, 5000.00, '2026-06-15', 'Bank Transfer'),
(3, 10000.00, '2026-07-01', 'Online Payment');
