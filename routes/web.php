<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\UserVerificationController;
use App\Http\Middleware\IsPelapor;
use App\Http\Middleware\IsSupport;
use App\Http\Middleware\IsSuperAdmin;

Route::get('/', function () {
    return redirect('/login');
});

// Route download template via PHP (bypass InfinityFree static file blocking)
Route::get('/download-template/{filename}', function ($filename) {
    $path = public_path('templates/' . $filename);
    if (!file_exists($path)) {
        abort(404, 'File template tidak ditemukan.');
    }
    return response()->download($path, $filename);
})->where('filename', '.*')->name('download.template');

// ROUTE SEMENTARA UNTUK RESET PASSWORD ADMIN
Route::get('/reset-admin-password', function () {
    $user = \App\Models\User::where('email', 'support@skk.co.id')->first();
    if ($user) {
        $user->password = \Illuminate\Support\Facades\Hash::make('password123');
        $user->save();
        return "Berhasil mereset password support@skk.co.id menjadi: <b>password123</b><br><a href='/login'>Kembali ke Login</a>";
    }
    return "User tidak ditemukan.";
});

// ROUTE SEMENTARA UNTUK GENERATE RUNNING CHECKLIST (InfinityFree)
Route::get('/generate-running-checklists', function () {
    $implementasis = \App\Models\ImplementasiKoperasi::all();
    $runningItems = [
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
        ['kategori' => 'Running - Kemandirian', 'nama_item' => 'Operator sudah memahami proses operasional Sakti']
    ];
    
    // Auto rename SAS to Sakti for existing DB records
    $renamedCount = 0;
    \App\Models\ImplementasiChecklist::where('nama_item', 'like', '%SAS%')->get()->each(function($chk) use (&$renamedCount) {
        $chk->update([
            'nama_item' => str_replace('SAS', 'Sakti', $chk->nama_item)
        ]);
        $renamedCount++;
    });

    $totalAdded = 0;
    foreach($implementasis as $impl) {
        foreach($runningItems as $item) {
            $exists = $impl->checklists()
                ->where('kategori', $item['kategori'])
                ->where('nama_item', $item['nama_item'])
                ->exists();

            if (!$exists) {
                $impl->checklists()->create([
                    'kategori' => $item['kategori'],
                    'nama_item' => $item['nama_item'],
                    'status' => 'Belum Dikirim',
                ]);
                $totalAdded++;
            }
        }
        $impl->updateProgres();
    }
    return "Selesai! Berhasil menambahkan {$totalAdded} item baru, memperbarui {$renamedCount} teks SAS menjadi Sakti, dan menghitung ulang seluruh persentase progres!<br><a href='/'>Kembali ke Aplikasi</a>";
});

// ROUTE SEMENTARA UNTUK MIGRASI DATABASE (InfinityFree)
Route::get('/migrate-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return "<h3>Migrasi Berhasil!</h3><pre>" . $output . "</pre><br><a href='/'>Kembali ke Aplikasi</a>";
    } catch (\Exception $e) {
        return "<h3>Gagal Migrasi!</h3><pre>" . $e->getMessage() . "</pre>";
    }
});

