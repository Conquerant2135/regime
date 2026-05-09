-- Migration: Ajouter une clé primaire auto-incrémentée à client_objectifs
-- Date: 2026-05-09

ALTER TABLE client_objectifs 
ADD COLUMN id INT AUTO_INCREMENT UNIQUE FIRST;

ALTER TABLE client_objectifs 
ADD PRIMARY KEY (id);

-- Garder l'unicité composite
ALTER TABLE client_objectifs 
ADD UNIQUE KEY unique_client_objectif_date (client_id, objectif_id, date_choix);
