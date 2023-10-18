<?php

namespace App\Http\Controllers;

use App\Http\Repositories\{ProductsRepository, SalesRepository};
use App\Http\Requests\{CreateSaleRequest, UpdateSaleRequest};
use App\Http\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct(
        private SalesRepository $saleRepository,
        private ProductsRepository $productRepository,
        private SaleService $saleService
    ) {}
    public function create(CreateSaleRequest $request): JsonResponse
    {

        DB::beginTransaction();

        try {

            $data = $request->validated();
            $serviceResponse = $this->saleService->create($data);

            DB::commit();

            return response()->json([
                "message" => "Sale {$serviceResponse->id} created successfully",
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

            $serviceResponse = $this->saleService->cancel($saleId);

            // $sale = $this->saleRepository->getById($saleId);

            // // TODO pode virar uma funcao de check status
            // if($sale->status != 'pending') {
            //     throw new \Exception("Sale ID {$saleId} cannot be canceled");
            //     // return response()->json([
            //     //     'message' => "Sale ID {$saleId} cannot be canceled",
            //     // ], Response::HTTP_BAD_REQUEST);
            // }

            // foreach ($sale->products as $productItem) {

            //     $quantitySold = $productItem->pivot->quantity;
            //     $this->productRepository->updateStock($productItem, $quantitySold);

            // }
            // $this->saleRepository->update($sale, 'status', 'canceled');

            DB::commit();

            return response()->json([
                'message' => "Sale ID {$saleId} canceled successfully",
                'data' => $serviceResponse->sale,
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
            $sale = $this->saleRepository->getById($saleId);

            // TODO pode virar uma funcao de check status
            if($sale->status != 'pending') {
                throw new \Exception("Sale ID {$saleId} cannot be updated");
                // return response()->json([
                //     'message' => "Sale ID {$saleId} cannot be updated",
                // ], Response::HTTP_BAD_REQUEST);
            }

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

            $sales = $this->saleRepository->history();

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
