<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Utils\HttpStatusMapper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        // dd($request->all());
        // validate incoming request
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // create sale
        $sale = Sale::create([
            'customer_name' => $request->customer_name,
            // 'status' => 'pending', nao precisa pois na migration ja esta definido um valor default de pending
        ]);

        // get products array
        $productsRequestData = $request->input('products');
        // aqui faz um for por cada produto dentro do array productsRequestData definido acima
        foreach ($productsRequestData as $productItem) {
            // get product in DB by id
            $product = Product::find($productItem['product_id']);
            // if product exists
            if($product) {
                // check if it has enough stock
                if($product->stock < $productItem['quantity']) {
                    return response()->json([
                        'message' => "Product {$product->name} is out of stock",
                    ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
                }
                $product->stock -= $productItem['quantity'];
                $product->save();
            }
            // if product does not exist return error message
            if(!$product) {
                return response()->json([
                    'message' => "Product ID {$productItem['product_id']} not found",
                ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
            }
            // attach product to sale, this create the link in sales_products table
            $sale->products()->attach($product->id, ['quantity' => $productItem['quantity']]);
        }

        // return created sale
        return response()->json([
            'message' => 'Sale created successfully',
            'data' => $sale,
        ], HttpStatusMapper::getStatusCode("CREATED"));
    }

    public function cancel(int $saleId): JsonResponse
    {
        // get sale by id
        $sale = Sale::find($saleId);
        // dd($sale);
        // if sale does not exist return error message
        if(!$sale) {
            return response()->json([
                'message' => "Sale ID {$saleId} not found",
            ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
        }
        // if sale status is not pending return error message
        if($sale->status != 'pending') {
            return response()->json([
                'message' => "Sale ID {$saleId} cannot be canceled",
            ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
        }
        // get products from sale
        $products = $sale->products;
        // dd($products);
        // foreach product in sale
        foreach ($products as $product) {
            // get product in DB by id
            // $productDB = Product::find($product->id);
            $productInSale = $sale->products->find($product->id);
            $quantitySold = $productInSale->pivot->quantity;
            // dd($product->stock);
            // add stock to product
            $product->stock += $quantitySold;
            // save product
            $product->save();
        }
        // update sale status to canceled
        $sale->update(['status' => 'canceled']);
        // return updated sale
        return response()->json([
            'message' => "Sale ID {$saleId} canceled successfully",
            'data' => $sale,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));
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
        // dd($sale);
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
        // get products from sale
        $products = $request->input('products');
        // dd($products);
        // foreach product in sale
        foreach ($products as $productItem) {
            // get product in DB by id
            $productDB = Product::find($productItem->id);
            // if product does not exist return error message
            if(!$productDB) {
                return response()->json([
                    'message' => "Product ID {$productItem->id} not found",
                ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
            }
            // check if it has enough stock
            if($productDB->stock < $productItem->quantity) {
                return response()->json([
                    'message' => "Product {$productDB->name} is out of stock",
                ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
            }
            // add stock to product
            $productDB->stock -= $productItem->quantity;
            // save product
            $productDB->save();
            // update product quantity in sale
            $sale->products()->updateExistingPivot($productDB->id, ['quantity' => $productItem->quantity]);
        }
    }

    public function getAll(): JsonResponse
    {
        // get all sales
        $sales = Sale::with(['products:id,name,price,sales_products.quantity as quantity'])->get();
        // TODO como eu tiro o pivot do retorno? so com um DTO?
        // return all sales
        return response()->json([
            'message' => 'Sales retrieved successfully',
            'data' => $sales,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));
    }
}
