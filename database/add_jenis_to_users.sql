-- Tambah kolom jenis ke tabel users
-- jenis = 1 (default) = karyawan dihitung di dashboard (termasuk tidak absen)
-- jenis = 0 = karyawan tidak dihitung di dashboard (walaupun tidak absen tidak masuk hitungan)

ALTER TABLE users
ADD COLUMN jenis TINYINT(1) NOT NULL DEFAULT 1 AFTER role;