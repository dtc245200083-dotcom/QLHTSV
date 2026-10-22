CREATE DATABASE IF NOT EXISTS qlhtsv
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE qlhtsv;

DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS classes;

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(20) NOT NULL UNIQUE,
    class_name VARCHAR(100) NOT NULL,
    major VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10) DEFAULT 'Khác',
    birth_date DATE,
    email VARCHAR(100),
    phone VARCHAR(20),
    address VARCHAR(255),
    class_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_class
        FOREIGN KEY (class_id)
        REFERENCES classes(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    score DECIMAL(4,2) NOT NULL,

    CONSTRAINT chk_score
        CHECK (score >= 0 AND score <= 10),

    CONSTRAINT fk_grade_student
        FOREIGN KEY (student_id)
        REFERENCES students(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

INSERT INTO classes (class_code, class_name, major) VALUES
('CNTT-K23G', 'Công nghệ thông tin K23G', 'Công nghệ thông tin'),
('CNTT-K23H', 'Công nghệ thông tin K23H', 'Công nghệ thông tin'),
('KTPM-K23', 'Kỹ thuật phần mềm K23', 'Kỹ thuật phần mềm');

INSERT INTO students
(student_code, full_name, gender, birth_date, email, phone, address, class_id)
VALUES
('SV001', 'Nguyễn Văn An', 'Nam', '2006-01-15',
 'an@example.com', '0912345678', 'Thái Nguyên', 1),

('SV002', 'Trần Thị Bình', 'Nữ', '2006-05-20',
 'binh@example.com', '0987654321', 'Tuyên Quang', 1),

('SV003', 'Lê Minh Đức', 'Nam', '2006-09-10',
 'duc@example.com', '0901234567', 'Hà Nội', 2),

('SV004', 'Phạm Thu Hà', 'Nữ', '2006-03-12',
 'ha@example.com', '0933333333', 'Thái Nguyên', 2),

('SV005', 'Đỗ Hoàng Nam', 'Nam', '2006-07-18',
 'nam@example.com', '0944444444', 'Bắc Giang', 3);

INSERT INTO grades (student_id, subject_name, score) VALUES
(1, 'Cơ sở dữ liệu', 8.5),
(1, 'Lập trình Web', 8.0),
(1, 'Hệ điều hành', 7.5),
(2, 'Cơ sở dữ liệu', 9.0),
(2, 'Lập trình Web', 8.5),
(3, 'Cơ sở dữ liệu', 7.5),
(4, 'Lập trình Web', 9.2),
(5, 'Hệ điều hành', 8.0);
