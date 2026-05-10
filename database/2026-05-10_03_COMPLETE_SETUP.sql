-- ============================================================================
-- SCRIPT COMPLET DE SETUP - REGIME DATABASE
-- Date: 2026-05-10
-- Description: Création complète de la BD avec toutes les tables, 
--              migrations et données consolidées
-- ============================================================================

-- ============================================================================
-- 1. CRÉATION DE LA BASE DE DONNÉES
-- ============================================================================

DROP DATABASE IF EXISTS regime;

CREATE DATABASE IF NOT EXISTS regime
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE regime;

-- ============================================================================
-- 2. CRÉATION DES TABLES
-- ============================================================================

-- Table: users (Utilisateurs du système)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    date_naissance DATE NOT NULL,
    taille DECIMAL(5,2) NOT NULL,
    poids DECIMAL(6,2) NOT NULL,
    sexe ENUM('homme', 'femme') NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user', 
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    CONSTRAINT chk_users_taille CHECK (taille > 0),
    CONSTRAINT chk_users_poids CHECK (poids > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: objectifs (Objectifs disponibles)
CREATE TABLE objectifs (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(60) NOT NULL,
    UNIQUE KEY uq_objectifs_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: client_objectifs (Historique des objectifs choisis par client)
CREATE TABLE client_objectifs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    objectif_id TINYINT UNSIGNED NOT NULL,
    date_choix DATETIME NOT NULL,
    action_poids DECIMAL(7,3) DEFAULT NULL,
    UNIQUE KEY unique_client_objectif_date (client_id, objectif_id, date_choix),
    KEY idx_client_objectifs_objectif (objectif_id),
    CONSTRAINT fk_client_objectifs_client
        FOREIGN KEY (client_id) REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_client_objectifs_objectif
        FOREIGN KEY (objectif_id) REFERENCES objectifs (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: regimes (Régimes disponibles)
CREATE TABLE regimes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL DEFAULT '',
    pourcentage_viande DECIMAL(5,2) NOT NULL DEFAULT 0,
    pourcentage_volaille DECIMAL(5,2) NOT NULL DEFAULT 0,
    pourcentage_poisson DECIMAL(5,2) NOT NULL DEFAULT 0,
    prix_par_jour DECIMAL(10,2) NOT NULL,
    impact_journalier DECIMAL(7,3) NOT NULL,
    CONSTRAINT chk_regimes_viande CHECK (pourcentage_viande >= 0),
    CONSTRAINT chk_regimes_volaille CHECK (pourcentage_volaille >= 0),
    CONSTRAINT chk_regimes_poisson CHECK (pourcentage_poisson >= 0),
    CONSTRAINT chk_regimes_total_pct CHECK (pourcentage_poisson + pourcentage_volaille + pourcentage_viande <= 100),
    CONSTRAINT chk_regimes_prix CHECK (prix_par_jour >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: sports (Sports disponibles)
CREATE TABLE sports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(120) NOT NULL,
    UNIQUE KEY uq_sports_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: regime_sports (Couples régime-sport)
CREATE TABLE regime_sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    regime_id BIGINT UNSIGNED NOT NULL,
    sport_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NULL,
    objectif_id TINYINT UNSIGNED NULL,
    date_choix DATETIME NULL,
    duree INT UNSIGNED NULL,
    UNIQUE KEY unique_regime_sport_client (regime_id, sport_id, client_id, objectif_id, date_choix),
    KEY idx_regime_sports_client_objectif (client_id, objectif_id),
    CONSTRAINT fk_regime_sports_regime
        FOREIGN KEY (regime_id) REFERENCES regimes (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_regime_sports_sport
        FOREIGN KEY (sport_id) REFERENCES sports (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: options (Options d'abonnement)
CREATE TABLE `options` (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(120) NOT NULL,
    remise DECIMAL(5,2) NOT NULL DEFAULT 0,
    prix_option DECIMAL(12,2) NOT NULL DEFAULT 0,
    UNIQUE KEY uq_options_libelle (libelle),
    CONSTRAINT chk_options_remise CHECK (remise BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: client_options (Options souscrites par client)
CREATE TABLE client_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    option_id BIGINT UNSIGNED NOT NULL,
    date_option DATETIME NOT NULL,
    UNIQUE KEY unique_client_option_date (client_id, option_id, date_option),
    KEY idx_client_options_option (option_id),
    CONSTRAINT fk_client_options_client
        FOREIGN KEY (client_id) REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_client_options_option
        FOREIGN KEY (option_id) REFERENCES `options` (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: raisons (Raisons de transactions)
CREATE TABLE raisons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(120) NOT NULL,
    UNIQUE KEY uq_raisons_libelle (libelle)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mvt_compte (Mouvements de compte/transactions)
CREATE TABLE mvt_compte (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    type_transaction ENUM('debit', 'credit') NOT NULL,
    date_mouvement DATETIME NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    raison_id BIGINT UNSIGNED NULL,
    KEY idx_mvt_compte_client (client_id),
    KEY idx_mvt_compte_raison (raison_id),
    CONSTRAINT fk_mvt_compte_client
        FOREIGN KEY (client_id) REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_mvt_compte_raison
        FOREIGN KEY (raison_id) REFERENCES raisons (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL,
    CONSTRAINT chk_mvt_compte_montant CHECK (montant > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: codes (Codes de recharge/promo)
CREATE TABLE codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    valeur VARCHAR(100) NOT NULL,
    gain DECIMAL(12,2) NOT NULL,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_codes_valeur (valeur),
    CONSTRAINT chk_codes_gain CHECK (gain > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. CRÉATION DES VUES SQL
-- ============================================================================

-- Vue: Combinaisons valides régime + sport (sans liaison client)
CREATE OR REPLACE VIEW v_regime_sport_possible AS
SELECT 
    r.id AS regime_id,
    r.nom AS regime_nom,
    r.pourcentage_viande,
    r.pourcentage_volaille,
    r.pourcentage_poisson,
    r.prix_par_jour,
    r.impact_journalier,
    s.id AS sport_id,
    s.libelle AS sport_libelle,
    CASE 
        WHEN r.impact_journalier > 0 THEN 'Prise de poids'
        WHEN r.impact_journalier < 0 THEN 'Perte de poids'
        ELSE 'Maintien'
    END AS effet
FROM regimes r
CROSS JOIN sports s
ORDER BY r.id, s.id;

-- ============================================================================
-- 4. INSERTION DES DONNÉES
-- ============================================================================

-- Données: Objectifs
INSERT INTO objectifs (libelle) VALUES
    ('perte de poids'),
    ('gain'),
    ('imc ideal');

-- Données: Options
INSERT INTO `options` (libelle, remise, prix_option) VALUES
    ('gold', 15.00, 49.90);

-- Données: Sports
INSERT INTO sports (libelle) VALUES
    ('Course à pied (jogging)'),
    ('Natation (crawl)'),
    ('Musculation (full body)'),
    ('Pilates'),
    ('Zumba');

-- Données: Régimes
INSERT INTO regimes (nom, pourcentage_viande, pourcentage_volaille, pourcentage_poisson, prix_par_jour, impact_journalier) VALUES
    ('Régime hyperprotéiné', 45.00, 35.00, 20.00, 12.50, 0.080),
    ('Régime poisson/légumes', 10.00, 10.00, 50.00, 15.00, -0.120),
    ('Régime équilibré standard', 25.00, 25.00, 25.00, 8.00, 0.000),
    ('Régime volaille/légumes', 15.00, 40.00, 15.00, 10.50, -0.060),
    ('Régime carnivore', 70.00, 20.00, 10.00, 14.00, 0.150);

-- Données: Utilisateurs (15 users + 1 admin)
INSERT INTO users (nom, email, date_naissance, taille, poids, sexe, mot_de_passe, role, created_at, updated_at) VALUES
    ('Alice Martin', 'alice.martin.2025@example.com', '1995-04-12', 165.00, 61.50, 'femme', 'test123', 'user', '2025-06-08 09:12:00', '2025-06-08 09:12:00'),
    ('Yanis Bernard', 'yanis.bernard.2025@example.com', '1992-11-03', 178.00, 82.10, 'homme', 'test123', 'user', '2025-07-14 11:05:00', '2025-07-14 11:05:00'),
    ('Sara Dupont', 'sara.dupont.2025@example.com', '1998-02-17', 170.00, 66.20, 'femme', 'test123', 'user', '2025-08-02 08:45:00', '2025-08-02 08:45:00'),
    ('Malo Girard', 'malo.girard.2025@example.com', '1991-06-20', 181.00, 88.00, 'homme', 'test123', 'user', '2025-08-23 17:20:00', '2025-08-23 17:20:00'),
    ('Nina Caron', 'nina.caron.2025@example.com', '1996-09-09', 162.00, 57.40, 'femme', 'test123', 'user', '2025-09-11 14:33:00', '2025-09-11 14:33:00'),
    ('Hugo Noel', 'hugo.noel.2025@example.com', '1989-01-30', 176.00, 79.70, 'homme', 'test123', 'user', '2025-10-06 10:18:00', '2025-10-06 10:18:00'),
    ('Lea Fournier', 'lea.fournier.2025@example.com', '1994-07-25', 168.00, 63.10, 'femme', 'test123', 'user', '2025-11-19 16:02:00', '2025-11-19 16:02:00'),
    ('Ilyes Roy', 'ilyes.roy.2025@example.com', '1990-12-04', 183.00, 90.30, 'homme', 'test123', 'user', '2025-12-03 12:40:00', '2025-12-03 12:40:00'),
    ('Emma Leroux', 'emma.leroux.2026@example.com', '1997-03-18', 164.00, 58.90, 'femme', 'test123', 'user', '2026-01-08 09:27:00', '2026-01-08 09:27:00'),
    ('Noe Lambert', 'noe.lambert.2026@example.com', '1993-05-21', 175.00, 74.60, 'homme', 'test123', 'user', '2026-01-26 18:15:00', '2026-01-26 18:15:00'),
    ('Adam Pelletier', 'adam.pelletier.2026@example.com', '1988-10-10', 179.00, 84.20, 'homme', 'test123', 'user', '2026-02-12 07:58:00', '2026-02-12 07:58:00'),
    ('Jade Colin', 'jade.colin.2026@example.com', '1999-08-28', 160.00, 55.80, 'femme', 'test123', 'user', '2026-03-15 13:04:00', '2026-03-15 13:04:00'),
    ('Lina Perrin', 'lina.perrin.2026@example.com', '1996-01-14', 167.00, 62.00, 'femme', 'test123', 'user', '2026-04-04 10:10:00', '2026-04-04 10:10:00'),
    ('Samir Meunier', 'samir.meunier.2026@example.com', '1991-09-02', 182.00, 86.40, 'homme', 'test123', 'user', '2026-04-29 19:25:00', '2026-04-29 19:25:00'),
    ('Chloe Henry', 'chloe.henry.2026@example.com', '1995-12-06', 169.00, 64.30, 'femme', 'test123', 'user', '2026-05-07 11:41:00', '2026-05-07 11:41:00'),
    ('Admin', 'admin@regime.com', '1990-01-01', 180.00, 80.00, 'homme', 'admin123', 'admin', '2026-05-09 10:00:00', '2026-05-09 10:00:00');

-- Données: Objectifs des clients
INSERT INTO client_objectifs (client_id, objectif_id, date_choix, action_poids) VALUES
    (1, 1, '2025-01-05 00:00:00', -5.000),
    (2, 2, '2025-01-10 00:00:00', 3.500),
    (3, 3, '2025-01-15 00:00:00', 0.000),
    (4, 1, '2025-02-03 00:00:00', -7.000),
    (5, 2, '2025-02-08 00:00:00', 4.000),
    (6, 3, '2025-02-14 00:00:00', 0.000),
    (7, 1, '2025-03-05 00:00:00', -6.000),
    (8, 2, '2025-03-12 00:00:00', 2.500),
    (9, 3, '2025-04-02 00:00:00', 0.000),
    (10, 1, '2025-04-18 00:00:00', -8.000),
    (11, 2, '2025-11-07 00:00:00', 5.000),
    (12, 3, '2025-11-14 00:00:00', 0.000),
    (13, 1, '2025-11-21 00:00:00', -4.500),
    (14, 2, '2025-12-02 00:00:00', 3.000),
    (15, 3, '2025-12-09 00:00:00', 0.000);

-- Données: Mouvements de compte (transactions)
INSERT INTO mvt_compte (client_id, type_transaction, date_mouvement, montant, raison_id) VALUES
    (1, 'credit', '2025-06-03 09:15:00', 120000.00, NULL),
    (2, 'debit', '2025-06-11 14:20:00', 35000.50, NULL),
    (3, 'credit', '2025-06-28 18:10:00', 85000.00, NULL),
    (4, 'debit', '2025-07-04 08:45:00', 42000.00, NULL),
    (5, 'credit', '2025-07-15 12:30:00', 15000.00, NULL),
    (6, 'debit', '2025-08-02 10:05:00', 28000.90, NULL),
    (7, 'credit', '2025-08-08 16:40:00', 20000.00, NULL),
    (8, 'debit', '2025-08-19 19:25:00', 64000.00, NULL),
    (9, 'credit', '2025-08-27 09:50:00', 95000.00, NULL),
    (10, 'debit', '2025-09-05 11:15:00', 18000.75, NULL),
    (11, 'credit', '2025-09-14 14:55:00', 17500.00, NULL),
    (12, 'debit', '2025-10-03 07:20:00', 52000.00, NULL),
    (13, 'credit', '2025-10-09 13:05:00', 13000.00, NULL),
    (14, 'debit', '2025-10-21 17:45:00', 47000.30, NULL),
    (15, 'credit', '2025-11-02 09:10:00', 22000.00, NULL),
    (1, 'debit', '2025-11-13 15:35:00', 39000.90, NULL),
    (3, 'credit', '2025-11-26 20:05:00', 11000.00, NULL),
    (2, 'debit', '2025-12-06 08:00:00', 25000.00, NULL),
    (4, 'credit', '2025-12-12 12:20:00', 18000.00, NULL),
    (5, 'debit', '2025-12-24 18:45:00', 71000.25, NULL),
    (6, 'credit', '2026-01-04 09:30:00', 14500.00, NULL),
    (7, 'debit', '2026-01-17 14:10:00', 33000.40, NULL),
    (8, 'credit', '2026-01-29 19:55:00', 90000.00, NULL),
    (9, 'debit', '2026-02-07 10:25:00', 58000.60, NULL),
    (10, 'credit', '2026-02-18 16:15:00', 21000.00, NULL),
    (11, 'debit', '2026-03-03 08:35:00', 44000.00, NULL),
    (12, 'credit', '2026-03-16 13:50:00', 16000.00, NULL),
    (13, 'debit', '2026-03-27 21:05:00', 29000.95, NULL),
    (14, 'credit', '2026-04-05 09:00:00', 19000.00, NULL),
    (15, 'debit', '2026-04-19 15:45:00', 72000.80, NULL),
    (1, 'credit', '2026-05-06 11:10:00', 23000.00, NULL),
    (2, 'debit', '2026-05-08 17:25:00', 38000.00, NULL);

-- Données: Codes de recharge
INSERT INTO codes (valeur, gain, is_used) VALUES
    ('PROMO10', 10.00, 0),
    ('PROMO20', 20.00, 0),
    ('WELCOME50', 50.00, 0),
    ('SUMMER100', 100.00, 0),
    ('FRIEND25', 25.00, 1),
    ('GOLD30', 30.00, 0),
    ('VIP150', 150.00, 0),
    ('NEWUSER40', 40.00, 0),
    ('REFERRAL60', 60.00, 1),
    ('SPECIAL99', 99.50, 0);

-- ============================================================================
-- 5. VÉRIFICATIONS FINALES
-- ============================================================================

-- Afficher les statistiques des tables
SELECT 'users' as table_name, COUNT(*) as row_count FROM users
UNION ALL
SELECT 'objectifs', COUNT(*) FROM objectifs
UNION ALL
SELECT 'client_objectifs', COUNT(*) FROM client_objectifs
UNION ALL
SELECT 'regimes', COUNT(*) FROM regimes
UNION ALL
SELECT 'sports', COUNT(*) FROM sports
UNION ALL
SELECT 'options', COUNT(*) FROM `options`
UNION ALL
SELECT 'client_options', COUNT(*) FROM client_options
UNION ALL
SELECT 'mvt_compte', COUNT(*) FROM mvt_compte
UNION ALL
SELECT 'codes', COUNT(*) FROM codes;

-- ============================================================================
-- FIN DU SCRIPT
-- ============================================================================
-- Base de données prête pour utilisation!
-- Utilisateurs de test:
--   - Email: alice.martin.2025@example.com | Pass: test123 (user normal)
--   - Email: admin@regime.com | Pass: admin123 (admin)
-- ============================================================================
