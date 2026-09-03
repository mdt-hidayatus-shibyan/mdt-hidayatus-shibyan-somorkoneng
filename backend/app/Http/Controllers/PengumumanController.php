<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\PengumumanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengumuman::query();

        // Fitur Pencarian berdasarkan Judul atau Konten
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('konten', 'like', "%{$search}%");
            });
        }

        // Urutkan dari yang terbaru, tampilkan 12 data per halaman
        $pengumumans = $query->latest()->paginate(12)->withQueryString();

        return view('pengumuman.index', compact('pengumumans'));
    }

    /**
     * Menampilkan form tambah pengumuman.
     */
    public function create()
    {
        return view('pengumuman.form');
    }

    /**
     * Menyimpan data pengumuman baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'             => 'required|string|max:255',
            'konten'            => 'required|string',
            'lampiran_pdf'      => 'nullable|file|mimes:pdf|max:10240',
            'tipe'              => 'required|in:Informasi,Penting,Kegiatan,Libur',
            'target_audience'   => 'required|in:Semua,Wali Murid,Ustadz',
            'status'            => 'required|in:Draft,Terbit,Arsip',
            'tanggal_mulai'     => 'nullable|date',
            'tanggal_selesai'   => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'lampiran_pdf.mimes'             => 'Lampiran harus berupa file PDF (.pdf).',
            'lampiran_pdf.max'               => 'Ukuran file PDF maksimal 10 MB.',
        ]);

        // Upload file PDF jika ada
        if ($request->hasFile('lampiran_pdf')) {
            $validated['lampiran_pdf'] = $request->file('lampiran_pdf')->store('uploads/pengumuman/pdf', 'public');
        }

        // Otomatis catat siapa yang membuat pengumuman ini
        $validated['user_id'] = Auth::id();

        $pengumuman = Pengumuman::create($validated);

        // Kirim notifikasi secara dinamis berdasarkan target_audience
        $target = collect();

        if ($validated['target_audience'] === 'Ustadz') {
            $target = User::role('ustadz')->get();
        } elseif ($validated['target_audience'] === 'Wali Murid') {
            $target = User::role('wali_murid')->get();
        } elseif ($validated['target_audience'] === 'Semua') {
            $target = User::all();
        }

        if ($target->isNotEmpty()) {
            FacadesNotification::send($target, new PengumumanNotification($pengumuman, [
                'title' => 'Pengumuman Baru',
                'body'  => $validated['judul'],
            ]));
        }

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman baru berhasil dipublikasikan!');
    }

    /**
     * Menampilkan detail pengumuman penuh (untuk dibaca).
     */
    public function show(Pengumuman $pengumuman)
    {
        return view('pengumuman.show', compact('pengumuman'));
    }

    /**
     * Menampilkan form edit pengumuman.
     */
    public function edit(Pengumuman $pengumuman)
    {
        return view('pengumuman.form', compact('pengumuman'));
    }

    /**
     * Memperbarui data pengumuman di database.
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul'           => 'required|string|max:255',
            'konten'          => 'required|string',
            'lampiran_pdf'    => 'nullable|file|mimes:pdf|max:10240',
            'hapus_lampiran'  => 'nullable|boolean',
            'tipe'            => 'required|in:Informasi,Penting,Kegiatan,Libur',
            'target_audience' => 'required|in:Semua,Wali Murid,Ustadz',
            'status'          => 'required|in:Draft,Terbit,Arsip',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'lampiran_pdf.mimes'             => 'Lampiran harus berupa file PDF (.pdf).',
            'lampiran_pdf.max'               => 'Ukuran file PDF maksimal 10 MB.',
        ]);

        // Hapus lampiran PDF jika user mencentang hapus
        if ($request->boolean('hapus_lampiran')) {
            if ($pengumuman->lampiran_pdf && Storage::disk('public')->exists($pengumuman->lampiran_pdf)) {
                Storage::disk('public')->delete($pengumuman->lampiran_pdf);
            }
            $validated['lampiran_pdf'] = null;
        }

        // Upload lampiran PDF baru jika ada
        if ($request->hasFile('lampiran_pdf')) {
            if ($pengumuman->lampiran_pdf && Storage::disk('public')->exists($pengumuman->lampiran_pdf)) {
                Storage::disk('public')->delete($pengumuman->lampiran_pdf);
            }
            $validated['lampiran_pdf'] = $request->file('lampiran_pdf')->store('uploads/pengumuman/pdf', 'public');
        }

        $pengumuman->update($validated);

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    /**
     * Menghapus pengumuman.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->lampiran_pdf && Storage::disk('public')->exists($pengumuman->lampiran_pdf)) {
            Storage::disk('public')->delete($pengumuman->lampiran_pdf);
        }

        $pengumuman->delete();

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
    }
}
