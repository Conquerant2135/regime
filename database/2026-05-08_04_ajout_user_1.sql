INSERT INTO users (nom, email, date_naissance, taille, poids, mot_de_passe, role) VALUES
('Alice Dupont', 'alice@mail.com', '1990-05-15', 165.00, 60.00, 'password123', 'user');

-- fix de la table client_objectifs pour lier l'objectif de perte de poids à Alice
CREATE TABLE client_objectifs (
    client_id BIGINT UNSIGNED NOT NULL,
    objectif_id TINYINT UNSIGNED NOT NULL,
    date_choix DATE NOT NULL,
    PRIMARY KEY (client_id, objectif_id, date_choix),
    KEY idx_client_objectifs_objectif (objectif_id),
    CONSTRAINT fk_client_objectifs_client
        FOREIGN KEY (client_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_client_objectifs_objectif
        FOREIGN KEY (objectif_id) REFERENCES objectifs(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

INSERT INTO client_objectifs (client_id, objectif_id, date_choix) VALUES
(1, 1, '2026-05-01'); 