<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;

class OrderDetailController extends Controller
{
    
    public function index(Request $request, $orderId){
        $ordderDetails = OrderDetail::where('order_id', $orderId)->get();
        return response()->json(['orderDetails' => $orderDetails]);
    }

    public function show($orderDetailId){
        $orderDetail = OrderDetail::findOrFail($orderDetailId);
        return response()->json(['orderDetail' => $orderDetail]);
    }
    
}
