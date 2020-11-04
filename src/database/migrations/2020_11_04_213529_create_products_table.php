<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('description', 350)->nullable();
            $table->string('composition')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->string('brand_name');
            $table->string('size_group_name')->nullable();
            $table->string('integration_product_id')->nullable();
            $table->double('weight');
            $table->double('height');
            $table->double('width');
            $table->double('length');
            $table->timestamp('updated_order_at')->nullable();
            $table->timestamp('featured_at')->nullable();
            $table->boolean('promotion')->default(false)->nullable();
            $table->timestamp('created_order_at')->nullable();
            $table->boolean('has_photo')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
