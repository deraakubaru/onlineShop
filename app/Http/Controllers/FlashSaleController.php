<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlashSale;

class FlashSaleController extends Controller
{

    public function index(){
        $flashsales = FlashSale::where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->get();

        return response()->json(['flash_sales' => $flashsales]);
    }

    public function store(){
        $attributes = request()->validate([
            'title' => 'required|max:255||string',
            'descrtiption' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'discount_percentage' => 'required',
        ]);

        $flashSale = FlashSale::create($attributes);

        return response()->json(['message' => 'Flash sale telah dibuat!']);
    }

    public function update($id){
        $attributes = request()->validate([
            'title' => 'required|max:255||string',
            'descrtiption' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'discount_percentage' => 'required',
        ]);
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->update($attributes);

        return response()->json(['message' => 'Flash sale telah diperbarui']);
    }

    public function destroy($id){
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->delete();

        return response()->json(['message' => 'Flash sale telah dihapus']);
    }

    public function endFlashSale(FlashSale $flashSale){
        $flashSale->end_time = now();
        $flashSale->save();

        return response()->json(['message' => 'Flash Sale selesai']);
    }
}
