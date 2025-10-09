<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        try {
            $query = Order::query()->with(['orderItems.product', 'user']);

            if($user->role !== 'admin'){
                $query->where('user_id', $user->id);
            }

            if($request->has('search')){
                $query->where('id', $request->get('search'));
            }

            $orders = $query->latest()->get();
            
            return response()->json([
                'message' => 'Successfully get all orders',
                'data' => OrderResource::collection($orders),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required'
        ]);
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $cart = $user->cart()->with('cartItems.product')->first();

            if(!$cart || $cart->cartItems->isEmpty()){
                return response()->json([
                    'message' => 'Cart is Empty',
                    'data' => null,
                ], 400);
            }
            
            $total = 0;
            foreach($cart->cartItems as $item){
                if($item->product->stock < $item->quantity){
                    throw new \Exception("Stock {$item->product->name} not suffient");
                }

                $price = $item->product->price;
                $subtotal = $price * $item->quantity;
                $total += $subtotal;
            }

            $order = new Order();
            $order['user_id'] = $user->id;
            $order['status'] = 'pending';
            $order['address'] = $request->address;
            $order['total'] = $total;
            $order->save();

            foreach ($cart->cartItems as $item) {
                $product = $item->product;
                $price = $product->price;
                $subtotal = $price * $item->quantity;

                $product->decrement('stock', $item->quantity);
                
                $orderItem = new OrderItem();
                $orderItem['order_id'] = $order->id;
                $orderItem['product_id'] = $item->product_id;         
                $orderItem['quantity'] = $item->quantity;         
                $orderItem['price'] = $price;         
                $orderItem['subtotal'] = $subtotal;
                $orderItem->save();
                $item->delete();
            }

            $order['total'] = $total;
            $order->save();


            DB::commit();

            return response()->json([
                'message' => 'order successfully added',
                'data' => $order->load('orderItems.product'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        try {
            $order = Order::query()->with(['orderItems.product', 'user']);

            if($user->role !== 'admin') {
                $order->where('user_id', $user->id);
            }

            $order->find($id);

            if(!$order) {
                return response()->json([
                    'message' => 'order not found',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Order found',
                'data' => new OrderResource($order),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,success,cancelled'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::query()->with(['orderItems.product', 'user'])->find($id);

            if(!$order) {
                return response()->json([
                    'message' => 'order not found',
                    'data' => null,
                ], 404);
            }

            $order['status'] = $request->status;
            if($request->status === 'success') {
                $order['completed_at'] = now();
            }

            if($request->status === 'cancelled') {
                foreach($order->orderItems as $item) {
                    $item->product->stock += $item->quantity;
                    $item->product->save();
                }
            }
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Order status updated',
                'data' => new OrderResource($order),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
