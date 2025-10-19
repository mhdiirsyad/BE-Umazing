<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function getStatistics()
    {
        try {
            $now = now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            $orders = Order::query()->with('orderItems')->where('status', 'success')->get()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

            $totalOrders = $orders->count();
            $totalRevenues = $orders->sum('total');

            $totalProductSold = Order::query()
                ->where('status', 'success')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->withSum('orderItems', 'quantity')
                ->get()
                ->sum('order_items_sum_quantity');


            $bestSellProducts = $orders->flatMap->orderItems
            ->groupBy('product_id')
            ->map(fn($items) => [
                'product_id' => $items->first()->product_id,
                'total_sold' => $items->sum('quantity'),
                'product' => $items->first()->product,
            ])->sortByDesc('total_sold')
            ->take(5);

            return response()->json([
                'message' => 'stats retrieved succesfully',
                'data' => [
                    'total_order' => $totalOrders,
                    'total_revenue' => $totalRevenues,
                    'total_product_sold' => $totalProductSold,
                    'best_sell_product' => $bestSellProducts,
                ]
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Errors occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
