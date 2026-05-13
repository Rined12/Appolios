<?php
require_once 'config/database.php';

$pdo = getConnection();

$sql = "
SET FOREIGN_KEY_CHECKS=0;

-- Table groupe
CREATE TABLE IF NOT EXISTS `groupe` (
  `id_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_createur` int(11) NOT NULL,
  `statut` varchar(50) DEFAULT 'public',
  `approval_statut` enum('en_cours','approuve','rejete') DEFAULT 'approuve',
  `image_url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_groupe`),
  KEY `id_createur` (`id_createur`),
  CONSTRAINT `groupe_ibfk_1` FOREIGN KEY (`id_createur`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table groupe_user
CREATE TABLE IF NOT EXISTS `groupe_user` (
  `id_groupe` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_rejoint` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_groupe`,`id_user`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `groupe_user_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupe` (`id_groupe`) ON DELETE CASCADE,
  CONSTRAINT `groupe_user_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table discussion
CREATE TABLE IF NOT EXISTS `discussion` (
  `id_discussion` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` text DEFAULT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_groupe` int(11) NOT NULL,
  `id_auteur` int(11) NOT NULL,
  `approval_statut` enum('en_cours','approuve','rejete') DEFAULT 'approuve',
  PRIMARY KEY (`id_discussion`),
  KEY `id_groupe` (`id_groupe`),
  KEY `id_auteur` (`id_auteur`),
  CONSTRAINT `discussion_ibfk_1` FOREIGN KEY (`id_groupe`) REFERENCES `groupe` (`id_groupe`) ON DELETE CASCADE,
  CONSTRAINT `discussion_ibfk_2` FOREIGN KEY (`id_auteur`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table live_locations
CREATE TABLE IF NOT EXISTS `live_locations` (
  `user_id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `shared_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`user_id`,`discussion_id`),
  KEY `discussion_id` (`discussion_id`),
  CONSTRAINT `live_locations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `live_locations_ibfk_2` FOREIGN KEY (`discussion_id`) REFERENCES `discussion` (`id_discussion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table discussion_messages
CREATE TABLE IF NOT EXISTS `discussion_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discussion_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `message_type` varchar(50) DEFAULT 'text',
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `discussion_id` (`discussion_id`),
  CONSTRAINT `discussion_messages_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussion` (`id_discussion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
";

try {
    $pdo->exec($sql);
    echo "Social Learning tables created successfully!\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
