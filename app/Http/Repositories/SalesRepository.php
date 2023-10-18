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
        public function getAll()
        {

        }
        public function getById()
        {

        }
        public function update()
        {

        }
}
