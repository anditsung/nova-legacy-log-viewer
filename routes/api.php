<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Anditsung\NovaLegacyLogViewer\Http\Controllers\LogViewerController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('/', LogViewerController::class);

Route::get('/log', [LogViewerController::class, 'fetch']);