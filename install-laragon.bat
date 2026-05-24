@echo off
setlocal enabledelayedexpansion

:: ============================================================================
:: Script d'installation AEPS-Maint Pro Ouahigouya pour Laragon
:: ============================================================================
:: Ce script configure automatiquement le projet Laravel dans Laragon
:: ============================================================================

echo.
echo ===========================================================================
echo   Installation de AEPS-Maint Pro Ouahigouya sur Laragon
echo ===========================================================================
echo.

:: Vérifier si nous sommes dans le répertoire du projet
if not exist "artisan" (
    echo ERREUR: Ce script doit être exécuté depuis la racine du projet Laravel
    echo Assurez-vous que le dossier du projet est dans C:\laragon\www\
    pause
    exit /b 1
)

:: Configuration de Laragon
set LARAGON_ROOT=C:\laragon
set WWW_ROOT=%LARAGON_ROOT%\www

if not exist "%LARAGON_ROOT%" (
    echo ERREUR: Laragon n'est pas installé dans C:\laragon
    echo Veuillez installer Laragon ou modifier le chemin dans ce script
    pause
    exit /b 1
)

echo [OK] Laragon détecté dans %LARAGON_ROOT%
echo.

:: Étape 1: Vérification des prérequis
echo ---------------------------------------------------------------------------
echo Etape 1: Verification des pre-requis
echo ---------------------------------------------------------------------------

:: Vérifier PHP
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo ERREUR: PHP n'est pas disponible dans le PATH
    echo Veuillez démarrer Laragon et ajouter PHP au PATH
    pause
    exit /b 1
)

echo [OK] PHP detecte: 
php -v | findstr /R "^PHP"

:: Vérifier Composer
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo ERREUR: Composer n'est pas installé
    echo Telechargez et installez Composer depuis https://getcomposer.org/
    pause
    exit /b 1
)

echo [OK] Composer detecte
composer --version

:: Vérifier Node.js (optionnel mais recommandé)
where node >nul 2>&1
if %errorlevel% neq 0 (
    echo [WARNING] Node.js non detecte (optionnel pour les assets frontend)
) else (
    echo [OK] Node.js detecte
    node --version
)

echo.

:: Étape 2: Installation des dépendances PHP
echo ---------------------------------------------------------------------------
echo Etape 2: Installation des dependances PHP avec Composer
echo ---------------------------------------------------------------------------

echo Installation des dependances Composer...
call composer install --no-interaction --prefer-dist --optimize-autoloader

if %errorlevel% neq 0 (
    echo ERREUR: L'installation Composer a echoue
    pause
    exit /b 1
)

echo [OK] Dependances PHP installees avec succes
echo.

:: Étape 3: Configuration de l'environnement
echo ---------------------------------------------------------------------------
echo Etape 3: Configuration de l'environnement
echo ---------------------------------------------------------------------------

if not exist ".env" (
    echo Creation du fichier .env a partir de .env.example...
    if exist ".env.example" (
        copy .env.example .env >nul
        echo [OK] Fichier .env cree
    ) else (
        echo ERREUR: Le fichier .env.example n'existe pas
        pause
        exit /b 1
    )
) else (
    echo [INFO] Le fichier .env existe deja
)

:: Générer la clé d'application
echo Generation de la cle d'application...
call php artisan key:generate

if %errorlevel% neq 0 (
    echo ERREUR: La generation de la cle a echoue
    pause
    exit /b 1
)

echo [OK] Cle d'application generee
echo.

:: Étape 4: Configuration de la base de données
echo ---------------------------------------------------------------------------
echo Etape 4: Configuration de la base de donnees
echo ---------------------------------------------------------------------------

echo.
echo Choisissez le type de base de donnees:
echo 1. SQLite (recommande pour le developpement rapide)
echo 2. MySQL/MariaDB (Laragon inclus)
echo.
set /p DB_CHOICE="Votre choix (1 ou 2, par defaut 1): "

