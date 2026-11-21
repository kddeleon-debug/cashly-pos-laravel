<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleService
{
    /**
     * Record a sale atomically: lock the product row, refuse to oversell,
     * decrease stock and store the transaction - all or nothing.
     *
     * @throws InsufficientStockException
     */
    public function record(int $productId, ?int $customerId, int $staffId, int $quantity): Sale
    {
        return DB::transaction(function () use ($productId, $customerId, $staffId, $quantity) {
            /** @var Product $product */
            $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();

            if ($quantity > $product->stock_quantity) {
                throw new InsufficientStockException($product->name, $product->stock_quantity, $quantity);
            }

            $product->decrement('stock_quantity', $quantity);

            return Sale::create([
                'product_id' => $product->id,
                'customer_id' => $customerId,
                'sold_by' => $staffId,
                // price is DECIMAL(10,2); compute in integer cents to avoid float drift
                'total_price' => number_format(((int) round($product->price * 100) * $quantity) / 100, 2, '.', ''),
                'quantity' => $quantity,
            ]);
        });
    }
}
