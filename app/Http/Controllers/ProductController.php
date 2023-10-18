<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Utils\HttpStatusMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create(CreateProductRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();
            $product = Product::create($data);

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

            $products = Product::all();

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

            $product = Product::find($productId);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], Response::HTTP_NOT_FOUND);
            }

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
            $product = Product::find($productId);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $product->update($data);

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
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $product->delete();

        return response()->json([
            "message" => "Product {$product->name} deleted successfully",
        ], Response::HTTP_OK);
    }
}
