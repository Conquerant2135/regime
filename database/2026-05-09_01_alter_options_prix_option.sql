ALTER TABLE options
    ADD COLUMN prix_option DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER remise;

UPDATE options
SET prix_option = 49.90
WHERE LOWER(libelle) = 'gold';