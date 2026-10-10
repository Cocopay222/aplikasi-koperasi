<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\MasterKategori;

class FaqController extends Controller
{
    /**
     * CRUD: Simpan FAQ baru (Support)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:master_kategoris,kategori_id',
            'pertanyaan'   => 'required|string|max:500',
            'jawaban'      => 'required|string',
            'visibility'   => 'nullable|in:public,internal',
            'is_active'    => 'required|boolean',
        ]);

        Faq::create([
            'kategori_id' => $request->kategori_id,
            'pertanyaan'  => $request->pertanyaan,
            'jawaban'     => $request->jawaban,
            'visibility'  => $request->visibility ?? 'public',
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('messages.faq_added'));
    }

    /**
     * CRUD: Update FAQ (Support)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:master_kategoris,kategori_id',
            'pertanyaan'   => 'required|string|max:500',
            'jawaban'      => 'required|string',
            'visibility'   => 'nullable|in:public,internal',
            'is_active'    => 'required|boolean',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'kategori_id' => $request->kategori_id,
            'pertanyaan'  => $request->pertanyaan,
            'jawaban'     => $request->jawaban,
            'visibility'  => $request->visibility ?? $faq->visibility,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('messages.faq_updated'));
    }

    /**
     * CRUD: Hapus FAQ (Support)
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return back()->with('success', __('messages.faq_deleted'));
    }

    /**
     * Tampilan Pusat Bantuan untuk Pelapor (FAQ Public)
     */
    public function indexPelapor(Request $request)
    {
        $kategoris = MasterKategori::all();
        $query = Faq::with('kategori')->active()->public();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $faqs = $query->orderBy('kategori_id')->latest()->get();

        return view('pelapor.bantuan', compact('faqs', 'kategoris'));
    }

    /**
     * API/JSON: Search FAQ public untuk Pelapor (Auto-suggest di form tiket)
     */
    public function searchPublic(Request $request)
    {
        $query = Faq::with('kategori:kategori_id,nama_kategori')->active()->public();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('jawaban', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $faqs = $query->select('faq_id', 'kategori_id', 'pertanyaan', 'jawaban', 'visibility')
                      ->limit(6)
                      ->get();

        return response()->json($faqs);
    }

    /**
     * API/JSON: Semua FAQ aktif (public + internal) untuk Support (insert to modal)
     */
    public function allForSupport(Request $request)
    {
        $query = Faq::with('kategori:kategori_id,nama_kategori')->active();

        // Opsional: filter by kategori_id
        if ($request->has('kategori_id') && $request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $faqs = $query->select('faq_id', 'kategori_id', 'pertanyaan', 'jawaban', 'visibility')
                      ->orderBy('kategori_id')
                      ->get();

        return response()->json($faqs);
    }
}
