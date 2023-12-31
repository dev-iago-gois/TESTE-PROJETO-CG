<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
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
    ],
    function () {
        Route::post("/", [ProductController::class, "create"]);
        Route::get("/", [ProductController::class, "getAll"]);
        Route::get("/{id}", [ProductController::class, "getById"]);
        Route::patch("/{id}", [ProductController::class, "update"]);
        Route::delete("/{id}", [ProductController::class, "delete"]);
    }
);

Route::group(
    [
        "prefix" => "sales",
    ],
    function () {
        Route::post("/", [SaleController::class, "create"]);
        Route::patch("/{id}/cancel", [SaleController::class, "cancel"]);
        Route::get("/history", [SaleController::class, "getAll"]);
        Route::patch("/{id}", [SaleController::class, "update"]);
    }
);

Route::get("/", function () {
    return response()->json([
        "success" => 'API is working',
    ]);
});
