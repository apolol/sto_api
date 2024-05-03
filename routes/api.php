<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientsController;
use App\Http\Controllers\Api\OrdersController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ResultsController;
use App\Http\Controllers\Api\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', [UserController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::group(['prefix' => 'clients'], function(){
        Route::get('/',[ClientsController::class,'getClients']);
        Route::get('/get_header_data',[ClientsController::class,'getHeaderInfo']);
        Route::get('/{id}',[ClientsController::class,'getClient']);
        Route::get('/get_client_car/{id}',[ClientsController::class,'getClientCars']);
        Route::get('/get_client_orders/{id}',[ClientsController::class,'getClientOrders']);

        Route::put('/update_client_car/{client_car}', [ClientsController::class, 'updateClientCar']);
        Route::put('/{client}', [ClientsController::class, 'updateClient']);
        Route::post('/store_regular_client', [ClientsController::class, 'storeRegularClient']);
        Route::post('/store_company_client', [ClientsController::class, 'storeCompanyClient']);
        Route::post('/store_new_car', [ClientsController::class, 'storeNewCar']);
    });
    Route::group(['prefix' => 'orders'], function(){
        Route::get('/',[OrdersController::class,'getOrders']);
        Route::get('/get_header_data',[OrdersController::class,'getHeaderInfo']);
        Route::get('/view/{order}',[OrdersController::class,'getOrder']);
        Route::get('/clients',[OrdersController::class,'getClients']);
        Route::post('/', [OrdersController::class, 'storeOrder']);
        Route::get('/get_works', [OrdersController::class, 'getWorks']);
        Route::get('/get_workers', [OrdersController::class, 'getWorkers']);
        Route::post('/store_work', [OrdersController::class, 'storeWork']);
        Route::put('/update_work/{orderWork}', [OrdersController::class, 'updateWork']);
        Route::post('/store_product', [OrdersController::class, 'storeProduct']);
        Route::put('/update_product/{orderProduct}', [OrdersController::class, 'updateProduct']);
        Route::put('/update_order/{order}', [OrdersController::class, 'updateOrder'])->middleware('role:admin');
        Route::put('/update_order_status/{order}', [OrdersController::class, 'updateOrderStatus'])->middleware('role:admin');
        Route::put('/send_sms/{order}', [OrdersController::class, 'sendSmsDone']);
        Route::put('/make_check/{order}', [OrdersController::class, 'makeCheck']);
        Route::delete('/delete/{order}', [OrdersController::class, 'deleteOrder']);
    });

    Route::group(['prefix' => 'results'], function(){
        Route::get('/',[ResultsController::class,'index']);
    });
    Route::get('/get_brands', [ServiceController::class, 'getBrands']);
});
