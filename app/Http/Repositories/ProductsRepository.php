<?php

namespace App\Http\Repositories;

use App\Models\Product;

class ProductsRepository
{
    public function __construct(
        protected Product $model
    ) {}
    public function create(array $data): Product
    {
        return $this->model->create($data);
    }
    public function getAll(): array
    {
        return $this->model->all()->toArray();
    }
    public function getById(int $id): Product
    {
        return $this->model->findOrFail($id);
    }
    public function update(int $id, array $data): Product
    {
        $product = $this->model->findOrFail($id);
        $product->update($data);
        return $product;
    }
    public function updateStock(Product $product, int $quantity): void
    {
        $product->stock += $quantity;
        $product->save();
    }
    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();
    }
}
