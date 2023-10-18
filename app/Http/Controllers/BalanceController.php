<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;

class BalanceController extends Controller
{

    public function show(){
        $user = Auth::user();
        return response()->json(['balance' => $user->balance]);
    }

    public function showStoreBalance($id){
        $store = Store::findOrFail($id);
        return response()->json(['balance' => $store->balance]);
    }

    public function topUp(Request $request){
        $user = Auth::user();
        $amount = $request->input('amount');
        $user->balance += $amount;
        $user->save();

        return response()->json(['message' => 'Top up berhasil!']);
    }

    public function topUpToko(Request $request, $id){
        $store = Store::findOrFail($id);
        $amount = $request->input('amount');
        $store->balance += $amount;
        $store->save();

        return response()->json(['message' => 'Top up berhasil!']);
    }
}
