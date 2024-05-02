<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->nullable();
            $table->foreignUuid('client_car_id')->nullable();
            $table->foreignUuid('worker_id')->nullable();
            $table->timestamp('start_work')->useCurrent();
            $table->timestamp('end_work')->nullable();
            $table->string('status')->default('Взято в роботу');
            $table->string('number');
            $table->timestamps();
        });

        if (config('app.env') !== 'testing') {
            DB::statement('ALTER TABLE orders ADD FULLTEXT (number)');
        }
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
