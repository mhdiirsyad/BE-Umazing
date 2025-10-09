<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request) 
    {
        $user = Auth::user();
        try {
            $cart = Cart::with('cartItems.product')
            ->where('user_id', $user->id)
            ->first();

            if(!$cart){
                return response()->json([
                    'message'=>'Cart Not Found',
                    'data'=> null,
                ], 404);
            }

            return response()->json([
                'message'=>'Cart found',
                'data'=>$cart->load('cartItems.product'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function add(CartStoreRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $cart = Cart::query()->firstOrCreate(
                ['user_id'=>$user->id]
            );

            $item = $cart->cartItems()->where('product_id', $data['product_id'])->first();

            if($item) {
                $item->quantity += $data['quantity'];
                $item->save();
            }else{
                $product = Product::query()->findOrFail($data['product_id']);
                $cart->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $data['quantity'],
                ]);
            }

            DB::commit();
            return response()->json([
                'message'=>'Item added to cart',
                'data' => $cart->load('cartItems.product'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateItem(Request $request, string $id)
    {
        $user = Auth::user();
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $item = CartItem::query()->findOrFail($id);

            $cart = $item->cart;
            if($cart->user_id !== $user->id){
                return response()->json([
                    'message' => 'Unauthorized',
                    'data'=> null,
                ], 403);
            }

            $item->update(['quantity'=>$request->quantity]);
            DB::commit();

            return response()->json([
                'message' => 'CartItem updated',
                'data' => $item->load('product'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeItem(Request $request, $id)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            $item = CartItem::query()->findOrFail($id);

            if($item->cart->user_id !== $user->id){
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null
                ]. 403);
            }
            
            $item->delete();
            DB::commit();

            return response()->json([
                'message' => 'item delete from cart',
                'data' => null
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
