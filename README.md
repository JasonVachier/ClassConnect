# ClassConnect 👨‍🏫👨‍👩‍👧

ClassConnect est une plateforme web simple qui facilite la communication entre
les **enseignants**, les **parents** et les **élèves du primaire**.

Le projet a été réalisé dans le cadre d’un travail universitaire à l’**UQAC**
(Université du Québec à Chicoutimi) pour le cours de développement Web.

---

## 🎯 Objectif du projet

- Proposer une interface **très simple** pour suivre la vie de la classe.
- Centraliser les **annonces importantes** (sorties, devoirs, rappels).
- Permettre un **échange encadré** entre professeurs et parents, sans utiliser
  les courriels ou numéros personnels des enseignants.

---

## 🧱 Fonctionnalités implémentées (version Bêta)

### Authentification

- Création de compte avec deux rôles :
  - `Professeur`
  - `Parent`
- Connexion / déconnexion
- Hashage des mots de passe (`password_hash` en PHP)

### Gestion des classes

- Un professeur peut :
  - créer plusieurs classes,
  - obtenir un **code de classe** généré automatiquement,
  - voir la liste de ses classes dans le *Dashboard*.

- Un parent peut :
  - rejoindre une classe à l’aide du **code de classe**,
  - voir la ou les classes auxquelles il est rattaché.

### Page de classe

Pour chaque classe :

- **Annonces**
  - le professeur peut publier des annonces (titre + contenu),
  - les parents voient la liste des annonces de la classe.

- **Messagerie**
  - parents et prof peuvent poster des messages,
  - les messages sont affichés chronologiquement avec le rôle (prof/parent).

### Navigation / pages

- `index.php` : page d’accueil (présentation rapide)
- `apropos.php` : page expliquant le concept et le contexte UQAC
- `faq.php` : page d’aide / FAQ
- `register.php` : inscription
- `login.php` : connexion
- `dashboard.php` : tableau de bord (vue différente prof/parent)
- `create_class.php` : création de classe (prof)
- `join_class.php` : rejoindre une classe (parent)
- `class.php` : page d’une classe (annonces + messages)
- `logout.php` : déconnexion

---

## 🛠️ Technologies utilisées

- **Front-end :**
  - HTML5
  - CSS3 (design custom, fond dégradé, cartes, responsive basique)

- **Back-end :**
  - PHP 8
  - Sessions PHP pour la gestion de l’authentification

- **Base de données :**
  - MySQL
  - Tables principales :
    - `users` (utilisateurs et rôles)
    - `classes` (classes créées par les profs)
    - `class_members` (association parents ↔ classes)
    - `announcements` (annonces de classe)
    - `messages` (messagerie de classe)

- **Outils :**
  - VS Code
  - Git / GitHub (branche `main` + branche `beta`)
  - Hébergement : Hostinger (déploiement du site pour la démo)

---

## ⚙️ Installation & mise en place

### 1. Prérequis

- PHP 8
- MySQL
- Serveur local type XAMPP / WAMP **ou** serveur web (ex. Hostinger)

### 2. Cloner le dépôt

```bash
git clone https://github.com/<ton-user>/ClassConnect.git
cd ClassConnect
