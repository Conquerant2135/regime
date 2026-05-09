INSERT INTO users (
	nom,
	email,
	date_naissance,
	taille,
	poids,
	mot_de_passe,
	role,
	created_at,
	updated_at
) VALUES
('Alice Martin', 'alice.martin.2025@example.com', '1995-04-12', 165.00, 61.50, 'test123', 'user', '2025-06-08 09:12:00', '2025-06-08 09:12:00'),
('Yanis Bernard', 'yanis.bernard.2025@example.com', '1992-11-03', 178.00, 82.10, 'test123', 'user', '2025-07-14 11:05:00', '2025-07-14 11:05:00'),
('Sara Dupont', 'sara.dupont.2025@example.com', '1998-02-17', 170.00, 66.20, 'test123', 'user', '2025-08-02 08:45:00', '2025-08-02 08:45:00'),
('Malo Girard', 'malo.girard.2025@example.com', '1991-06-20', 181.00, 88.00, 'test123', 'user', '2025-08-23 17:20:00', '2025-08-23 17:20:00'),
('Nina Caron', 'nina.caron.2025@example.com', '1996-09-09', 162.00, 57.40, 'test123', 'user', '2025-09-11 14:33:00', '2025-09-11 14:33:00'),
('Hugo Noel', 'hugo.noel.2025@example.com', '1989-01-30', 176.00, 79.70, 'test123', 'user', '2025-10-06 10:18:00', '2025-10-06 10:18:00'),
('Lea Fournier', 'lea.fournier.2025@example.com', '1994-07-25', 168.00, 63.10, 'test123', 'user', '2025-11-19 16:02:00', '2025-11-19 16:02:00'),
('Ilyes Roy', 'ilyes.roy.2025@example.com', '1990-12-04', 183.00, 90.30, 'test123', 'user', '2025-12-03 12:40:00', '2025-12-03 12:40:00'),
('Emma Leroux', 'emma.leroux.2026@example.com', '1997-03-18', 164.00, 58.90, 'test123', 'user', '2026-01-08 09:27:00', '2026-01-08 09:27:00'),
('Noe Lambert', 'noe.lambert.2026@example.com', '1993-05-21', 175.00, 74.60, 'test123', 'user', '2026-01-26 18:15:00', '2026-01-26 18:15:00'),
('Adam Pelletier', 'adam.pelletier.2026@example.com', '1988-10-10', 179.00, 84.20, 'test123', 'user', '2026-02-12 07:58:00', '2026-02-12 07:58:00'),
('Jade Colin', 'jade.colin.2026@example.com', '1999-08-28', 160.00, 55.80, 'test123', 'user', '2026-03-15 13:04:00', '2026-03-15 13:04:00'),
('Lina Perrin', 'lina.perrin.2026@example.com', '1996-01-14', 167.00, 62.00, 'test123', 'user', '2026-04-04 10:10:00', '2026-04-04 10:10:00'),
('Samir Meunier', 'samir.meunier.2026@example.com', '1991-09-02', 182.00, 86.40, 'test123', 'user', '2026-04-29 19:25:00', '2026-04-29 19:25:00'),
('Chloe Henry', 'chloe.henry.2026@example.com', '1995-12-06', 169.00, 64.30, 'test123', 'user', '2026-05-07 11:41:00', '2026-05-07 11:41:00');

SELECT
    MAX(co.date_option) AS last_option,
    COALESCE(opt.libelle, 'normal') AS option_type,
    COALESCE(co.option_id , 0) AS co.option_id
    COUNT(co.option_id) AS total
FROM users u
LEFT JOIN client_options co ON co.client_id = u.id
LEFT JOIN options opt ON opt.id = co.option_id
WHERE u.role != 'admin'
GROUP BY opt.libelle;