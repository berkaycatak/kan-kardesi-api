<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Blood\BloodController;
use App\Http\Middleware\StorePlayerId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// AUTH
Route::middleware([StorePlayerId::class])->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post("/login", [AuthController::class, "login"])->name("login");
        Route::post("/register", [AuthController::class, "register"])->name("register");

        Route::post("/splash", [AuthController::class, "loginCheck"])->name("splash");
    });

    Route::group(["middleware" => ["auth:sanctum"]], function() {
        Route::prefix('/blood')->group(function () {
            Route::post("/request", [BloodController::class, "sendRequest"])->name("send_blood_request");
        });
    });
});