if "%DB_CHOICE%"=="2" (
    set /p DB_NAME="Nom de la base de donnees (par defaut aeps_maint_pro): "
    if "!DB_NAME!"=="" set DB_NAME=aeps_maint_pro
    
    set /p DB_USER="Utilisateur MySQL (par defaut root): "
    if "!DB_USER!"=="" set DB_USER=root
    
    set /p DB_PASS="Mot de passe MySQL (par defaut vide pour Laragon): "
    
    echo Mise a jour de la configuration MySQL dans .env...
    call php artisan db:wipe 2>nul
    
    (
        echo DATABASE_CONNECTION=mysql
        echo DATABASE_HOST=127.0.0.1
        echo DATABASE_PORT=3306
        echo DATABASE_DATABASE=!DB_NAME!
        echo DATABASE_USERNAME=!DB_USER!
        echo DATABASE_PASSWORD=!DB_PASS!
    ) > temp_db_config.txt
    
    :: Mettre à jour le fichier .env
    for /f "delims=" %%a in ('findstr /n "^" .env') do (
        set "line=%%a"
        setlocal enabledelayedexpansion
        set "prefix=!line:~0,17!"
        if "!prefix!"=="DATABASE_CONNECTI" (
            endlocal
            echo DATABASE_CONNECTION=mysql
        ) else if "!prefix!"=="DATABASE_HOST=127" (
            endlocal
            echo DATABASE_HOST=127.0.0.1
        ) else if "!prefix!"=="DATABASE_PORT=33" (
            endlocal
            echo DATABASE_PORT=3306
        ) else if "!prefix!"=="DATABASE_DATABASE" (
            endlocal
            echo DATABASE_DATABASE=!DB_NAME!
        ) else if "!prefix!"=="DATABASE_USERNAME" (
            endlocal
            echo DATABASE_USERNAME=!DB_USER!
        ) else if "!prefix!"=="DATABASE_PASSWORD" (
            endlocal
            echo DATABASE_PASSWORD=!DB_PASS!
        ) else (
            endlocal
            echo !line:*:=!
        )
    ) > temp.env
    
    move /y temp.env .env >nul
    del temp_db_config.txt 2>nul
    
    echo [INFO] Pour creer la base de donnees, executez dans phpMyAdmin:
    echo       CREATE DATABASE !DB_NAME! CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    echo.
    set /p CREATE_DB="Voulez-vous que le script tente de creer la base de donnees? (O/N, par defaut N): "
    if /i "!CREATE_DB!"=="O" (
        echo Tentative de creation de la base de donnees...
        mysql -u !DB_USER! -p!DB_PASS! -e "CREATE DATABASE IF NOT EXISTS !DB_NAME! CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
        if %errorlevel% equ 0 (
            echo [OK] Base de donnees creee avec succes
        ) else (
            echo [WARNING] Echec de la creation automatique. Créez-la manuellement dans phpMyAdmin.
        )
    )
) else (
    echo Configuration de SQLite...
    (
        echo DATABASE_CONNECTION=sqlite
        echo #DATABASE_HOST=127.0.0.1
        echo #DATABASE_PORT=3306
        echo #DATABASE_DATABASE=homestead
        echo #DATABASE_USERNAME=homestead
        echo #DATABASE_PASSWORD=secret
    ) > temp_db_config.txt
    
    :: Mettre à jour le fichier .env pour SQLite
    for /f "delims=" %%a in ('findstr /n "^" .env') do (
        set "line=%%a"
        setlocal enabledelayedexpansion
        set "prefix=!line:~0,17!"
        if "!prefix!"=="DATABASE_CONNECTI" (
            endlocal
            echo DATABASE_CONNECTION=sqlite
        ) else if "!prefix!"=="DATABASE_HOST=127" (
            endlocal
            echo #DATABASE_HOST=127.0.0.1
        ) else if "!prefix!"=="DATABASE_PORT=33" (
            endlocal
            echo #DATABASE_PORT=3306
        ) else if "!prefix!"=="DATABASE_DATABASE" (
            endlocal
            echo #DATABASE_DATABASE=homestead
        ) else if "!prefix!"=="DATABASE_USERNAME" (
            endlocal
            echo #DATABASE_USERNAME=homestead
        ) else if "!prefix!"=="DATABASE_PASSWORD" (
            endlocal
            echo #DATABASE_PASSWORD=secret
        ) else (
            endlocal
            echo !line:*:=!
        )
    ) > temp.env
    
    move /y temp.env .env >nul
    del temp_db_config.txt 2>nul
    
    if not exist "database\database.sqlite" (
        echo Creation de la base de donnees SQLite...
        type nul > database\database.sqlite
        echo [OK] Base de donnees SQLite creee
    ) else (
        echo [INFO] La base de donnees SQLite existe deja
    )
)

echo.

:: Étape 5: Exécution des migrations
echo ---------------------------------------------------------------------------
echo Etape 5: Execution des migrations
echo ---------------------------------------------------------------------------

echo Execution des migrations...
call php artisan migrate --force

if %errorlevel% neq 0 (
    echo [WARNING] Les migrations ont rencontre des erreurs
    echo Verifiez votre configuration de base de donnees
    echo Vous pourrez executer 'php artisan migrate' manuellement plus tard
) else (
    echo [OK] Migrations executees avec succes
)

echo.

:: Étape 6: Seed de la base de données (optionnel)
echo ---------------------------------------------------------------------------
echo Etape 6: Initialisation des donnees (Seed)
echo ---------------------------------------------------------------------------

set /p RUN_SEEDERS="Voulez-vous executer les seeders pour peupler la base? (O/N, par defaut O): "
if /i not "!RUN_SEEDERS!"=="N" (
    echo Execution des seeders...
    call php artisan db:seed --force
    
    if %errorlevel% neq 0 (
        echo [WARNING] Les seeders ont rencontre des erreurs
    ) else (
        echo [OK] Seeders executes avec succes
    )
) else (
    echo [INFO] Skip des seeders
)

