ALTER TABLE regimes ADD COLUMN nom VARCHAR(255) NOT NULL DEFAULT '';

-- Si les IDs existent déjà, utilisez UPDATE
UPDATE regimes SET 
    nom = CASE id
        WHEN 1 THEN 'Régime hyperprotéiné'
        WHEN 2 THEN 'Régime poisson/légumes'
        WHEN 3 THEN 'Régime équilibré standard'
        WHEN 4 THEN 'Régime volaille/légumes'
        WHEN 5 THEN 'Régime carnivore'
    END
WHERE id IN (1, 2, 3, 4, 5);