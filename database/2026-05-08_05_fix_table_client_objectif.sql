ALTER TABLE client_objectifs ADD COLUMN action_poids DECIMAL(7,3) NOT NULL DEFAULT 0;

-- Mise à jour des données existantes pour refléter les objectifs de poids
UPDATE client_objectifs SET action_poids = -5.000 WHERE client_id = 1; -- Perte de poids