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
            "stock" => "integer",
        ]);

        $product = Product::create([
            "name" => $request->name,
            "description" => $request->description,
            "price" => $request->price,
            "stock" => $request->stock,
        ]);

        return response()->json([
            "message" => "Product created successfully",
            "data" => $product,
        ], HttpStatusMapper::getStatusCode("CREATED"));

        // TODO Duvida:
        // com eu acesso a mensagem de erro pra criar ifs no meio para um
        // produto que ja foi cadastrado anteriormente por exemplo?
    }

    public function getAll()
    {
        $products = Product::all();

        return response()->json([
            "message" => "Products retrieved successfully",
            "data" => $products,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));

        // TODO Duvida:
        // retornar o que esta no estoque > 0  ou todos os produtos?
    }
}
