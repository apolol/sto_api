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

    public function brandsList(){
        $brands = Brand::select('brands.*')
            ->join('brands AS parent_brands', 'brands.parent_id', '=', 'parent_brands.id')
            ->where('brands.parent_id', '!=', null)
            ->orderBy('parent_brands.title', 'ASC')
            ->paginate(30);
        return \response()->json($brands);
    }

    public function storeBrand(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'brand' => 'required',
        ],[
            'title.required' => 'Невказано назву',
            'brand.required' => 'Невказано марку',
        ]);
        $brand = new Brand();
        $brand->title = $request->title;
        $brand->parent_id = $request->brand['id'];
        $brand->save();
    }

}
