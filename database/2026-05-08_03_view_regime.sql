-- Vue des combinaisons valides régime + sport (sans liaison client)
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