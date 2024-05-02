<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_cars', function (Blueprint $table) {
            $table->uuid('id')->primary();;
            $table->foreignUuid('client_id');
            $table->string('vin')->nullable();
            $table->string('odometer')->nullable();
            $table->string('year')->nullable();
            $table->string('engine_type')->nullable();
            $table->string('car_plate')->nullable();
            $table->string('engine_value')->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('brand_id');
            $table->timestamps();
        });
        if (config('app.env') !== 'testing') {
            DB::statement('ALTER TABLE client_cars ADD FULLTEXT (vin)');
        }
    }

    public function down()
    {
        Schema::dropIfExists('client_cars');

    }
};
