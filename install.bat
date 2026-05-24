@echo off
setlocal EnableDelayedExpansion

:: ========================================================================
:: SCRIPT D'INSTALLATION AUTOMATIQUE - AEPS-MAINT PRO OUAHIGOUYA
:: Compatible Laragon (Windows)
:: ========================================================================

echo.
echo ==========================================
echo   AEPS-Maint Pro Ouahigouya - Installer
echo   Version: 1.0.0 (Industrielle)
echo ==========================================
echo.

:: 1. VÉRIFICATION DES PRÉREQUIS
echo [1/8] Vérification des prérequis...

where php >nul 2>nul
if %errorlevel% neq 0 (
    echo ERREUR: PHP n'est pas trouvé dans le PATH. Assurez-vous que Laragon est lancé.
    pause
    exit /b 1
)

where composer >nul 2>nul
if %errorlevel% neq 0 (
    echo ERREUR: Composer n'est pas trouvé. Veuillez l'installer ou lancer Laragon.
    pause
    exit /b 1
)
echo OK: PHP et Composer détectés.

:: 2. INSTALLATION DES DÉPENDANCES
echo.
echo [2/8] Installation des dépendances Composer (cela peut prendre quelques minutes)...
call composer install --no-interaction --prefer-dist --optimize-autoloader
if %errorlevel% neq 0 (
    echo ERREUR: L'installation des dépendances a échoué.
    pause
    exit /b 1
)
echo OK: Dépendances installées.

:: 3. CONFIGURATION DU FICHIER .ENV
echo.
echo [3/8] Configuration du fichier .env...

if not exist .env (
    if exist .env.example (
        copy .env.example .env >nul
        echo Fichier .env créé à partir de .env.example.
    ) else (
        echo ERREUR: Le fichier .env.example est introuvable.
        pause
        exit /b 1
    )
) else (
    echo Le fichier .env existe déjà.
)

:: Mise à jour dynamique des paramètres DB pour Laragon
echo Mise à jour des paramètres de base de données pour Laragon...
(
    findstr /v "^DB_CONNECTION=" .env
    findstr /v "^DB_HOST=" .env
    findstr /v "^DB_PORT=" .env
    findstr /v "^DB_DATABASE=" .env
    findstr /v "^DB_USERNAME=" .env
    findstr /v "^DB_PASSWORD=" .env
) > .env.tmp

echo DB_CONNECTION=mysql>> .env.tmp
echo DB_HOST=127.0.0.1>> .env.tmp
echo DB_PORT=3306>> .env.tmp
echo DB_DATABASE=aeps_yadega>> .env.tmp
echo DB_USERNAME=root>> .env.tmp
echo DB_PASSWORD=>> .env.tmp

move /y .env.tmp .env >nul
echo OK: Fichier .env configuré.

:: 4. GÉNÉRATION DE LA CLÉ APP_KEY
echo.
echo [4/8] Génération de la clé de sécurité de l'application...
call php artisan key:generate
if %errorlevel% neq 0 (
    echo ERREUR: Échec de la génération de la clé.
    pause
    exit /b 1
)
echo OK: Clé générée.

:: 5. CRÉATION DE LA BASE DE DONNÉES
echo.
echo [5/8] Création de la base de données 'aeps_yadega'...

:: Tentative de création via mysql CLI (présent dans Laragon)
:: On suppose que mysql est accessible via le PATH de Laragon ou on utilise une commande générique
set "MYSQL_CMD=mysql -u root -e"

:: Note: Si le mot de passe root n'est pas vide sous Laragon, il faudra ajuster ici.
:: Par défaut Laragon: root sans mot de passe.
echo CREATE DATABASE IF NOT EXISTS aeps_yadega CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; | mysql -u root
if %errorlevel% neq 0 (
    echo ATTENTION: La création automatique de la DB a échoué (peut-être que mysql n'est pas dans le PATH).
    echo Veuillez créer la base de données 'aeps_yadega' manuellement via phpMyAdmin.
    echo Appuyez sur une touche pour continuer avec les migrations (si la DB existe déjà)...
    pause
) else (
    echo OK: Base de données créée avec succès.
)

:: 6. MIGRATIONS ET SEEDERS
echo.
echo [6/8] Exécution des migrations et peuplement des données (Seeders)...
call php artisan migrate --seed --force
if %errorlevel% neq 0 (
    echo ERREUR: Les migrations ont échoué. Vérifiez votre connexion à la base de données.
    pause
    exit /b 1
)
echo OK: Migrations et seeders exécutés.

:: 7. LIEN SYMBOLIQUE POUR LES STOCKAGES
echo.
echo [7/8] Création du lien symbolique pour les fichiers uploadés...
call php artisan storage:link
echo OK: Lien symbolique créé.

:: 8. NETTOYAGE DES CACHES
echo.
echo [8/8] Optimisation et nettoyage des caches...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
echo OK: Caches optimisés.

:: RÉSULTAT FINAL
echo.
echo ==========================================
echo   INSTALLATION TERMINÉE AVEC SUCCÈS !
echo ==========================================
echo.
echo URL d'accès : http://localhost/aeps-maint (ou http://aeps-maint.test si virtualisé)
echo.
echo IDENTIFIANTS PAR DÉFAUT :
echo -------------------------
echo Email    : admin@onea.bf
echo Mot de passe : admin123
echo.
echo Rôles créés : SuperAdmin, Gestionnaire, Technicien, Observateur
echo.
echo Prochaines étapes :
echo 1. Ouvrez votre navigateur.
echo 2. Connectez-vous avec les identifiants ci-dessus.
echo 3. Allez dans 'Paramètres' pour configurer vos clés API IA (Mistral/Groq).
echo.
pause
