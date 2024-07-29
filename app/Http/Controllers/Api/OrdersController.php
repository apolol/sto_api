<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderProductRequest;
use App\Http\Requests\StoreOrderWorkRequest;
use App\Http\Requests\UpdateOrderWorkRequest;
use App\Http\Requests\UpdateOrderProductRequest;
use App\Models\CRM\Client;
use App\Models\CRM\ClientCar;
use App\Models\CRM\Order;
use App\Models\CRM\OrderProduct;
use App\Models\CRM\OrderWork;
use App\Models\CRM\Worker;
use App\Models\CRM\WorkType;
use App\Services\VchasnoCasa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Mockery\Exception;


class OrdersController extends Controller
{
    public function getOrders(Request $request): JsonResponse|Response
    {
       // $orders = Order::filter(['search' => $request->get('search')])->with(['client', 'car.brand.parent', 'works', 'products'])->orderBy('created_at', 'desc')->paginate(20);
        $orders = Order::filter(['search' => $request->get('search'), 'pdv' => $request->get('pdv')])
            ->with(['client', 'car.brand.parent', 'works', 'products'])
            ->withSum('products as products_sum', \DB::raw('price_for_client * count'))
            ->withSum('works as works_sum', \DB::raw('price * count'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return \response()->json($orders);
    }

    public function getHeaderInfo(): JsonResponse|Response
    {
        $clients = Order::count();
        $clients_previous_month = Order::where('created_at', '>=', now()->subMonth())->count();
        $clients_this_month = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return response()->json([
            'clients' => $clients,
            'clients_previous_month' => $clients_previous_month,
            'clients_this_month' => $clients_this_month,
            'percentage_difference' => round( $this->percentageDifference($clients_previous_month, $clients_this_month), 2),
        ]);
    }

    /**
     * Calculate the percentage difference between two numbers.
     *
     *
     * @param float $number1 The first number
     * @param float $number2 The second number
     * @return float The percentage difference
     */
    private function percentageDifference($number1, $number2)
    {
        $difference = $number2 - $number1;
        if ($number1 == 0 || $number2 == 0) {
            return 0;
        }
        $percentageDifference = ($difference / $number1) * 100;
        return $percentageDifference;
    }

    public function getOrder(Order $order): JsonResponse|Response
    {
        $order = $order->load(['client','car.brand.parent', 'works.worker','works.work_name', 'products']);
        $repair_sum = 0;
        $parts_sum = 0;
        $parts_sum_client = 0;

        foreach ($order->works as $work) {
            $repair_sum += $work->price * $work->count;
        }

        foreach ($order->products as $product) {
            if ($product->count > 0 && $product->real_price > 0) {
                $parts_sum += $product->real_price * $product->count;
                $parts_sum_client += $product->price_for_client * $product->count;
            }

        }

        $order->repair_sum = $repair_sum;
        $order->parts_sum = $parts_sum;

        return \response()->json([
            'order' => $order,
            'repair_sum' => $repair_sum,
            'parts_sum' => $parts_sum,
            'parts_sum_client' => $parts_sum_client,
            'full_for_client' => $repair_sum + $parts_sum_client,
            'full' => $repair_sum + $parts_sum
        ]);
    }

    public function getClients(Request $request): JsonResponse|Response
    {
        $clients = Client::filter(['search' => $request->get('search')])->orderBy('created_at', 'desc')->limit(5)->get();
        return \response()->json($clients);
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'car_id' => 'required',
            'type' => 'required',
        ],[
            'client_id.required' => 'Клієнт невибрано',
            'car_id.required' => 'Автомобіль невибрано',
        ]);

        $car = ClientCar::where('id', $request->car_id)->first();
        $odometer = ($car != null ? $car->odometer : 0);

        $order = Order::create([
            'number' => strtoupper(\Str::random(2) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . "/" . date('d')),
            'client_id' => $request->client_id,
            'client_car_id' => $request->car_id,
            'odometer' => $odometer,
            'type' => $request->type,
            'start_work' => Carbon::now()
        ]);

        if($car != null){
            $car->odometer = $odometer;
            $car->update();
        }
        return \response()->json($order);
    }

    public function getWorks(): JsonResponse|Response
    {
        return  \response()->json(WorkType::all());
    }
    public function getWorkers(): JsonResponse|Response
    {
        return  \response()->json(Worker::all());
    }

