# Guide d'Installation sur Laragon

## AEPS-Maint Pro Ouahigouya

Ce guide vous accompagne dans l'installation de l'application sur Laragon (Windows).

---

## 📋 Prérequis

1. **Laragon** installé (version 5 ou supérieure)
   - Télécharger depuis: https://laragon.org/download/
   
2. **Le projet Laravel** déjà présent dans `C:\laragon\www\aeps-maint-pro`

---

## 🚀 Installation Automatique (Recommandée)

### Étape 1: Placer le projet dans Laragon

```
Copiez le dossier du projet dans: C:\laragon\www\aeps-maint-pro
```

### Étape 2: Exécuter le script d'installation

1. Ouvrez **Laragon** et démarrez les services (Apache/Nginx + MySQL)
2. Naviguez vers le dossier du projet:
   ```
   cd C:\laragon\www\aeps-maint-pro
   ```
3. Double-cliquez sur `install-laragon.bat` OU exécutez en tant qu'administrateur:
   ```cmd
   install-laragon.bat
   ```

### Étape 3: Suivre les instructions

Le script va automatiquement:
- ✅ Vérifier les prérequis (PHP, Composer, Node.js)
- ✅ Installer les dépendances Composer
- ✅ Créer/configurer le fichier `.env`
- ✅ Générer la clé d'application
- ✅ Configurer la base de données (SQLite ou MySQL)
- ✅ Exécuter les migrations
- ✅ Optionnel: Exécuter les seeders
- ✅ Optionnel: Installer les dépendances Node.js
- ✅ Configurer les permissions
- ✅ Créer le lien symbolique storage

### Étape 4: Accéder à l'application

Une fois l'installation terminée:

**Option A - Via le domaine Laragon:**
```
http://aeps-maint-pro.test
```

**Option B - Via localhost:**
```
http://localhost:8000
```
(En exécutant `php artisan serve --port=8000`)

---

## 🔧 Installation Manuelle (Alternative)

Si vous préférez installer manuellement:

### 1. Installer les dépendances PHP

```cmd
cd C:\laragon\www\aeps-maint-pro
composer install --no-interaction --prefer-dist --optimize-autoloader
```

### 2. Configurer l'environnement

```cmd
copy .env.example .env
php artisan key:generate
```

### 3. Configurer la base de données

Éditez le fichier `.env`:

**Pour SQLite (rapide):**
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=homestead
# DB_USERNAME=homestead
# DB_PASSWORD=secret
```

Puis créez la base:
```cmd
type nul > database\database.sqlite
```

**Pour MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aeps_maint_pro
DB_USERNAME=root
DB_PASSWORD=
```

Créez la base dans phpMyAdmin:
```sql
CREATE DATABASE aeps_maint_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Exécuter les migrations

```cmd
php artisan migrate
```

### 5. Exécuter les seeders (optionnel)

```cmd
php artisan db:seed
```

### 6. Configurer les permissions

```cmd
icacls storage /grant Everyone:(OI)(CI)F /T
icacls bootstrap\cache /grant Everyone:(OI)(CI)F /T
```

### 7. Créer le lien symbolique

```cmd
php artisan storage:link
```

### 8. Nettoyer les caches

```cmd
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 👤 Identifiants par défaut

Après avoir exécuté les seeders:

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@aeps-maint.pro | password |
| Technicien | tech@aeps-maint.pro | password |
| Utilisateur | user@aeps-maint.pro | password |

⚠️ **IMPORTANT**: Changez ces mots de passe après la première connexion!

---

## 🔐 Sécurité

Cette version inclut les corrections de sécurité suivantes:

- ✅ Système de rôles et permissions
- ✅ Protection CSRF sur toutes les routes
- ✅ Rate limiting sur l'API IA (10 req/min)
- ✅ Transactions pour les opérations critiques
- ✅ Validation stricte des entrées
- ✅ Contrôle d'accès basé sur les propriétaires
- ✅ Plus de modification du fichier .env à l'exécution

Consultez `SECURITY_FIXES.md` pour plus de détails.

---

## 🛠️ Commandes Utiles

### Démarrer le serveur de développement
```cmd
php artisan serve --port=8000
```

### Recréer la base de données
```cmd
php artisan migrate:fresh --seed
```

### Optimiser pour la production
```cmd
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Mode maintenance
```cmd
php artisan down    # Activer
php artisan up      # Désactiver
```

---

## ❓ Dépannage

### Erreur: "PHP n'est pas disponible"
- Démarrez Laragon
- Cliquez sur Menu → Apache/Nginx → Redémarrer
- Vérifiez que PHP est dans le PATH système

### Erreur: "Base de données non trouvée"
- Pour MySQL: Créez la base manuellement dans phpMyAdmin
- Pour SQLite: Vérifiez que `database/database.sqlite` existe

### Erreur: "Permission denied"
- Exécutez le script en tant qu'administrateur
- Vérifiez les permissions sur `storage/` et `bootstrap/cache/`

### Erreur: "Composer memory limit"
```cmd
set COMPOSER_MEMORY_LIMIT=-1
composer install
```

---

## 📞 Support

Pour toute question ou problème:

1. Consultez les logs: `storage/logs/laravel.log`
2. Activez le mode debug dans `.env`:
   ```env
   APP_DEBUG=true
   ```
3. Vérifiez la configuration: `php artisan env`

---

## 📝 Notes Importantes

- **Ne modifiez jamais manuellement le fichier `.env` en production** via l'interface web
- **Changez toujours la clé `APP_KEY`** avant de mettre en production
- **Sauvegardez régulièrement** votre base de données
- **Mettez à jour régulièrement** les dépendances: `composer update`

---

**Document créé le:** $(date)
**Version du guide:** 1.0
**Compatible avec:** Laravel 10.x+, Laragon 5.x+
