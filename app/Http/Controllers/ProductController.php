<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Utils\HttpStatusMapper;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:100",
            "description" => "nullable|string",
            "price" => "required|numeric",
            "quantity" => "integer",
        ]);

        $product = Product::create([
            "name" => $request->name,
            "description" => $request->description,
            "price" => $request->price,
            "quantity" => $request->quantity,
        ]);

        return response()->json([
            "message" => "Product created successfully",
            "data" => $product,
        ], HttpStatusMapper::getStatusCode("CREATED"));

        // TODO Duvida:
        // com eu acesso a mensagem de erro pra criar ifs no meio para um
        // produto que ja foi cadastrado anteriormente por exemplo?
    }
}
