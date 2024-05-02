<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('send_sms')->default(0)->nullable();
            $table->date('last_sms_date')->nullable();
            $table->text('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->text('company_iban')->nullable();
            $table->text('company_edrpu')->nullable();
            $table->text('company_ipn')->nullable();
            $table->integer('type')->default(0)->nullable();
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
};
