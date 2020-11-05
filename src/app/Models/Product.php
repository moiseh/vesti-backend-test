<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'composition',
        'price',
        'active',
        'brand_name',
        'size_group_name',
        'integration_product_id',
        'weight',
        'height',
        'width',
        'length',
        'updated_order_at',
        'created_order_at',
        'created_at',
        'updated_at',
        'featured_at',
        'promotion',
        'has_photo',
    ];
}
