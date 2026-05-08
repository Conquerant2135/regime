CREATE DATABASE IF NOT EXISTS regime
	CHARACTER SET utf8mb4
	COLLATE utf8mb4_unicode_ci;

USE regime;

CREATE TABLE users (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	nom VARCHAR(120) NOT NULL,
	email VARCHAR(190) NOT NULL,
	date_naissance DATE NOT NULL,
	taille DECIMAL(5,2) NOT NULL,
	poids DECIMAL(6,2) NOT NULL,
	mot_de_passe VARCHAR(255) NOT NULL,
	role ENUM('user', 'admin') DEFAULT 'user', 
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	KEY uq_users_email (email),
	CONSTRAINT chk_clients_taille CHECK (taille > 0),
	CONSTRAINT chk_clients_poids CHECK (poids > 0)
);

CREATE TABLE objectifs (
	id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	libelle VARCHAR(60) NOT NULL,
	KEY uq_objectifs_libelle (libelle)
);

CREATE TABLE client_objectifs (
	client_id BIGINT UNSIGNED NOT NULL,
	objectif_id TINYINT UNSIGNED NOT NULL,
	date_choix DATE NOT NULL,
	PRIMARY KEY (client_id, objectif_id, date_choix),
	KEY idx_client_objectifs_objectif (objectif_id),
	CONSTRAINT fk_client_objectifs_client
		FOREIGN KEY (client_id) REFERENCES users (id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CONSTRAINT fk_client_objectifs_objectif
		FOREIGN KEY (objectif_id) REFERENCES objectifs (id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
);

CREATE TABLE regimes (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	pourcentage_viande DECIMAL(5,2) NOT NULL DEFAULT 0,
	pourcentage_volaille DECIMAL(5,2) NOT NULL DEFAULT 0,
	pourcentage_poisson DECIMAL(5,2) NOT NULL DEFAULT 0,
	prix_par_jour DECIMAL(10,2) NOT NULL,
	impact_journalier DECIMAL(7,3) NOT NULL,
	CONSTRAINT chk_regimes_viande CHECK (pourcentage_viande >= 0),
	CONSTRAINT chk_regimes_legume CHECK (pourcentage_volaille >= 0),
	CONSTRAINT chk_regimes_poisson CHECK (pourcentage_poisson >= 0),
	CONSTRAINT chk_total_prct CHECK ( pourcentage_poisson + pourcentage_volaille + pourcentage_viande <= 100),
	CONSTRAINT chk_regimes_prix CHECK (prix_par_jour >= 0)
);

CREATE TABLE sports (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	libelle VARCHAR(120) NOT NULL,
	KEY uq_sports_libelle (libelle)
);

CREATE TABLE regime_sports (
	regime_id BIGINT UNSIGNED NOT NULL,
	sport_id BIGINT UNSIGNED NOT NULL,
	client_id BIGINT UNSIGNED NOT NULL,
	objectif_id TINYINT UNSIGNED NOT NULL,
	date_choix DATE NOT NULL,
	duree INT UNSIGNED NOT NULL,
	PRIMARY KEY (regime_id, sport_id, client_id, objectif_id, date_choix),
	KEY idx_regime_sports_client_objectif (client_id, objectif_id, date_choix),
	CONSTRAINT fk_regime_sports_regime
		FOREIGN KEY (regime_id) REFERENCES regimes (id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CONSTRAINT fk_regime_sports_sport
		FOREIGN KEY (sport_id) REFERENCES sports (id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT,
	CONSTRAINT fk_regime_sports_client_objectif
		FOREIGN KEY (client_id, objectif_id, date_choix)
		REFERENCES client_objectifs (client_id, objectif_id, date_choix)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CONSTRAINT chk_regime_sports_duree CHECK (duree > 0)
);

CREATE TABLE options (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	libelle VARCHAR(120) NOT NULL,
	remise DECIMAL(5,2) NOT NULL DEFAULT 0,
	KEY uq_options_libelle (libelle),
	CONSTRAINT chk_options_remise CHECK (remise BETWEEN 0 AND 100)
);

CREATE TABLE client_options (
	client_id BIGINT UNSIGNED NOT NULL,
	option_id BIGINT UNSIGNED NOT NULL,
	date_option DATE NOT NULL,
	PRIMARY KEY (client_id, option_id, date_option),
	KEY idx_client_options_option (option_id),
	CONSTRAINT fk_client_options_client
		FOREIGN KEY (client_id) REFERENCES users (id)
		ON UPDATE CASCADE
		ON DELETE CASCADE,
	CONSTRAINT fk_client_options_option
		FOREIGN KEY (option_id) REFERENCES options (id)
		ON UPDATE CASCADE
		ON DELETE RESTRICT
);

CREATE TABLE raisons (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	libelle VARCHAR(120) NOT NULL,
	KEY uq_raisons_libelle (libelle)
);

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
);

CREATE TABLE codes (
	id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	valeur VARCHAR(100) NOT NULL,
	gain DECIMAL(12,2) NOT NULL,
	is_used TINYINT(1) NOT NULL DEFAULT 0,
	created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UNIQUE KEY uq_codes_valeur (valeur),
	CONSTRAINT chk_codes_gain CHECK (gain > 0)
);