// ROUTE SEMENTARA UNTUK MIGRASI & FORCE CLEAR CACHE (InfinityFree)
Route::get('/run-migrations', function () {
    $message = "<h3>🔧 Server Diagnostik & Clear Cache</h3>";

    // 1. Diagnostik: Cek versi file routes/web.php di server
    $routeFile = base_path('routes/web.php');
    $message .= "<b>📄 routes/web.php</b><br>";
    $message .= "Ukuran: " . filesize($routeFile) . " bytes<br>";
    $message .= "Terakhir diubah: " . date('Y-m-d H:i:s', filemtime($routeFile)) . "<br>";
    
    // Cek apakah file routes/web.php mengandung kata 'koperasi.update'
    $routeContent = file_get_contents($routeFile);
    if (str_contains($routeContent, "master-data.koperasi.update")) {
        $message .= "✅ Route koperasi.update <b>DITEMUKAN</b> di file routes/web.php<br>";
    } else {
        $message .= "❌ Route koperasi.update <b>TIDAK ADA</b> di file routes/web.php! <b>File belum terupload!</b><br>";
    }

    // Cek versi file master-data.blade.php
    $viewFile = resource_path('views/support/master-data.blade.php');
    if (file_exists($viewFile)) {
        $message .= "<br><b>📄 master-data.blade.php</b><br>";
        $message .= "Ukuran: " . filesize($viewFile) . " bytes<br>";
        $message .= "Terakhir diubah: " . date('Y-m-d H:i:s', filemtime($viewFile)) . "<br>";
    }

    // Cek AppServiceProvider
    $aspFile = app_path('Providers/AppServiceProvider.php');
    if (file_exists($aspFile)) {
        $aspContent = file_get_contents($aspFile);
        $message .= "<br><b>📄 AppServiceProvider.php</b><br>";
        $message .= "Ukuran: " . filesize($aspFile) . " bytes<br>";
        if (str_contains($aspContent, "forceScheme")) {
            $message .= "✅ forceScheme('https') <b>ADA</b><br>";
        } else {
            $message .= "❌ forceScheme('https') <b>BELUM ADA</b>! File belum terupload!<br>";
        }
    }

    $message .= "<hr>";

    // 2. Pembuatan Tabel implementasi_followups Secara Langsung
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('implementasi_followups')) {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            \Illuminate\Support\Facades\Schema::create('implementasi_followups', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('implementasi_id');
                $table->date('tanggal_followup')->nullable();
                $table->date('tanggal_followup_berikutnya')->nullable();
                $table->date('target_tanggal_tindakan')->nullable();
                $table->string('jenis_tindakan')->nullable();
                $table->string('pic_tindakan')->nullable();
                $table->string('status_tindakan')->nullable();
                $table->text('hasil_komunikasi')->nullable();
                $table->text('kendala_koperasi')->nullable();
                $table->text('komitmen_koperasi')->nullable();
                $table->text('tindakan_berikutnya')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('implementasi_id')->references('id')->on('implementasi_koperasi')->onDelete('cascade');
                $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            });
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            $message .= "✅ Tabel 'implementasi_followups' BERHASIL DIBUAT!<br>";
        } else {
            $message .= "ℹ️ Tabel 'implementasi_followups' sudah ada.<br>";
        }
    } catch (\Exception $e) {
        $message .= "❌ Gagal membuat tabel implementasi_followups: " . $e->getMessage() . "<br>";
    }

    // 2b. Pembuatan Kolom ebook & kantor_cabang Secara Langsung
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('master_aplikasis') && !\Illuminate\Support\Facades\Schema::hasColumn('master_aplikasis', 'ebook')) {
            \Illuminate\Support\Facades\Schema::table('master_aplikasis', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->text('ebook')->nullable();
            });
            $message .= "✅ Kolom 'ebook' BERHASIL DITAMBAHKAN ke master_aplikasis!<br>";
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('implementasi_koperasi') && !\Illuminate\Support\Facades\Schema::hasColumn('implementasi_koperasi', 'kantor_cabang')) {
            \Illuminate\Support\Facades\Schema::table('implementasi_koperasi', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('kantor_cabang')->nullable();
            });
            $message .= "✅ Kolom 'kantor_cabang' BERHASIL DITAMBAHKAN ke implementasi_koperasi!<br>";
        }
    } catch (\Exception $e) {
        $message .= "⚠️ Gagal menambah kolom: " . $e->getMessage() . "<br>";
    }

    // 3. Jalankan Migrasi Sisa
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $message .= "✅ Migrasi Sisa Berhasil!<br>";
    } catch (\Exception $e) {
        $message .= "⚠️ Migrasi Sisa: " . $e->getMessage() . "<br>";
    }

    // 3. Force Clear Cache
    try {
        $cacheFiles = glob(base_path('bootstrap/cache/*.php'));
        foreach ($cacheFiles as $file) {
            @unlink($file);
        }

        $viewFiles = glob(storage_path('framework/views/*.php'));
        $viewCount = count($viewFiles);
        foreach ($viewFiles as $file) {
            @unlink($file);
        }

        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $message .= "✅ Force Clear Cache Berhasil! ($viewCount view cache files dihapus)<br>";
    } catch (\Exception $e) {
        $message .= "❌ Gagal Clear Cache: " . $e->getMessage() . "<br>";
    }

    // 4. Cek route terdaftar
    $message .= "<hr><b>🔍 Cek Route Terdaftar:</b><br>";
    $routeCollection = Route::getRoutes();
    $checkRoutes = [
        'support.master-data.koperasi.update',
        'support.master-data.aplikasi.update',
        'support.master-data.kategori.update',
        'support.tickets.update',
    ];
    foreach ($checkRoutes as $routeName) {
        try {
            $r = $routeCollection->getByName($routeName);
            if ($r) {
                $message .= "✅ $routeName → " . implode('|', $r->methods()) . " " . $r->uri() . "<br>";
            } else {
                $message .= "❌ $routeName → TIDAK TERDAFTAR<br>";
            }
        } catch (\Exception $e) {
            $message .= "❌ $routeName → ERROR: " . $e->getMessage() . "<br>";
        }
    }

    return $message;
});