    public function storeWork(StoreOrderWorkRequest $request): JsonResponse|Response
    {
        $new_id = null;
        if ($request->get('new_work_name') != null) {
            $new_id = WorkType::create(['title' => $request->get('new_work_name'), 'price' => $request->get('price')]);
        }

        $orderWork = new OrderWork();
        $orderWork->order_id = $request->get('order_id');
        $orderWork->worker_id = $request->get('worker_id');
        $orderWork->work_id = $new_id != null ? $new_id->id : $request->get('work_id');
        $orderWork->count = $request->get('count');
        $orderWork->price = $request->get('price');
        $orderWork->save();

        return \response()->json($orderWork);
    }

    public function updateWork(OrderWork $orderWork, UpdateOrderWorkRequest $request): JsonResponse|Response
    {
        $new_id = null;
        if ($request->get('new_work_name') != null) {
            $new_id = WorkType::create(['title' => $request->get('new_work_name'), 'price' => $request->get('price')]);
        }

        $orderWork->worker_id = $request->get('worker_id');
        $orderWork->work_id = $new_id != null ? $new_id->id : $request->get('work_id');
        $orderWork->count = $request->get('count');
        $orderWork->price = $request->get('price');
        $orderWork->save();

        return \response()->json($orderWork);
    }

    public function storeProduct(StoreOrderProductRequest $request): JsonResponse|Response
    {
        $product = OrderProduct::updateOrCreate([
            'id'=>$request->get('id')
        ],
            [
                'order_id'=>$request->get('order_id'),
                'title'=>$request->get('title'),
                'where_get'=>$request->get('where_get'),
                'price_for_client'=>ceil($request->get('price_for_client'),),
                'real_price'=>$request->get('real_price'),
                'count'=>$request->get('count'),
                'discount'=>$request->get('discount'),
                'articul'=>$request->get('articul'),
                'brand'=>$request->get('brand'),
                'uktz'=>$request->get('uktz')
            ]
        );

        return \response()->json($product);
    }

    public function updateProduct(OrderProduct $orderProduct, UpdateOrderProductRequest $request): JsonResponse|Response
    {
        $product = OrderProduct::updateOrCreate([
            'id'=>$request->get('id')
        ],
            [
                'order_id'=>$request->get('order_id'),
                'title'=>$request->get('title'),
                'where_get'=>$request->get('where_get'),
                'price_for_client'=>ceil($request->get('price_for_client'),),
                'real_price'=>$request->get('real_price'),
                'count'=>$request->get('count'),
                'discount'=>$request->get('discount'),
                'articul'=>$request->get('articul'),
                'brand'=>$request->get('brand'),
                'uktz'=>$request->get('uktz')
            ]
        );

        return \response()->json($product);
    }

    public function updateOrderStatus(Order $order, Request $request): JsonResponse|Response
    {
        $order->status = ($request->status == 'Взято в роботу' ? 'Завершено' : 'Взято в роботу');
        $order->finish_work = ($request->status == 'Взято в роботу' ? Carbon::now() : null) ;
        $order->update();

        return \response()->json([],200);
    }

    public function updateOrder(Order $order, Request $request): JsonResponse|Response
    {
        $order->pay_status =  $request->pay_status;
        $order->odometer =  $request->odometer;
        $order->update();

        return \response()->json([],200);
    }

    public function sendSmsDone(Order $order): JsonResponse|Response
    {
        $order = $order->load(['client']);
        \TurboSMS::sendMessages('38'.$order->client->phone, 'Ваш автомобіль готовий.');
        return \response()->json([],200);
    }

    public function deleteOrder(Order $order)
    {
        $order->load(['products','works']);
        foreach($order->products as $pr){
            $pr->delete();
        }

        foreach($order->works as $wr){
            $wr->delete();
        }
        $order->delete();
        return \response()->json([],200);
    }

