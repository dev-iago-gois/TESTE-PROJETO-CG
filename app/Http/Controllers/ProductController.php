<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Utils\HttpStatusMapper;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function create(Request $request): JsonResponse
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
    }

    public function getAll(): JsonResponse
    {
        // busca todos os produtos
        $products = Product::all();

        // retorna os produtos
        return response()->json([
            "message" => "Products retrieved successfully",
            "data" => $products,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));
    }

    public function getById(int $productId): JsonResponse
    {
        // valida a raquisicao
        $validator = Validator::make(
            ["id" => $productId],
            ["id" => "required|integer"]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => "Bad request, invalid id",
                "erros" => $validator->errors(),
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

    public function update(int $productId, Request $request): JsonResponse
    {
        // dd($request->all());

        // valida a raquisicao
        $idValidator = Validator::make(
            ["id" => $productId],
            // TODO Eh necessario esse exists:products,id?
            //  isso deixa a aplicacao mais lenta? R: nao.
            // validar com https://www.youtube.com/watch?v=5eDGg-DHabs
            ["id" => "required|integer|exists:products,id"]
        );

        if ($idValidator->fails()) {
            return response()->json([
                "message" => "Bad request, invalid id",
                "erros" => $idValidator->errors(),
            ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
        }

        // Valida os dados de atuilizacao
        // TODO aqui caberia um middleware de validacao https://www.youtube.com/watch?v=5eDGg-DHabs
        $requestValidator = Validator::make(
            $request->all(),
            [
                "name" => "nullable|string|max:100",
                "description" => "nullable|string",
                "price" => "nullable|numeric",
                "stock" => "nullable|integer",
            ]
        );

        if ($requestValidator->fails()) {
            return response()->json([
                "message" => "Bad request, invalid data",
                "erros" => $requestValidator->errors(),
            ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
        }

        // busca o produto
        $product = Product::find($productId);

        // Verifica se o produto existe
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], HttpStatusMapper::getStatusCode('NOT_FOUND'));
        }

        // Atualiza o produto
        $product->update($request->all());

        // retorna o produto atualizado
        return response()->json([
            "message" => "Product updated successfully",
            // Aqui poderia retornar o produto atualizado
            // ou a hora de atualizacao
            "data" => $product["updated_at"],
        ], HttpStatusMapper::getStatusCode("ACCEPTED"));
    }

    public function delete(int $productId): JsonResponse
    {
        // valida a raquisicao
        $validator = Validator::make(
            ["id" => $productId],
            ["id" => "required|integer"]
        );

        if ($validator->fails()) {
            return response()->json([
                "message" => "Bad request, invalid id",
                "erros" => $validator->errors(),
            ], HttpStatusMapper::getStatusCode("BAD_REQUEST"));
        }

        // busca o produto
        $product = Product::find($productId);

        // Verifica se o produto existe
        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], HttpStatusMapper::getStatusCode('NOT_FOUND'));
        }

        // Deleta o produto
        $product->delete();

        // retorna o produto deletado
        return response()->json([
            "message" => "Product deleted successfully",
            // "data" => $product,
        ], HttpStatusMapper::getStatusCode("SUCCESS"));
    }
}
