<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProduct extends Model
{
    // use HasFactory;
    protected $table = "sales_products";

    protected $fillable = [
        "sale_id",
        "product_id",
        "quantity",
    ];

    // A tabela intermediaria nao recebe relationship
}
