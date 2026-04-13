-- Exécuter une fois sur la base APPOLIOS (phpMyAdmin ou mysql CLI).
-- Ajoute la validation admin pour les groupes et discussions créés par étudiants / enseignants.

ALTER TABLE `groupe`
    ADD COLUMN `approval_statut` ENUM('en_attente','approuve','refuse') NOT NULL DEFAULT 'approuve'
    AFTER `statut`;

ALTER TABLE `discussion`
    ADD COLUMN `approval_statut` ENUM('en_attente','approuve','refuse') NOT NULL DEFAULT 'approuve'
    AFTER `id_auteur`;
