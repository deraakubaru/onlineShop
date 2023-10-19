<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Store;
use App\Models\FlashSale;

class CartController extends Controller
{

    public function index(){
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->get();

        return response()->json(['carts' => $carts]);
    }

    public function addToCart(){
        $attributes = request()->validate([
            'user_id' => 'required|exists:users,id',
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

    public function checkout(){
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->get();

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderDetails = [];

            $order = Order::create(['user_id' => $user->id]);

            foreach ($cartItems as $cartItem) {
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                if ($cartItem->quantity > $product->quantity) {
                    throw new \Exception('Pembelian melebihi stock yang tersedia!');
                }

                $subtotal = $product->price * $cartItem->quantity;

                if($product->flash_sale_id !== null){
                    $flashSale = FlashSale::find($product->flash_sale_id);
                    $flashSaleDiscount = $flashSale->discount_percentage / 100;
                    $subtotal = $subtotal * (1 - $flashSaleDiscount);
                }
                $totalAmount += $subtotal;

                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $subtotal,
                ];

                // Reduce the product's quantity after each cart item
                $product->quantity -= $cartItem->quantity;
                $product->save();
            }

            if ($totalAmount > $user->balance) {
                throw new \Exception('Uang anda tidak cukup!');
            }

            //Reduce user's balance
            $user->balance -= $totalAmount;
            $user->save();

            //increase store's balance with total amount
            $store = Store::find($product->store_id);
            $store->balance += $totalAmount;
            $store->save();

            //add total amount to orders
            $order->total_amount = $totalAmount;
            $order->save();

            //Create order details in a from array above
            OrderDetail::insert($orderDetails);

            //Remove cart items
            $cartItems->each->delete();

            DB::commit();

            return response()->json(['message' => 'Pembelian berhasil!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}