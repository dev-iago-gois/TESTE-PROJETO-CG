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
            $serviceResponse = $this->saleService->update($saleId, $data);

            DB::commit();

            return response()->json([
                "message" => "Sale ID {$saleId} updated successfully",
                "data" => $serviceResponse->sale,
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
