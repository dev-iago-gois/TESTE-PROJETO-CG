<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Utils\HttpStatusMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        // valida a raquisicao
        $request->validate([
            "name" => "required|string|max:100",
            "description" => "nullable|string",
            "price" => "required|numeric",
            "stock" => "integer",
        ]);

        // cria o produto
        $product = Product::create([
            "name" => $request->name,
            "description" => $request->description,
            "price" => $request->price,
            "stock" => $request->stock,
        ]);

        // retorna o produto criado
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
        // busca todos os produtos
        $products = Product::all();

        // retorna os produtos
        return response()->json([
            "message" => "Products retrieved successfully",
            "data" => $products,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));

        // TODO Duvida:
        // retornar o que esta no estoque > 0  ou todos os produtos?
    }

    public function getById(int $productId)
    {
        // valida a raquisicao
        $validator = Validator::make(
            ["id" => $productId],
            ["id" => "required|integer"]
        );
        if ($validator->fails()) {
            return response()->json([
                "message" => "Bad request, invalid id",
            ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
        }

        // busca o produto
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                "message" => "Product not found",
            ], HttpStatusMapper::getStatusCode("NOT_FOUND"));
        }

        return response()->json([
            "message" => "Product retrieved successfully",
            "data" => $product,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));
    }
}
