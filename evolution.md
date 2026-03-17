# Plan de développement du projet – Salon de Coiffure

## 📅 Lundi — Analyse et conception

### Lundi matin – Analyse du projet
- Analyse approfondie du besoin client.
- Identification des utilisateurs (client et administrateur).
- Définition précise des fonctionnalités attendues.
- Étude comparative de sites de réservation existants pour s'inspirer des bonnes pratiques.

### Lundi après-midi – Conception UX/UI
- **Wireframes et maquettes**  
  - Création de wireframes papier puis numériques (Figma / Balsamiq).  
  - Définition de l’arborescence, de la navigation et des écrans principaux.  
  - **Livrable** : Wireframes complets + maquettes visuelles validées par le formateur.

- **Modélisation de la base de données**  
  - Conception du MCD / MLD, définition des tables, relations et contraintes.  
  - Validation de la cohérence avec les fonctionnalités.  
  - **Livrable** : Schéma de BDD + scripts SQL de création + jeu de données de test.

---

## 📅 Mardi — Développement Front‑End

### Mardi matin – Structure
- **Architecture HTML/CSS**  
  - Création de la structure HTML sémantique.  
  - Intégration CSS responsive avec Bootstrap.  
  - Mise en place de la navigation et du layout général.  
  - **Livrable** : Pages HTML complètes avec CSS responsive fonctionnel.

- **Environnement de développement**  
  - Configuration de XAMPP (serveur local, PHP, MySQL).  
  - Création de la base de données.  
  - Initialisation du dépôt Git et structuration des dossiers du projet.  
  - **Livrable** : Environnement de développement opérationnel + premier commit Git.

### Mardi après-midi – Interactivité
- **JavaScript et interactions**  
  - Implémentation de JavaScript pour les formulaires, validation côté client.  
  - Ajout d’interactions utilisateur (modals, onglets, calendrier interactif).  
  - **Livrable** : Interface utilisateur complètement interactive.

- **JALON OBLIGATOIRE – Validation Front‑End**  
  - Démonstration de l’interface complète au formateur.  
  - Validation de l’ergonomie, du responsive et des interactions.  
  - **Livrable** : Front‑end 100% fonctionnel validé par le formateur.

---

## 📅 Mercredi — Développement Back‑End

### Mercredi matin – Base du back‑end
- **Configuration PHP et base de données**  
  - Création des classes PHP et mise en place de la connexion à la BDD avec PDO.  
  - Implémentation d’un pattern MVC basique.  
  - **Livrable** : Architecture back‑end initialisée + connexion BDD opérationnelle.

- **CRUD fondamental**  
  - Implémentation des opérations de base (Create, Read, Update, Delete) pour l’entité principale du projet (services, réservations…).  
  - **Livrable** : CRUD principal fonctionnel avec tests.

### Mercredi après-midi – Sécurité et fonctionnalités avancées
- **Sécurisation de l’application**  
  - Protection contre les injections SQL (requêtes préparées).  
  - Protection contre les failles XSS (validation / échappement).  
  - Mise en place de tokens CSRF sur les formulaires sensibles.  
  - Gestion sécurisée des sessions.  
  - **Livrable** : Application sécurisée selon les standards du web.

- **Fonctionnalités métier spécifiques**  
  - Développement des fonctionnalités avancées propres au projet (envoi d’emails, géolocalisation, génération de PDF, calculs complexes…).  
  - **Livrable** : Fonctionnalités spécialisées opérationnelles.

---

## 📅 Jeudi — Intégration, tests et finalisation

### Jeudi matin – Intégration et tests
- **Connexion Front‑End / Back‑End**  
  - Intégration complète des interfaces avec les scripts / API PHP.  
  - Tests d’intégration et résolution des bugs.  
  - **Livrable** : Application intégrée et fonctionnelle de bout en bout.

- **Tests utilisateurs et optimisation**  
  - Tests effectués par d’autres équipes (ou utilisateurs tests).  
  - Correction des bugs identifiés, optimisation des performances et de l’ergonomie.  
  - **Livrable** : Application testée et optimisée.

### Jeudi après-midi – Finalisation et documentation
- **Documentation technique**  
  - Rédaction d’un README complet (prérequis, installation, configuration).  
  - Documentation de l’API (routes, paramètres, exemples).  
  - Commentaires dans le code.  
  - **Livrable** : Documentation complète et professionnelle.

- **JALON OBLIGATOIRE – Application finalisée**  
  - Validation de l’application complète par le formateur.  
  - Vérification des fonctionnalités, de la sécurité et de la documentation.  
  - **Livrable** : Application 100% finalisée + documentation associée.

---

## 📅 Vendredi — Soutenance et déploiement

### Vendredi matin – Préparation des soutenances
- **Préparation de la présentation**  
  - Création du support de présentation (slides).  
  - Répétition de la démonstration.  
  - Préparation des réponses aux questions techniques.  
  - **Livrable** : Support de présentation + démonstration rodée.

- **Déploiement final**  
  - Mise en ligne de l’application sur un serveur accessible.  
  - Tests finaux sur l’environnement de production.  
  - Sauvegarde complète du projet.  
  - **Livrable** : Application déployée et accessible en ligne.

### Vendredi après-midi – Soutenances et évaluation
- **Présentations du projet**  
  - Soutenance de 15 minutes par équipe : contexte, démonstration, choix techniques, difficultés rencontrées.  
  - **Livrable** : Présentation professionnelle + démonstration en direct.

- **Questions techniques et évaluation**  
  - 10 minutes de questions par le jury.  
  - Évaluation individuelle des compétences acquises.  
  - **Livrable** : Évaluation complète du projet et des compétences de chaque membre.

