<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFollowupFieldsToImplementasiKoperasiTable extends Migration
{
    public function up()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            if (!Schema::hasColumn('implementasi_koperasi', 'tanggal_followup')) {
                $table->date('tanggal_followup')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'hasil_komunikasi')) {
                $table->text('hasil_komunikasi')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'kendala_koperasi')) {
                $table->text('kendala_koperasi')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'komitmen_koperasi')) {
                $table->text('komitmen_koperasi')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'tanggal_followup_berikutnya')) {
                $table->date('tanggal_followup_berikutnya')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_followup',
                'hasil_komunikasi',
                'kendala_koperasi',
                'komitmen_koperasi',
                'tanggal_followup_berikutnya',
            ]);
        });
    }
}
