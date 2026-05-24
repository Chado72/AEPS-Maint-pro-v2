# Middleware de Sécurité

Ce dossier contient les middleware personnalisés pour sécuriser l'application AEPS-Maint Pro Ouahigouya.

## Middlewares disponibles

### CheckRole
Vérifie que l'utilisateur a un rôle spécifique (ex: admin, technicien).
Usage: `->middleware('role:admin')`

### CheckPermission  
Vérifie que l'utilisateur a une permission spécifique via son rôle.
Usage: `->middleware('permission:manage_stock')`

### EnsureUserIsActive
Vérifie que le compte utilisateur est actif. Si désactivé, déconnecte l'utilisateur.

### OwnsAiSession
Vérifie que l'utilisateur possède la session IA ou est administrateur.

## Enregistrement des middlewares

Dans `bootstrap/app.php` ou `app/Http/Kernel.php`, enregistrer:

```php
'role' => \App\Http\Middleware\CheckRole::class,
'permission' => \App\Http\Middleware\CheckPermission::class,
'active' => \App\Http\Middleware\EnsureUserIsActive::class,
'owns.ai.session' => \App\Http\Middleware\OwnsAiSession::class,
```
