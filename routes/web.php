<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard.index')->name('home')->middleware('auth');
Route::livewire('/cars', 'pages::car.index')->name('cars')->middleware('auth');
Route::livewire('/maintenance', 'pages::maintenance.index')->name('maintenance')->middleware('auth');
Route::livewire('/fuelling', 'pages::fuelling.index')->name('fuelling')->middleware('auth');
Route::livewire('/auth/login', 'pages::auth.login')->name('login');
Route::livewire('/auth/register', 'pages::auth.register')->name('register');


