<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmploymentController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\ShowController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\Admin\UserRoleController;

Route::prefix('admin')->name('admin.')->middleware(['auth','role:Admin'])->group(function () {
    Route::get('user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
    Route::get('user-roles/{user}/edit', [UserRoleController::class, 'edit'])->name('user-roles.edit');
    Route::post('user-roles/{user}', [UserRoleController::class, 'update'])->name('user-roles.update');
    
    // Permissions management
    Route::get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/create', [\App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('permissions.store');
    Route::delete('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('permissions.destroy');
    
    // Role permission management
    Route::get('roles', [\App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('roles.index');
    Route::get('roles/create', [\App\Http\Controllers\Admin\RolePermissionController::class, 'create'])->name('roles.create');
    Route::post('roles', [\App\Http\Controllers\Admin\RolePermissionController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::post('roles/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'destroy'])->name('roles.destroy');
});

// Admin simple management pages for event types and departments
Route::prefix('admin')->name('admin.')->middleware(['auth','role:Admin'])->group(function () {
    Route::get('event-types', [\App\Http\Controllers\Admin\EventTypeController::class, 'index'])->name('event_types.index');
    Route::get('event-types/{eventType}/edit', [\App\Http\Controllers\Admin\EventTypeController::class, 'edit'])->name('event_types.edit');
    Route::post('event-types', [\App\Http\Controllers\Admin\EventTypeController::class, 'store'])->name('event_types.store');
    Route::put('event-types/{eventType}', [\App\Http\Controllers\Admin\EventTypeController::class, 'update'])->name('event_types.update');
    Route::delete('event-types/{eventType}', [\App\Http\Controllers\Admin\EventTypeController::class, 'destroy'])->name('event_types.destroy');

    Route::get('departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'index'])->name('departments.index');
    Route::get('departments/{department}/edit', [\App\Http\Controllers\Admin\DepartmentController::class, 'edit'])->name('departments.edit');
    Route::post('departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'store'])->name('departments.store');
    Route::put('departments/{department}', [\App\Http\Controllers\Admin\DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('departments/{department}', [\App\Http\Controllers\Admin\DepartmentController::class, 'destroy'])->name('departments.destroy');
    // Positions within a department
    Route::post('departments/{department}/positions', [\App\Http\Controllers\Admin\PositionController::class, 'store'])->name('departments.positions.store');
    Route::get('positions/{position}/edit', [\App\Http\Controllers\Admin\PositionController::class, 'edit'])->name('positions.edit');
    Route::put('positions/{position}', [\App\Http\Controllers\Admin\PositionController::class, 'update'])->name('positions.update');
    Route::delete('positions/{position}', [\App\Http\Controllers\Admin\PositionController::class, 'destroy'])->name('positions.destroy');
});

// Contacts CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('contacts', ContactController::class);
});

// Companies CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('companies', CompanyController::class);
});

// Seasons CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('seasons', \App\Http\Controllers\SeasonController::class);
});

// Employment records
Route::middleware(['auth'])->group(function () {
    Route::post('employments', [EmploymentController::class, 'store'])->name('employments.store');
    Route::put('employments/{employment}', [EmploymentController::class, 'update'])->name('employments.update');
    Route::delete('employments/{employment}', [EmploymentController::class, 'destroy'])->name('employments.destroy');
    Route::get('employables/search', [EmploymentController::class, 'searchEmployables'])->name('employables.search');

    // Addresses CRUD (polymorphic)
    Route::post('addresses', [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put('addresses/{address}', [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('addresses/{address}', [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
});

// Venues/Buildings/Spaces CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('venues', VenueController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('spaces', SpaceController::class);
    Route::resource('shows', ShowController::class);
    Route::resource('show_catalogues', \App\Http\Controllers\ShowCatalogueController::class);
    Route::resource('productions', \App\Http\Controllers\ProductionController::class);
    Route::resource('productions.events', \App\Http\Controllers\EventController::class);
    Route::resource('events', \App\Http\Controllers\EventController::class)->only(['show','update','destroy']);
    Route::post('productions/{production}/companies', [\App\Http\Controllers\ProductionController::class, 'attachCompany'])->name('productions.companies.attach');
    Route::delete('productions/{production}/companies/{company}', [\App\Http\Controllers\ProductionController::class, 'detachCompany'])->name('productions.companies.detach');
    Route::post('productions/{production}/contacts', [\App\Http\Controllers\ProductionController::class, 'attachContact'])->name('productions.contacts.attach');
    Route::delete('productions/{production}/contacts/{contact}', [\App\Http\Controllers\ProductionController::class, 'detachContact'])->name('productions.contacts.detach');
    Route::put('productions/{production}/contacts/{contact}/pivot', [\App\Http\Controllers\ProductionController::class, 'updateContactPivot'])->name('productions.contacts.update-pivot');
    Route::get('locations', [LocationsController::class, 'index'])->name('locations.index');
    
    // Contact email addresses
    Route::resource('emails', \App\Http\Controllers\EmailAddressController::class);
    // Contact phone numbers
    Route::resource('phones', \App\Http\Controllers\PhoneNumberController::class);
});
