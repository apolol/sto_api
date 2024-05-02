<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CRM\Brand;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function getBrands()
    {
        if (\request()->has('type')){
            $brands = Brand::where('parent_id', null)->orderBy('title', 'ASC')->get();
            return \response()->json($brands);
        }else{
            $brands = Brand::where('parent_id', '!=' ,null)->orderBy('title', 'ASC')->get();
            return \response()->json($brands);
        }
    }
}