---

## ✅ Synthèse des jalons et livrables

| Jour & période | Activités principales | Livrables clés |
|----------------|------------------------|----------------|
| Lundi matin | Analyse du besoin | Cahier des charges fonctionnel |
| Lundi après-midi | Wireframes, maquettes, modélisation BDD | Wireframes, maquettes validées, schéma BDD + scripts SQL |
| Mardi matin | HTML/CSS, environnement, Git | Pages HTML/CSS responsive, environnement prêt, premier commit |
| Mardi après-midi | JavaScript, interactions | Interface interactive, validation formateur |
| Mercredi matin | PHP, MVC, CRUD | Back‑end structuré, CRUD fonctionnel |
| Mercredi après-midi | Sécurité, fonctionnalités avancées | Application sécurisée, fonctionnalités métier |
| Jeudi matin | Intégration, tests | Application intégrée, testée, optimisée |
| Jeudi après-midi | Documentation, validation finale | Documentation complète, application finalisée |
| Vendredi matin | Préparation soutenance, déploiement | Support de présentation, application en ligne |
| Vendredi après-midi | Soutenances, évaluation | Présentation, évaluation individuelle |

---

**Remarque** : Ce plan est conçu pour une équipe suivant une méthodologie de développement itérative. Adaptez les horaires et la répartition des tâches selon la taille de votre équipe et les contraintes spécifiques.
# Système de Réservation pour Salon de Coiffure

## 1. Présentation du projet

### 🎯 Objectif
Développer une application web permettant aux clients de réserver un rendez-vous en ligne 24h/24.

### 🧩 Problème à résoudre
- Réservations uniquement par téléphone
- Manque de disponibilité hors horaires d’ouverture
- Gestion manuelle des rendez-vous
- Risques de conflits de réservation

### 💡 Solution proposée
- Consultation des services
- Affichage des horaires disponibles
- Réservation en ligne
- Confirmation automatique
- Interface administrateur pour la gestion

## 2. Technologies utilisées

### Front-end
- **HTML5** — structure des pages
- **CSS3** — design et mise en forme
- **JavaScript** — interactions et dynamisme

### Back-end
- **PHP** — logique métier et traitement des données

### Base de données
- **MySQL / SQL** — stockage des informations

## 3. Utilisateurs du système

### 👤 Client
- Consulter les services
- Réserver un rendez-vous
- Recevoir une confirmation

### 👨‍💼 Administrateur
- Gérer les services
- Voir les réservations
- Modifier/annuler un rendez-vous
- Gérer les horaires

## 4. Gestion du projet — Évolution jour par jour

- **Lundi — Analyse du projet**
  - Analyse du besoin
  - Identification des utilisateurs
  - Définition des fonctionnalités
  - Étude des sites existants

- **Lundi après-midi — Conception**
  - Création des maquettes (accueil, services, réservation, contact)
  - Conception de la base de données

- **Mardi — Développement Front-End**
  - *Matin* : Structure
    - Création des pages HTML
    - Mise en forme CSS
    - Navigation
  - *Après-midi* : JavaScript
    - Validation des formulaires
    - Calendrier de réservation
    - Affichage dynamique

- **Mercredi — Développement Back-End**
  - *Matin* : Serveur
    - Installation environnement
    - Connexion PHP ↔ MySQL
    - Fonctions de base
  - *Après-midi* : Logique métier
    - Enregistrement des réservations
    - Vérification des disponibilités
    - Gestion des services
    - Authentification admin

- **Jeudi — Intégration & Tests**
  - Connexion front/back
  - Tests de réservation
  - Correction des bugs
  - Optimisation

- **Vendredi — Finalisation**
  - Préparation de la soutenance
  - Démonstration du site
  - Explication des choix techniques

## 5. Arborescence technique du projet
salon-reservation/
│
├── index.php
├── services.php
├── reservation.php
├── contact.php
│
├── admin/
│ ├── login.php
│ ├── dashboard.php
│ ├── reservations.php
│ ├── services.php
│ ├── users.php
│ └── logout.php
│
├── config/
│ └── database.php
│
├── includes/
│ ├── header.php
│ ├── navbar.php
│ └── footer.php
│
├── controllers/
│ ├── reservationController.php
│ ├── serviceController.php
│ └── userController.php
│
├── models/
│ ├── Reservation.php
│ ├── Service.php
│ └── User.php
│
├── assets/
│ ├── css/
│ │ └── style.css
│ ├── js/
│ │ └── script.js
│ └── images/
│
├── api/
│ ├── getServices.php
│ ├── getDisponibilites.php
│ └── createReservation.php
│
├── sql/
│ └── database.sql
│
└── README.md

## 6. Structure de la base de données

### Table : `services`
- `id`
- `nom`
- `description`
- `durée`
- `prix`

### Table : `reservations`
- `id`
- `id_service`
- `date`
- `heure`
- `nom_client`
- `email`
- `téléphone`
- `statut`

### Table : `disponibilites`
- `id`
- `jour`
- `heure_ouverture`
- `heure_fermeture`

### Table : `users`
- `id`
- `nom`
- `email`
- `mot_de_passe`
- `rôle`

## 7. Sécurité du projet

- Validation des données (client & serveur)
- Protection contre injections SQL
- Protection XSS
- Connexion administrateur sécurisée

## 8. Conclusion

Ce projet constitue une application web complète permettant la gestion moderne des rendez-vous d’un salon de coiffure. Il mobilise des compétences en :

- développement front-end
- programmation back-end
- conception de base de données
- gestion de projet

Il offre une solution efficace, intuitive et professionnelle pour la gestion des réservations.