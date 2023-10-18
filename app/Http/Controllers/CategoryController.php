<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{

    public function index(){
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }

    public function store(){
        
        $attributes = request()->validate([
            'category_name' => 'required|string',
        ]);

        $category = Category::create($attributes);

        return response()->json(['message' => 'Kategori berhasil dibuat!']);
    }

    public function update($id){
        try{
            $attributes = request()->validate([
                'category_name' => 'required|string',
            ]);

            $category = Category::find($id);

            if(!$category){
                return response()->json(['message' => 'Kategori tidak ditemukan!']);
            }else{
                $category->update($attributes);
            }
        }catch(Exception $e){
            return response()->json(['error' => 'Kesalahan!' . $e->getMessage()]);
        }
    }

    public function destroy($id){
        $category = Category::find($id);
        $category->delete();
        return response()->json(['message' => 'Kategori telah dihapus!']);
    }
}
