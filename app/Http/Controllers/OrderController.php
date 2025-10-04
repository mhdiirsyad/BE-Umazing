<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $user = Auth::user();
        try {
            $orders = Order::query();

            if($user->role !== 'admin'){
                $orders->where('user_id', $user->id);
            }

            $orders->latest()->get();
            
            return response()->json([
                'message' => 'Successfully get all orders',
                'data' => $orders,
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

            $order = new Order();
            $order['user_id'] = $user->id;
            $order['status'] = 'pending';
            $order['address'] = $request->address;
            $order['total'] = 0;
            $order->save();

            $total = 0;

            foreach($cart->cartItems as $item){
                $price = $item->product->price;
                $subtotal = $price * $item->quantity;

                $orderItem = new OrderItem();
                $orderItem['order_id'] = $order->id;
                $orderItem['product_id'] = $item->product_id;         
                $orderItem['quantity'] = $item->quantity;         
                $orderItem['price'] = $price;         
                $orderItem['subtotal'] = $subtotal;
                $orderItem->save();

                $total += $subtotal;
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
            $order = Order::query();

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
                'data' => $order,
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
            'status' => 'required|in:pending,paid,shiping,success,cancelled'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::query()->find($id);

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
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Order status updated',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
