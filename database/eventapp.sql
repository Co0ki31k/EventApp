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
    role ENUM('user', 'admin', 'company') DEFAULT 'user' NOT NULL,
    email_verified BOOLEAN DEFAULT 0,
    is_active BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- ---------------------
-- Tabela weryfikacji maila przy rejestracji
-- ---------------------

CREATE TABLE EmailVerifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    code VARCHAR(10) DEFAULT NULL,
    expires_at DATETIME NOT NULL,
    verified_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    UNIQUE(token),
    INDEX (user_id),
    INDEX (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------
-- Tabela resetu haseł (jednorazowe linki)
-- ---------------------
CREATE TABLE PasswordResets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    UNIQUE(token),
    INDEX (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------
-- Tabela kategorii głównych (Sport, Filmy, Muzyka, etc.)
-- ---------------------
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------
-- Tabela podkategorii/tagów (Koszykówka, Piłka nożna, etc.)
-- ---------------------
CREATE TABLE Subcategories (
    subcategory_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE,
    UNIQUE(category_id, name)
);

-- ---------------------
-- Tabela powiązań użytkownik - podkategorie (wybory użytkownika)
-- ---------------------
CREATE TABLE UserInterests (
    user_id INT NOT NULL,
    subcategory_id INT NOT NULL,
    selected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(user_id, subcategory_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (subcategory_id) REFERENCES Subcategories(subcategory_id) ON DELETE CASCADE
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
-- Tabela powiązań wydarzenia - podkategorie (tagi wydarzenia)
-- ---------------------
CREATE TABLE EventSubcategories (
    event_id INT NOT NULL,
    subcategory_id INT NOT NULL,
    PRIMARY KEY(event_id, subcategory_id),
    FOREIGN KEY (event_id) REFERENCES Events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (subcategory_id) REFERENCES Subcategories(subcategory_id) ON DELETE CASCADE
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
CREATE INDEX idx_subcategory_category ON Subcategories(category_id);
CREATE INDEX idx_user_interests ON UserInterests(user_id);

-- ---------------------
-- Przykładowe dane - Kategorie główne
-- ---------------------
INSERT INTO Categories (name, description, icon) VALUES
('Sport', 'Aktywności sportowe i fitness', 'sport-icon.svg'),
('Filmy i Seriale', 'Kino, streaming i produkcje filmowe', 'movie-icon.svg'),
('Muzyka', 'Koncerty, festiwale i gatunki muzyczne', 'music-icon.svg'),
('Technologia', 'IT, gadżety i innowacje', 'tech-icon.svg'),
('Sztuka', 'Malarstwo, rzeźba i sztuki wizualne', 'art-icon.svg'),
('Podróże', 'Turystyka i odkrywanie świata', 'travel-icon.svg');

-- ---------------------
-- Przykładowe dane - Podkategorie dla Sportu
-- ---------------------
INSERT INTO Subcategories (category_id, name, description) VALUES
(1, 'Piłka nożna', 'Mecze i treningi piłkarskie'),
(1, 'Koszykówka', 'Basketball i streetball'),
(1, 'Siatkówka', 'Volleyball - plażowa i halowa'),
(1, 'Bieganie', 'Jogging, maratony i biegi'),
(1, 'Fitness', 'Siłownia i ćwiczenia'),
(1, 'Joga', 'Praktyki jogi i medytacja'),
(1, 'Pływanie', 'Basen i pływanie otwarte'),
(1, 'Tenis', 'Tenis ziemny i stołowy'),
(1, 'Wspinaczka', 'Climbing i bouldering'),
(1, 'Sporty wodne', 'Kajaki, żeglarstwo, surfing');

-- ---------------------
-- Przykładowe dane - Podkategorie dla Filmów
-- ---------------------
INSERT INTO Subcategories (category_id, name, description) VALUES
(2, 'Akcja', 'Filmy akcji i przygodowe'),
(2, 'Komedia', 'Filmy komediowe'),
(2, 'Dramat', 'Filmy dramatyczne'),
(2, 'Sci-Fi', 'Science fiction i fantasy'),
(2, 'Horror', 'Filmy grozy'),
(2, 'Thriller', 'Filmy sensacyjne'),
(2, 'Dokumentalne', 'Dokumenty i reportaże'),
(2, 'Animacje', 'Filmy animowane'),
(2, 'Seriale', 'Produkcje serialowe');

-- ---------------------
-- Przykładowe dane - Podkategorie dla Muzyki
-- ---------------------
INSERT INTO Subcategories (category_id, name, description) VALUES
(3, 'Rock', 'Muzyka rockowa'),
(3, 'Pop', 'Muzyka pop'),
(3, 'Hip-Hop', 'Rap i hip-hop'),
(3, 'Jazz', 'Jazz i blues'),
(3, 'Elektroniczna', 'EDM, techno, house'),
(3, 'Klasyczna', 'Muzyka klasyczna'),
(3, 'Metal', 'Heavy metal i subgatunki'),
(3, 'Indie', 'Muzyka indie i alternatywna'),
(3, 'Reggae', 'Reggae i ska');

-- ---------------------
-- Przykładowe dane - Podkategorie dla Technologii
-- ---------------------
INSERT INTO Subcategories (category_id, name, description) VALUES
(4, 'Programowanie', 'Coding i development'),
(4, 'AI i ML', 'Sztuczna inteligencja'),
(4, 'Gaming', 'Gry komputerowe i konsole'),
(4, 'Gadżety', 'Elektronika i urządzenia'),
(4, 'Cyberbezpieczeństwo', 'Security i hacking'),
(4, 'Blockchain', 'Kryptowaluty i blockchain'),
(4, 'Fotografia', 'Fotografia cyfrowa'),
(4, 'Drony', 'Latające urządzenia');

-- ---------------------
-- Przykładowe dane - Podkategorie dla Sztuki
-- ---------------------
INSERT INTO Subcategories (category_id, name, description) VALUES
(5, 'Malarstwo', 'Obrazy i techniki malarskie'),
(5, 'Rzeźba', 'Rzeźbiarstwo'),
(5, 'Fotografia artystyczna', 'Art photography'),
(5, 'Grafika', 'Graphic design'),
(5, 'Street art', 'Graffiti i murale'),
(5, 'Rękodzieło', 'Handmade i DIY');

-- ---------------------
-- Przykładowe dane - Podkategorie dla Podróży
-- ---------------------
INSERT INTO Subcategories (category_id, name, description) VALUES
(6, 'Wycieczki górskie', 'Trekking i hiking'),
(6, 'Plaże', 'Wakacje nad morzem'),
(6, 'City breaks', 'Zwiedzanie miast'),
(6, 'Backpacking', 'Podróże plecakowe'),
(6, 'Kemping', 'Camping i survival'),
(6, 'Kultura lokalna', 'Poznawanie tradycji');
