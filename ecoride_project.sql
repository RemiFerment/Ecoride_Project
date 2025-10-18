CREATE DATABASE IF NOT EXISTS ecoride_project;

USE ecoride_project;

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(50),
    birth_date DATE,
    postal_adress VARCHAR(255),
    is_verified TINYINT(1) DEFAULT 0,
    grade SMALLINT DEFAULT NULL,
    ecopiece INT DEFAULT 20,
    photo LONGBLOB DEFAULT NULL
);

CREATE TABLE marque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);


CREATE TABLE car (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(50) NOT NULL,
    registration VARCHAR(50) NOT NULL,
    power_engine VARCHAR(50),
    first_date_registration DATE,
    color VARCHAR(255),

    marque_id INT,
    FOREIGN KEY (marque_id) REFERENCES marque(id) ON DELETE SET NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

CREATE TABLE carpooling (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    start_place VARCHAR(255) NOT NULL,
    end_place VARCHAR(255) NOT NULL,
    start_address VARCHAR(255),
    end_address VARCHAR(255),
    statut VARCHAR(50) DEFAULT 'AVAILABLE',
    available_seat INT NOT NULL,
    price_per_person INT NOT NULL,

    created_by_id INT NOT NULL,
    FOREIGN KEY (created_by_id) REFERENCES user(id) ON DELETE CASCADE,
    car_id INT,
    FOREIGN KEY (car_id) REFERENCES car(id) ON DELETE SET NULL
);

CREATE TABLE participation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    carpooling_id INT NOT NULL,
    FOREIGN KEY (carpooling_id) REFERENCES carpooling(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, carpooling_id)
);

CREATE TABLE review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    role VARCHAR(100) NOT NULL,
    status VARCHAR(50) DEFAULT 'TO_BE_CHECKED'
);

CREATE TABLE user_review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,

    review_id INT NOT NULL,
    FOREIGN KEY (review_id) REFERENCES review(id) ON DELETE CASCADE,

    affected_user_id INT,
    FOREIGN KEY (affected_user_id) REFERENCES user(id) ON DELETE CASCADE,

    carpooling_id INT NOT NULL,
    FOREIGN KEY (carpooling_id) REFERENCES carpooling(id) ON DELETE CASCADE
);


INSERT INTO marque (id, name) VALUES
(1, 'Toyota'),
(2, 'Volkswagen'),
(3, 'Ford'),
(4, 'Honda'),
(5, 'Chevrolet'),
(6, 'Mercedes-Benz'),
(7, 'BMW'),
(8, 'Audi'),
(9, 'Hyundai'),
(10, 'Nissan'),
(11, 'Kia'),
(12, 'Peugeot'),
(13, 'Renault'),
(14, 'Fiat'),
(15, 'Opel'),
(16, 'Mazda'),
(17, 'Volvo'),
(18, 'Jaguar'),
(19, 'Land Rover'),
(20, 'Porsche'),
(21, 'Ferrari'),
(22, 'Lamborghini'),
(23, 'Maserati'),
(24, 'Bentley'),
(25, 'Rolls-Royce'),
(26, 'Jeep'),
(27, 'Subaru'),
(28, 'Mitsubishi'),
(29, 'Citroën'),
(30, 'Suzuki');

INSERT INTO user (id, first_name, last_name, username, email, password, phone_number, birth_date, postal_adress, is_verified, grade, ecopiece)
VALUES
(1, 'Alice', 'Martin', 'alice_m', 'alice@example.com', '$2y$10$wKyUEbQqnRf7ahCvSlux7O7rn3pHsOczsM6K5Z54J2ZI.ZEdhUt/a', '0612345678', '1995-03-12', '12 Rue des Lilas, Paris', 1, 5, 120),
(2, 'Julien', 'Dubois', 'julien_d', 'julien@example.com', '$2y$10$Kq8lIjhGRLu/Tr2lxYO3oetisEg/H4RMc64zxWTyXz9qOoiszYfx2', '0622334455', '1990-07-04', '8 Avenue de la Gare, Lyon', 1, 3, 85),
(3, 'Clara', 'Roux', 'clara_r', 'clara@example.com', '$2y$10$v3E7DXVG92XlHztH8lQh2.zT8vY2GLP3QqqE4BSR4Izay5Szj1SKO', '0699887766', '1998-11-22', '5 Rue du Soleil, Marseille', 0, NULL, 40),
(4, 'Thomas', 'Legrand', 'thomas_l', 'thomas@example.com', '$2y$10$MTsy9gpAz3ZAHvlS4fe4ZuGscrHey962bSdTQpAqdO1noPmltLUWC', '0644556677', '1992-09-15', '22 Boulevard Victor Hugo, Toulouse', 1, 4, 210);

INSERT INTO car (id, model, registration, power_engine, first_date_registration, color, marque_id, user_id)
VALUES
(1, 'Yaris', 'AB-123-CD', 'Electrique', '2020-05-10', 'Blue', 1, 1),
(2, 'Clio', 'BC-456-DE', 'Essence', '2019-03-05', 'Red', 13, 2),
(3, 'A3', 'CD-789-EF', 'Diesel', '2021-07-21', 'Black', 8, 4);

INSERT INTO carpooling (id, start_date, end_date, start_place, end_place, start_address, end_address, statut, available_seat, price_per_person, created_by_id, car_id)
VALUES
(1, '2025-10-15 08:00:00', '2025-10-15 11:30:00', 'Paris', 'Lyon', '12 Rue des Lilas, Paris', 'Gare Part-Dieu, Lyon', 'AVAILABLE', 3, 25, 1, 1),
(2, '2025-10-17 17:00:00', '2025-10-17 20:00:00', 'Lyon', 'Marseille', '8 Avenue de la Gare, Lyon', 'Vieux-Port, Marseille', 'AVAILABLE', 2, 20, 2, 2),
(3, '2025-10-20 07:30:00', '2025-10-20 10:00:00', 'Toulouse', 'Bordeaux', '22 Boulevard Victor Hugo, Toulouse', 'Place des Quinconces, Bordeaux', 'FULL', 0, 18, 4, 3);


INSERT INTO participation (id, user_id, carpooling_id)
VALUES
(1, 2, 1),
(2, 3, 1),
(3, 1, 2),
(4, 3, 3);


INSERT INTO review (id, comment, role, status)
VALUES
(1, 'Super voyage! De très bonne conversation merci encore!', 'ROLE_PASSENGER', 'APPROVED'),
(2, 'Une bonne ambiance!', 'ROLE_PASSENGER', 'APPROVED'),
(3, 'Voiture propre. Conduite impaccable', 'ROLE_PASSENGER', 'APPROVED'),
(4, 'Un peu en retard mais ça va!', 'ROLE_PASSENGER', 'TO_BE_CHECKED');


INSERT INTO user_review (id, user_id, review_id, affected_user_id, carpooling_id)
VALUES
(1, 2, 1, 1, 1), 
(2, 3, 2, 1, 1), 
(3, 1, 3, 2, 2), 
(4, 4, 4, 3, 3); 