    public function makeCheck(Order $order): JsonResponse|Response
    {
        $order->load(['products','works']);
        $rows = collect();
        $pays = collect();
        if($order->check != null){
            return \response()->json(['message'=>'Помилка, дані вже відправлені в ДФС', 'code' => 1]);
        }
        $sum = 0;
        foreach ($order->works as $work){
            $rows->push([
                'code' => 'ID: '.$work->work_name->id,
                'name' => $work->work_name->title,
                'cnt' => (int) $work->count,
                'price' => ceil($work->price),
                'disc' => 0,
                'taxgrp' => '2'
            ]);
            $sum = $sum + ($work->price * $work->count);
        }
        foreach ($order->products as $product){
            $rows->push([
                'code' => 'ID: '.$product->id,
                'name' => $product->title,
                'cnt' => (int) $product->count,
                'price' => (int) ceil($product->price_for_client),
                'disc' => 0,
                'taxgrp' => '2'
            ]);
            $sum = $sum + (ceil($product->price_for_client) * $product->count);
        }

        if($order->pay_status == 'В касу'){
            $pays->push([
                'type' => 0,
                'sum' => ceil($sum),
                'change' => 0,
            ]);
        }else{
            $pays->push([
                'type' => 1,
                'sum' => ceil($sum),
                'change' => 0,
            ]);
        }
        $sum = ceil($sum);
        $service = new VchasnoCasa();
        $temp = $service->sendPostRequestWithAuthorization('+38'.$order->client->phone,$rows,$pays,$sum);
        if(isset($temp->info->doccode)){
            $order->update(['check'=>$temp->info->doccode]);
            return \response()->json(['message'=>'Чек сформований. Номер чеку: '.$temp->info->doccode, 'code' => 0 ]);
        }else{
            \Log::error(json_decode(json_encode($temp), true));
            return \response()->json(['message'=>'Помилка, адмін проінформований', 'code' => 1]);
        }
    }

