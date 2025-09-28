<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseProductRequest;
use App\Http\Requests\WarehouseProductUpdateRequest;
use App\Http\Resources\WarehouseProductResource;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Js;

class WarehouseProductController extends Controller
{
    //
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService){
        $this->warehouseService = $warehouseService;
    }

    public function attach(WarehouseProductRequest $request, int $warehouseId) {
        $this->warehouseService->attachProducts($warehouseId, $request->input('product_id'), $request->input('stock'));

        return response()->json(['message' => 'Product attached to warehouse successfully'], 200);
    }

    public function detach(int $warehouseId, int $productId) {
        $this->warehouseService->detachProducts($warehouseId, $productId);
        return response()->json(['message' => 'Product detached from warehouse successfully'], 200);
    }

    public function update(WarehouseProductUpdateRequest $request, int $warehouseId, int $productId) {
        $WarehouseProduct = $this->warehouseService->updateProductStock($warehouseId, $productId, $request->validated()['stock']);
        return response()->json(['message'=> 'Stock updated succesfully', 'data' => $WarehouseProduct], 200);
    }

}