<?php

namespace App\Http\Controllers;

use App\Http\Repositories\
{
    ProductsRepository,
    SalesRepository
};
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct(
        private SalesRepository $saleRepository,
        private ProductsRepository $productRepository
    ) {}
    public function create(CreateSaleRequest $request): JsonResponse
    {

        DB::beginTransaction();

        try {

            $data = $request->validated();
            $sale = $this->saleRepository->create($data['customer_name']);

            foreach ($data['products'] as $productItem) {

                $product = $this->productRepository->getById($productItem['product_id']);

                // TODO pode virar uma funcao de check stock
                if($product->stock < $productItem['quantity']) {
                    return response()->json([
                        'message' => "Product {$product->name} is out of stock",
                    ], Response::HTTP_BAD_REQUEST);
                }

                $this->productRepository->updateStock($product, -$productItem['quantity']);

                $this->saleRepository->attachProductToSale($sale, $product, $productItem['quantity']);
            }

            DB::commit();

            return response()->json([
                "message" => "Sale {$sale->id} created successfully",
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Sale creation failed",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function cancel(int $saleId): JsonResponse
    {
        DB::beginTransaction();

        try {

            $sale = Sale::find($saleId);

            if(!$sale) {
                return response()->json([
                    'message' => "Sale ID {$saleId} not found",
                ], Response::HTTP_NOT_FOUND);
            }

            if($sale->status != 'pending') {
                return response()->json([
                    'message' => "Sale ID {$saleId} cannot be canceled",
                ], Response::HTTP_BAD_REQUEST);
            }

            foreach ($sale->products as $productItem) {

                $quantitySold = $productItem->pivot->quantity;
                $productItem->stock += $quantitySold;

                $productItem->save();
            }

            $sale->update(['status' => 'canceled']);

            DB::commit();

            return response()->json([
                'message' => "Sale ID {$saleId} canceled successfully",
                'data' => $sale,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

                DB::rollBack();

                return response()->json([
                    "message" => "Sale cancelation failed",
                    "error" => $e->getMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(int $saleId, UpdateSaleRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $data = $request->validated();
            $sale = Sale::find($saleId);

            if(!$sale) {
                return response()->json([
                    'message' => "Sale ID {$saleId} not found",
                ], Response::HTTP_NOT_FOUND);
            }

            if($sale->status != 'pending') {
                return response()->json([
                    'message' => "Sale ID {$saleId} cannot be updated",
                ], Response::HTTP_BAD_REQUEST);
            }

            foreach ($data['products'] as $productItem) {
                $productDB = Product::find($productItem['product_id']);

                if(!$productDB) {
                    return response()->json([
                        'message' => "Product ID {$productItem['product_id']} not found",
                    ], Response::HTTP_NOT_FOUND);
                }

                $previousQuantity = $sale->products->find($productItem['product_id'])->pivot->quantity;
                $newQuantity = $productItem['quantity'];
                $productDB->stock += $previousQuantity;

                if($productDB->stock < $newQuantity) {
                    return response()->json([
                        'message' => "Product {$productDB->name} is out of stock",
                    ], Response::HTTP_BAD_REQUEST);
                }

                $productDB->stock -= $newQuantity;

                $productDB->save();

                $sale->products()->updateExistingPivot(
                    $productItem['product_id'],
                    ['quantity' => $newQuantity]
                );

            }

            DB::commit();

            return response()->json([
                "message" => "Sale ID {$saleId} updated successfully",
                "data" => $sale,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "message" => "Sale update failed",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function getAll(): JsonResponse
    {
        try {

            $sales = Sale::with(['products:id,name,price,sales_products.quantity as quantity'])->get();

            return response()->json([
                'message' => 'Sales retrieved successfully',
                'data' => $sales,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Sales retrieval failed',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
