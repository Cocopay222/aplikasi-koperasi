<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusCutoffToImplementasiKoperasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            if (!Schema::hasColumn('implementasi_koperasi', 'status_cutoff')) {
                $table->string('status_cutoff')->default('Menunggu Penentuan Cut-Off')->nullable();
            }
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
            $table->dropColumn('status_cutoff');
        });
    }
}
