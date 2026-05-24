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

/*
|--------------------------------------------------------------------------
| Web Routes - AEPS-Maint Pro Ouahigouya
|--------------------------------------------------------------------------
*/

// Page de connexion gérée par Laravel UI ou Breeze (à configurer)
// Route::get('/login', function () { return view('auth.login'); })->name('login');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Communes
    Route::resource('communes', CommuneController::class);
    
    // Villages
    Route::resource('villages', VillageController::class);
    
    // Sites
    Route::resource('sites', SiteController::class);
    Route::get('sites/{site}/export-pdf', [SiteController::class, 'exportPdf'])->name('sites.export.pdf');
    
    // Forages
    Route::resource('forages', ForageController::class);
    
    // Interventions
    Route::resource('interventions', InterventionController::class);
    Route::get('interventions/{intervention}/export-pdf', [InterventionController::class, 'exportPdf'])->name('interventions.export.pdf');
    
    // Pièces détachées
    Route::resource('spare-parts', SparePartController::class);
    
    // Rapports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/site/{siteId}', [ReportController::class, 'sitePdf'])->name('reports.site.pdf');
    Route::get('reports/forage/{forageId}', [ReportController::class, 'foragePdf'])->name('reports.forage.pdf');
    Route::get('reports/intervention/{interventionId}', [ReportController::class, 'interventionPdf'])->name('reports.intervention.pdf');
    Route::post('reports/monthly', [ReportController::class, 'monthlyPdf'])->name('reports.monthly.pdf');
    Route::get('reports/commune/{communeId}', [ReportController::class, 'communePdf'])->name('reports.commune.pdf');
    Route::get('reports/global', [ReportController::class, 'globalPdf'])->name('reports.global.pdf');
    Route::post('reports/interventions-period', [ReportController::class, 'interventionsPeriod'])->name('reports.interventions.period.pdf');
    
    // Assistant IA
    Route::get('ai-chat', [AiChatController::class, 'index'])->name('ai.chat.index');
    Route::post('ai-chat', [AiChatController::class, 'chat'])->name('ai.chat.send');
    Route::get('ai-chat/session/{session}', [AiChatController::class, 'show'])->name('ai.chat.show');
    Route::delete('ai-chat/session/{session}', [AiChatController::class, 'destroy'])->name('ai.chat.destroy');
    Route::post('ai-chat/clear-history', [AiChatController::class, 'clearHistory'])->name('ai.chat.clear');
    
    // Paramètres
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});

// Routes d'authentification (si Laravel UI/Breeze n'est pas utilisé)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');
