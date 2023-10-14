<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    // use HasFactory;
    protected $table = "sales";

    protected $fillable = [
        "customer_name",
        "status",
    ];

    // TODO add relationship
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_products', 'sale_id', 'product_id')
            ->withPivot('quantity');
    }
}
