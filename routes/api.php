<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(
    [
        "prefix" => "products",
        // "middleware" => "auth:sanctum",
    ],
    function () {
        // Route::get("/", [ProductController::class, "getAll"]);
        Route::post("/", [ProductController::class, "create"]);
        Route::get("/", [ProductController::class, "getAll"]);
        Route::get("/{id}", [ProductController::class, "getById"]);
    }
);

Route::get("/", function () {
    return response()->json([
        "success" => 'API is working',
    ]);
});
