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
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();;
            $table->string('title');
            $table->foreignUuid('parent_id')->nullable();
            $table->timestamps();
        });

        if (config('app.env') !== 'testing') {
            DB::statement('ALTER TABLE brands ADD FULLTEXT (title)');
        }
    }

    public function down()
    {
        Schema::dropIfExists('brands');
    }
};
