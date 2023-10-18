<?php

namespace App\Http\Repositories;

use App\Models\Product;
use App\Models\Sale;

class SalesRepository
{
    public function __construct(
        protected Sale $model
    ) {}
        public function create(string $customer_name): Sale
        {
            return $this->model->create([
                'customer_name' => $customer_name
            ]);
        }
        public function attachProductToSale(Sale $sale, Product $product, int $quantity): void
        {
            $sale->products()->attach(
                $product->id,
                ['quantity' => $quantity]
            );
        }
        public function history(): array
        {
            return $this->model->with('products')->get()->toArray();
        }
        public function getById( int $id): Sale
        {
            return $this->model->findOrFail($id);
        }

        public function update(Sale $sale, string $column, $value): void
        {
            $sale->update([$column => $value]);
        }
        public function updatePivot(Sale $sale, int $productId, int $quantity): void
        {
            $sale->products()->updateExistingPivot(
                $productId,
                ['quantity' => $quantity]
            );
        }
}
