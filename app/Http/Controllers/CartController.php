<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Moedels\User;
use App\Moedels\Order;
use App\Moedels\OrderDetail;

class CartController extends Controller
{

    public function index(){
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->get();

        return response()->json(['carts' => $carts]);
    }

    public function addToCart(){
        $attributes = request()->validate([
            'user_id' => 'required|exists:user,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
        ]);
        $cart = Cart::create($attributes);
        
        return response()->json(['message' => 'Produk telah ditambahkan ke keranjang.']);
    }

    public function updateCartItem($id){
        $attributes = request()->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cartItem = Cart::findOrFail($id);
        $cartItem->update($attributes);

        return response()->json(['message' => 'Jumlah produk berhasil diubah']);
    }

    public function removeCartItem($id){
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Produk berhasil dihapus dalam keranjang.']);
    }

    public function checkout(Request $request){
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->get();

        DB::beginTransaction();
        try{
            $totalAmount = 0;

            $order = Order::create(['user_id' => $user->id]);

            foreach($cartItems as $cartItem){
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                if($cartItem->quantity > $product->quantity){
                    DB::rollBack();
                    return response()->json(['error' => 'Pembelian melebihi stock yang tersedia!']);
                }

                $product->quantity -= $charItem->quantity;
                $product->save();

                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                ]);

                $totalAmount += $product->price * $cartItem->quantity;

                $cartItem->delete();
            }

            if($totalAmount > $user->balance){
                DB::rollBack();
                return response()->json(['error' => 'Uang anda tidak cukup!']);
            }

            $user->balance -= $totalAmount;
            $user->save();

            return response()->json(['message' => 'Pembelian berhasil!']);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error' => 'Proses pembelian gagal! Silahkan cobalagi.']);
        }
    }
}
