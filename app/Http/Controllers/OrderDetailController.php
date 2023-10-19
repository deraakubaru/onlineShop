<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderDetail;

class OrderDetailController extends Controller
{
    
    //shows carted items
    public function index(Request $request, $orderId){
        $orderDetails = OrderDetail::where('order_id', $orderId)->get();
        return response()->json(['orderDetails' => $orderDetails]);
    }

    //shows carted item
    public function show($orderDetailId){
        $orderDetail = OrderDetail::findOrFail($orderDetailId);
        return response()->json(['orderDetail' => $orderDetail]);
    }
    
}
