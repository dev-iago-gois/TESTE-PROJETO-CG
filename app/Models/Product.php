<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // use HasFactory;
    protected $table = "products";

    protected $fillable = [
        "name",
        "description",
        "price",
        "stock",
    ];

    // TODO add relationship
    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sales_products', 'product_id', 'sale_id')
            ->withPivot('stock');
    }

}
