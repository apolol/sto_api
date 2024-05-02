<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\StoreCompanyClientRequest;
use App\Http\Requests\StoreRegularClientRequest;
use App\Models\CRM\Client;
use App\Models\CRM\ClientCar;
use App\Models\CRM\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
class ClientsController extends Controller
{
    /**
     * Get clients with optional search filter and paginate the results.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getClients(Request $request): JsonResponse|Response
    {
        $clients = Client::filter(['search' => $request->get('search')])->orderBy('created_at', 'desc')->paginate(15);
        return \response()->json($clients);
    }

    public function getHeaderInfo()
    {
        $clients = Client::count();
        $clients_previous_month = Client::where('created_at', '>=', now()->subMonth())->count();
        $clients_this_month = Client::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

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
     * @param float $number1 The first number
     * @param float $number2 The second number
     * @return float The percentage difference
     */
    private function percentageDifference($number1, $number2) {
        $difference = $number2 - $number1;
        if ($number1 == 0 || $number2 == 0) {
            return 0;
        }
        $percentageDifference = ($difference / $number1) * 100;
        return $percentageDifference;
    }

    /**
     * Get client by UUID.
     *
     * @param  string  $uuid  The UUID of the client
     * @return JsonResponse
     */
    public function getClient($uuid): JsonResponse
    {
        $client = Client::where('id', $uuid)->first();
        return \response()->json($client);
    }

    /**
     * Get the cars of a client by UUID.
     *
     * @param string $uuid The UUID of the client
     * @return \Illuminate\Http\JsonResponse The JSON response containing the client's cars
     */
    public function getClientCars($uuid)
    {
        $cars = ClientCar::with(['brand.parent'])->where('client_id', $uuid)->get();
        return \response()->json($cars, 200);
    }

    public function getClientOrders($uuid)
    {
        $orders = Order::with('car.brand.parent')->where('client_id', $uuid)->orderBy('created_at','DESC')->get();
        return \response()->json($orders, 200);
    }

    /**
     * Update client information.
     *
     * @param Client $client The client to update
     * @param ClientRequest $request The request data
     * @return JsonResponse
     */
    public function updateClient(Client $client, Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|max:255|min:3',
            'last_name' => 'required|max:255|min:3',
            'phone' => 'required|max:10|min:10',
        ],
        [
            'first_name.required' => 'Ім`я не може бути порожнім',
            'last_name.required' => 'Фамілія не може бути порожньою',
            'phone.required' => 'Номер телефону не може бути порожнім',
            'phone.max' => 'Номер телефону має бути не більше 10 символів',
            'phone.min' => 'Номер телефону має бути не менше 10 символів',
        ]);
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->phone = $request->phone;
        $client->update();
        return \response()->json('', 200);
    }

    /**
     * Update the client's car based on the request data.
     *
     * @param ClientCar $client_car
     * @param Request $request
     * @return JsonResponse
     */
    public function updateClientCar(ClientCar $client_car, Request $request): JsonResponse
    {
        $client_car->update($request->except(['id','client_id']));
        if ($client_car->brand_id != $request->brand['id']) {
            $client_car->brand_id = $request->brand['id'];
            $client_car->update();
        }
        return \response()->json('', 200);
    }

    public function storeNewCar(Request $request){
        $client_car = new ClientCar();
        $client_car->client_id = $request->client_id;
        $client_car->brand_id = $request->brand['id'];
        $client_car->year = $request->year;
        $client_car->odometer = $request->odometer;
        $client_car->engine_type = $request->engine_type;
        $client_car->car_plate = $request->car_plate;
        $client_car->engine_value = $request->engine_value;
        $client_car->vin = $request->vin;
        $client_car->description = $request->description;
        $client_car->save();
        return \response()->json($client_car, 200);
    }

    public function storeRegularClient(StoreRegularClientRequest $request)
    {
        $client = new Client();
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->phone = $request->phone;
        $client->type = 0;
        $client->save();

        return \response()->json($client, 200);
    }

    public function storeCompanyClient(StoreCompanyClientRequest $request)
    {
        $client = new Client();
        $client->company_name = $request->company_name;
        $client->company_address = $request->company_address;
        $client->company_iban = $request->company_iban;
        $client->company_edrpu = $request->company_edrpu;
        $client->company_ipn = $request->company_ipn;
        $client->phone = $request->phone;
        $client->type = 1;
        $client->save();

        return \response()->json($client, 200);
    }
}
