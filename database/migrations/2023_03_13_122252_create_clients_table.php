<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();;
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        if (config('app.env') !== 'testing') {
            DB::statement('ALTER TABLE clients ADD FULLTEXT (first_name, last_name, phone)');
        }
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
