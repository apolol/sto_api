<?php

use App\Http\Controllers\Api\OrdersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

Route::get('orders/print_order/{order}', [OrdersController::class, 'print'])->name('print_order');
Route::get('orders/print_order_pdv/{order}', [OrdersController::class, 'printPdv'])->name('print_order_pdv');
