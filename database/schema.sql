CREATE DATABASE IF NOT EXISTS esprit_book_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE esprit_book_mvc;

CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS book (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    publication_date DATE NOT NULL,
    language VARCHAR(10) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    number_of_copies INT NOT NULL,
    category_id INT NOT NULL,
    CONSTRAINT fk_book_category
        FOREIGN KEY (category_id) REFERENCES category(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO category (name)
SELECT 'Science' WHERE NOT EXISTS (SELECT 1 FROM category WHERE name = 'Science');
INSERT INTO category (name)
SELECT 'Literature' WHERE NOT EXISTS (SELECT 1 FROM category WHERE name = 'Literature');
INSERT INTO category (name)
SELECT 'Technology' WHERE NOT EXISTS (SELECT 1 FROM category WHERE name = 'Technology');
INSERT INTO category (name)
SELECT 'History' WHERE NOT EXISTS (SELECT 1 FROM category WHERE name = 'History');
INSERT INTO category (name)
SELECT 'Arts' WHERE NOT EXISTS (SELECT 1 FROM category WHERE name = 'Arts');
