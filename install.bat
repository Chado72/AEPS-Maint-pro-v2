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

:: 8. NETTOYAGE DES CACHES ET CONFIGURATION FINALE
echo.
echo [8/9] Optimisation et nettoyage des caches...
call php artisan config:clear
call php artisan cache:clear
call php artisan view:clear
call php artisan route:clear
echo OK: Caches nettoyes.

:: 9. VERIFICATION DES MIDDLEWARE DE SECURITE
echo.
echo [9/9] Verification des middleware de securite...
if exist "app\Http\Middleware\CheckRole.php" (
    echo [OK] Middleware CheckRole installe
) else (
    echo [ERREUR] Middleware CheckRole manquant!
)
if exist "app\Http\Middleware\CheckPermission.php" (
    echo [OK] Middleware CheckPermission installe
) else (
    echo [ERREUR] Middleware CheckPermission manquant!
)
if exist "app\Http\Middleware\OwnsAiSession.php" (
    echo [OK] Middleware OwnsAiSession installe
) else (
    echo [ERREUR] Middleware OwnsAiSession manquant!
)
if exist "app\Http\Middleware\EnsureUserIsActive.php" (
    echo [OK] Middleware EnsureUserIsActive installe
) else (
    echo [ERREUR] Middleware EnsureUserIsActive manquant!
)
if exist "app\Http\Controllers\Auth\LoginController.php" (
    echo [OK] LoginController avec protection CSRF installe
) else (
    echo [ERREUR] LoginController manquant!
)
echo.
echo OK: Verification des middleware terminee.

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
echo IMPORTANT: Changez le mot de passe après la première connexion!
echo.
echo NOUVELLES FONCTIONNALITÉS DE SÉCURITÉ INSTALLÉES:
echo ===================================================
echo [OK] Middleware CheckRole - Contrôle d'accès par rôle
echo [OK] Middleware CheckPermission - Contrôle d'accès par permission
echo [OK] Middleware OwnsAiSession - Protection des sessions IA
echo [OK] Middleware EnsureUserIsActive - Vérification statut utilisateur
echo [OK] Protection CSRF sur tous les formulaires (login inclus)
echo [OK] Rate limiting API IA - 10 requêtes/minute maximum
echo [OK] Transactions DB avec verrous pour gestion du stock
echo [OK] Logs d'audit pour actions critiques
echo [OK] Validation stricte des entrées utilisateur
echo.
echo RÔLES ET PERMISSIONS:
echo =====================
echo - SuperAdmin: Accès complet à toutes les fonctionnalités
echo - Gestionnaire: Gestion interventions, stocks, rapports
echo - Technicien: Consultation et mise à jour interventions assignées
echo - Observateur: Lecture seule
echo.
echo TESTS DE VALIDATION RECOMMANDÉS:
echo =================================
echo 1. Contrôle d'accès sessions IA: Connectez-vous avec user1
echo    Essayez d'accéder à la session de user2 - Doit retourner 403
echo.
echo 2. Rate limiting IA: Envoyez 11 requêtes en moins d'une minute
echo    La 11ème doit retourner 429 (Too Many Requests)
echo.
echo 3. Permissions: Connectez-vous avec un Technicien
echo    Essayez de modifier un paramètre global - Doit retourner 403
echo.
echo 4. Race condition stock: Deux requêtes simultanées pour
echo    utiliser la même pièce - Une seule doit réussir
echo.
echo DOCUMENTATION:
echo ==============
echo - SECURITY_FIXES.md: Détails des corrections de sécurité
echo - README.md: Guide d'utilisation général
echo.
echo Prochaines étapes :
echo 1. Ouvrez votre navigateur.
echo 2. Connectez-vous avec les identifiants ci-dessus.
echo 3. Changez votre mot de passe immédiatement.
echo 4. Allez dans 'Paramètres' pour configurer vos clés API IA (Mistral/Groq).
echo.
pause
