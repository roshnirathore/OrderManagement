<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\OrderResource;

class UserController extends Controller
{
    public function index() {

    }

    public function store(Request $request) {

        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('user-api-token')->plainTextToken;

            $response =  response()->json([
                'message' => 'User created successfully',
                'data' => $user,
                'extra_data' => [
                    'token' => $token
                ]
            ],201);

        } catch (Exception $e) {
            $response =  response()->json([
                'error' => $e->getMessage()
            ],500);
        }
       return $response;

    }

    public function orderList($userId) {

        try {

        
        $order = Order::with('items')->where('user_id' , $userId)->get();
        $response  = !empty($order) ? OrderResource::collection($order) : [];
            
            
    } catch (Exception $e) {
        $response =  response()->json([
            'error' => $e->getMessage()
        ],400);
    }
    return $response;

    }
}
