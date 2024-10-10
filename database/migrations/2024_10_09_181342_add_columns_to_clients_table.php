<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->tinyInteger('payment_category')
            ->comment('[EVERY_MONTH => 1,EVERY_15_DAYS => 2,EVERY_WEEK => 3,ON_DELIVERED => 4]')
            ->after('phone');
           $table->unsignedBigInteger('representative_id')->nullable()->after('payment_category');
           $table->foreign('representative_id')->references('id')->on('representatives')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['payment_category', 'representative_id']);
        });
    }
};
