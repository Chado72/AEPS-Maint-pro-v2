# AEPS-Maint Pro Ouahigouya
## Système de Gestion de Maintenance des Points d'Eau (ONEA - Yadéga)

Bienvenue dans le guide officiel d'installation et d'utilisation. Ce document est conçu pour vous accompagner pas à pas, même si vous débutez avec Laravel ou Laragon.

---

## 📋 TABLE DES MATIÈRES

1. [Prérequis](#1-prérequis)
2. [Installation de Laragon](#2-installation-de-laragon)
3. [Installation du Projet](#3-installation-du-projet)
4. [Configuration de la Base de Données](#4-configuration-de-la-base-de-données)
5. [Lancement de l'Application](#5-lancement-de-lapplication)
6. [Première Connexion](#6-première-connexion)
7. [Guide des Fonctionnalités](#7-guide-des-fonctionnalités)
8. [Configuration de l'IA](#8-configuration-de-lia)
9. [Dépannage](#9-dépannage)

---

## 1. PRÉREQUIS

Avant de commencer, assurez-vous d'avoir :
- ✅ Un ordinateur sous Windows (10 ou 11 recommandé).
- ✅ Une connexion Internet stable (pour télécharger les outils).
- ✅ Les droits administrateur sur votre ordinateur.

---

## 2. INSTALLATION DE LARAGON

**Laragon** est un outil tout-en-un qui contient le serveur web, PHP et la base de données MySQL.

### Étape 2.1 : Télécharger Laragon
1. Allez sur le site officiel : [https://laragon.org/download/](https://laragon.org/download/)
2. Cliquez sur **"Download Laragon Full"** (version complète).
3. Enregistrez le fichier `.exe` sur votre bureau.

### Étape 2.2 : Installer Laragon
1. Double-cliquez sur le fichier téléchargé.
2. Acceptez les termes et cliquez sur **Next**.
3. Choisissez le dossier d'installation (par défaut : `C:\laragon`). **Ne changez rien**, c'est plus simple.
4. Cochez toutes les options proposées (Apache, Nginx, MySQL, PHP, etc.).
5. Cliquez sur **Install** et attendez la fin.
6. À la fin, cliquez sur **Finish**. Laragon va se lancer automatiquement.

### Étape 2.3 : Démarrer les services
1. Dans la fenêtre de Laragon, cliquez sur le gros bouton **"Start All"**.
2. Attendez que les indicateurs deviennent **verts** (Apache et MySQL).
3. Si tout est vert, cliquez sur le bouton **"Root"** en bas à droite. Cela ouvre le dossier où nous mettrons notre projet.

---

## 3. INSTALLATION DU PROJET

Nous allons maintenant placer les fichiers du projet dans le dossier de Laragon.

### Étape 3.1 : Placer les fichiers
1. Dans le dossier qui vient de s'ouvrir (`C:\laragon\www`), créez un nouveau dossier nommé : `aeps-maint`.
2. Copiez **tous les fichiers de ce projet** (ceux que vous avez générés ou téléchargés) à l'intérieur de `C:\laragon\www\aeps-maint`.
   
   > ⚠️ **Important** : Ne copiez pas le dossier parent, mais bien le *contenu* directement dans `aeps-maint`.
   > La structure doit ressembler à : `C:\laragon\www\aeps-maint\app`, `C:\laragon\www\aeps-maint\routes`, etc.

### Étape 3.2 : Ouvrir le terminal
1. Retournez dans Laragon.
2. Cliquez sur l'icône **Terminal** (ou faites un clic droit dans le dossier du projet -> "Open Terminal here").
3. Une fenêtre noire (invite de commande) s'ouvre. Nous allons taper des commandes dedans.

### Étape 3.3 : Installer les dépendances (Composer)
Le projet a besoin de bibliothèques PHP pour fonctionner.
1. Dans le terminal, tapez la commande suivante et appuyez sur **Entrée** :
   ```bash
   composer install
   ```
2. Laissez tourner le téléchargement. Cela peut prendre 2 à 5 minutes selon votre connexion.
3. Quand vous voyez "Done!", passez à l'étape suivante.

### Étape 3.4 : Créer le fichier de configuration
1. Dans le dossier du projet (`C:\laragon\www\aeps-maint`), cherchez un fichier nommé `.env.example`.
2. Copiez-ce fichier et renommez la copie en `.env` (sans le mot "example").
   
   > 💡 **Astuce** : Si vous ne voyez pas les extensions de fichiers (.txt, .example), activez-les dans l'explorateur Windows (Affichage -> Extensions de noms de fichiers).

3. Retournez dans le terminal et tapez :
   ```bash
   php artisan key:generate
   ```
   > Cela génère une clé de sécurité unique. Vous devriez voir : "Application key set successfully."

---

## 4. CONFIGURATION DE LA BASE DE DONNÉES

Nous allons créer la base de données vide dans phpMyAdmin.

### Étape 4.1 : Accéder à phpMyAdmin
1. Dans Laragon, cliquez sur le bouton **"phpMyAdmin"** (ou ouvrez votre navigateur et allez sur `http://localhost/phpmyadmin`).
2. Une page bleue s'ouvre. C'est le gestionnaire de base de données.

### Étape 4.2 : Créer la base de données
1. Cliquez sur l'onglet **"Nouvelle"** (ou "New") dans la colonne de gauche.
2. Dans le champ "Nom de la base de données", écrivez exactement : `aeps_yadega`
3. À côté, dans "Interclassement", choisissez : `utf8mb4_general_ci` (très important pour les accents et caractères spéciaux).
4. Cliquez sur le bouton **"Créer"**.

### Étape 4.3 : Relier le projet à la base de données
1. Retournez dans votre dossier projet `C:\laragon\www\aeps-maint`.
2. Ouvrez le fichier `.env` avec le Bloc-notes (ou un éditeur de code comme VS Code).
3. Cherchez les lignes suivantes et modifiez-les pour qu'elles ressemblent exactement à ceci :

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=aeps_yadega
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > ⚠️ **Attention** : Laissez `DB_PASSWORD=` vide (rien après le égal). Par défaut sur Laragon, root n'a pas de mot de passe.

4. Enregistrez le fichier `.env` et fermez-le.

### Étape 4.4 : Lancer les migrations et les données de test
Retournez dans le **Terminal** noir et lancez ces deux commandes l'une après l'autre :

1. **Créer les tables :**
   ```bash
   php artisan migrate
   ```
   > Si tout va bien, vous verrez une liste de tables créées ("Created table: users", etc.).

2. **Remplir avec des données de test (Seeders) :**
   ```bash
   php artisan db:seed
   ```
   > Cela va créer les communes, villages, rôles et un utilisateur administrateur par défaut.

---

## 5. LANCEMENT DE L'APPLICATION

Tout est prêt ! Nous pouvons ouvrir l'application.

1. Assurez-vous que Laragon est toujours sur **"Start All"** (vert).
2. Ouvrez votre navigateur (Chrome, Firefox, Edge).
3. Tapez l'adresse suivante dans la barre d'adresse :
   ```
   http://aeps-maint.test
   ```
   > 🎉 **Félicitations !** Laragon crée automatiquement cette adresse magique basée sur le nom de votre dossier.
   
   *Si cela ne fonctionne pas, essayez : `http://localhost/aeps-maint`*

Vous devriez voir la page de connexion de **AEPS-Maint Pro**.

---

## 6. PREMIÈRE CONNEXION

Grâce aux données de test (seeders), un compte administrateur a été créé pour vous.

- **Email** : `admin@onea.bf`
- **Mot de passe** : `admin123`

1. Entrez ces informations sur la page de connexion.
2. Cliquez sur **Se connecter**.
3. Vous arrivez sur le **Tableau de Bord (Dashboard)**.

> 🔒 **Sécurité** : Changez immédiatement ce mot de passe dans la section "Mon Profil" après votre première connexion.

---

## 7. GUIDE DES FONCTIONNALITÉS

Voici comment utiliser les différents modules de l'application.

### 🏠 Tableau de Bord
- Vue d'ensemble des statistiques : Nombre de sites, interventions en cours, taux de panne.
- Graphiques visuels pour suivre l'évolution mensuelle.

### 🗺️ Géographie (Communes & Villages)
1. Allez dans **Géographie > Communes**.
2. Cliquez sur **+ Nouvelle Commune** pour ajouter une zone (ex: Ouahigouya, Séguénéga).
3. Dans chaque commune, vous pouvez ajouter des **Villages**.
   > *Note : Un site doit obligatoirement être rattaché à un village existant.*

### 💧 Infrastructure (Sites & Forages)
C'est le cœur du système.
1. Allez dans **Infrastructure > Sites**.
2. Cliquez sur **+ Nouveau Site**.
   - Remplissez le nom du site (ex: "Forage de Kossouka").
   - Sélectionnez la Commune et le Village.
   - Ajoutez les coordonnées GPS (Latitude/Longitude) si disponibles.
3. Une fois le site créé, allez dans l'onglet **Forages** de ce site.
   - Un site peut avoir **plusieurs forages**. Ajoutez-les un par un.
   - Précisez la profondeur, le débit, et l'équipement de pompage.

### ⚡ Énergie
- Dans la fiche d'un Site ou d'un Forage, vous pouvez gérer les sources d'énergie.
- Ajoutez des panneaux solaires, des groupes électrogènes ou le réseau SONABEL.
- Suivez la puissance (Wc) et l'état des batteries.

### 🛠️ Interventions & Maintenance
Quand une panne survient :
1. Allez dans **Maintenance > Interventions**.
2. Cliquez sur **+ Nouvelle Intervention**.
3. Sélectionnez le **Site** concerné.
4. Décrivez la panne et les travaux effectués.
5. **Pièces utilisées** : Dans le formulaire, vous pouvez sélectionner des pièces détachées dans le stock. Le stock sera automatiquement décrémenté.
6. Changez le statut (En cours, Terminé, Validé).

### 📦 Pièces de Rechange (Stock)
- Allez dans **Stock > Pièces Détachées**.
- Gérez votre inventaire : Pompes, clapets, câbles, onduleurs.
- Définissez un seuil d'alerte pour être prévenu quand le stock est bas.

### 📄 Rapports PDF
L'application génère des documents professionnels prêts à imprimer.
1. Allez dans **Rapports**.
2. Choisissez le type :
   - **Fiche Site** : Détails techniques complets d'un point d'eau.
   - **Rapport d'Intervention** : Bon de travail signé.
   - **Rapport Mensuel** : Statistiques globales pour la province.
   - **Rapport par Commune** : État des lieux par zone géographique.
3. Cliquez sur **Générer PDF**. Le téléchargement se lance automatiquement.

### 🤖 Assistant IA (Intelligence Artificielle)
Un outil puissant pour vous aider à analyser les pannes récurrentes ou rédiger des rapports.
*(Voir section 8 pour la configuration)*

---

## 8. CONFIGURATION DE L'IA

Pour activer l'assistant intelligent, vous devez fournir une clé API (Mistral ou Groq).

1. Connectez-vous en tant qu'administrateur.
2. Allez dans **Paramètres > Configuration IA**.
3. Choisissez votre fournisseur (Mistral AI ou Groq).
4. Collez votre clé API secrète dans le champ prévu.
   - *Où trouver une clé ?* Créez un compte sur [console.mistral.ai](https://console.mistral.ai) ou [groq.com](https://groq.com).
5. Cliquez sur **Sauvegarder**.
6. Allez maintenant dans le menu **Assistant IA**.
   - Posez une question : *"Quelle est la pièce la plus souvent changée ce mois-ci ?"*
   - L'IA analysera vos données et répondra.

> 💡 **Note** : Si vous ne configurez pas de clé, le module IA restera masqué ou affichera un message d'avertissement, sans bloquer le reste de l'application.

---

## 9. DÉPANNAGE (FAQ)

### ❓ La page affiche une erreur "500 Server Error"
- Vérifiez que vous avez bien lancé `composer install`.
- Vérifiez que le fichier `.env` existe et contient la bonne base de données.
- Regardez le fichier `storage/logs/laravel.log` pour voir le détail de l'erreur.

### ❓ Erreur de connexion à la base de données
- Vérifiez que MySQL est bien démarré dans Laragon (vert).
- Vérifiez que `DB_PASSWORD=` est bien vide dans le fichier `.env`.
- Vérifiez que la base `aeps_yadega` existe dans phpMyAdmin.

### ❓ Les images ou PDF ne se chargent pas
- Dans le terminal, lancez cette commande pour créer le lien vers le dossier de stockage :
  ```bash
  php artisan storage:link
  ```

### ❓ Comment remettre à zéro la base de données ?
Si vous avez fait des tests et voulez tout effacer pour recommencer proprement :
1. Allez dans phpMyAdmin et supprimez la base `aeps_yadega`.
2. Recréez-la (voir étape 4.2).
3. Relancez les commandes dans le terminal :
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Attention : cette commande efface toutes les données !)*

---

## 📞 SUPPORT & MAINTENANCE

Ce projet a été développé pour la province du Yadéga.
En cas de bug critique ou de besoin d'évolution, contactez l'équipe de développement technique.

**Version** : 1.0.0 Industrielle
**Stack** : Laravel 10, MySQL, Bootstrap 5, DomPDF, IA Mistral/Groq.

---
*Développé avec ❤️ pour l'accès à l'eau potable au Burkina Faso.*
