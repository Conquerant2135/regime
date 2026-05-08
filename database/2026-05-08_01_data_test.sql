-- Sports variés avec différents niveaux d'intensité
INSERT INTO sports (libelle) VALUES
('Course à pied (jogging)'),
('Natation (crawl)'),
('Musculation (full body)'),
('Pilates'),
('Zumba');

-- Régimes : impact_journalier > 0 = prise de poids, < 0 = perte de poids
INSERT INTO regimes (pourcentage_viande, pourcentage_volaille, pourcentage_poisson, prix_par_jour, impact_journalier) VALUES
-- Régime hyperprotéiné (prise de muscle)
(45.00, 35.00, 20.00, 12.50, 0.080),
-- Régime poisson/légumes (perte de poids)
(10.00, 10.00, 50.00, 15.00, -0.120),
-- Régime équilibré standard
(25.00, 25.00, 25.00, 8.00, 0.000),
-- Régime volaille/légumes (perte modérée)
(15.00, 40.00, 15.00, 10.50, -0.060),
-- Régime carnivore (prise de masse rapide)
(70.00, 20.00, 10.00, 14.00, 0.150);
-- Régime spécial végétarien (pesco possible)
--(5.00, 5.00, 45.00, 11.00, -0.030),
-- Régime minceur express
--(8.00, 12.00, 30.00, 13.00, -0.180),
-- Régime prise de masse (équilibré++)
--(35.00, 35.00, 15.00, 9.50, 0.100);