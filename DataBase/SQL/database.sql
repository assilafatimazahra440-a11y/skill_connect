CREATE DATABASE IF NOT EXISTS skill_connect;
USE skill_connect;
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,    
    name        VARCHAR(100) NOT NULL,             
    email       VARCHAR(150) NOT NULL UNIQUE,      
    password    VARCHAR(255) NOT NULL,             
    profile_pic VARCHAR(255) DEFAULT 'default.png',
    bio         TEXT DEFAULT '',                   
    skill_teach VARCHAR(100) DEFAULT '',           
    skill_learn VARCHAR(100) DEFAULT '',           
    points      INT DEFAULT 0,                     
    reputation  INT DEFAULT 0,                     
    badge       VARCHAR(50) DEFAULT 'Beginner',    
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP 
);


CREATE TABLE skill_requests (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sender_id   INT NOT NULL,       
    receiver_id INT NOT NULL,       
    status      VARCHAR(20) DEFAULT 'pending', 
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO users (name, email, password, bio, skill_teach, skill_learn, points, reputation, badge) VALUES

('Fatima Zahra',
 'fatima@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Hi! I am a passionate English teacher who loves helping others learn.',
 'English', 'Excel',
 120, 85, 'Helper'),

('Ahmed Benali',
 'ahmed@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'I am a graphic designer looking to improve my programming skills.',
 'Graphic Design', 'Python',
 80, 60, 'Helper'),

('Youssef Mansouri',
 'youssef@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Excel expert and data analyst. I can help anyone master spreadsheets!',
 'Excel', 'English',
 200, 150, 'Mentor'),

('Sara Alaoui',
 'sara@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Python developer and coding enthusiast. Love to share knowledge.',
 'Python', 'Graphic Design',
 160, 110, 'Mentor'),

('Karim Idrissi',
 'karim@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Just started learning web development. Excited to connect with others!',
 'Photography', 'Web Development',
 20, 10, 'Beginner');

INSERT INTO skill_requests (sender_id, receiver_id, status) VALUES
(1, 3, 'completed'),   
(2, 1, 'accepted'),    
(4, 2, 'pending'),     
(3, 4, 'completed'),   
(5, 1, 'pending');     

