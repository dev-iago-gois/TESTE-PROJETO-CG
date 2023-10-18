<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ProductsRepository;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        private ProductsRepository $repository
    ) {}
    public function create(CreateProductRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();

            $product = $this->repository->create($data);

            DB::commit();

            return response()->json([
                "message" => "Product created successfully",
                "data" => $product,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Product creation failed",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function getAll(): JsonResponse
    {
        try {

            $products = $this->repository->getAll();

            return response()->json([
                "message" => "Products retrieved successfully",
                "data" => $products,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json([
                "message" => "Products retrieval failed",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getById(int $productId): JsonResponse
    {
        try {

            $product = $this->repository->getById($productId);

            return response()->json([
                "message" => "Product retrieved successfully",
                "data" => $product,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
                return response()->json([
                    "message" => "Product retrieval failed",
                    "error" => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $productId, UpdateProductRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();

            $product = $this->repository->update($productId, $data);

            DB::commit();

            return response()->json([
                "message" => "Product updated successfully",
                "data" => $product,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Product update failed",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(int $productId): JsonResponse
    {

        DB::beginTransaction();

        try {

            $this->repository->delete($productId);

            DB::commit();

            return response()->json([
                "message" => "Product deleted successfully",
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Product deletion failed",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
