<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionalAndRegencyIdToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('regional_id')->nullable()->after('id');
            $table->unsignedBigInteger('regency_id')->nullable()->after('regional_id');

            // Jika Anda ingin menambahkan foreign key constraints, pastikan tabel 'regionals' dan 'regencies' ada:
            // $table->foreign('regional_id')->references('id')->on('regionals');
            // $table->foreign('regency_id')->references('id')->on('regencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('regional_id');
            $table->dropColumn('regency_id');

            // Jika Anda menambahkan foreign key constraints, pastikan juga menghapusnya:
            // $table->dropForeign(['regional_id']);
            // $table->dropForeign(['regency_id']);
        });
    }
}
