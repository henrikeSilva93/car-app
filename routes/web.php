<?php

use Illuminate\Support\Facades\Route;
use App\Services\PrismService;
use App\Services\StatisticService;

Route::livewire('/', 'pages::dashboard.index')->name('home')->middleware('auth');
Route::livewire('/cars', 'pages::car.index')->name('cars')->middleware('auth');
Route::livewire('/maintenance', 'pages::maintenance.index')->name('maintenance')->middleware('auth');
Route::livewire('/fuelling', 'pages::fuelling.index')->name('fuelling')->middleware('auth');
Route::livewire('/auth/login', 'pages::auth.login')->name('login');
Route::livewire('/auth/register', 'pages::auth.register')->name('register');

Route::prefix('graph')->group(function () {
    Route::get('/maintenance/{car_id}', function(){
        $statisticService = new \App\Services\StatisticService();
        return $statisticService->MaitenanceGraph(request()->car_id);
    })->name('graph.maintenance');
});