Route::get('/run-migrate-cabang', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('implementasi_koperasi', 'kantor_cabang')) {
            \Illuminate\Support\Facades\Schema::table('implementasi_koperasi', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('kantor_cabang')->nullable()->after('instansi_id');
            });
            return "✅ BERHASIL! Kolom 'kantor_cabang' telah sukses ditambahkan ke tabel database!";
        }
        return "ℹ️ Kolom 'kantor_cabang' SUDAH ADA di tabel database Anda.";
    } catch (\Exception $e) {
        return "❌ Error: " . $e->getMessage();
    }
});

Route::get('/check-templates', function() {
    $message = "<h3>📁 Cek Folder Templates di Server</h3>";
    
    $paths = [
        'public_path("templates")' => public_path('templates'),
        'public_path("../../templates")' => public_path('../../templates'),
        'base_path("../templates")' => base_path('../templates'),
        'storage_path("app/public/templates")' => storage_path('app/public/templates'),
    ];

    foreach ($paths as $name => $path) {
        $message .= "<b>$name</b><br> Path Asli: $path<br>";
        if (is_dir($path)) {
            $files = scandir($path);
            $message .= "✅ Folder ditemukan! Isi file:<br><ul>";
            foreach ($files as $f) {
                if ($f !== '.' && $f !== '..') {
                    $size = is_file("$path/$f") ? round(filesize("$path/$f") / 1024, 2) . " KB" : 'DIR';
                    $message .= "<li>$f ($size)</li>";
                }
            }
            $message .= "</ul>";
        } else {
            $message .= "❌ Folder tidak ditemukan!<br><br>";
        }
    }
    
    return $message;
});

Route::get('/download-template/{filename}', function($filename) {
    // decode once more just in case
    $filename = urldecode($filename);
    
    // try to find it in public/templates first
    $path = public_path('templates/' . $filename);
    if (file_exists($path)) {
        return response()->download($path);
    }
    
    // if not found, try to look at ../templates (in case it's in the htdocs/templates)
    $altPath = public_path('../../templates/' . $filename);
    if (file_exists($altPath)) {
        return response()->download($altPath);
    }
    
    // Try one directory up (if user uploaded to htdocs/templates and public is inside htdocs/sistem/public)
    $basePath = base_path('../templates/' . $filename);
    if (file_exists($basePath)) {
        return response()->download($basePath);
    }
    
    // Fallback: look directly in storage if it's there
    if (Storage::disk('public')->exists('templates/' . $filename)) {
        return Storage::disk('public')->download('templates/' . $filename);
    }

    return "<h2>❌ ERROR: File Template Tidak Ditemukan!</h2>
            <p>Sistem mencari file bernama: <b>" . htmlspecialchars($filename) . "</b></p>
            <p>Namun file tersebut tidak ada di folder manapun di server. Silakan cek nama filenya apakah sama persis (huruf besar/kecil berpengaruh) dan pastikan sudah diupload.</p>
            <p><a href='/check-templates'>Klik di sini untuk melihat daftar file yang ada di server</a></p>";
})->where('filename', '.*')->name('download.template');


