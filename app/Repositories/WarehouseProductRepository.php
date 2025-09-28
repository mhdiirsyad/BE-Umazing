<?php

namespace App\Repositories;

use App\Models\WarehouseProduct;
use Illuminate\Validation\ValidationException;

class WarehouseProductRepository {
    public function getByWarehouseAndProduct(int $warehouseId, int $productId): ?WarehouseProduct //harus return ini
    {
        return WarehouseProduct::where('warehouse_id', $warehouseId)->where('product_id', $productId)->first();
    }

    public function updateStock(int $warehouseId, int $productId, int $stock): WarehouseProduct //harus return ini
    {
        $warehouseProduct = $this->getByWarehouseAndProduct($warehouseId, $productId);

        if (!$warehouseProduct) {
            throw ValidationException::withMessages(['message' => 'Product not found for this warehouse.']);
        }

        $warehouseProduct->update(['stock'=> $stock]);
        return $warehouseProduct; 
    }
}

