<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\PengumumanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;

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
            'tipe'              => 'required|in:Informasi,Penting,Kegiatan,Libur',
            'target_audience'   => 'required|in:Semua,Wali Murid,Ustadz',
            'status'            => 'required|in:Draft,Terbit,Arsip',
            'tanggal_mulai'     => 'nullable|date',
            'tanggal_selesai'   => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        // Otomatis catat siapa yang membuat pengumuman ini
        $validated['user_id'] = Auth::id();

        $pengumuman = Pengumuman::create($validated);

        // Kirim notifikasi secara dinamis berdasarkan target_audience
        $target = collect();

        if ($validated['target_audience'] === 'Ustadz') {
            $target = User::role('ustadz')->get();
        } elseif ($validated['target_audience'] === 'Wali Murid') {
            $target = User::role('wali_murid')->get(); // Sesuaikan nama role di database Anda
        } elseif ($validated['target_audience'] === 'Semua') {
            $target = User::all(); // Atau gabungan beberapa role
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
            'tipe'            => 'required|in:Informasi,Penting,Kegiatan,Libur',
            'target_audience' => 'required|in:Semua,Wali Murid,Ustadz',
            'status'          => 'required|in:Draft,Terbit,Arsip',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $pengumuman->update($validated);

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    /**
     * Menghapus pengumuman.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
    }
    //
}
