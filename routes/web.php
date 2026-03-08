<?php

declare(strict_types=1);

use App\Services\StatisticService;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard.index')->name('home')->middleware('auth');
Route::livewire('/cars', 'pages::car.index')->name('cars')->middleware('auth');
Route::livewire('/maintenance', 'pages::maintenance.index')->name('maintenance')->middleware('auth');
Route::livewire('/fuelling', 'pages::fuelling.index')->name('fuelling')->middleware('auth');
Route::livewire('/auth/login', 'pages::auth.login')->name('login');
Route::livewire('/auth/register', 'pages::auth.register')->name('register');

Route::prefix('graph')->group(function () {
    Route::get('/maintenance/{cadrId}', function (int $carId) {
        $statisticService = new StatisticService;

        return $statisticService->maintenanceGraph($carId);
    })->name('graph.maintenance');

    Route::get('/fuelling/{carId}', function (int $carId) {
        $statisticService = new StatisticService;

        return $statisticService->fuellingGraph($carId);
    })->name('graph.fuelling');

    Route::get('/maintenance-total-last-12-months/{carId}', function (int $carId) {
        $statisticService = new StatisticService;

        return $statisticService->totalCostLast12MonthsGraph($carId);
    })->name('graph.maintenance.total-last-12-months');
});
