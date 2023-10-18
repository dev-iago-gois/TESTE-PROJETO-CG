<?php

namespace App\Http\Services;

use App\Http\Repositories\{ProductsRepository, SalesRepository};
use App\Http\Utils\StatusChecker;

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
    public function cancel(int $saleId): object
    {
        $sale = $this->saleRepository->getById($saleId);

        // TODO pode virar uma funcao de check status
        StatusChecker::checkStatus($saleId, $sale->status);

        foreach ($sale->products as $productItem) {

            $quantitySold = $productItem->pivot->quantity;
            $this->productRepository->updateStock($productItem, $quantitySold);

        }
        $this->saleRepository->update($sale, 'status', 'canceled');

        return (object)["sale" => $sale];
    }
    public function update(int $saleId, array $data): object
    {
        $sale = $this->saleRepository->getById($saleId);

        StatusChecker::checkStatus($saleId, $sale->status);

        foreach ($data['products'] as $productItem) {
            $productDB = $this->productRepository->getById($productItem['product_id']);

            $previousQuantity = $sale->products->find($productItem['product_id'])->pivot->quantity;
            $newQuantity = $productItem['quantity'];

            $this->productRepository->updateStock($productDB, $previousQuantity);

            if($productDB->stock < $newQuantity) {
                throw new \Exception("Product {$productDB->name} is out of stock");
                // return response()->json([
                //     'message' => "Product {$productDB->name} is out of stock",
                // ], Response::HTTP_BAD_REQUEST);
            }

            $this->productRepository->updateStock($productDB, -$newQuantity);

            $this->saleRepository->updatePivot($sale, $productItem['product_id'], $newQuantity);

        }
        return (object)["sale" => $sale];
    }
}
