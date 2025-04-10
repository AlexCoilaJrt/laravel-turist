<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['error'=>'Inicie Sesion'],Response::HTTP_UNAUTHORIZED);
})->name('login');
