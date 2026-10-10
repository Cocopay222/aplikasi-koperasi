<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\MasterAplikasi;
use App\Models\MasterKategori;
use App\Models\Faq;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // --- PELAPOR METHODS ---
    public function pelaporDashboard(Request $request)
    {
        $tickets = Ticket::where('pelapor_id', Auth::id())->latest('tanggal_input')->take(5)->get();
        $aplikasis = MasterAplikasi::where('is_active', true)->get();
        
        $totalOpen = Ticket::where('pelapor_id', Auth::id())->whereIn('status', [\App\Enums\TicketStatus::OPEN, \App\Enums\TicketStatus::PROSES])->count();
        $totalPending = Ticket::where('pelapor_id', Auth::id())->where('status', \App\Enums\TicketStatus::PENDING)->count();
        $totalDone = Ticket::where('pelapor_id', Auth::id())->where('status', \App\Enums\TicketStatus::DONE)->count();

        // Data FAQ untuk Tab Pusat Solusi / FAQ
        $kategoris = MasterKategori::all();
        $faqsQuery = Faq::with('kategori')->active()->public();
        if ($request->filled('faq_search')) {
            $search = $request->faq_search;
            $faqsQuery->where(function($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }
        if ($request->filled('faq_kategori_id')) {
            $faqsQuery->where('kategori_id', $request->faq_kategori_id);
        }
        $faqs = $faqsQuery->orderBy('kategori_id')->latest()->get();

        return view('pelapor.dashboard', compact('tickets', 'aplikasis', 'totalOpen', 'totalPending', 'totalDone', 'faqs', 'kategoris'));
    }

    public function pelaporRiwayat(Request $request)
    {
        $query = Ticket::where('pelapor_id', Auth::id());
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $tickets = $query->latest('tanggal_input')->get();
        $aplikasis = MasterAplikasi::where('is_active', true)->get();
        
        return view('pelapor.riwayat', compact('tickets', 'aplikasis'));
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();

        // Update kontak & email pelapor jika diisi
        $user = Auth::user();
        $userUpdates = [];
        if ($request->filled('whatsapp')) {
            $userUpdates['whatsapp'] = trim($request->whatsapp);
        }
        if ($request->filled('email')) {
            $userUpdates['email'] = trim($request->email);
        }
        if (!empty($userUpdates)) {
            $user->update($userUpdates);
        }
        
        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $lampiranPaths[] = $file->store('lampiran_tiket', 'public');
            }
        }

        Ticket::create([
            'pelapor_id' => Auth::id(),
            'aplikasi_id' => $data['aplikasi_id'],
            'permasalahan' => $data['permasalahan'],
            'lampiran' => count($lampiranPaths) > 0 ? $lampiranPaths : null,
            'status' => TicketStatus::OPEN->value,
            'tanggal_input' => now(),
        ]);

        return back()->with('success', __('messages.ticket_created'));
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->pelapor_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status !== TicketStatus::OPEN) {
            return back()->with('error', __('messages.cannot_delete_ticket'));
        }

        if ($ticket->lampiran) {
            $lampirans = is_array($ticket->lampiran) ? $ticket->lampiran : [$ticket->lampiran];
            foreach ($lampirans as $lamp) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($lamp)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($lamp);
                }
            }
        }

        $ticket->delete();

        return back()->with('success', __('messages.ticket_deleted'));
    }

    public function updatePelapor(StoreTicketRequest $request, Ticket $ticket)
    {
        if ($ticket->pelapor_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status !== TicketStatus::OPEN) {
            return back()->with('error', __('messages.error_laporan_ditangani'));
        }

        $data = $request->validated();
        
        $updateData = [
            'aplikasi_id' => $data['aplikasi_id'],
            'permasalahan' => $data['permasalahan'],
        ];

        // Jika pelapor tidak memilih file, biarkan lampiran lama
        // Jika pelapor mengunggah file baru, hapus file lama dan simpan yang baru
        if ($request->hasFile('lampiran')) {
            if ($ticket->lampiran) {
                $lampirans = is_array($ticket->lampiran) ? $ticket->lampiran : [$ticket->lampiran];
                foreach ($lampirans as $lamp) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($lamp)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($lamp);
                    }
                }
            }

            $lampiranPaths = [];
            foreach ($request->file('lampiran') as $file) {
                $lampiranPaths[] = $file->store('lampiran_tiket', 'public');
            }
            $updateData['lampiran'] = count($lampiranPaths) > 0 ? $lampiranPaths : null;
        }

        $ticket->update($updateData);

        return back()->with('success', __('messages.report_updated_successfully'));
    }


    public function uploadBalasan(Request $request, Ticket $ticket)
    {
        if ($ticket->pelapor_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status === TicketStatus::DONE) {
            return back()->with('error', __('messages.error_upload_surat_selesai'));
        }

        $request->validate([
            'surat_balasan' => 'required',
            'surat_balasan.*' => 'file|mimes:pdf,doc,docx,xlsx,csv,pptx,ppsx,xlsm,docm,xlsb,zip,rar|max:5120',
        ]);

        $paths = [];
        if ($ticket->surat_balasan) {
            $existing = json_decode($ticket->surat_balasan, true);
            if (is_array($existing)) {
                $paths = $existing;
            } else {
                // Backward compatibility if it wasn't JSON
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($ticket->surat_balasan)) {
                    $paths = [$ticket->surat_balasan];
                }
            }
        }

        if ($request->hasFile('surat_balasan')) {
            $files = $request->file('surat_balasan');
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                // Generate safe filename with timestamp to prevent collision
                $filename = time() . '_' . $file->getClientOriginalName();
                // Replace spaces with underscores for better URL safety
                $filename = str_replace(' ', '_', $filename);
                
                $paths[] = $file->storeAs('surat_balasan', $filename, 'public');
            }

            $ticket->update([
                'surat_balasan' => json_encode($paths)
            ]);

            // Notify Support
            $supportUsers = \App\Models\User::where('role', \App\Enums\UserRole::SUPPORT->value)->get();
            if ($ticket->pic_support_id) {
                $supportUsers = \App\Models\User::where('user_id', $ticket->pic_support_id)->get();
            }
            foreach ($supportUsers as $support) {
                $support->notify(new \App\Notifications\TicketDocumentNotification(
                    'Surat Balasan Baru',
                    "Pelapor telah mengunggah surat balasan untuk tiket #{$ticket->ticket_id}.",
                    $ticket->ticket_id,
                    route('support.tickets.dokumen', $ticket->ticket_id)
                ));
            }
        }

        return back()->with('success', __('messages.surat_balasan_berhasil_diunggah'));
    }

    public function deleteBalasan(Request $request, Ticket $ticket, $index)
    {
        if ($ticket->pelapor_id !== Auth::id()) {
            abort(403);
        }

        if ($ticket->status === TicketStatus::DONE) {
            return back()->with('error', __('messages.error_hapus_surat_selesai'));
        }

        if ($ticket->surat_balasan) {
            $existing = json_decode($ticket->surat_balasan, true);
            if (is_array($existing) && isset($existing[$index])) {
                $path = $existing[$index];
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
                unset($existing[$index]);
                // Re-index array
                $existing = array_values($existing);
                
                $ticket->update([
                    'surat_balasan' => count($existing) > 0 ? json_encode($existing) : null
                ]);
            }
        }

        return back()->with('success', __('messages.success_hapus_surat'));
    }

    public function dokumen($ticket_id)
    {
        $ticket = Ticket::with(['pelapor.instansi', 'aplikasi', 'kategori', 'picSupport'])->where('ticket_id', $ticket_id)->firstOrFail();
        
        // Ensure user is authorized to view it
        if (Auth::user()->role === \App\Enums\UserRole::PELAPOR->value && $ticket->pelapor_id !== Auth::id()) {
            abort(403);
        }

        return view('shared.dokumen', compact('ticket'));
    }

    // --- SUPPORT METHODS ---
    public function supportDashboard(Request $request)
    {
        $query = Ticket::with(['pelapor.instansi', 'aplikasi', 'kategori']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%$search%")
                  ->orWhere('permasalahan', 'like', "%$search%");
            });
        }

        $tickets = $query->latest('updated_at')->paginate(10);
        $kategoris = MasterKategori::all();

        // Ambil daftar user pelapor yang belum diverifikasi
        $pendingUsers = \App\Models\User::with('instansi')
            ->where('role', \App\Enums\UserRole::PELAPOR->value)
            ->where('is_verified', false)
            ->latest()
            ->get();

        return view('support.dashboard', compact('tickets', 'kategoris', 'pendingUsers'));
    }

    public function prioritas(Request $request)
    {
        $query = Ticket::with(['pelapor.instansi', 'aplikasi', 'kategori'])
            ->whereNotIn('status', [\App\Enums\TicketStatus::DONE->value, 'Done', 'Selesai']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('aplikasi_id')) {
            $query->where('aplikasi_id', $request->aplikasi_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_id', 'like', "%$search%")
                  ->orWhere('permasalahan', 'like', "%$search%")
                  ->orWhereHas('pelapor.instansi', function($iq) use ($search) {
                      $iq->where('nama_instansi', 'like', "%$search%");
                  });
            });
        }

        // Order by created_at ASC (Oldest first)
        $tickets = $query->oldest('created_at')->paginate(20);
        $kategoris = MasterKategori::all();
        $aplikasis = MasterAplikasi::all();

        // Ambil daftar user pelapor yang belum diverifikasi
        $pendingUsers = \App\Models\User::with('instansi')
            ->where('role', \App\Enums\UserRole::PELAPOR->value)
            ->where('is_verified', false)
            ->latest()
            ->get();

        return view('support.prioritas', compact('tickets', 'kategoris', 'aplikasis', 'pendingUsers'));
    }

    public function updateSupport(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|string',
            'kategori_id' => 'nullable',
            'penyelesaian' => 'nullable|string',
            'pencegahan' => 'nullable|string',
            'link_ticket' => 'nullable|string',
            'template_laporan' => 'nullable|string',
            'is_faq' => 'nullable|boolean',
            'lampiran_support' => 'nullable|array',
            'lampiran_support.*' => 'file|mimes:jpg,jpeg,png,mp4,pdf,doc,docx,xls,xlsx,csv,ppt,pptx,ppsx,xlsm,docm,xlsb,zip,rar|max:5120',
        ]);

        $data = $request->only(['status', 'kategori_id', 'penyelesaian', 'pencegahan', 'link_ticket', 'template_laporan']);
        $data['is_faq'] = $request->has('is_faq');
        
        // Selalu ubah PIC Support ke user yang sedang melakukan update
        $data['pic_support_id'] = Auth::id();
        
        $targetStatus = $data['status'];
        $currentStatusStr = is_object($ticket->status) ? $ticket->status->value : (string)$ticket->status;

        if ($targetStatus === TicketStatus::DONE->value && $currentStatusStr !== TicketStatus::DONE->value) {
            $data['tanggal_penyelesaian'] = now();
        }

        if ($request->has('hapus_lampiran_support') && $request->hapus_lampiran_support == '1') {
            if ($ticket->lampiran_support) {
                $lampirans = is_array($ticket->lampiran_support) ? $ticket->lampiran_support : [$ticket->lampiran_support];
                foreach ($lampirans as $lamp) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($lamp)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($lamp);
                    }
                }
            }
            $data['lampiran_support'] = null;
        }

        if ($request->hasFile('lampiran_support')) {
            if ($ticket->lampiran_support) {
                $lampirans = is_array($ticket->lampiran_support) ? $ticket->lampiran_support : [$ticket->lampiran_support];
                foreach ($lampirans as $lamp) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($lamp)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($lamp);
                    }
                }
            }
            $lampiranSupportPaths = [];
            foreach ($request->file('lampiran_support') as $file) {
                $lampiranSupportPaths[] = $file->store('lampiran_tiket', 'public');
            }
            $data['lampiran_support'] = count($lampiranSupportPaths) > 0 ? $lampiranSupportPaths : null;
        }

        $oldData = [
            'status' => is_object($ticket->status) ? $ticket->status->value : $ticket->status,
            'kategori_id' => $ticket->kategori_id,
            'penyelesaian' => $ticket->penyelesaian,
            'pencegahan' => $ticket->pencegahan,
            'link_ticket' => $ticket->link_ticket,
            'template_laporan' => $ticket->template_laporan,
        ];

        $ticket->update($data);

        // Record Ticket Log secara aman
        try {
            \App\Models\TicketLog::create([
                'ticket_id' => $ticket->ticket_id,
                'user_id' => Auth::id(),
                'aktivitas' => 'Update Tiket oleh PIC Support',
                'data_sebelum' => $oldData,
                'data_sesudah' => [
                    'status' => is_object($ticket->status) ? $ticket->status->value : $ticket->status,
                    'kategori_id' => $ticket->kategori_id,
                    'penyelesaian' => $ticket->penyelesaian,
                    'pencegahan' => $ticket->pencegahan,
                    'link_ticket' => $ticket->link_ticket,
                ],
                'catatan' => 'Tiket diperbarui oleh ' . (Auth::user()->nama ?? 'PIC Support')
            ]);
        } catch (\Throwable $e) {
            \Log::error('TicketLog error: ' . $e->getMessage());
        }

        // Jika opsi is_faq dicentang, otomatis simpan/update ke Master Data FAQ
        if ($data['is_faq'] && !empty($ticket->kategori_id) && !empty($ticket->penyelesaian)) {
            Faq::updateOrCreate(
                [
                    'kategori_id' => $ticket->kategori_id,
                    'pertanyaan'  => $ticket->permasalahan,
                ],
                [
                    'jawaban'     => $ticket->penyelesaian,
                    'visibility'  => 'internal',
                    'is_active'   => true,
                ]
            );
        }

        // Notify Pelapor jika ada dokumen/lampiran baru dari support
        $hasNewDocument = false;
        if ($request->hasFile('lampiran_support')) {
            $hasNewDocument = true;
        }
        if (isset($data['template_laporan']) && $data['template_laporan'] !== $oldData['template_laporan']) {
            $hasNewDocument = true;
        }

        if ($hasNewDocument && $ticket->pelapor) {
            $ticket->pelapor->notify(new \App\Notifications\TicketDocumentNotification(
                'Dokumen Baru dari Support',
                "Tim Support telah melampirkan dokumen baru pada tiket #{$ticket->ticket_id}.",
                $ticket->ticket_id,
                route('pelapor.tickets.dokumen', $ticket->ticket_id)
            ));
        }

        return back()->with('success', __('messages.ticket_updated'));
    }
}
