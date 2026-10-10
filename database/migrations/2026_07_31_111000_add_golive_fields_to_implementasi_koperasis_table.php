<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoliveFieldsToImplementasiKoperasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->string('metode_pendampingan')->nullable()->after('status_go_live');
            $table->string('link_meeting')->nullable()->after('metode_pendampingan');
            $table->text('catatan_kesiapan')->nullable()->after('link_meeting');
            $table->text('potensi_risiko')->nullable()->after('catatan_kesiapan');
            $table->text('rencana_mitigasi')->nullable()->after('potensi_risiko');
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
            $table->dropColumn([
                'metode_pendampingan',
                'link_meeting',
                'catatan_kesiapan',
                'potensi_risiko',
                'rencana_mitigasi'
            ]);
        });
    }
}
