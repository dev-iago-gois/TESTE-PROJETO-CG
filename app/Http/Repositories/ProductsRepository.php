<?php

namespace App\Http\Repositories;
use App\Models\Product;

class ProductsRepository
{
    public function __construct(
        protected Product $model
    ) {}
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function getAll()
    {
        return $this->model->all();
    }
    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }
    public function update(int $id, array $data)
    {
        $product = $this->model->findOrFail($id);
        $product->update($data);
        return $product;
    }
    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();
    }
}
