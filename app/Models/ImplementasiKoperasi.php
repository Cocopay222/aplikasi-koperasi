<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementasiKoperasi extends Model
{
    use HasFactory;

    protected $table = 'implementasi_koperasi';

    protected $fillable = [
        'nomor_implementasi',
        'instansi_id',
        'kantor_cabang',
        'aplikasi_id',
        'tanggal_pelatihan',
        'tanggal_selesai',
        'metode_pelatihan',
        'berita_acara',
        'nama_trainer',
        'pic_sakti_id',
        'anggota_hadir',
        'pic_koperasi',
        'kontak_pic',
        'email_pic',
        'catatan_pelatihan',
        'target_go_live',
        'waktu_go_live',
        'tempat_go_live',
        'status_go_live',
        'metode_pendampingan',
        'link_meeting',
        'catatan_kesiapan',
        'potensi_risiko',
        'rencana_mitigasi',
        'tanggal_cut_off',
        'status',
        'progres',
        'tindakan_berikutnya',
        'pic_tindakan',
        'target_tanggal_tindakan',
        'status_tindakan',
        'periode_transaksi_terakhir',
        'saldo_terakhir',
        'tanggal_tutup_buku',
        'tanggal_mulai_aplikasi',
        'pic_validasi',
        'catatan_cutoff',
        'status_cutoff',
        'jenis_tindakan',
        'tanggal_followup',
        'hasil_komunikasi',
        'kendala_koperasi',
        'komitmen_koperasi',
        'tanggal_followup_berikutnya',
        'tanggal_mulai_support',
        'tanggal_selesai_support',
        'durasi_support',
    ];

    protected $casts = [
        'tanggal_pelatihan' => 'date',
        'tanggal_selesai' => 'date',
        'target_go_live' => 'date',
        'tanggal_cut_off' => 'date',
        'target_tanggal_tindakan' => 'date',
        'tanggal_tutup_buku' => 'date',
        'tanggal_mulai_aplikasi' => 'date',
        'tanggal_mulai_support' => 'date',
        'tanggal_selesai_support' => 'date',
        'progres' => 'decimal:2',
        'tanggal_followup' => 'date',
        'tanggal_followup_berikutnya' => 'date',
    ];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id', 'instansi_id');
    }

    public function aplikasi()
    {
        return $this->belongsTo(MasterAplikasi::class, 'aplikasi_id', 'aplikasi_id');
    }

    public function aplikasis()
    {
        return $this->belongsToMany(MasterAplikasi::class, 'aplikasi_implementasi', 'implementasi_id', 'aplikasi_id');
    }

    public function picSakti()
    {
        return $this->belongsTo(User::class, 'pic_sakti_id', 'user_id');
    }

    public function checklists()
    {
        return $this->hasMany(ImplementasiChecklist::class, 'implementasi_id');
    }

    public function logs()
    {
        return $this->hasMany(ImplementasiLog::class, 'implementasi_id')->orderBy('created_at', 'desc');
    }

    public function followUps()
    {
        return $this->hasMany(ImplementasiFollowUp::class, 'implementasi_id')->orderBy('created_at', 'desc');
    }

    /**
     * Menghitung persentase progres secara otomatis berdasarkan jumlah checklist
     * yang berstatus 'Sudah Valid' lalu mengupdate kolom progres.
     */
    public function updateProgres()
    {
        $query = $this->checklists();
        
        $allDone = ['Sudah Valid', 'Selesai', 'Done', 'Migrasi Selesai'];
        
        $queryRunning = (clone $query)->where('kategori', 'like', 'Running%');
        $totalRunning = $queryRunning->count();

        $validCutoffStatuses = ['Cut-Off Valid', 'Cut-Off Diterima', 'Cut-Off Dijadwalkan'];
        $validGoLiveStatuses = ['Siap Go Live', 'Go-Live Selesai', 'Selesai', 'Done'];

        if ($totalRunning > 0) {
            // Bobot saat Running Monitoring checklist tersedia (Total = 100%)
            // 1. Bobot Kesiapan (Data Utama, Keuangan, Master, dll.) : 30%
            $queryKesiapan = (clone $query)->where('kategori', '!=', 'Migrasi')->where('kategori', 'not like', 'Running%');
            $totalKesiapan = $queryKesiapan->count();
            $doneKesiapan = (clone $queryKesiapan)->whereIn('status', $allDone)->count();
            $nilaiKesiapan = ($totalKesiapan > 0) ? ($doneKesiapan / $totalKesiapan) * 30 : 0;

            // 2. Bobot Migrasi (Kategori 'Migrasi') : 30%
            $queryMigrasi = (clone $query)->where('kategori', 'Migrasi');
            $totalMigrasi = $queryMigrasi->count();
            $doneMigrasi = (clone $queryMigrasi)->whereIn('status', $allDone)->count();
            $nilaiMigrasi = ($totalMigrasi > 0) ? ($doneMigrasi / $totalMigrasi) * 30 : 0;

            // 3. Bobot Cut-Off Date : 15% (Hanya jika status_cutoff Valid / Diterima / Dijadwalkan)
            $nilaiCutoff = in_array($this->status_cutoff, $validCutoffStatuses) ? 15 : 0;

            // 4. Bobot Running Monitoring (Kategori 'Running - ...') : 25%
            $doneRunning = (clone $queryRunning)->whereIn('status', $allDone)->count();
            $nilaiRunning = ($totalRunning > 0) ? ($doneRunning / $totalRunning) * 25 : 0;

            // --- Kalkulasi Total Progres ---
            $persentase = round($nilaiKesiapan + $nilaiMigrasi + $nilaiCutoff + $nilaiRunning, 2);
        } else {
            // Bobot default sebelum ada checklist Running Monitoring (Total = 100%)
            $queryKesiapan = (clone $query)->where('kategori', '!=', 'Migrasi');
            $totalKesiapan = $queryKesiapan->count();
            $doneKesiapan = (clone $queryKesiapan)->whereIn('status', $allDone)->count();
            $nilaiKesiapan = ($totalKesiapan > 0) ? ($doneKesiapan / $totalKesiapan) * 45 : 0;

            $queryMigrasi = (clone $query)->where('kategori', 'Migrasi');
            $totalMigrasi = $queryMigrasi->count();
            $doneMigrasi = (clone $queryMigrasi)->whereIn('status', $allDone)->count();
            $nilaiMigrasi = ($totalMigrasi > 0) ? ($doneMigrasi / $totalMigrasi) * 40 : 0;

            // Bobot Cut-Off Date : 15%
            $nilaiCutoff = in_array($this->status_cutoff, $validCutoffStatuses) ? 15 : 0;

            $persentase = round($nilaiKesiapan + $nilaiMigrasi + $nilaiCutoff, 2);
        }

        $updateData = ['progres' => $persentase];

        // Otomatis ubah Action Status (status_tindakan) dan Status Utama (status)
        if ($persentase >= 100) {
            if ($this->status_tindakan !== 'Selesai' && $this->status_tindakan !== 'Implementasi Selesai') {
                $updateData['status_tindakan'] = 'Selesai';
            }
            if ($this->status !== 'Implementasi Selesai') {
                $updateData['status'] = 'Implementasi Selesai';
            }
        } else {
            if ($this->status === 'Implementasi Selesai') {
                $updateData['status'] = 'Proses Pendampingan';
            }
        }

        $this->update($updateData);

        return $persentase;
    }
    public function checkAndSetGoLiveDate()
    {
        if ($this->target_go_live !== null) {
            return;
        }

        $allDone = ['Sudah Valid', 'Done', 'Selesai', 'Migrasi Selesai'];

        // 1. Data utama sudah tersedia (Kategori: Data Utama) & 6. User aplikasi (Kategori: Data Utama)
        $dataUtamaNotDone = $this->checklists()->where('kategori', 'Data Utama')->whereNotIn('status', $allDone)->count();

        // 2. Data cut-off sudah disepakati
        $cutOffSet = !empty($this->tanggal_cut_off);

        // 3. Migrasi selesai
        $migrasiNotDone = $this->checklists()->where('kategori', 'Migrasi')->whereNotIn('status', $allDone)->count();

        // 4. Koperasi sudah siap menjalankan transaksi (catatan_kesiapan terisi)
        $ready = !empty($this->catatan_kesiapan);

        // 5. PIC koperasi telah ditentukan (anggota_hadir atau pic_koperasi terisi)
        $picSet = !empty($this->anggota_hadir) || !empty($this->pic_koperasi);

        // 6. Jadwal pendampingan telah disiapkan (metode_pendampingan terisi)
        $jadwalSet = !empty($this->metode_pendampingan);

        if ($dataUtamaNotDone === 0 && $cutOffSet && $migrasiNotDone === 0 && $ready && $picSet && $jadwalSet) {
            $this->target_go_live = \Carbon\Carbon::today();
            $this->save();

            \App\Models\ImplementasiLog::create([
                'implementasi_id' => $this->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                'aktivitas' => 'Otomatis Set Tanggal Go-Live',
                'catatan' => 'Semua prasyarat telah terpenuhi, tanggal Go-Live otomatis diisi dengan tanggal hari ini.'
            ]);
        }
    }
}
