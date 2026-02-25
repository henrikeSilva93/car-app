<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard.index')->name('home');
Route::livewire('/cars', 'pages::car.index')->name('cars');
Route::livewire('/maintenance', 'pages::maintenance.index')->name('maintenance');
Route::livewire('/fuelling', 'pages::fuelling.index')->name('fuelling');

