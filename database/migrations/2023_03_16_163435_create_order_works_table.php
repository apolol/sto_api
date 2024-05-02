<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_works', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->nullable();
            $table->foreignUuid('work_id')->nullable();
            $table->string('price')->default(0)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_works');
    }
};

