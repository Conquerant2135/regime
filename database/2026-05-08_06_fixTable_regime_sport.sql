-- Fix: Corriger la clé étrangère de regime_sports
-- Problème: La FK forçait date_choix identique entre client_objectifs et regime_sports
-- Solution: Reference seulement (client_id, objectif_id)

-- 1. Supprimer la contrainte actuelle (elle demande une date identique)
ALTER TABLE regime_sports 
DROP FOREIGN KEY fk_regime_sports_client_objectif;

-- 2. Ajouter une clé unique à client_objectifs sur (client_id, objectif_id)
-- Car on ne peut référencer que sur une clé unique/primaire
ALTER TABLE client_objectifs 
ADD UNIQUE KEY uq_client_objectif (client_id, objectif_id);

-- 3. Ajouter la nouvelle clé étrangère CORRECTE
-- Sans date_choix, juste la combinaison (client_id, objectif_id)
ALTER TABLE regime_sports 
ADD CONSTRAINT fk_regime_sports_client_objectif 
FOREIGN KEY (client_id, objectif_id) 
REFERENCES client_objectifs (client_id, objectif_id)
ON UPDATE CASCADE
ON DELETE CASCADE;

-- Vérification: afficher les contraintes
SHOW CREATE TABLE regime_sports\G

