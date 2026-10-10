<?php

namespace App\Http\Controllers;

use App\Models\ImplementasiKoperasi;
use App\Models\ImplementasiChecklist;
use App\Models\ImplementasiLog;
use App\Models\Instansi;
use App\Models\MasterAplikasi;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImplementasiController extends Controller
{
    /**
     * Menampilkan data dashboard implementasi
     */
    public function index(Request $request)
    {
        $query = ImplementasiKoperasi::with(['instansi', 'aplikasi', 'aplikasis', 'picSakti'])
            ->orderBy('updated_at', 'desc');

        if (Auth::user()->role === UserRole::PELAPOR) {
            $query->where('instansi_id', Auth::user()->instansi_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_implementasi', 'like', "%{$search}%")
                  ->orWhereHas('instansi', function($q) use ($search) {
                      $q->where('nama_instansi', 'like', "%{$search}%");
                  });
            });
        }

        $implementasis = $query->get();

        $instansis = Instansi::orderBy('nama_instansi')->get();
        $aplikasis = MasterAplikasi::where('is_active', true)->orderBy('nama_aplikasi')->get();
        $usersSupport = User::whereIn('role', [UserRole::SUPPORT, UserRole::SUPERADMIN])->orderBy('nama')->get();

        return view('implementasi.index', compact('implementasis', 'instansis', 'aplikasis', 'usersSupport'));
    }

    /**
     * Menampilkan halaman detail implementasi (Tabs UI)
     */
    public function show($id)
    {
        $implementasi = ImplementasiKoperasi::with([
            'instansi', 
            'aplikasi', 
            'aplikasis',
            'picSakti', 
            'checklists' => function($q) {
                $q->orderBy('kategori', 'asc')->orderBy('id', 'asc');
            }, 
            'logs',
            'followUps.creator'
        ])->findOrFail($id);

        // Security check for Pelapor
        if (Auth::user()->role === UserRole::PELAPOR && $implementasi->instansi_id !== Auth::user()->instansi_id) {
            abort(403, 'Anda tidak memiliki akses ke data implementasi ini.');
        }

        // Auto-seed item Migrasi jika belum ada
        $hasMigrasi = $implementasi->checklists->where('kategori', 'Migrasi')->count() > 0;
        if (!$hasMigrasi) {
            $migrasiItems = [
                'Migrasi Data Anggota',
                'Migrasi Data Simpanan Modal',
                'Migrasi Data Simpanan Harian',
                'Migrasi Data Simpanan Berjangka',
                'Migrasi Data Simpanan Deposito',
                'Migrasi Data Pinjaman',
                'Migrasi Data Kode Pos',
                'Migrasi Data Perkiraan',
                'Migrasi Kelengkapan Data Anggota',
            ];
            foreach ($migrasiItems as $item) {
                $implementasi->checklists()->create([
                    'kategori' => 'Migrasi',
                    'nama_item' => $item,
                    'status' => 'Belum Dikirim'
                ]);
            }
            $implementasi->load(['checklists' => function($q) {
                $q->orderBy('kategori', 'asc')->orderBy('id', 'asc');
            }]);
        }

        // Auto-create initial log jika riwayat aktivitas masih kosong
        try {
            if (!$implementasi->logs || $implementasi->logs->count() === 0) {
                ImplementasiLog::create([
                    'implementasi_id' => $implementasi->id,
                    'user_id' => Auth::id(),
                    'aktivitas' => 'Data Implementasi Dibuat',
                    'catatan' => 'Implementasi didaftarkan ke sistem.'
                ]);
                $implementasi->load('logs');
            }
        } catch (\Exception $e) {
            \Log::error('Auto log error: ' . $e->getMessage());
        }

        return view('implementasi.show', compact('implementasi'));
    }

    /**
     * Menyimpan data implementasi baru
     */
    public function store(Request $request)
    {
        // Cek jika instansi_id berupa string teks (koperasi baru diketik manual)
        $instansiId = $request->instansi_id;
        if (!empty($instansiId) && !is_numeric($instansiId)) {
            $newInstansi = \App\Models\Instansi::create([
                'nama_instansi' => $instansiId,
                'jenis_instansi' => 'KSP', // Default
            ]);
            $request->merge(['instansi_id' => $newInstansi->instansi_id]);
        }

        $request->validate([
            'instansi_id' => 'required|exists:instansis,instansi_id',
            'aplikasi_id' => 'required|array',
            'aplikasi_id.*' => 'required|exists:master_aplikasis,aplikasi_id',
            'tanggal_pelatihan' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_pelatihan',
            'metode_pelatihan' => 'required|string',
            'berita_acara' => 'nullable|file|mimes:pdf|max:5120',
            'nama_trainer' => 'nullable|array',
            'nama_trainer.*' => 'nullable|string',
            'anggota_hadir' => 'required|array',
            'anggota_hadir.*' => 'required|string',
            'kontak_pic' => 'required|array',
            'kontak_pic.*' => 'required|string',
            'email_pic' => 'nullable|email',
            'catatan_pelatihan' => 'nullable|string',
            'target_go_live' => 'nullable|date',
            'waktu_go_live' => 'nullable|date_format:H:i',
            'tempat_go_live' => 'nullable|string',
            'status_go_live' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        // Generate Nomor Implementasi: IMP/SAKTI/YYYY/001
        $year = date('Y');
        $lastImpl = ImplementasiKoperasi::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $nextNumber = $lastImpl ? intval(substr($lastImpl->nomor_implementasi, -3)) + 1 : 1;
        $nomor_implementasi = 'IMP/SAKTI/' . $year . '/' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $anggotaList = [];
        if (is_array($request->anggota_hadir)) {
            foreach ($request->anggota_hadir as $idx => $nama) {
                $nama = trim($nama);
                if (empty($nama)) continue;
                $posisi = isset($request->posisi_anggota[$idx]) ? trim($request->posisi_anggota[$idx]) : '';
                $anggotaList[] = !empty($posisi) ? "$nama ($posisi)" : $nama;
            }
        }
        $anggotaHadirStr = implode(', ', $anggotaList);

        $tempatGoLive = $request->tempat_go_live;
        if ($tempatGoLive === 'Lokasi' && $request->filled('detail_lokasi')) {
            $tempatGoLive = trim($request->detail_lokasi);
        }

        $dataToSave = [
            'nomor_implementasi' => $nomor_implementasi,
            'instansi_id' => $request->instansi_id,
            'kantor_cabang' => $request->kantor_cabang,
            'aplikasi_id' => is_array($request->aplikasi_id) ? $request->aplikasi_id[0] : $request->aplikasi_id, // Backward compatibility
            'tanggal_pelatihan' => $request->tanggal_pelatihan,
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_pelatihan,
            'metode_pelatihan' => $request->metode_pelatihan,
            'berita_acara' => $request->hasFile('berita_acara') ? $request->file('berita_acara')->store('berita_acara', 'public') : null,
            'nama_trainer' => is_array($request->nama_trainer) ? implode(', ', array_filter($request->nama_trainer)) : null,
            'anggota_hadir' => $anggotaHadirStr,
            'kontak_pic' => is_array($request->kontak_pic) ? implode(', ', array_filter($request->kontak_pic)) : $request->kontak_pic,
            'email_pic' => $request->email_pic,
            'catatan_pelatihan' => $request->catatan_pelatihan,
            'tanggal_mulai_support' => $request->tanggal_mulai_support,
            'tanggal_selesai_support' => $request->tanggal_selesai_support,
            'durasi_support' => $request->durasi_support,
            'target_go_live' => $request->target_go_live,
            'waktu_go_live' => $request->waktu_go_live,
            'tempat_go_live' => $tempatGoLive,
            'status_go_live' => $request->status_go_live ?? 'Belum Siap Go Live',
            'status' => $request->status ?? 'Pelatihan Dijadwalkan',
            'tindakan_berikutnya' => 'Follow-Up Kesiapan Koperasi',
            'pic_tindakan' => $anggotaHadirStr ?: 'Tim Support',
        ];

        $implementasi = ImplementasiKoperasi::create($dataToSave);

        // Sync pivot table
        if (is_array($request->aplikasi_id)) {
            $implementasi->aplikasis()->sync($request->aplikasi_id);
        }

        // Auto-generate checklists
        $defaultChecklists = [
            // Readiness Checklists
            ['kategori' => 'Data Utama', 'nama_item' => 'Data anggota tersedia'],
            ['kategori' => 'Data Utama', 'nama_item' => 'Data user atau pengguna tersedia'],
            ['kategori' => 'Keuangan', 'nama_item' => 'Data simpanan tersedia'],
            ['kategori' => 'Keuangan', 'nama_item' => 'Data pinjaman tersedia'],
            ['kategori' => 'Keuangan', 'nama_item' => 'Data saldo awal tersedia'],
            ['kategori' => 'Keuangan', 'nama_item' => 'Data COA tersedia'],
            ['kategori' => 'Master', 'nama_item' => 'Data produk simpanan tersedia'],
            ['kategori' => 'Master', 'nama_item' => 'Data produk pinjaman tersedia'],
            
            // Migrasi Checklists
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Anggota'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Simpanan Modal'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Simpanan Harian'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Simpanan Berjangka'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Simpanan Deposito'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Pinjaman'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Kode Pos'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Data Perkiraan'],
            ['kategori' => 'Migrasi', 'nama_item' => 'Migrasi Kelengkapan Data Anggota'],
            
            // Running Monitoring Checklists
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Aplikasi dapat diakses/login dengan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Aplikasi dapat digunakan tanpa error'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Perpindahan/menu aplikasi berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Proses input data berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Proses penyimpanan transaksi berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Transaksi berhasil diproses dan tercatat'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Data/transaksi yang sudah disimpan dapat ditampilkan kembali'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Laporan dapat ditampilkan dengan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Cetak/export laporan berjalan normal'],
            ['kategori' => 'Running - Aplikasi', 'nama_item' => 'Tidak terdapat kendala yang menghambat operasional koperasi'],
            ['kategori' => 'Running - Penggunaan Aplikasi', 'nama_item' => 'Sakti sudah digunakan sebagai aplikasi operasional utama'],
            ['kategori' => 'Running - Penggunaan Aplikasi', 'nama_item' => 'Operator aktif menggunakan Sakti'],
            ['kategori' => 'Running - Penggunaan Aplikasi', 'nama_item' => 'Proses manual/Excel untuk transaksi utama sudah ditinggalkan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi anggota sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi simpanan sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi pinjaman sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi angsuran sudah berjalan'],
            ['kategori' => 'Running - Transaksi', 'nama_item' => 'Transaksi kas sudah berjalan'],
            ['kategori' => 'Running - Laporan', 'nama_item' => 'Laporan sudah digunakan oleh koperasi'],
            ['kategori' => 'Running - Laporan', 'nama_item' => 'Laporan Sakti menjadi acuan operasional'],
            ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator dapat melakukan transaksi tanpa bantuan Support'],
            ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator dapat melakukan pengecekan/koreksi sederhana sendiri'],
            ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator sudah memahami proses operasional Sakti'],
        ];

        foreach ($defaultChecklists as $chk) {
            ImplementasiChecklist::create([
                'implementasi_id' => $implementasi->id,
                'kategori' => $chk['kategori'],
                'nama_item' => $chk['nama_item'],
            ]);
        }

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Data Implementasi Dibuat',
            'catatan' => 'Pelatihan selesai, lanjut ke follow up kesiapan.'
        ]);

        return redirect()->route('implementasi.index')->with('success', __('messages.impl_added'));
    }

    /**
     * Method AJAX untuk update checklist dan catat log
     */
    public function updateChecklist(Request $request, $id)
    {
        if (Auth::user()->role === UserRole::PELAPOR) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'status' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $checklist = ImplementasiChecklist::findOrFail($id);
        
        $oldStatus = $checklist->status;
        $oldCatatan = $checklist->catatan;

        $checklist->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        $implementasi = $checklist->implementasi;

        // Mencatat log aktivitas
        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Update Checklist: ' . $checklist->nama_item,
            'data_sebelum' => ['status' => $oldStatus, 'catatan' => $oldCatatan],
            'data_sesudah' => ['status' => $checklist->status, 'catatan' => $checklist->catatan],
            'catatan' => null
        ]);

        // Hitung ulang progres
        $newProgres = $implementasi->updateProgres();
        
        // Cek syarat go-live otomatis
        $implementasi->checkAndSetGoLiveDate();

        // Siapkan data kondisi Go-Live untuk diupdate via JS
        $allDone = ['Sudah Valid', 'Done', 'Selesai', 'Migrasi Selesai'];
        $dataUtamaNotDone = $implementasi->checklists()->where('kategori', 'Data Utama')->whereNotIn('status', $allDone)->count();
        $migrasiNotDone = $implementasi->checklists()->where('kategori', 'Migrasi')->whereNotIn('status', $allDone)->count();
        
        $syarat = [
            'Pelatihan Selesai' => !empty($implementasi->tanggal_selesai) || !empty($implementasi->tanggal_pelatihan) || !empty($implementasi->berita_acara),
            'Data Utama & User Aplikasi Tersedia' => $dataUtamaNotDone === 0,
            'Data Cut-Off Disepakati' => !empty($implementasi->tanggal_cut_off),
            'Migrasi Selesai' => $migrasiNotDone === 0,
            'PIC Koperasi Ditentukan' => !empty($implementasi->anggota_hadir) || !empty($implementasi->pic_koperasi),
        ];
        
        $canGoLive = !in_array(false, $syarat, true);

        return response()->json([
            'success' => true,
            'message' => 'Item checklist berhasil diupdate',
            'new_progres' => $newProgres,
            'catatan' => $checklist->catatan,
            'syarat' => $syarat,
            'can_go_live' => $canGoLive
        ]);
    }

    /**
     * Menambah item checklist kustom
     */
    public function storeChecklist(Request $request, $id)
    {
        if (Auth::user()->role === UserRole::PELAPOR) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'nama_item' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
        ]);

        $implementasi = ImplementasiKoperasi::findOrFail($id);

        $checklist = $implementasi->checklists()->create([
            'nama_item' => $request->nama_item,
            'kategori' => $request->kategori ?? 'Lainnya',
            'status' => 'Belum Dikirim',
        ]);

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Tambah Item Checklist: ' . $checklist->nama_item,
            'data_sebelum' => null,
            'data_sesudah' => ['nama_item' => $checklist->nama_item],
            'catatan' => null
        ]);

        $newProgres = $implementasi->updateProgres();

        return response()->json([
            'success' => true,
            'message' => 'Item checklist berhasil ditambahkan',
            'new_progres' => $newProgres,
            'checklist' => $checklist
        ]);
    }

    /**
     * Menghapus item checklist
     */
    public function destroyChecklist($id)
    {
        if (Auth::user()->role === UserRole::PELAPOR) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $checklist = ImplementasiChecklist::findOrFail($id);
        $implementasi = $checklist->implementasi;

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Hapus Item Checklist: ' . $checklist->nama_item,
            'data_sebelum' => ['nama_item' => $checklist->nama_item],
            'data_sesudah' => null,
            'catatan' => null
        ]);

        $checklist->delete();

        $newProgres = $implementasi->updateProgres();

        return response()->json([
            'success' => true,
            'message' => 'Item checklist berhasil dihapus',
            'new_progres' => $newProgres
        ]);
    }

    /**
     * Menampilkan form edit implementasi
     */
    public function edit($id)
    {
        $implementasi = ImplementasiKoperasi::with(['aplikasis'])->findOrFail($id);
        
        // Security check for Pelapor
        if (Auth::user()->role === UserRole::PELAPOR && $implementasi->instansi_id !== Auth::user()->instansi_id) {
            abort(403, 'Anda tidak memiliki akses ke data implementasi ini.');
        }

        $instansis = Instansi::orderBy('nama_instansi')->get();
        $aplikasis = MasterAplikasi::where('is_active', true)->orderBy('nama_aplikasi')->get();
        $usersSupport = User::whereIn('role', [UserRole::SUPPORT, UserRole::SUPERADMIN])->orderBy('nama')->get();

        if (request()->ajax()) {
            return view('implementasi.partials.edit_form', compact('implementasi', 'instansis', 'aplikasis', 'usersSupport'));
        }

        return view('implementasi.edit', compact('implementasi', 'instansis', 'aplikasis', 'usersSupport'));
    }

    /**
     * Menyimpan pembaruan data implementasi
     */
    public function update(Request $request, $id)
    {
        $implementasi = ImplementasiKoperasi::findOrFail($id);

        if (Auth::user()->role === UserRole::PELAPOR) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        if ($request->filled('waktu_go_live')) {
            $request->merge(['waktu_go_live' => str_replace('.', ':', trim($request->waktu_go_live))]);
        }

        // Cek jika instansi_id berupa string teks (koperasi baru diketik manual)
        $instansiId = $request->instansi_id;
        if (!empty($instansiId) && !is_numeric($instansiId)) {
            $newInstansi = \App\Models\Instansi::create([
                'nama_instansi' => $instansiId,
                'jenis_instansi' => 'KSP', // Default
            ]);
            $request->merge(['instansi_id' => $newInstansi->instansi_id]);
        }

        $request->validate([
            'instansi_id' => 'required|exists:instansis,instansi_id',
            'aplikasi_id' => 'required|array',
            'aplikasi_id.*' => 'required|exists:master_aplikasis,aplikasi_id',
            'tanggal_pelatihan' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_pelatihan',
            'metode_pelatihan' => 'required|string',
            'berita_acara' => 'nullable|file|mimes:pdf|max:5120',
            'nama_trainer' => 'nullable|array',
            'nama_trainer.*' => 'nullable|string',
            'anggota_hadir' => 'required|array',
            'anggota_hadir.*' => 'required|string',
            'kontak_pic' => 'required|array',
            'kontak_pic.*' => 'required|string',
            'email_pic' => 'nullable|email',
            'catatan_pelatihan' => 'nullable|string',
            'target_go_live' => 'nullable|date',
            'waktu_go_live' => 'nullable|date_format:H:i',
            'tempat_go_live' => 'nullable|string',
            'status_go_live' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $berita_acara_path = $implementasi->berita_acara;
        if ($request->hasFile('berita_acara')) {
            if ($implementasi->berita_acara && Storage::disk('public')->exists($implementasi->berita_acara)) {
                Storage::disk('public')->delete($implementasi->berita_acara);
            }
            $berita_acara_path = $request->file('berita_acara')->store('berita_acara', 'public');
        }

        $anggotaList = [];
        if (is_array($request->anggota_hadir)) {
            foreach ($request->anggota_hadir as $idx => $nama) {
                $nama = trim($nama);
                if (empty($nama)) continue;
                $posisi = isset($request->posisi_anggota[$idx]) ? trim($request->posisi_anggota[$idx]) : '';
                $anggotaList[] = !empty($posisi) ? "$nama ($posisi)" : $nama;
            }
        }
        $anggotaHadirStr = implode(', ', $anggotaList);

        if ($request->status === 'Implementasi Selesai') {
            if ($implementasi->progres < 100) {
                return redirect()->back()->withInput()->with('error', __('messages.error_impl_selesai_progres'));
            }
            if ($implementasi->status_tindakan !== 'Implementasi Selesai') {
                return redirect()->back()->withInput()->with('error', __('messages.error_impl_selesai_tindakan'));
            }
        }

        $tempatGoLive = $request->tempat_go_live;
        if ($tempatGoLive === 'Lokasi' && $request->filled('detail_lokasi')) {
            $tempatGoLive = trim($request->detail_lokasi);
        }

        $dataToUpdate = [
            'instansi_id' => $request->instansi_id,
            'kantor_cabang' => $request->kantor_cabang,
            'aplikasi_id' => is_array($request->aplikasi_id) ? $request->aplikasi_id[0] : $request->aplikasi_id, // Backward compatibility
            'tanggal_pelatihan' => $request->tanggal_pelatihan,
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_pelatihan,
            'metode_pelatihan' => $request->metode_pelatihan,
            'berita_acara' => $berita_acara_path,
            'nama_trainer' => is_array($request->nama_trainer) ? implode(', ', array_filter($request->nama_trainer)) : null,
            'anggota_hadir' => $anggotaHadirStr,
            'kontak_pic' => is_array($request->kontak_pic) ? implode(', ', array_filter($request->kontak_pic)) : $request->kontak_pic,
            'email_pic' => $request->email_pic,
            'catatan_pelatihan' => $request->catatan_pelatihan,
            'tanggal_mulai_support' => $request->tanggal_mulai_support,
            'tanggal_selesai_support' => $request->tanggal_selesai_support,
            'durasi_support' => $request->durasi_support,
            'target_go_live' => $request->target_go_live,
            'waktu_go_live' => $request->waktu_go_live,
            'tempat_go_live' => $tempatGoLive,
            'status_go_live' => $request->status_go_live ?? 'Belum Siap Go Live',
            'status' => $request->status ?? $implementasi->status,
            'pic_tindakan' => $anggotaHadirStr ?: 'Tim Support',
        ];

        $implementasi->update($dataToUpdate);

        // Sync pivot table
        if (is_array($request->aplikasi_id)) {
            $implementasi->aplikasis()->sync($request->aplikasi_id);
        }

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Data Implementasi Diperbarui',
            'catatan' => 'Data implementasi diperbarui melalui form edit.'
        ]);

        $implementasi->updateProgres();
        $implementasi->checkAndSetGoLiveDate();

        return redirect()->route('implementasi.index')->with('success', __('messages.impl_updated'));
    }

    /**
     * Memperbarui data Go-Live
     */
    public function updateGoLive(Request $request, $id)
    {
        $implementasi = ImplementasiKoperasi::findOrFail($id);

        if ($request->filled('waktu_go_live')) {
            $request->merge(['waktu_go_live' => str_replace('.', ':', trim($request->waktu_go_live))]);
        }

        $request->validate([
            'target_go_live' => 'nullable|date',
            'waktu_go_live' => 'nullable|date_format:H:i',
            'tempat_go_live' => 'nullable|string',
            'status_go_live' => 'nullable|string',
            'metode_pendampingan' => 'nullable|string',
            'link_meeting' => 'nullable|string',
            'catatan_kesiapan' => 'nullable|string',
            'potensi_risiko' => 'nullable|string',
            'rencana_mitigasi' => 'nullable|string',
        ]);

        $tempatGoLive = $request->tempat_go_live;
        if ($tempatGoLive === 'Lokasi' && $request->filled('detail_lokasi')) {
            $tempatGoLive = trim($request->detail_lokasi);
        }

        $implementasi->update([
            'target_go_live' => $request->target_go_live,
            'waktu_go_live' => $request->waktu_go_live,
            'tempat_go_live' => $tempatGoLive,
            'status_go_live' => $request->status_go_live ?? 'Belum Siap Go Live',
            'metode_pendampingan' => $request->metode_pendampingan,
            'link_meeting' => $request->link_meeting,
            'catatan_kesiapan' => $request->catatan_kesiapan,
            'potensi_risiko' => $request->potensi_risiko,
            'rencana_mitigasi' => $request->rencana_mitigasi,
        ]);

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Detail Go-Live Diperbarui',
            'catatan' => 'Data Go-Live diperbarui melalui halaman detail.'
        ]);

        $implementasi->updateProgres();
        $implementasi->checkAndSetGoLiveDate();

        return redirect()->route('implementasi.show', $id)->with('success', __('messages.golive_updated'));
    }

    public function updateCutOff(Request $request, $id)
    {
        if (Auth::user()->role === UserRole::PELAPOR) {
            abort(403, 'Akses ditolak.');
        }

        $implementasi = ImplementasiKoperasi::findOrFail($id);

        $request->validate([
            'tanggal_cut_off' => 'nullable|date',
            'periode_transaksi_terakhir' => 'nullable|string',
            'saldo_terakhir' => 'nullable|string',
            'tanggal_tutup_buku' => 'nullable|date',
            'tanggal_mulai_aplikasi' => 'nullable|date',
            'pic_validasi' => 'nullable|string',
            'catatan_cutoff' => 'nullable|string',
            'status_cutoff' => 'nullable|string',
        ]);

        $implementasi->update([
            'tanggal_cut_off' => $request->tanggal_cut_off,
            'periode_transaksi_terakhir' => $request->periode_transaksi_terakhir,
            'saldo_terakhir' => $request->saldo_terakhir,
            'tanggal_tutup_buku' => $request->tanggal_tutup_buku,
            'tanggal_mulai_aplikasi' => $request->tanggal_mulai_aplikasi,
            'pic_validasi' => $request->pic_validasi,
            'catatan_cutoff' => $request->catatan_cutoff,
            'status_cutoff' => $request->status_cutoff ?? 'Menunggu Penentuan Cut-Off',
        ]);

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Detail Cut-Off Diperbarui',
            'catatan' => 'Data Cut-Off diperbarui melalui halaman detail.'
        ]);

        $implementasi->updateProgres();
        $implementasi->checkAndSetGoLiveDate();

        return redirect()->route('implementasi.show', $id)->with('success', __('messages.cutoff_updated'));
    }

    public function updateFollowUp(Request $request, $id)
    {
        $implementasi = ImplementasiKoperasi::findOrFail($id);

        $request->validate([
            'jenis_tindakan' => 'nullable|string',
            'tanggal_followup' => 'nullable|date',
            'tindakan_berikutnya' => 'nullable|string',
            'pic_tindakan' => 'nullable|string',
            'target_tanggal_tindakan' => 'nullable|date',
            'status_tindakan' => 'nullable|string',
            'hasil_komunikasi' => 'nullable|string',
            'kendala_koperasi' => 'nullable|string',
            'komitmen_koperasi' => 'nullable|string',
            'tanggal_followup_berikutnya' => 'nullable|date',
        ]);
        $newStatusTindakan = $request->status_tindakan ?? 'Persiapan Data';
        if ($newStatusTindakan === 'Implementasi Selesai' && $implementasi->progres < 100) {
            return redirect()->back()->withInput()->with('error', __('messages.error_impl_tindakan_selesai_syarat'));
        }

        if ($implementasi->status === 'Implementasi Selesai' && $newStatusTindakan !== 'Implementasi Selesai') {
            return redirect()->back()->withInput()->with('error', __('messages.error_impl_tindakan_utama_selesai'));
        }

        $implementasi->update([
            'jenis_tindakan' => $request->jenis_tindakan,
            'tanggal_followup' => $request->tanggal_followup,
            'tindakan_berikutnya' => $request->tindakan_berikutnya,
            'pic_tindakan' => $request->pic_tindakan,
            'target_tanggal_tindakan' => $request->target_tanggal_tindakan,
            'status_tindakan' => $request->status_tindakan ?? 'Persiapan Data',
            'hasil_komunikasi' => $request->hasil_komunikasi,
            'kendala_koperasi' => $request->kendala_koperasi,
            'komitmen_koperasi' => $request->komitmen_koperasi,
            'tanggal_followup_berikutnya' => $request->tanggal_followup_berikutnya,
        ]);

        ImplementasiLog::create([
            'implementasi_id' => $implementasi->id,
            'user_id' => Auth::id(),
            'aktivitas' => 'Aktivitas Follow-Up Diperbarui',
            'catatan' => 'Data Follow-Up diperbarui melalui halaman detail.'
        ]);

        \App\Models\ImplementasiFollowUp::create([
            'implementasi_id' => $implementasi->id,
            'jenis_tindakan' => $request->jenis_tindakan,
            'tanggal_followup' => $request->tanggal_followup,
            'tindakan_berikutnya' => $request->tindakan_berikutnya,
            'pic_tindakan' => $request->pic_tindakan,
            'target_tanggal_tindakan' => $request->target_tanggal_tindakan,
            'status_tindakan' => $request->status_tindakan ?? 'Persiapan Data',
            'hasil_komunikasi' => $request->hasil_komunikasi,
            'kendala_koperasi' => $request->kendala_koperasi,
            'komitmen_koperasi' => $request->komitmen_koperasi,
            'tanggal_followup_berikutnya' => $request->tanggal_followup_berikutnya,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('implementasi.show', $id)->with('success', __('messages.followup_updated'));
    }

    /**
     * Menghapus data implementasi
     */
    public function destroy($id)
    {
        if (Auth::user()->role === UserRole::PELAPOR) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $implementasi = ImplementasiKoperasi::findOrFail($id);
        
        // Hapus relasi pivot aplikasi jika ada
        $implementasi->aplikasis()->detach();
        
        // Hapus data (relasi checklist dan log otomatis terhapus karena foreign key onDelete cascade)
        $implementasi->delete();

        return redirect()->route('implementasi.index')->with('success', __('messages.impl_deleted'));
    }
}
