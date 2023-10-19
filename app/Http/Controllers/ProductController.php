<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    
    public function index(){
        $products = Product::all();
        return response()->json(['products' => $products]);
    }

    public function show($id){
        $product = Product::findOrFail($id);
        return response()->json(['product' => $product]);
    }

    public function store(){
        $attributes = request()->validate([
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);
        $product = Product::create($attributes);
        
        return response()->json(['message' => 'Produk telah ditambahkan.']);
    }

    public function update($id){
        try{
            $attributes = request()->validate([
                'store_id' => 'required|exists:stores,id',
                'category_id' => 'required|exists:categories,id',
                'product_name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'quantity' => 'required|integer',
            ]);

            $product = Product::find($id);

            if(!$product){
                return response()->json(['message' => 'Prodak tidak ditemukan!']);
            }
        }catch(\Exception $e){
            return response()->json(['error' => 'Kesalahan!' . $e->getMessage()]);
        }
    }

    public function destroy($id){
        $product = Product::find($id);
        $product->delete();
        return response()->json(['message' => 'Produk telah dihapus!']);
    }

    public function associateWithFlashSale(Product $product, FlashSale $flashSale){
        $product->flash_sale_id = $flashSale->id;
        $product->save();

        return response()->json(['message' => 'Produk telah masuk kedalam produk flash sale']);
    }

    public function disassociateWithFlashSale(Product $product){
        $product->flash_sale_id = null;
        $product->save();

        return response()->json(['message' => 'Produk telah kembali normal']);
    }
}
