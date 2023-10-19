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

    //show items in cart of a logged user
    public function index(){
        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->get();

        return response()->json(['carts' => $carts]);
    }

    //add an item/product into cart
    public function addToCart(){
        $attributes = request()->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
        ]);
        $cart = Cart::create($attributes);
        
        return response()->json(['message' => 'Produk telah ditambahkan ke keranjang.']);
    }

    //change the quantity of a product inside cart
    public function updateCartItem($id){
        $attributes = request()->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $cartItem = Cart::findOrFail($id);
        $cartItem->update($attributes);

        return response()->json(['message' => 'Jumlah produk berhasil diubah']);
    }

    //remove a product inside cart
    public function removeCartItem($id){
        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Produk berhasil dihapus dalam keranjang.']);
    }

    //checkout logic
    //laravel has DB::transaction, DB::commit, and lockForUpdate, i use them to handle race condition
    //as what laravel's documentation tells
    public function checkout(){
        //initiate buyer's variable
        $user = Auth::user();
        //to get user's items inside cart
        $cartItems = Cart::where('user_id', $user->id)->get();

        DB::beginTransaction();
        try {
            //initiate total amount with zero to help the process of count total amount in looping
            $totalAmount = 0;
            $orderDetails = [];

            //initiate to create an order data
            $order = Order::create(['user_id' => $user->id]);

            //looping logic to get each item and math procedure for the subtotal, total amount, quantity.
            foreach ($cartItems as $cartItem) {
                $product = Product::where('id', $cartItem->product_id)->lockForUpdate()->first();

                //a validator if stock of a products less than requested
                if ($cartItem->quantity > $product->quantity) {
                    throw new \Exception('Pembelian melebihi stock yang tersedia!');
                }

                $subtotal = $product->price * $cartItem->quantity;

                //logic if product associated with flash sale
                if($product->flash_sale_id !== null){
                    $flashSale = FlashSale::find($product->flash_sale_id);
                    $flashSaleDiscount = $flashSale->discount_percentage / 100;
                    $subtotal = $subtotal * (1 - $flashSaleDiscount);
                }

                //total amount
                $totalAmount += $subtotal;

                //holds order details in array
                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $subtotal,
                ];

                //Reduce the product's quantity by quantity that requested
                $product->quantity -= $cartItem->quantity;
                $product->save();
            }

            //a validator if user's balance less than total amount
            if ($totalAmount > $user->balance) {
                throw new \Exception('Uang anda tidak cukup!');
            }

            //Reduce user's balance by total amount
            $user->balance -= $totalAmount;
            $user->save();

            //increase store's balance by total amount
            $store = Store::find($product->store_id);
            $store->balance += $totalAmount;
            $store->save();

            //add total amount to orders initiated earlier
            $order->total_amount = $totalAmount;
            $order->save();

            //an execute to create the order details inside array that holds order details data
            OrderDetail::insert($orderDetails);

            //clear up user's cart
            $cartItems->each->delete();

            //close the transanction
            DB::commit();

            return response()->json(['message' => 'Pembelian berhasil!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()]);
        }
    }

}