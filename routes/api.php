<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BoxController;
use App\Http\Controllers\Api\ScanController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/ping', function () {
    return response()->json(['ok' => true]);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    //API FITUR 1
    // ScanFragment: list box aktif (stat = 0)
    Route::get('/boxes/active', [BoxController::class, 'active']);

    // validate barcode vs box
    Route::post('/scan/validate', [ScanController::class, 'validateScan']);

    // simpan ke itemscan_mst
    Route::post('/scan/save', [ScanController::class, 'saveScan']);

    //API FITUR 2 narik data itemscan_mst
    

    //API FITUR 3

    //DAN SETERUSNYA
});

