<?php

namespace App\Http\Controllers;

use App\Models\CRM\Order;
use Illuminate\Http\Request;
use function Spatie\LaravelPdf\Support\pdf;

class PrintController extends Controller
{
    public function print(Order $order){
        $order->load(['client','car.brand.parent', 'works.work_name', 'products']);
        $sum_for_work = 0;
        $sum_for_prod = 0;

        foreach($order->works as $work)
        {
            $sum_for_work = $sum_for_work + ($work->price * $work->count);
        }

        foreach($order->products as $prod)
        {
            $sum_for_prod = $sum_for_prod + ($prod->price_for_client * $prod->count);
        }

        return view('print',[
            'data' => $order,
            'sum_for_work'=> number_format((float)$sum_for_work, 2, '.', ''),
            'sum_for_prod'=> number_format((float)$sum_for_prod, 2, '.', '')
        ]);
    }

    public function printinvoice(Order $order){
        $order->load(['client','car.brand.parent', 'works.work_name', 'products']);
        $sum_for_work = 0;
        $sum_for_prod = 0;

        foreach($order->works as $work)
        {
            $sum_for_work = $sum_for_work + ($work->price * $work->count);
        }

        foreach($order->products as $prod)
        {
            $sum_for_prod = $sum_for_prod + ($prod->price_for_client * $prod->count);
        }

        return view('print',[
            'data' => $order,
            'sum_for_work'=> number_format((float)$sum_for_work, 2, '.', ''),
            'sum_for_prod'=> number_format((float)$sum_for_prod, 2, '.', '')
        ]);
    }

}
