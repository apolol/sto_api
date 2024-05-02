<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_works', function (Blueprint $table) {
            $table->string('count')->default(1)->nullable();
        });
    }

    public function down()
    {
        Schema::table('order_works', function (Blueprint $table) {
            //
        });
    }
};
