<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'product_id',
        'color_name',
        'size_name',
        'quantity',
        'order',
        'updated_at',
    ];
}
