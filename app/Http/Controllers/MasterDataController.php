<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterAplikasi;
use App\Models\MasterKategori;
use App\Models\Instansi;
use App\Models\User;
use App\Models\Faq;
use App\Enums\UserRole;
use App\Enums\TicketStatus;

class MasterDataController extends Controller
{
    public function index()
    {
        $aplikasis = MasterAplikasi::all();
        $kategoris = MasterKategori::all();
        $instansis = Instansi::withCount([
            'users',
            'tickets as active_tickets_count' => function ($query) {
                $query->where('status', '!=', TicketStatus::DONE->value);
            },
            'tickets as total_tickets_count'
        ])->with('users')->get();
        $supportPics = User::where('role', UserRole::SUPPORT)->get();
        $statuses = TicketStatus::cases();
        $faqs = Faq::with('kategori')->orderBy('kategori_id')->get();

        return view('support.master-data', compact('aplikasis', 'kategoris', 'instansis', 'supportPics', 'statuses', 'faqs'));
    }
    
    public function storeAplikasi(Request $request)
    {
        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'ebooks.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'link' => 'nullable|url',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $ebookPaths = [];
        if ($request->hasFile('ebooks')) {
            foreach ($request->file('ebooks') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $originalName);
                $ebookPaths[] = $file->storeAs('ebooks', $filename, 'public');
            }
        }

        MasterAplikasi::create([
            'nama_aplikasi' => $request->nama_aplikasi,
            'deskripsi' => $request->deskripsi,
            'ebook' => $ebookPaths,
            'link' => $request->link,
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => true
        ]);

