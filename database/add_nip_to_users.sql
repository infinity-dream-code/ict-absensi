-- Tambah kolom NIP ke tabel users (untuk database yang sudah ada)
-- Login tetap menggunakan NIK/username, tidak berubah.

ALTER TABLE users ADD COLUMN nip VARCHAR(255) NULL AFTER nik;

-- Jika ingin NIP unik (opsional, jalankan setelah data NIP diisi dan tidak ada duplikat):
-- ALTER TABLE users ADD UNIQUE KEY users_nip_unique (nip);