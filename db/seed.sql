INSERT INTO categories (name, description) VALUES
    ('Informatique', 'Ordinateurs, accessoires et équipements informatiques'),
    ('Consommables', 'Articles consommables et fournitures'),
    ('Autres', 'Articles non classés')
ON DUPLICATE KEY UPDATE description = VALUES(description);
