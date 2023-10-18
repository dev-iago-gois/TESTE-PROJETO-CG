<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Utils\HttpStatusMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create(CreateSaleRequest $request): JsonResponse
    {

        DB::beginTransaction();

        try {

            $data = $request->validated();
            $sale = Sale::create([
                'customer_name' => $data["customer_name"]
            ]);

            foreach ($data['products'] as $productItem) {

                $product = Product::find($productItem['product_id']);

                if(!$product) {
                    return response()->json([
                        'message' => "Product ID {$productItem['product_id']} not found",
                    ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
                }

                if($product) {
                    // TODO pode virar uma funcao de check stock
                    if($product->stock < $productItem['quantity']) {
                        return response()->json([
                            'message' => "Product {$product->name} is out of stock",
                        ], Response::HTTP_BAD_REQUEST);
                    }

                    $product->stock -= $productItem['quantity'];
                    $product->save();
                }

                $sale->products()->attach(
                    $product->id,
                    ['quantity' => $productItem['quantity']]
                );
            }

            DB::commit();

            return response()->json([
                "message" => "Sale created successfully",
                "data" => $sale,
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

    public function update(int $saleId, Request $request): JsonResponse
    {
        // validate incoming request
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // get sale by id
        $sale = Sale::find($saleId);
        // if sale does not exist return error message
        if(!$sale) {
            return response()->json([
                'message' => "Sale ID {$saleId} not found",
            ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
        }
        // if sale status is not pending return error message
        if($sale->status != 'pending') {
            return response()->json([
                'message' => "Sale ID {$saleId} cannot be updated",
            ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
        }

        $originalQuantities = [];
        foreach ($sale->products as $product) {
            // dd($product->pivot->quantity);
            $originalQuantities[$product->id] = $product->pivot->quantity;
        }
        // dd($originalQuantities);

        foreach ($request->input('products') as $productItem) {
            $productInSale = $sale->products->find($productItem['product_id']);
            // $quantitySold = $productInSale->pivot->quantity;
            $productDB = Product::find($productItem['product_id']);

            if(!$productInSale) {
                return response()->json([
                    'message' => "Product ID {$productItem->id} not found",
                ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
            }

            $newQuantity = $productItem['quantity'];

            $productDB->stock += $originalQuantities[$productItem['product_id']];
            // dd([$productDB->stock, $newQuantity]);
            // dd($originalQuantities[$productItem['product_id']]);

            if($productDB->stock < $newQuantity) {
                return response()->json([
                    'message' => "Product {$productInSale->name} is out of stock",
                ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
                // TODO
                // Response::HTTP_BAD_REQUEST;
            }
            // dd($productDB->stock);
            // TODO O ERRO TA POR AQUI

            $productDB->stock -= $newQuantity;
            // dd([$productDB->stock, $newQuantity]);

            $productDB->save();

            $productInSale->pivot->quantity = $newQuantity;
            $productInSale->pivot->save();
        }

        $updatedSale = $sale->products();
        // return updated sale
        return response()->json([
            'message' => "Sale ID {$saleId} updated successfully",
            'data' => $updatedSale,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));
    }

    public function getAll(): JsonResponse
    {
        try {
            $sales = Sale::with(['products:id,name,price,sales_products.quantity as quantity'])->get();
            return response()->json([
                'message' => 'Sales retrieved successfully',
                'data' => $sales,
            ], HttpStatusMapper::getStatusCode("SUCCESS"));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
