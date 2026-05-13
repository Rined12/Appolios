# 🎓 APPOLIOS — Advanced E-Learning Platform

![APPOLIOS Banner](https://img.shields.io/badge/Architecture-MVC-blue?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Production--Ready-success?style=for-the-badge)
![PHP Version](https://img.shields.io/badge/PHP-%3E%3D_8.0-777bb4?style=for-the-badge&logo=php)
![JS](https://img.shields.io/badge/JS-Modern-yellow?style=for-the-badge&logo=javascript)

**APPOLIOS** est une plateforme d'apprentissage en ligne (LMS) de nouvelle génération, conçue pour offrir une expérience utilisateur fluide, sécurisée et immersive. Alliant design moderne "Neo-UI" et technologies de pointe, elle permet une gestion complète du cycle éducatif pour les étudiants, enseignants et administrateurs.

---

## ✨ Fonctionnalités Clés

### 🔒 Sécurité & Authentification
*   **Face ID Login** : Connexion biométrique ultra-rapide utilisant l'IA (`face-api.js`).
*   **Google OAuth 2.0** : Intégration transparente pour une connexion en un clic.
*   **Protection Avancée** : Intégration de Google reCAPTCHA v2 et gestion sécurisée des sessions.

### 📚 Expérience d'Apprentissage
*   **Multilingue Natif** : Support complet du Français, Anglais et Arabe avec basculement dynamique et support **RTL**.
*   **Système de Quiz Pro** : Évaluations interactives avec historique de progression et analyses de performances.
*   **Gamification** : Système de badges et de récompenses pour booster l'engagement des étudiants.
*   **Mode Sombre** : Interface adaptative pour un confort visuel optimal (Neo-UI Design).

### 🛠️ Administration & Enseignement
*   **Dashboard Instructeur** : Création de cours, gestion des chapitres et suivi en temps réel des élèves.
*   **Portail Administrateur** : Gestion complète des utilisateurs, audit des logs et prédictions analytiques sur 7 jours.
*   **Gestion d'Événements** : Système de planification et de réservation d'événements éducatifs.

---

## 🚀 Architecture Technique

Le projet repose sur une structure **MVC (Modèle-Vue-Contrôleur)** personnalisée pour une maintenance aisée et une scalabilité optimale.

*   **Backend** : PHP 8.x (Custom MVC Framework)
*   **Frontend** : JavaScript moderne, CSS3 (Vanilla + Custom Design System), SweetAlert2.
*   **Base de données** : MySQL avec gestion fine des relations.
*   **IA & API** :
    *   `face-api.js` pour la reconnaissance faciale.
    *   API Google Cloud pour l'authentification.
    *   Intégration d'un Chatbot intelligent.

---

## 🛠️ Installation

1.  **Cloner le dépôt** :
    ```bash
    git clone https://github.com/votre-repo/APPOLIOS.git
    ```
2.  **Configuration Environnement** :
    *   Renommez `.env.example` en `.env`.
    *   Configurez vos accès base de données et clés API (Google Sign-In, reCAPTCHA).
3.  **Base de données** :
    *   Importez le fichier `appolios_structure.sql` dans votre serveur MySQL (XAMPP/WAMP).
4.  **Lancer le projet** :
    *   Placez le dossier dans `htdocs`.
    *   Accédez à `http://localhost/APPOLIOS`.

---

## 🎨 Design System (Neo-UI)

APPOLIOS utilise un système de design exclusif basé sur :
*   **Glassmorphism** : Effets de transparence et flou d'arrière-plan.
*   **Micro-animations** : Transitions fluides pour chaque interaction.
*   **Typographie Premium** : Utilisation de polices modernes (Inter, Poppins).

---

## 👥 Rôles Utilisateurs

| Rôle | Accès |
| :--- | :--- |
| **Étudiant** | Cours, Quiz, Événements, Profil personnel, Gamification. |
| **Enseignant** | Gestion des contenus, Création de quiz, Statistiques de classe. |
| **Admin** | Supervision globale, Gestion des rôles, Audit logs, Analytics. |

---

## 📝 Licence

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'informations.

---

> **APPOLIOS** — Apprenez mieux, avancez plus loin.
