<?php

use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\EquipmentTypeController;
use Illuminate\Support\Facades\Route;


Route::apiResource('equipment', EquipmentController::class);
Route::get('equipment-type', [EquipmentTypeController::class, 'index']);
