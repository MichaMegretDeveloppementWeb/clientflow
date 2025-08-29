<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\Clients\ClientCrudController;
use App\Http\Controllers\Clients\ClientDetailController;
use App\Http\Controllers\Clients\ClientListController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Events\EventCrudController;
use App\Http\Controllers\Events\EventDetailController;
use App\Http\Controllers\Events\EventListController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Projects\ProjectCrudController;
use App\Http\Controllers\Projects\ProjectDetailController;
use App\Http\Controllers\Projects\ProjectListController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    /*Tableau de bord*/

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/urgent-tasks', [DashboardController::class, 'urgentTasks'])->name('dashboard.urgent-tasks');
    Route::get('dashboard/recent-activities', [DashboardController::class, 'recentActivities'])->name('dashboard.recent-activities');
    Route::get('dashboard/revenue-chart', [DashboardController::class, 'revenueChart'])->name('dashboard.revenue-chart');
    Route::get('dashboard/statistics', [DashboardController::class, 'statistics'])->name('dashboard.statistics');
    Route::get('dashboard/billing', [DashboardController::class, 'billing'])->name('dashboard.billing');
    Route::get('dashboard/quick-stats', [DashboardController::class, 'quickStats'])->name('dashboard.quick-stats');
    Route::get('dashboard/help', [DashboardController::class, 'help'])->name('dashboard.help');



    /*Projets*/

    // Project list
    Route::get('projects', [ProjectListController::class, 'index'])->name('projects.index');

    // Project CRUD (create MUST be before {project} routes)  
    Route::get('projects/create', [ProjectCrudController::class, 'create'])->name('projects.create');
    Route::post('projects', [ProjectCrudController::class, 'store'])->name('projects.store');

    // Project detail and edit
    Route::get('projects/{project}', [ProjectDetailController::class, 'show'])->name('projects.show');
    Route::get('projects/{project}/edit', [ProjectCrudController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectCrudController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectCrudController::class, 'destroy'])->name('projects.destroy');

    // Commented out old resource route
    // Route::resource('projects', ProjectController::class);



    /*Clients*/

    // Client list
    Route::get('clients', [ClientListController::class, 'index'])->name('clients.index');

    // Client CRUD (create MUST be before {client} routes)
    Route::get('clients/create', [ClientCrudController::class, 'create'])->name('clients.create');
    Route::post('clients', [ClientCrudController::class, 'store'])->name('clients.store');

    // Client detail and edit
    Route::get('clients/{client}', [ClientDetailController::class, 'show'])->name('clients.show');
    Route::get('clients/{client}/edit', [ClientCrudController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientCrudController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [ClientCrudController::class, 'destroy'])->name('clients.destroy');

    // Commented out old resource route
    // Route::resource('clients', ClientController::class);





    /*Evenement*/

    Route::get('events', [EventListController::class, 'index'])->name('events.index');

    Route::get('events/create', [EventCrudController::class, 'create'])->name('events.create');
    Route::post('events', [EventCrudController::class, 'store'])->name('events.store');

    Route::get('events/{event}', [EventDetailController::class, 'show'])->name('events.show');
    Route::patch('events/{event}/update-status', [EventCrudController::class, 'updateStatus'])->name('events.updateStatus');
    Route::patch('events/{event}/update-payment-status', [EventCrudController::class, 'updatePaymentStatus'])->name('events.updatePaymentStatus');

    Route::get('events/{event}/edit', [EventCrudController::class, 'edit'])->name('events.edit');
    Route::put('events/{event}', [EventCrudController::class, 'update'])->name('events.update');



    Route::delete('events/{event}', [EventCrudController::class, 'destroy'])->name('events.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
