<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ForageController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\OwnsAiSession;

/*
|--------------------------------------------------------------------------
| Web Routes - AEPS-Maint Pro Ouahigouya
|--------------------------------------------------------------------------
*/

// Page de connexion
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Déconnexion
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Routes protégées par authentification
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Communes - Requiert permission view_all ou admin
    Route::resource('communes', CommuneController::class);
    
    // Villages - Requiert permission view_all ou admin
    Route::resource('villages', VillageController::class);
    
    // Sites - Requiert permission view_all ou admin
    Route::resource('sites', SiteController::class);
    Route::get('sites/{site}/export-pdf', [SiteController::class, 'exportPdf'])->name('sites.export.pdf');
    
    // Forages - Requiert permission view_all ou admin
    Route::resource('forages', ForageController::class);
    
    // Interventions - Gestion des permissions dans le controller
    Route::resource('interventions', InterventionController::class);
    Route::get('interventions/{intervention}/export-pdf', [InterventionController::class, 'exportPdf'])->name('interventions.export.pdf');
    
    // Pièces détachées - Requiert permission manage_stock pour modification
    Route::resource('spare-parts', SparePartController::class);
    
    // Rapports - Requiert permission generate_reports ou admin
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/site/{siteId}', [ReportController::class, 'sitePdf'])->name('reports.site.pdf');
    Route::get('reports/forage/{forageId}', [ReportController::class, 'foragePdf'])->name('reports.forage.pdf');
    Route::get('reports/intervention/{interventionId}', [ReportController::class, 'interventionPdf'])->name('reports.intervention.pdf');
    Route::post('reports/monthly', [ReportController::class, 'monthlyPdf'])->name('reports.monthly.pdf');
    Route::get('reports/commune/{communeId}', [ReportController::class, 'communePdf'])->name('reports.commune.pdf');
    Route::get('reports/global', [ReportController::class, 'globalPdf'])->name('reports.global.pdf');
    Route::post('reports/interventions-period', [ReportController::class, 'interventionsPeriod'])->name('reports.interventions.period.pdf');
    
    // Assistant IA - Avec middleware de propriété et rate limiting
    Route::middleware([OwnsAiSession::class])->group(function () {
        Route::get('ai-chat', [AiChatController::class, 'index'])->name('ai.chat.index');
        Route::post('ai-chat', [AiChatController::class, 'chat'])->name('ai.chat.send');
        Route::get('ai-chat/session/{session}', [AiChatController::class, 'show'])->name('ai.chat.show');
        Route::delete('ai-chat/session/{session}', [AiChatController::class, 'destroy'])->name('ai.chat.destroy');
        Route::post('ai-chat/clear-history', [AiChatController::class, 'clearHistory'])->name('ai.chat.clear');
    });
    
    // Paramètres - Admin seulement
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
