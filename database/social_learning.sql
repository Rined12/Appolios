-- ============================================================
-- Social Learning Module — SQL Schema
-- Platform : APPOLIOS E-Learning
-- Path     : database/social_learning.sql
-- Charset  : utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Table : groupe
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `groupe` (
    `id_groupe`     INT          NOT NULL AUTO_INCREMENT,
    `nom_groupe`    VARCHAR(100) NOT NULL,
    `description`   TEXT         NOT NULL,
    `date_creation` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `statut`            ENUM('actif','archivé') NOT NULL DEFAULT 'actif',
    `approval_statut`   ENUM('en_attente','approuve','refuse') NOT NULL DEFAULT 'approuve',
    `id_createur`   INT          NOT NULL,
    PRIMARY KEY (`id_groupe`),
    CONSTRAINT `fk_groupe_createur`
        FOREIGN KEY (`id_createur`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Table : groupe_user  (jointure)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `groupe_user` (
    `id_groupe`    INT  NOT NULL,
    `id_user`      INT  NOT NULL,
    `role`         ENUM('admin','membre') NOT NULL DEFAULT 'membre',
    `date_adhesion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_groupe`, `id_user`),
    CONSTRAINT `fk_gu_groupe`
        FOREIGN KEY (`id_groupe`) REFERENCES `groupe`(`id_groupe`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_gu_user`
        FOREIGN KEY (`id_user`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Table : discussion
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `discussion` (
    `id_discussion` INT          NOT NULL AUTO_INCREMENT,
    `titre`         VARCHAR(200) NOT NULL,
    `contenu`       TEXT         NOT NULL,
    `date_creation` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `nb_likes`      INT          NOT NULL DEFAULT 0,
    `id_groupe`     INT          NOT NULL,
    `id_auteur`     INT          NOT NULL,
    `approval_statut` ENUM('en_attente','approuve','refuse') NOT NULL DEFAULT 'approuve',
    PRIMARY KEY (`id_discussion`),
    CONSTRAINT `fk_discussion_groupe`
        FOREIGN KEY (`id_groupe`) REFERENCES `groupe`(`id_groupe`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_discussion_auteur`
        FOREIGN KEY (`id_auteur`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Table : message
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `message` (
    `id_message`    INT      NOT NULL AUTO_INCREMENT,
    `contenu`       TEXT     NOT NULL,
    `date_envoi`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id_discussion` INT      NOT NULL,
    `id_auteur`     INT      NOT NULL,
    PRIMARY KEY (`id_message`),
    CONSTRAINT `fk_message_discussion`
        FOREIGN KEY (`id_discussion`) REFERENCES `discussion`(`id_discussion`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_message_auteur`
        FOREIGN KEY (`id_auteur`) REFERENCES `users`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
