<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;


Route::apiResource('roles', RolController::class);
Route::apiResource('usuarios', UsuarioController::class);
