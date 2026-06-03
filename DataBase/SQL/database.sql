
CREATE DATABASE IF NOT EXISTS skill_connect;
USE skill_connect;


CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    bio         TEXT,
    photo       VARCHAR(255)  DEFAULT 'default.png',
    skill_teach VARCHAR(100),
    skill_learn VARCHAR(100),
    points      INT           DEFAULT 100,   
    reputation  DECIMAL(3,1)  DEFAULT 0.0,   
    badge       VARCHAR(50)   DEFAULT 'Beginner',
    completed   INT           DEFAULT 0,    
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE skill_requests (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    sender_id    INT          NOT NULL,      
    receiver_id  INT          NOT NULL,      
    message      TEXT,                       
    status       ENUM('pending','accepted','completed','cancelled') DEFAULT 'pending',
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (sender_id)   REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);


CREATE TABLE ratings (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    request_id  INT          NOT NULL,       
    rater_id    INT          NOT NULL,       
    rated_id    INT          NOT NULL,       
    stars       INT          NOT NULL,       
    comment     TEXT,                        
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,

  
    FOREIGN KEY (request_id) REFERENCES skill_requests(id),
    FOREIGN KEY (rater_id)   REFERENCES users(id),
    FOREIGN KEY (rated_id)   REFERENCES users(id)
);


INSERT INTO users (name, email, password, bio, skill_teach, skill_learn, points, reputation, badge, completed) VALUES

('Fatima Zahra El Idrissi',
 'fatima@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password123
 'Passionate English teacher from Casablanca. I love helping people improve their communication skills.',
 'English',
 'Graphic Design',
 245, 4.8, 'Mentor', 22),

('Ahmed El Amrani',
 'ahmed@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Software developer from Rabat. I enjoy teaching Python and learning new creative skills.',
 'Python Programming',
 'Photography',
 180, 4.5, 'Active Helper', 14),

('Youssef Alaoui',
 'youssef@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Excel expert and data analyst from Fes. I can teach you Excel from zero to advanced.',
 'Microsoft Excel',
 'English',
 310, 4.9, 'Expert', 35),

('Salma Bennani',
 'salma@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Graphic designer from Marrakech. I create beautiful logos and social media content.',
 'Graphic Design',
 'Python Programming',
 155, 4.3, 'Helper', 7),

('Omar Tazi',
 'omar@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Photography enthusiast from Agadir. I love capturing moments and teaching others.',
 'Photography',
 'Microsoft Excel',
 90, 3.9, 'Helper', 5),

('Nadia Chraibi',
 'nadia@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'New to the platform! I speak French fluently and want to learn web design.',
 'French',
 'Web Design',
 100, 0.0, 'Beginner', 0),

('Karim Mounir',
 'karim@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Music teacher from Casablanca. I play guitar and oud and love sharing music knowledge.',
 'Music & Guitar',
 'English',
 130, 4.1, 'Active Helper', 11),

('Zineb Fassi',
 'zineb@skillconnect.ma',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Cooking expert from Fes. I teach traditional Moroccan cooking and want to learn Excel.',
 'Moroccan Cooking',
 'Microsoft Excel',
 75, 4.6, 'Beginner', 3);



INSERT INTO skill_requests (sender_id, receiver_id, message, status) VALUES
(2, 1, 'Hi Fatima! I would love to improve my English. Can you help me?', 'completed'),
(4, 2, 'Ahmed, can you teach me the basics of Python?', 'completed'),
(5, 3, 'Youssef, I need to learn Excel for my job.', 'completed'),
(1, 4, 'Salma, I need a logo for my project!', 'completed'),
(3, 7, 'Karim, I want to learn guitar basics.', 'accepted'),
(6, 3, 'Youssef, can you teach me Excel from scratch?', 'pending'),
(7, 1, 'Fatima, I want to improve my English conversation.', 'pending');



INSERT INTO ratings (request_id, rater_id, rated_id, stars, comment) VALUES
(1, 2, 1, 5, 'Fatima is an amazing teacher! Very patient and clear.'),
(2, 4, 2, 4, 'Ahmed explains Python really well. I learned a lot!'),
(3, 5, 3, 5, 'Youssef is the best Excel teacher. Highly recommended!'),
(4, 1, 4, 4, 'Salma made a beautiful design. Very talented!');

