<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    
    public function index(){
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->get();
        return response()->json(['orders' => $orders]);
    }

    public function show($id){
        $order = Order::findOrFail($id);
        return response()->json(['order' => $order]);
    }
}