        return back()->with('success', __('messages.app_added'));
    }

    public function updateAplikasi(Request $request, $id)
    {
        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'ebooks.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'link' => 'nullable|url',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $aplikasi = MasterAplikasi::findOrFail($id);

        $ebookPaths = is_array($aplikasi->ebook) ? $aplikasi->ebook : [];
        if ($request->hasFile('ebooks')) {
            foreach ($request->file('ebooks') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $originalName);
                $ebookPaths[] = $file->storeAs('ebooks', $filename, 'public');
            }
        }

        $aplikasi->update([
            'nama_aplikasi' => $request->nama_aplikasi,
            'deskripsi' => $request->deskripsi,
            'ebook' => $ebookPaths,
            'link' => $request->link,
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('messages.app_updated'));
    }

    public function destroyAplikasi($id)
    {
        $aplikasi = MasterAplikasi::findOrFail($id);

        if ($aplikasi->tickets()->count() > 0) {
            return back()->with('error', __('messages.app_cannot_delete_has_tickets'));
        }

        if (is_array($aplikasi->ebook)) {
            foreach ($aplikasi->ebook as $path) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            }
        }

        $aplikasi->delete();

        return back()->with('success', __('messages.app_deleted'));
    }

    public function bulkDeleteEbook(Request $request, $id)
    {
        $request->validate([
            'delete_ebook_indices' => 'required|array',
            'delete_ebook_indices.*' => 'integer'
        ]);

        $aplikasi = MasterAplikasi::findOrFail($id);
        $ebooks = is_array($aplikasi->ebook) ? $aplikasi->ebook : [];
        
        $indices = $request->delete_ebook_indices;
        rsort($indices);

        foreach ($indices as $index) {
            if (isset($ebooks[$index])) {
                $path = $ebooks[$index];
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
                unset($ebooks[$index]);
            }
        }

        $aplikasi->update(['ebook' => array_values($ebooks)]);
        
        return back()->with('success', __('messages.success_hapus_ebook_pilihan'));
    }

    public function deleteEbook($id, $index)
    {
        $aplikasi = MasterAplikasi::findOrFail($id);
        $ebooks = is_array($aplikasi->ebook) ? $aplikasi->ebook : [];

        if (isset($ebooks[$index])) {
            $path = $ebooks[$index];
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
            unset($ebooks[$index]);
            $aplikasi->update(['ebook' => array_values($ebooks)]);
            return back()->with('success', __('messages.success_hapus_ebook'));
        }

        return back()->with('error', __('messages.error_ebook_tidak_ditemukan'));
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255'
        ]);

        MasterKategori::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return back()->with('success', __('messages.cat_added'));
    }

    public function updateKategori(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255'
        ]);

        $kategori = MasterKategori::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return back()->with('success', __('messages.cat_updated'));
    }

    public function destroyKategori($id)
    {
        $kategori = MasterKategori::findOrFail($id);

        if ($kategori->tickets()->count() > 0) {
            return back()->with('error', __('messages.cat_cannot_delete_has_tickets'));
        }

        $kategori->delete();

        return back()->with('success', __('messages.cat_deleted'));
    }

    public function updateKoperasi(Request $request, $id)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:255',
        ]);

        $instansi = Instansi::findOrFail($id);
        $instansi->update([
            'nama_instansi' => $request->nama_instansi,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
        ]);

        return back()->with('success', __('messages.success_update_koperasi'));
    }

    public function storeKoperasiAjax(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $namaClean = trim($request->nama_instansi);

        // Prevent duplicates (Cegah data ganda)
        $existing = Instansi::whereRaw('LOWER(nama_instansi) = ?', [mb_strtolower($namaClean)])->first();
        if ($existing) {
            $updateData = array_filter([
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
            ]);
            if (!empty($updateData)) {
                $existing->update($updateData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Koperasi sudah terdaftar dan berhasil dipilih.',
                'instansi' => [
                    'instansi_id' => $existing->instansi_id,
                    'nama_instansi' => $existing->nama_instansi,
                    'alamat' => $existing->alamat,
                    'no_telp' => $existing->no_telp,
                ]
            ]);
        }

        $instansi = Instansi::create([
            'nama_instansi' => $namaClean,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koperasi berhasil ditambahkan.',
            'instansi' => [
                'instansi_id' => $instansi->instansi_id,
                'nama_instansi' => $instansi->nama_instansi,
                'alamat' => $instansi->alamat,
                'no_telp' => $instansi->no_telp,
            ]
        ]);
    }

    public function destroyKoperasi($id)
    {
        $instansi = Instansi::findOrFail($id);

        if ($instansi->users()->count() > 0) {
            return back()->with('error', __('messages.error_hapus_koperasi_akun'));
        }

        $instansi->delete();

        return back()->with('success', __('messages.success_hapus_koperasi'));
    }

    public function export()
    {
        $aplikasis = MasterAplikasi::all();
        $kategoris = MasterKategori::all();
        $instansis = Instansi::all();
        $supportPics = User::where('role', UserRole::SUPPORT)->get();
        $statuses = TicketStatus::cases();

        $maxRows = max(
            count($aplikasis),
            count($kategoris),
            count($instansis),
            count($supportPics),
            count($statuses)
        );

        $headerStyle = 'bgcolor="#e2e8f0" color="#0f172a" border="thin#475569"';
        $cellStyle   = 'border="thin#64748b"';

        $data = [
            [
                "<style {$headerStyle}><b>Nama Koperasi</b></style>",
                "<style {$headerStyle}><b>Jenis Case</b></style>",
                "<style {$headerStyle}><b>Jenis Aplikasi</b></style>",
                "<style {$headerStyle}><b>PIC TIM SUPPORT</b></style>",
                "<style {$headerStyle}><b>Status</b></style>",
            ]
        ];

        for ($i = 0; $i < $maxRows; $i++) {
            $val1 = isset($instansis[$i]) ? $instansis[$i]->nama_instansi : '';
            $val2 = isset($kategoris[$i]) ? $kategoris[$i]->nama_kategori : '';
            $val3 = isset($aplikasis[$i]) ? $aplikasis[$i]->nama_aplikasi : '';
            $val4 = isset($supportPics[$i]) ? $supportPics[$i]->nama : '';
            $val5 = isset($statuses[$i]) ? $statuses[$i]->value : '';

            $data[] = [
                "<style {$cellStyle}>" . htmlspecialchars($val1) . "</style>",
                "<style {$cellStyle}>" . htmlspecialchars($val2) . "</style>",
                "<style {$cellStyle}>" . htmlspecialchars($val3) . "</style>",
                "<style {$cellStyle}>" . htmlspecialchars($val4) . "</style>",
                "<style {$cellStyle}>" . htmlspecialchars($val5) . "</style>",
            ];
        }

        $filename = "Master_Data_Export_" . date('Ymd_His') . ".xlsx";
        $xlsx = \App\Helpers\SimpleXLSXGen::fromArray($data);

        return response((string) $xlsx)->withHeaders([
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
