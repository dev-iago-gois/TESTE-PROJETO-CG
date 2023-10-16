<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Utils\HttpStatusMapper;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function create(Request $request)
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
}
