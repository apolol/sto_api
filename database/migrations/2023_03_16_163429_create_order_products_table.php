<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->nullable();
            $table->string('title')->nullable();
            $table->string('where_get')->nullable();
            $table->string('price_for_client')->nullable();
            $table->string('real_price')->nullable();
            $table->string('discount')->nullable();
            $table->string('articul')->nullable();
            $table->string('brand')->nullable();
            $table->integer('count')->nullable()->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_products');
    }
};
