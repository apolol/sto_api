<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CRM\Client;
use App\Models\CRM\Wheel;
use Illuminate\Http\JsonResponse;


class WheelsController extends Controller
{
    public function index(Client $client) : JsonResponse {
        $wheells = Wheel::where('client_id', $client->id)->get();

        return response()->json([
            'wheels' => $wheells
        ]);
    }

    public function create(Request $requset) : JsonResponse  {
        $whell = new Wheel();
        $whell->client_id = $requset->client_id;
        $whell->title = $requset?->title ?? 'Невказано';
        $whell->place = $requset?->place ?? 'Невказано';
        $whell->count = $requset?->count ?? 1;
        
        $whell->save();
        
        return response()->json();
    }

    public function delete(Whell $wheel) : JsonResponse  {
        $wheel->delete();
        return response()->json();
    }
}