// ROUTE SEMENTARA: Hapus Aplikasi Duplikat
Route::get('/cleanup-duplicates', function () {
    try {
        $duplicates = \Illuminate\Support\Facades\DB::select("
            SELECT nama_aplikasi, MIN(aplikasi_id) as min_id
            FROM master_aplikasis
            GROUP BY nama_aplikasi
            HAVING COUNT(aplikasi_id) > 1
        ");
        
        $deleted = 0;
        foreach ($duplicates as $dup) {
            $deleted += \Illuminate\Support\Facades\DB::table('master_aplikasis')
                ->where('nama_aplikasi', $dup->nama_aplikasi)
                ->where('aplikasi_id', '>', $dup->min_id)
                ->delete();
        }
        
        return "Berhasil menghapus {$deleted} data aplikasi yang duplikat. Silakan kembali ke halaman sebelumnya.";
    } catch (\Exception $e) {
        return "Terjadi Kesalahan: " . $e->getMessage();
    }
});

// ROUTE SEMENTARA: Fix kolom cutoff yang belum ada
Route::get('/fix-cutoff-columns', function () {
    $results = [];
    $columns = [
        'periode_transaksi_terakhir' => "ALTER TABLE implementasi_koperasi ADD COLUMN periode_transaksi_terakhir VARCHAR(255) NULL",
        'saldo_terakhir' => "ALTER TABLE implementasi_koperasi ADD COLUMN saldo_terakhir VARCHAR(255) NULL",
        'tanggal_tutup_buku' => "ALTER TABLE implementasi_koperasi ADD COLUMN tanggal_tutup_buku DATE NULL",
        'tanggal_mulai_aplikasi' => "ALTER TABLE implementasi_koperasi ADD COLUMN tanggal_mulai_aplikasi DATE NULL",
        'pic_validasi' => "ALTER TABLE implementasi_koperasi ADD COLUMN pic_validasi VARCHAR(255) NULL",
        'catatan_cutoff' => "ALTER TABLE implementasi_koperasi ADD COLUMN catatan_cutoff TEXT NULL",
        'status_cutoff' => "ALTER TABLE implementasi_koperasi ADD COLUMN status_cutoff VARCHAR(255) NULL DEFAULT 'Menunggu Penentuan Cut-Off'",
        'tanggal_followup' => "ALTER TABLE implementasi_koperasi ADD COLUMN tanggal_followup DATE NULL",
        'hasil_komunikasi' => "ALTER TABLE implementasi_koperasi ADD COLUMN hasil_komunikasi TEXT NULL",
        'kendala_koperasi' => "ALTER TABLE implementasi_koperasi ADD COLUMN kendala_koperasi TEXT NULL",
        'komitmen_koperasi' => "ALTER TABLE implementasi_koperasi ADD COLUMN komitmen_koperasi TEXT NULL",
        'tanggal_followup_berikutnya' => "ALTER TABLE implementasi_koperasi ADD COLUMN tanggal_followup_berikutnya DATE NULL",
    ];
    foreach ($columns as $col => $sql) {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('implementasi_koperasi', $col)) {
            \Illuminate\Support\Facades\DB::statement($sql);
            $results[] = "Kolom '{$col}' pada implementasi_koperasi berhasil ditambahkan.";
        } else {
            $results[] = "Kolom '{$col}' pada implementasi_koperasi sudah ada (skip).";
        }
    }

    if (!\Illuminate\Support\Facades\Schema::hasColumn('tickets', 'template_laporan')) {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE tickets ADD COLUMN template_laporan VARCHAR(255) NULL");
        $results[] = "Kolom 'template_laporan' pada tickets berhasil ditambahkan.";
    } else {
        $results[] = "Kolom 'template_laporan' pada tickets sudah ada (skip).";
    }

    if (!\Illuminate\Support\Facades\Schema::hasColumn('master_aplikasis', 'username')) {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE master_aplikasis ADD COLUMN username VARCHAR(255) NULL");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE master_aplikasis ADD COLUMN password VARCHAR(255) NULL");
        $results[] = "Kolom 'username' dan 'password' pada master_aplikasis berhasil ditambahkan.";
    } else {
        $results[] = "Kolom 'username' dan 'password' pada master_aplikasis sudah ada (skip).";
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $results[] = "\nCache aplikasi berhasil dibersihkan (optimize:clear).";
    } catch (\Exception $e) {
        $results[] = "\nWarning Clear Cache: " . $e->getMessage();
    }

    return "<pre>" . implode("\n", $results) . "\n\nSelesai! Silakan kembali ke halaman aplikasi.</pre>";
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'storeRegister']);
Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Lupa Kata Sandi & Reset Password (via Email SMTP)
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/profil-instansi', function () {
        $aplikasis = \App\Models\MasterAplikasi::where('is_active', true)->get();
        return view('profil-instansi', compact('aplikasis'));
    })->name('profil.instansi');
    
    Route::put('/profil-instansi', [AuthController::class, 'updateInstansi'])->name('profil.instansi.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('profil.password.update');

    Route::get('/pengaturan', [AuthController::class, 'pengaturan'])->name('pengaturan');
    Route::put('/pengaturan', [AuthController::class, 'updatePengaturan'])->name('pengaturan.update');
    Route::post('/pengaturan/bahasa', [AuthController::class, 'updateLanguage'])->name('pengaturan.bahasa');

    // Notifications (accessible by all authenticated users: Pelapor & Support)
    Route::get('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // Implementasi & Go-Live Koperasi
    Route::middleware([\App\Http\Middleware\IsSupport::class])->prefix('implementasi')->name('implementasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ImplementasiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\ImplementasiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\ImplementasiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\ImplementasiController::class, 'update'])->name('update');
        Route::put('/{id}/golive', [\App\Http\Controllers\ImplementasiController::class, 'updateGoLive'])->name('golive.update');
        Route::put('/{id}/cutoff', [\App\Http\Controllers\ImplementasiController::class, 'updateCutOff'])->name('cutoff.update');
        Route::put('/{id}/followup', [\App\Http\Controllers\ImplementasiController::class, 'updateFollowUp'])->name('followup.update');
        Route::get('/{id}', [\App\Http\Controllers\ImplementasiController::class, 'show'])->name('show');
        Route::delete('/{id}', [\App\Http\Controllers\ImplementasiController::class, 'destroy'])->name('destroy');
        Route::put('/checklist/{id}', [\App\Http\Controllers\ImplementasiController::class, 'updateChecklist'])->name('checklist.update');
        Route::post('/{id}/checklist', [\App\Http\Controllers\ImplementasiController::class, 'storeChecklist'])->name('checklist.store');
        Route::delete('/checklist/{id}', [\App\Http\Controllers\ImplementasiController::class, 'destroyChecklist'])->name('checklist.destroy');
    });
});

