<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ImplementasiKoperasi;
use App\Models\User;
use App\Notifications\ImplementasiFollowUpNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class CheckImplementasiFollowUp extends Command
{
    protected $signature = 'check:followup';
    protected $description = 'Cek kondisi implementasi koperasi dan berikan notifikasi follow-up otomatis.';

    public function handle()
    {
        $implementasis = ImplementasiKoperasi::with('instansi')->get();
        $today = Carbon::today();

        foreach ($implementasis as $impl) {
            $namaKoperasi = $impl->instansi->nama_instansi ?? 'Koperasi ' . $impl->id;
            
            // Tentukan penerima notifikasi (PIC PT SAKTI)
            $usersToNotify = collect();
            if ($impl->pic_sakti_id) {
                $user = User::find($impl->pic_sakti_id);
                if ($user) $usersToNotify->push($user);
            }
            if ($usersToNotify->isEmpty()) {
                // Jika tidak ada PIC, kirim ke superadmin
                // Asumsi role Superadmin adalah 'Superadmin'
                $usersToNotify = User::where('role', 'Superadmin')->get();
            }

            if ($usersToNotify->isEmpty()) {
                continue;
            }

            $messages = [];

            // 1. Koperasi belum memberikan konfirmasi setelah pelatihan (misal lewat 5 hari)
            if (in_array($impl->status, ['Menunggu Konfirmasi', 'Menunggu Validasi']) && $impl->tanggal_pelatihan) {
                if ($impl->tanggal_pelatihan->diffInDays($today) >= 5) {
                    $messages[] = "$namaKoperasi belum memberikan konfirmasi tanggal running selama 5 hari setelah pelatihan. Silakan lakukan follow-up.";
                }
            }

            // 2. Koperasi belum menentukan tanggal running
            if (empty($impl->target_go_live) && $impl->tanggal_pelatihan && $impl->tanggal_pelatihan->diffInDays($today) >= 3) {
                $messages[] = "$namaKoperasi belum menentukan target tanggal running (go-live).";
            }

            // 3. Data cut-off belum tersedia
            if (empty($impl->tanggal_cut_off) && $impl->target_go_live) {
                $messages[] = "$namaKoperasi belum melengkapi data cut-off.";
            }

            // 4. Migrasi belum dilakukan (Progres < 100) - Hanya alert jika H-5 go live
            if ($impl->progres < 100 && $impl->target_go_live && $today->diffInDays($impl->target_go_live, false) <= 5 && $today->diffInDays($impl->target_go_live, false) >= 0) {
                $messages[] = "Migrasi data $namaKoperasi belum 100% dan target Go-Live mendekati (H-" . $today->diffInDays($impl->target_go_live, false) . ").";
            }

            // 5. Validasi belum diberikan
            if (empty($impl->pic_validasi) && $impl->target_go_live && $today->diffInDays($impl->target_go_live, false) <= 3 && $today->diffInDays($impl->target_go_live, false) >= 0) {
                $messages[] = "$namaKoperasi belum memberikan data PIC validasi menjelang go-live.";
            }

            // 6. Jadwal meeting belum dikonfirmasi
            if ($impl->status_go_live === 'Belum Siap Go Live' && $impl->target_go_live && $today->diffInDays($impl->target_go_live, false) <= 3 && $today->diffInDays($impl->target_go_live, false) >= 0) {
                $messages[] = "$namaKoperasi belum siap go-live meskipun target sudah dekat.";
            }

            // 7. Tidak ada aktivitas selama beberapa hari (7 hari)
            if ($impl->updated_at && $impl->updated_at->diffInDays($today) >= 7 && $impl->status !== 'Go-Live') {
                $messages[] = "Tidak ada pembaruan aktivitas untuk $namaKoperasi selama lebih dari 7 hari.";
            }

            // Kirim Notifikasi jika ada pesan
            foreach ($messages as $msg) {
                Notification::send($usersToNotify, new ImplementasiFollowUpNotification('Pengingat Follow-Up', $msg, $impl->id));
            }
        }

        $this->info('Pengecekan follow-up selesai.');
    }
}
