<?php

use App\Http\Controllers\Api\V2\TicketController;
use Illuminate\Support\Facades\Route;

Route::apiResource("tickets", TicketController::class);

// Route::get("/user", function (Request $request) {
//     return $request->user();
// })->middleware("auth:sanctum");
