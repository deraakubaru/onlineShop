<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'product_name',
        'description',
        'price',
        'quantity',
    ];

    public function category(){
        return $this->belongsTo(category::class);
    }
}
