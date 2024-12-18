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
        Schema::table('rasied_ayni', function (Blueprint $table) {
            $table->dropIndex('rasied_ayni_branch_id_foreign');
            $table->dropIndex('rasied_ayni_storage_id_foreign');
            $table->dropIndex('rasied_ayni_productive_id_foreign');
            $table->foreign('branch_id')->on('branches')->references('id')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreign('storage_id')->on('storages')->references('id')->cascadeOnDelete()->cascadeOnDelete();
            $table->foreign('productive_id')->on('productive')->references('id')->cascadeOnDelete()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rasied_ayni', function (Blueprint $table) {
            $table->index('rasied_ayni_branch_id_foreign');
            $table->index('rasied_ayni_storage_id_foreign');
            $table->index('rasied_ayni_productive_id_foreign');
            $table->dropForeign('branch_id');
            $table->dropForeign('storage_id');
            $table->dropForeign('productive_id');
        });
    }
};
