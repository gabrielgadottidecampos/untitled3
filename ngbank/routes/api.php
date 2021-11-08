<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('equipe','App\Http\Controllers\EquipeController');
Route::apiResource('funcionario','App\Http\Controllers\FuncionarioController');
Route::apiResource('venda','App\Http\Controllers\VendaController');


// login ------------------
Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('logout', 'App\Http\Controllers\AuthController@logout');
Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
Route::post('me', 'App\Http\Controllers\AuthController@me');
