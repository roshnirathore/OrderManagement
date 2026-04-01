<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(Request $request) {

        try {
            $perPage = $request->per_page ?? 10;
            $status = $request->status ?? null;

            $query = Order::with('items');

            if($status) {
                $query->where('status' , $status);
            }

            $orders = $query->paginate($perPage);
            
            $response  = !empty($orders) ? OrderResource::collection($orders) : [];
                
            
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
            DB::beginTransaction();
            $totalAmount = 0;

            $request->validate([
                'customer_id'=> 'required|exists:users,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
    
            ]);
            $order =  Order::create([
                'user_id' => $request->customer_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            
            foreach($request->items as $item) {

                
                $product = Product::where('id' , $item['product_id'])->lockForUpdate()->first();

                if($item['quantity'] > $product->stock) {
                    return response()->json([
                        'message' => 'product stock limit exceed',
                    
                    ],401);
                }
                $totalAmount += $product->price;
                $updateStock = $product->stock - $item['quantity'];
                $product->update(['stock' => $updateStock]);

                OrderItem::create([
                    'order_id' =>$order->id,
                    'product_id' =>$item['product_id'],
                    'quantity' =>$item['quantity'],
                    'price' => $product->price
                ]);

               
            }

            $order->update(['total_amount' => $totalAmount]);
            DB::commit();
            $response =  response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            
            ],201);

        } catch (Exception $e) {
            DB::rollBack();
            $response =  response()->json([
                'error' => $e->getMessage()
            ],500);
        }
       return $response;

       
    }
}
