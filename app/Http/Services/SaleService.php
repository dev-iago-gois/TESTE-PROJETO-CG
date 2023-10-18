<?php

namespace App\Http\Services;

use App\Http\Repositories\{ProductsRepository, SalesRepository};

class SaleService
{
    public function __construct(
        private SalesRepository $saleRepository,
        private ProductsRepository $productRepository
    ) {}

    public function create(array $data): object
    {
        $sale = $this->saleRepository->create($data['customer_name']);

        foreach ($data['products'] as $productItem) {

            $product = $this->productRepository->getById($productItem['product_id']);

            // TODO pode virar uma funcao de check stock
            if($product->stock < $productItem['quantity']) {
                throw new \Exception("Product {$product->name} is out of stock");
                // return response()->json([
                //     'message' => "Product {$product->name} is out of stock",
                // ], Response::HTTP_BAD_REQUEST);
            }

            $this->productRepository->updateStock($product, -$productItem['quantity']);

            $this->saleRepository->attachProductToSale($sale, $product, $productItem['quantity']);
        }

        return (object)["id" => $sale->id];
    }
    public function cancel(array $data): void
    {
    }
    public function update(array $data): void
    {
    }
}
