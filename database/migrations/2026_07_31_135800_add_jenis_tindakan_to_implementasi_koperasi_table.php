<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJenisTindakanToImplementasiKoperasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->string('jenis_tindakan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->dropColumn('jenis_tindakan');
        });
    }
}
