<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
   
    public function index(Request $request) 
    {
        try{
            $products = Cache::remember('product_list', 60 , function() use ($request) {

                $query = Product::query();

                if(isset($request->search) && $request->search) {
                    $query->where('name' , 'like' , "%{$request->search}%");
                }
                return $query->get();
            });

            $response  = !empty($products) ? ProductResource::collection($products) : [];
            
            
        } catch (Exception $e) {
            $response =  response()->json([
                'error' => $e->getMessage()
            ],400);
        }
        return $response;
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|min:2',
                'price' => 'required|numeric|min:1',
                'stock' => 'required|integer|min:1',
            ]);

            $product = Product::create($request->only([
                'name',
                'price',
                'stock',
            ]));

            $response =  response()->json([
                'message' => 'Product saved successfully',
                'data' => $product
            ],201);
        } catch(Exception $e) {
            $response =  response()->json([
                'error' => $e->getMessage()
            ],500);
        }
        
        return $response;
    }
}
