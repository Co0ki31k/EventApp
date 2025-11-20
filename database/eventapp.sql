-- ---------------------
-- Baza danych: EventApp
-- ---------------------
CREATE DATABASE IF NOT EXISTS EventApp;
USE EventApp;

-- ---------------------
-- Tabela użytkowników
-- ---------------------
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    avatar_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- ---------------------
-- Tabela kategorii zainteresowań
-- ---------------------
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- ---------------------
-- Tabela powiązań użytkownik - kategorie (wiele do wielu)
-- ---------------------
CREATE TABLE UserCategories (
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY(user_id, category_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE
);

-- ---------------------
-- Tabela wydarzeń
-- ---------------------
CREATE TABLE Events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    location VARCHAR(255),
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    start_datetime DATETIME,
    end_datetime DATETIME,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES Users(user_id) ON DELETE SET NULL
);

-- ---------------------
-- Tabela powiązań wydarzenia - kategorie (wiele do wielu)
-- ---------------------
CREATE TABLE EventCategories (
    event_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY(event_id, category_id),
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE
);

-- ---------------------
-- Tabela uczestnictwa użytkowników w wydarzeniach
-- ---------------------
CREATE TABLE EventParticipants (
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('going','interested','not_going') DEFAULT 'interested',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- ---------------------
-- Tabela ocen wydarzeń
-- ---------------------
CREATE TABLE EventRatings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    rated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- ---------------------
-- Tabela znajomych (wzajemne relacje)
-- ---------------------
CREATE TABLE Friends (
    user_id INT NOT NULL,
    friend_id INT NOT NULL,
    status ENUM('pending','accepted','blocked') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(user_id, friend_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (friend_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- ---------------------
-- Tabela statystyk użytkownika (podsumowania)
-- ---------------------
CREATE TABLE UserStats (
    user_id INT PRIMARY KEY,
    total_events INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,
    last_event_date DATETIME,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- ---------------------
-- Przykładowe indeksy dla przyspieszenia wyszukiwania
-- ---------------------
CREATE INDEX idx_event_start ON Events(start_datetime);
CREATE INDEX idx_event_location ON Events(latitude, longitude);
CREATE INDEX idx_user_last_login ON Users(last_login);