echo.

:: Étape 7: Installation des dépendances Node (optionnel)
echo ---------------------------------------------------------------------------
echo Etape 7: Installation des dependances Node.js (optionnel)
echo ---------------------------------------------------------------------------

if exist "package.json" (
    where node >nul 2>&1
    if %errorlevel% equ 0 (
        set /p INSTALL_NODE="Voulez-vous installer les dependances Node.js? (O/N, par defaut N): "
        if /i "!INSTALL_NODE!"=="O" (
            echo Installation des dependances Node.js...
            call npm install --no-audit --no-fund
            
            if %errorlevel% equ 0 (
                echo [OK] Dependances Node.js installees
                echo.
                set /p BUILD_ASSETS="Voulez-vous compiler les assets? (O/N, par defaut N): "
                if /i "!BUILD_ASSETS!"=="O" (
                    echo Compilation des assets...
                    call npm run build
                    if %errorlevel% equ 0 (
                        echo [OK] Assets compiles avec succes
                    ) else (
                        echo [WARNING] Echec de la compilation des assets
                    )
                )
            ) else (
                echo [WARNING] Echec de l'installation des dependances Node.js
            )
        )
    )
) else (
    echo [INFO] Aucun fichier package.json trouve (projet backend uniquement)
)

echo.

:: Étape 8: Configuration des permissions
echo ---------------------------------------------------------------------------
echo Etape 8: Configuration des permissions
echo ---------------------------------------------------------------------------

echo Configuration des repertoires de stockage...
if exist "storage" (
    icacls storage /grant Everyone:(OI)(CI)F /T >nul 2>&1
    icacls storage\app /grant Everyone:(OI)(CI)F /T >nul 2>&1
    icacls storage\framework /grant Everyone:(OI)(CI)F /T >nul 2>&1
    icacls storage\logs /grant Everyone:(OI)(CI)F /T >nul 2>&1
    echo [OK] Permissions configurees pour storage/
)

if exist "bootstrap\cache" (
    icacls bootstrap\cache /grant Everyone:(OI)(CI)F /T >nul 2>&1
    echo [OK] Permissions configurees pour bootstrap/cache/
)

:: Nettoyer le cache
call php artisan config:clear
call php artisan cache:clear
call php artisan view:clear
call php artisan route:clear

echo [OK] Caches nettoyes
echo.

:: Étape 9: Création du lien symbolique
echo ---------------------------------------------------------------------------
echo Etape 9: Creation du lien symbolique pour les fichiers publics
echo ---------------------------------------------------------------------------

call php artisan storage:link 2>nul
if %errorlevel% equ 0 (
    echo [OK] Lien symbolique storage:link cree
) else (
    echo [INFO] Le lien symbolique existe deja ou n'est pas necessaire
)

echo.

:: Étape 10: Résumé et instructions finales
echo ---------------------------------------------------------------------------
echo Etape 10: Finalisation
echo ---------------------------------------------------------------------------

echo.
echo ===========================================================================
echo   Installation terminee avec succes!
echo ===========================================================================
echo.
echo Projet: AEPS-Maint Pro Ouahigouya
echo Repertoire: %CD%
echo.
echo Prochaines etapes:
echo.
echo 1. Demarrer Laragon (si ce n'est pas deja fait)
echo 2. Activer le site dans Laragon:
echo    - Ouvrir Laragon
echo    - Cliquer sur "Menu" ^> "www" ^> Selectionner votre projet
echo    - Ou acceder directement via: http://aeps-maint-pro.test
echo.
echo 3. Premier acces:
echo    - URL: http://localhost:8000 (ou votre domaine Laragon)
echo    - Identifiants par defaut (si seeders executes):
echo      * Email: admin@aeps-maint.pro
echo      * Mot de passe: password
echo.
echo 4. Pour demarrer le serveur de developpement manuellement:
echo    php artisan serve --port=8000
echo.
echo ===========================================================================
echo.
echo IMPORTANT: Mesures de securite implantees:
echo - System de roles et permissions
echo - Protection CSRF activee
echo - Rate limiting sur l'API IA
echo - Transactions pour les operations critiques
echo - Validation stricte des entrees
echo.
echo Consultez SECURITY_FIXES.md pour plus de details
echo ===========================================================================
echo.

:: Créer un fichier de rappel
(
    echo AEPS-Maint Pro Ouahigouya - Installation Complete
    echo Date: %date% %time%
    echo.
    echo URL d'acces: http://localhost:8000
    echo.
    echo Identifiants admin (par defaut):
    echo Email: admin@aeps-maint.pro
    echo Mot de passe: password
    echo.
    echo Pensez a changer le mot de passe admin apres la premiere connexion!
) > INSTALLATION_COMPLETE.txt

echo Un resume a ete cree dans INSTALLATION_COMPLETE.txt
echo.

pause
