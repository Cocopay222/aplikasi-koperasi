<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCutoffDetailsToImplementasiKoperasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('implementasi_koperasi', function (Blueprint $table) {
            if (!Schema::hasColumn('implementasi_koperasi', 'periode_transaksi_terakhir')) {
                $table->string('periode_transaksi_terakhir')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'saldo_terakhir')) {
                $table->string('saldo_terakhir')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'tanggal_tutup_buku')) {
                $table->date('tanggal_tutup_buku')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'tanggal_mulai_aplikasi')) {
                $table->date('tanggal_mulai_aplikasi')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'pic_validasi')) {
                $table->string('pic_validasi')->nullable();
            }
            if (!Schema::hasColumn('implementasi_koperasi', 'catatan_cutoff')) {
                $table->text('catatan_cutoff')->nullable();
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
            $table->dropColumn([
                'periode_transaksi_terakhir',
                'saldo_terakhir',
                'tanggal_tutup_buku',
                'tanggal_mulai_aplikasi',
                'pic_validasi',
                'catatan_cutoff'
            ]);
        });
    }
}
