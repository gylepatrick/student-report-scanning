CREATE DATABASE barcode_db;
USE barcode_db;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code_text VARCHAR(255) UNIQUE NOT NULL,  -- student barcode
    name VARCHAR(100) NOT NULL
);

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    misbehaviour VARCHAR(100) NOT NULL,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

INSERT INTO students (code_text, name) VALUES
('1234567890', 'John Doe'),
('9876543210', 'Jane Smith'),
('ABC123', 'Alice Johnson');
