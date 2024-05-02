<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CRM\Brand;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->id());

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->roles[0]['name'] ?? null
        ]);
    }
}
