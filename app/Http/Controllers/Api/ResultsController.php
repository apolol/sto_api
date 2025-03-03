<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CRM\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResultsController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $range = json_decode($request->get('selected'));
        $start = Carbon::parse($range->start)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $end = Carbon::parse($range->end)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $result = Order::with('client','car.brand.parent','works.worker','works','works.work_name','products')
            ->where('status','Завершено')
            ->whereBetween('finish_work', [$start, $end])
            ->get();
        $byWork = collect();
        $byParts = collect();
        $byCar = collect();
        $sum_for_work = 0;
        $sum_for_parts = 0;
        $sum_for_client_parts = 0;
        foreach ($result as $order){
            $temp_price_work = 0;
            $temp_price_prod = 0;
            $temp_price_pclient = 0;
            foreach ($order->works as $work){
                try {
                    $temp_price_work += ($work->price != null || $work->price != 0) ? $work->price * $work->count : 0;
                    if( $order->discount_works != 0){
                        $temp_price_work = $temp_price_work - ($temp_price_work * $order->discount_works / 100);
                    }
                    $sum_for_work += $temp_price_work;
                    $byWork->push([
                        'number' => $order->number,
                        'name' => $work->work_name->title,
                        'price' => $work->price,
                        'count' => $work->count,
                        'full_price' => ($work->price != null || $work->price != 0) ? $work->price * $work->count : 0,
                        'worker' => $work->worker->name,
                        'date' => Carbon::parse($order->finish_work)->format('Y-m-d'),
                    ]);
                }catch (\Exception $exception){

                }
            }
            foreach ($order->products as $product){
                try {
                    $temp_price_prod += ($product->real_price != null || $product->real_price != 0) ? $product->real_price * $product->count : 0;
                    $temp_price_pclient += ($product->price_for_client != null || $product->price_for_client != 0) ? $product->price_for_client * $product->count : 0;
                    if( $order->discount_products != 0){
                        $temp_price_pclient = $temp_price_pclient - ($temp_price_pclient * $order->discount_products / 100);
                        $temp_price_prod = $temp_price_prod - ($temp_price_pclient * $order->discount_products / 100);
                    }
                    $sum_for_client_parts += $temp_price_pclient;
                    $sum_for_parts += $temp_price_prod;
                    $byParts->push([
                        'number' => $order->number,
                        'name' => $product->title,
                        'price_real' => $product->real_price,
                        'count' => $product->count,
                        'price_client' => $product->price_for_client,
                        'full_price_real' => number_format( ($product->real_price != null || $product->real_price != 0) ? $product->real_price * $product->count : 0, 2, '.', ''),
                        'full_price_client' => number_format( ($product->price_for_client != null || $product->price_for_client != 0) ? $product->price_for_client * $product->count : 0, 2, '.', ''),
                        'where_get' => $product->where_get,
                    ]);
                }catch (\Exception $exception){

                }
            }
            try {
                $byCar->push([
                    'number' => $order->number,
                    'name' => $order->car->brand->full_name,
                    'work_price' => $temp_price_work,
                    'price' => $temp_price_prod,
                    'price_client' => number_format( $temp_price_pclient, 2, '.', '') ,
                    'car_plate' => $order->car->car_plate,
                    'pay_status' => $order->pay_status,
                ]);
            }catch (\Exception $exception){
                dump($exception->getMessage());
            }

        }

        return \response()->json([
            'byWork' => $byWork,
            'byParts' => $byParts,
            'byCar' => $byCar,
            'sum_for_work' => number_format( $sum_for_work, 2, '.', ''),
            'parts_price' => number_format($sum_for_parts, 2, '.', '') ,
            'client_parts_price' => number_format($sum_for_client_parts, 2, '.', ''),
            'diff' => number_format($sum_for_client_parts - $sum_for_parts, 2, '.', '')
        ]);
    }
}