// Akses Pelapor
Route::middleware(['auth', IsPelapor::class])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('/dashboard', [TicketController::class, 'pelaporDashboard'])->name('dashboard');
    Route::get('/riwayat', [TicketController::class, 'pelaporRiwayat'])->name('riwayat');
    Route::get('/tickets/{ticket}/dokumen', [TicketController::class, 'dokumen'])->name('tickets.dokumen');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::put('/tickets/{ticket}/edit', [TicketController::class, 'updatePelapor'])->name('tickets.update_pelapor');
    Route::post('/tickets/{ticket}/upload-balasan', [TicketController::class, 'uploadBalasan'])->name('tickets.upload_balasan');
    Route::delete('/tickets/{ticket}/balasan/{index}', [TicketController::class, 'deleteBalasan'])->name('tickets.delete_balasan');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::view('/bantuan', 'pelapor.bantuan')->name('bantuan');
    Route::get('/faq/search', [FaqController::class, 'searchPublic'])->name('faq.search');

});

// Akses Support
Route::middleware(['auth', IsSupport::class])->prefix('support')->name('support.')->group(function () {
    Route::get('/dashboard', [TicketController::class, 'supportDashboard'])->name('dashboard');
    Route::get('/prioritas', [TicketController::class, 'prioritas'])->name('prioritas');
    Route::get('/tickets/{ticket}', function () {
        return redirect()->route('support.dashboard');
    });
    Route::get('/tickets/{ticket}/dokumen', [TicketController::class, 'dokumen'])->name('tickets.dokumen');
    Route::match(['PUT', 'POST'], '/tickets/{ticket}', [TicketController::class, 'updateSupport'])->name('tickets.update');
    
    // Reporting
    Route::get('/recap', [ReportController::class, 'index'])->name('recap');
    Route::get('/recap/diagram', [ReportController::class, 'diagram'])->name('recap.diagram');
    Route::get('/recap/table', [ReportController::class, 'table'])->name('recap.table');
    Route::get('/recap/history-pic', [ReportController::class, 'historyPic'])->name('recap.history-pic');
    Route::get('/recap/template-surat', [ReportController::class, 'templateSurat'])->name('recap.template-surat');
    Route::get('/recap/detail', [ReportController::class, 'detail'])->name('recap.detail');
    
    // Master Data
    Route::get('/master-data', [MasterDataController::class, 'index'])->name('master-data.index');
    Route::get('/master-data/export', [MasterDataController::class, 'export'])->name('master-data.export');
    Route::post('/master-data/aplikasi', [MasterDataController::class, 'storeAplikasi'])->name('master-data.aplikasi.store');
    Route::match(['PUT', 'POST'], '/master-data/aplikasi/{id}', [MasterDataController::class, 'updateAplikasi'])->name('master-data.aplikasi.update');
    Route::delete('/master-data/aplikasi/{id}/ebook/bulk-delete', [MasterDataController::class, 'bulkDeleteEbook'])->name('master-data.aplikasi.ebook.bulk-destroy');
    Route::delete('/master-data/aplikasi/{id}/ebook/{index}', [MasterDataController::class, 'deleteEbook'])->name('master-data.aplikasi.ebook.destroy');
    Route::delete('/master-data/aplikasi/{id}', [MasterDataController::class, 'destroyAplikasi'])->name('master-data.aplikasi.destroy');
    Route::post('/master-data/kategori', [MasterDataController::class, 'storeKategori'])->name('master-data.kategori.store');
    Route::match(['PUT', 'POST'], '/master-data/kategori/{id}', [MasterDataController::class, 'updateKategori'])->name('master-data.kategori.update');
    Route::delete('/master-data/kategori/{id}', [MasterDataController::class, 'destroyKategori'])->name('master-data.kategori.destroy');
    Route::post('/master-data/koperasi/ajax-store', [MasterDataController::class, 'storeKoperasiAjax'])->name('master-data.koperasi.ajax-store');
    Route::match(['PUT', 'POST'], '/master-data/koperasi/{id}', [MasterDataController::class, 'updateKoperasi'])->name('master-data.koperasi.update');
    Route::delete('/master-data/koperasi/{id}', [MasterDataController::class, 'destroyKoperasi'])->name('master-data.koperasi.destroy');

    // Verifikasi Akun Pelapor
    Route::put('/users/{user}/verify', [UserVerificationController::class, 'verify'])->name('users.verify');
    Route::delete('/users/{user}/reject', [UserVerificationController::class, 'reject'])->name('users.reject');
    
    // Profil Saya (Support)
    Route::get('/profil-saya', [AuthController::class, 'showProfilSaya'])->name('profil.saya');
    Route::put('/profil-saya', [AuthController::class, 'updateProfilSaya'])->name('profil.saya.update');

    // Profil Lengkap Pelapor
    Route::get('/pelapor/{user}/profil', [AuthController::class, 'showPelaporProfile'])->name('pelapor.profile');

    // FAQ CRUD (Master Data)
    Route::post('/master-data/faq', [FaqController::class, 'store'])->name('master-data.faq.store');
    Route::put('/master-data/faq/{id}', [FaqController::class, 'update'])->name('master-data.faq.update');
    Route::delete('/master-data/faq/{id}', [FaqController::class, 'destroy'])->name('master-data.faq.destroy');

    // FAQ API (JSON untuk modal insert di Support)
    Route::get('/faq/list', [FaqController::class, 'allForSupport'])->name('faq.list');
});

use App\Http\Controllers\SuperAdminController;

// Akses Super Admin (Eksklusif)
Route::middleware(['auth', IsSuperAdmin::class])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/pengguna', [SuperAdminController::class, 'pengguna'])->name('pengguna');
    Route::post('/pengguna', [SuperAdminController::class, 'storePengguna'])->name('pengguna.store');
    Route::put('/pengguna/{user}', [SuperAdminController::class, 'updatePengguna'])->name('pengguna.update');
    Route::delete('/pengguna/{user}', [SuperAdminController::class, 'destroyPengguna'])->name('pengguna.destroy');
});