    public function print(Order $order)
    {
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

    public function printPdv(Order $order){
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

        $sum_pdv_work = number_format($this->calculatePercentage($sum_for_work, 20), 2, '.', '');
        $sum_pdv_parts =  number_format($this->calculatePercentage($sum_for_prod, 20), 2, '.', '');
        $sum_work_with_pdv = number_format((float)$sum_for_work + (float)$sum_pdv_work, 2, '.', '');
        $sum_parts_with_pdv= number_format((float)$sum_for_prod + (float)$sum_pdv_parts, 2, '.', '');
        $sum_without_pdv = $sum_for_work + $sum_for_prod;
        $sum_wit_pdv = (float)$sum_work_with_pdv + (float)$sum_parts_with_pdv;
        $sum_pdv = (float)$sum_pdv_work + (float)$sum_pdv_parts;
        $parts = explode(".",number_format($sum_wit_pdv, 2, '.', ''));
        $decimal = $parts[1];

        $text_pay = 'Введіть дані в ручну';
        try{
            $text_pay = $this->number2string($sum_wit_pdv).', '.$decimal.' коп.';
        }catch(\Exception $e){

        }

        return view('print_pdv',[
            'data' => $order,
            'sum_for_work'=> number_format((float)$sum_for_work, 2, '.', ''),
            'sum_for_prod'=> number_format((float)$sum_for_prod, 2, '.', ''),
            'sum_pdv_work'=> $sum_pdv_work,
            'sum_pdv_parts'=> $sum_pdv_parts,
            'sum_work_with_pdv'=> $sum_work_with_pdv,
            'sum_parts_with_pdv'=>  $sum_parts_with_pdv,
            'sum_without_pdv'=>number_format((float)$sum_without_pdv, 2, '.', ''),
            'sum_wit_pdv'=>number_format($sum_wit_pdv, 2, '.', ''),
            'sum_pdv'=>number_format($sum_pdv, 2, '.', ''),
            'text_pay'=>$text_pay,
        ]);
    }

    public function printPdvInvoice(Order $order){
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

        $sum_pdv_work = number_format($this->calculatePercentage($sum_for_work, 20), 2, '.', '');
        $sum_pdv_parts =  number_format($this->calculatePercentage($sum_for_prod, 20), 2, '.', '');
        $sum_work_with_pdv = number_format((float)$sum_for_work + (float)$sum_pdv_work, 2, '.', '');
        $sum_parts_with_pdv= number_format((float)$sum_for_prod + (float)$sum_pdv_parts, 2, '.', '');
        $sum_without_pdv = $sum_for_work + $sum_for_prod;
        $sum_wit_pdv = (float)$sum_work_with_pdv + (float)$sum_parts_with_pdv;
        $sum_pdv = (float)$sum_pdv_work + (float)$sum_pdv_parts;
        $parts = explode(".",number_format($sum_wit_pdv, 2, '.', ''));
        $decimal = $parts[1];

        $day_to_pay = Carbon::now()->addDay(5);
        $text_pay = 'Введіть дані в ручну';
        try{
            $text_pay = $this->number2string($sum_wit_pdv).', '.$decimal.' коп.';
        }catch(\Exception $e){

        }
        return view('print_pdv_rah',[
            'data' => $order,
            'sum_for_work'=> number_format((float)$sum_for_work, 2, '.', ''),
            'sum_for_prod'=> number_format((float)$sum_for_prod, 2, '.', ''),
            'sum_pdv_work'=> $sum_pdv_work,
            'sum_pdv_parts'=> $sum_pdv_parts,
            'sum_work_with_pdv'=> $sum_work_with_pdv,
            'sum_parts_with_pdv'=>  $sum_parts_with_pdv,
            'sum_without_pdv'=>number_format((float)$sum_without_pdv, 2, '.', ''),
            'sum_wit_pdv'=>number_format($sum_wit_pdv, 2, '.', ''),
            'sum_pdv'=>number_format($sum_pdv, 2, '.', ''),
            'text_pay'=>$text_pay,
            'day_to_pay'=>$day_to_pay->format('d.m.Y')
        ]);
    }

    function calculatePercentage($amount, $percentage) {
        return $amount * ($percentage / 100);
    }

    function number2string($number) {
        // Словник чисел
        static $dic = [
            [-2 => 'два', -1 => 'одна', 1 => 'один', 2 => 'два', 3 => 'три', 4 => 'чотири', 5 => 'п\'ять', 6 => 'шість', 7 => 'сім', 8 => 'вісім', 9 => 'дев\'ять', 10 => 'десять', 11 => 'одинадцять', 12 => 'дванадцять', 13 => 'тринадцять', 14 => 'чотирнадцять', 15 => 'п\'ятнадцять', 16 => 'шістнадцять', 17 => 'сімнадцять', 18 => 'вісімнадцять', 19 => 'дев\'ятнадцять', 20 => 'двадцять', 30 => 'тридцять', 40 => 'сорок', 50 => 'п\'ятдесят', 60 => 'шістдесят', 70 => 'сімдесят', 80 => 'вісімдесят', 90 => 'дев\'яносто', 100 => 'сто', 200 => 'двісті', 300 => 'триста', 400 => 'чотириста', 500 => 'п\'ятсот', 600 => 'шістсот', 700 => 'сімсот', 800 => 'вісімсот', 900 => 'дев\'ятсот'],
            [['грн.', 'грн.', 'грн.'], ['тисяча', 'тисячі', 'тисяч']],
            [2, 0, 1, 1, 1, 2]
        ];

        // Результат
        $result = [];

        // Доповнюємо число нулями
        $number = str_pad($number, ceil(strlen($number) / 3) * 3, '0', STR_PAD_LEFT);

        // Розбиваємо число на частини по 3 цифри
        $parts = array_reverse(str_split($number, 3));

        // Обробляємо кожну частину
        foreach ($parts as $i => $part) {
            if ($part > 0) {
                $digits = [];

                // Сотні
                if ($part > 99) {
                    $digits[] = floor($part / 100) * 100;
                }

                // Десятки та одиниці
                if ($mod1 = $part % 100) {
                    $mod2 = $part % 10;
                    $flag = $i == 1 && $mod1 != 11 && $mod1 != 12 && $mod2 < 3 ? -1 : 1;
                    if ($mod1 < 20 || !$mod2) {
                        $digits[] = $flag * $mod1;
                    } else {
                        $digits[] = floor($mod1 / 10) * 10;
                        $digits[] = $flag * $mod2;
                    }
                }

                // Плюралізація
                $last = abs(end($digits));

                foreach ($digits as $j => $digit) {
                    $digits[$j] = $dic[0][$digit];
                }

                // Додаємо позначення порядку або валюту
                $digits[] = $dic[1][$i][(($last % 100) > 4 && $last < 20) ? 2 : $dic[2][min($last % 10, 5)]];

                // Об'єднуємо текст.number
                array_unshift($result, join(' ', $digits));
            }
        }

        return join(' ', $result);
    }

    public function deleteWork($work)
    {
        try {
            $work = OrderWork::find($work);
            $work->delete();
            return \response()->json([],200);
        }catch (Exception $e){
            return \response()->json([],400);
        }
    }

    public function deleteProduct($product)
    {

        try {
            $product = OrderProduct::find($product);
            $product->delete();
            return \response()->json([],200);
        }catch (Exception $e){
            return \response()->json([],400);
        }
    }

}
