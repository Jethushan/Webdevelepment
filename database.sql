-- Drone-Criticus Database Setup
USE ID498100_dronecriticus;

-- Tabel: drone modellen
CREATE TABLE IF NOT EXISTS drones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merk VARCHAR(100) NOT NULL,
    model VARCHAR(150) NOT NULL,
    beschrijving TEXT,
    prijs DECIMAL(10, 2),
    afbeelding VARCHAR(255),
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: reviews
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drone_id INT NOT NULL,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    titel VARCHAR(200) NOT NULL,
    inhoud TEXT NOT NULL,
    score TINYINT NOT NULL CHECK (score BETWEEN 1 AND 5),
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drone_id) REFERENCES drones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: gebruikers (admin)
CREATE TABLE IF NOT EXISTS gebruikers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gebruikersnaam VARCHAR(100) NOT NULL UNIQUE,
    wachtwoord_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'bezoeker') NOT NULL DEFAULT 'bezoeker',
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Standaard admin account (wachtwoord: Admin1234!)
INSERT INTO gebruikers (gebruikersnaam, wachtwoord_hash, rol) VALUES
('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Voorbeelddata: drone modellen
INSERT INTO drones (merk, model, beschrijving, prijs, afbeelding) VALUES
('DJI', 'Mini 4 Pro', 'Compacte drone met 4K camera en obstakelvermijding. Perfect voor beginners en gevorderden.', 759.00, 'dji-mini4pro.jpg'),
('DJI', 'Air 3', 'Dual camera drone met tele- en groothoeklens. Uitstekende beeldkwaliteit.', 1099.00, 'dji-air3.jpg'),
('DJI', 'Mavic 3 Pro', 'Professionele drone met triple-camera systeem. De absolute top voor luchtfotografie.', 2199.00, 'dji-mavic3pro.jpg'),
('Autel', 'EVO Lite+', 'Krachtige concurrent van DJI met 6K sensor en lange vluchttijd.', 849.00, 'autel-evolite.jpg'),
('Holy Stone', 'HS720E', 'Budgetvriendelijke optie met GPS en 4K EIS camera. Ideaal als startersdrone.', 189.00, 'holystone-hs720e.jpg'),
('Parrot', 'ANAFI USA', 'Militaire kwaliteit drone met thermische camera optie. Speciaal voor professioneel gebruik.', 2499.00, 'parrot-anafiusa.jpg');

-- Voorbeeldreviews
INSERT INTO reviews (drone_id, naam, email, titel, inhoud, score) VALUES
(1, 'Thomas Declercq', 'thomas@example.com', 'Geweldige mini-drone!', 'De DJI Mini 4 Pro overtreft alle verwachtingen. De beeldkwaliteit is indrukwekkend voor zijn formaat en de obstakelvermijding werkt uitstekend. Enige minpunt is de prijs van extra accu''s.', 5),
(1, 'Sara Vermeersch', 'sara@example.com', 'Goed maar met beperkingen', 'Mooie drone maar de vluchttijd valt wat tegen in de wind. Camera is top, app werkt vlot. Voor de prijs is het een goede aankoop.', 4),
(2, 'Pieter Janssen', 'pieter@example.com', 'Dubbele camera is geweldig', 'De Air 3 heeft me volledig overtuigd. De combinatie van tele en groothoek maakt zoveel meer mogelijk. Aanrader voor wie serieus met luchtfotografie wil beginnen.', 5),
(3, 'Lena Bogaert', 'lena@example.com', 'Professioneel maar duur', 'Ongelooflijke kwaliteit maar ook een serieuze investering. De triple camera geeft mogelijkheden die geen andere consumer drone heeft. Als je budget het toelaat: absoluut kopen.', 4),
(5, 'Joris Willems', 'joris@example.com', 'Beste budgetdrone op de markt', 'Voor de prijs is dit ongelofelijk. GPS werkt perfect, camera doet wat het moet. Niet vergelijkbaar met DJI maar als starter is dit ideaal om van te leren.', 4);
