<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{

    public function index(){
        $stores = Store::all();
        return response()->json(['stores' => $stores]);
    }

    public function show($id){
        $store = Store::findOrFail($id);
        return response()->json(['store' => $store]);
    }
    
    public function store(){
        $attributes = request()->validate([
            'user_id' => 'required|exists:users,id',
            'store_name' => 'required|string',
            'description' => 'required|string'
        ]);

        $store = Store::create($attributes);

        return response()->json(['message' => 'Toko telah berhasil dibuat !']);
    }

    public function update($id){
        try{
            $attributes = request()->validate([
                'store_name' => 'required|string',
            ]);

            $store = Store::find($id);

            if(!$store){
                return response()->json(['message' => 'Toko tidak diketahui!']);
            }
        }catch(Exception $e){
            return response()->json(['error' => 'Kesalahan!' . $e->getMessage()]);
        }
    }

    public function destroy($id){
        $store = Store::find($id);
        $store->delete();
        return response()->json(['message' => 'Toko telah dihapus!']);
    }
}
