# 🔌 SPESIFIKASI ENDPOINT REST API (LARAVEL BACKEND)
> **MDT Hidayatus Shibyan — Mobile API Reference**

---

## 🔐 1. AUTENTIKASI & PROFIL
- `POST /api/login` ➔ Login Ustadz (Username/Email/NIGM + Password).
- `POST /api/logout` ➔ Revoke Bearer Token.
- `GET /api/profile` ➔ Mendapatkan data user & biodata ustadz yang sedang login.
- `POST /api/profile/update-password` ➔ Ubah password akun.
- `POST /api/ustadz/update-biodata` ➔ Simpan pembaruan biodata (NIGM tidak dapat diubah).
- `POST /api/ustadz/update-foto` ➔ Upload foto profil ustadz (Multipart / Base64).
- `POST /api/ustadz/update-tanda-tangan` ➔ Upload tanda tangan digital (PNG).

---

## 📊 2. DASHBOARD & MASTER UMUM
- `GET /api/dashboard` ➔ Ringkasan statistik, jadwal mengajar hari ini, info keuangan ruangan.
- `GET /api/pengumuman` ➔ Daftar pengumuman aktif madrasah.
- `GET /api/kalendar-pendidikan` ➔ Agenda kegiatan akademik madrasah.
- `GET /api/murid/ruangan` ➔ Daftar murid berdasarkan ruangan wali.
- `GET /api/mata-pelajaran` ➔ Master mata pelajaran madrasah.

---

## 📋 3. PRESENSI
- `GET /api/presensi-murid/sesi` ➔ Jadwal sesi mengajar ustadz untuk presensi.
- `GET /api/presensi-murid/murid?ruangan_id={id}` ➔ Daftar murid pada ruangan tertentu beserta status presensi terakhir.
- `POST /api/presensi-murid/simpan` ➔ Simpan daftar presensi murid (Batch array: murid_id, status, keterangan).
- `POST /api/presensi-ustadz/checkin` ➔ Check-in masuk ustadz (Sesi, Foto, Lokasi).
- `GET /api/presensi-ustadz/riwayat` ➔ Riwayat presensi ustadz bulanan.

---

## 💰 4. KAS RUANGAN & SETORAN
- `GET /api/kas-ruangan/ringkasan` ➔ Ringkasan total target kas, total terkumpul, sisa saldo.
- `GET /api/kas-ruangan/murid-list?ruangan_id={id}` ➔ Daftar murid, nominal target, total dibayar, status lunas.
- `POST /api/kas-ruangan/bayar` ➔ Simpan pembayaran iuran kas murid.
- `POST /api/kas-ruangan/batal-bayar` ➔ Batalkan transaksi iuran kas tertentu.
- `POST /api/kas-ruangan/target-kas` ➔ Ubah target iuran kas per murid.
- `GET /api/kas-ruangan/setoran/penerima-list` ➔ Daftar penerima setoran (Bendahara/Pimpinan).
- `GET /api/kas-ruangan/setoran/riwayat` ➔ Riwayat setoran kas ruangan.
- `POST /api/kas-ruangan/setoran/simpan` ➔ Buat setoran kas baru (Nominal, bukti transfer, penerima).
- `POST /api/kas-ruangan/setoran/update` ➔ Edit setoran kas.
- `POST /api/kas-ruangan/setoran/hapus` ➔ Hapus setoran kas.

---

## 🧾 5. TAGIHAN SPP & NON-SPP
- `GET /api/tagihan/spp/ringkasan` ➔ Statistik keseluruhan SPP ruangan (Target, Dibayar, Tunggakan).
- `GET /api/tagihan/spp/murid-list?ruangan_id={id}` ➔ Daftar murid & progres SPP 11 Bulan Hijriyah.
- `GET /api/tagihan/spp/kartu?murid_id={id}` ➔ Detail kartu SPP 11 Bulan Hijriyah per murid.
- `GET /api/tagihan/non-spp/master-list` ➔ Master tagihan non-SPP aktif (Pembangunan, Kitab, dll).
- `GET /api/tagihan/non-spp/ringkasan?pengaturan_id={id}` ➔ Ringkasan tagihan non-SPP tertentu.
- `GET /api/tagihan/non-spp/murid-list?pengaturan_id={id}&ruangan_id={id}` ➔ Daftar murid pada tagihan non-SPP.
- `POST /api/tagihan/non-spp/bayar` ➔ Proses pembayaran tagihan non-SPP (Single / Massal).
- `POST /api/tagihan/non-spp/batal-bayar` ➔ Batalkan status bayar tagihan non-SPP.

---

## ⚖️ 6. PELANGGARAN & KEDISIPLINAN
- `GET /api/pelanggaran/referensi` ➔ Master kategori & referensi pelanggaran beserta bobot poin.
- `GET /api/pelanggaran/ruangan-list` ➔ Daftar ruangan dan total murid/kasus.
- `GET /api/pelanggaran/murid-by-ruangan?ruangan_id={id}` ➔ List murid dan total poin akumulasi.
- `GET /api/pelanggaran/harian?tanggal={date}` ➔ Log catatan pelanggaran harian.
- `POST /api/pelanggaran/simpan` ➔ Simpan pelanggaran baru (Single atau Massal).
- `POST /api/pelanggaran/hapus` ➔ Hapus catatan pelanggaran.

---

## 📈 7. PUSAT LAPORAN TERPADU
- `GET /api/laporan/presensi-murid?ruangan_id={id}&bulan_hijriyah_id={id}` ➔ Rekap kehadiran murid.
- `GET /api/laporan/presensi-ustadz?ustadz_id={id}&tahun_pelajaran={thn}` ➔ Rekap kehadiran ustadz.
- `GET /api/laporan/pelanggaran-murid?ruangan_id={id}&status={disiplin/tidak}` ➔ Rekap kedisiplinan.
- `GET /api/laporan/ujian?ruangan_id={id}&semester={1/2}&ujian={uts/uas}` ➔ Rekap nilai ujian & ranking.
- `GET /api/laporan/kenaikan-kelas?ruangan_id={id}` ➔ Rekapitulasi kenaikan kelas & rekomendasi final.
