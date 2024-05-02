<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('bay_place')->nullable();
            $table->string('price')->nullable();
            $table->string('articul')->nullable();
            $table->string('brand')->nullable();
            $table->float('count')->nullable()->default(1);
            $table->string('invoice')->nullable();
            $table->boolean('active')->default(1);
            $table->text('additional')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
};
