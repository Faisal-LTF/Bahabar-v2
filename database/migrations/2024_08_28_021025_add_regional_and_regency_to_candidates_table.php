<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->unsignedBigInteger('regional_id')->nullable()->after('event_id');
            $table->unsignedBigInteger('regency_id')->nullable()->after('regional_id');

            // Jika ingin menambahkan foreign key (opsional)
            $table->foreign('regional_id')->references('id')->on('regionals');
            $table->foreign('regency_id')->references('id')->on('regencies');
        });
    }

    public function down()
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropForeign(['regional_id']);
            $table->dropForeign(['regency_id']);
            $table->dropColumn('regional_id');
            $table->dropColumn('regency_id');
        });
    }
};
