-- =============================================================
-- database.sql
--
-- Script de création de la base de données PostgreSQL pour
-- l'Atelier 22 (routes web + routes API) et de la table `books`,
-- avec quelques données par défaut pour tester rapidement.
--
-- Utilisation :
--   psql -U postgres -f database.sql
-- =============================================================

-- --------------------------------------------------------------
-- 1. Création de la base de données
-- --------------------------------------------------------------
-- (à exécuter en étant connecté à une base existante, ex: "postgres",
--  car PostgreSQL ne permet pas de créer une base depuis elle-même)

DROP DATABASE IF EXISTS atelier22;
CREATE DATABASE atelier22
    ENCODING 'UTF8'
    LC_COLLATE 'fr_FR.UTF-8'
    LC_CTYPE 'fr_FR.UTF-8'
    TEMPLATE template0;

-- Bascule sur la base nouvellement créée.
-- (\c est une commande psql, pas du SQL standard — fonctionne
--  uniquement en exécutant ce script via psql, pas via un simple
--  driver PDO qui ne supporte pas les commandes méta).
\c atelier22

-- --------------------------------------------------------------
-- 2. Création de la table `books`
-- --------------------------------------------------------------

DROP TABLE IF EXISTS books;

CREATE TABLE books (
    id          SERIAL PRIMARY KEY,
    titre       VARCHAR(255) NOT NULL,
    auteur      VARCHAR(255) NOT NULL,
    disponible  BOOLEAN NOT NULL DEFAULT TRUE
);

-- Un petit commentaire de table, utile pour la documentation interne.
COMMENT ON TABLE books IS 'Catalogue de livres — Atelier 22';
COMMENT ON COLUMN books.disponible IS 'TRUE = empruntable, FALSE = déjà emprunté';

-- --------------------------------------------------------------
-- 3. Données par défaut
-- --------------------------------------------------------------

INSERT INTO books (titre, auteur, disponible) VALUES
    ('1984',                          'George Orwell',        TRUE),
    ('Le Petit Prince',               'Antoine de Saint-Exupéry', TRUE),
    ('Les Misérables',                'Victor Hugo',           FALSE),
    ('L''Étranger',                   'Albert Camus',          TRUE),
    ('Fahrenheit 451',                'Ray Bradbury',          FALSE),
    ('Une brève histoire du temps',   'Stephen Hawking',       TRUE);

-- --------------------------------------------------------------
-- 4. Vérification rapide
-- --------------------------------------------------------------

SELECT id, titre, auteur, disponible FROM books ORDER BY id;