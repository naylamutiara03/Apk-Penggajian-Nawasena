<?php
// =========================================================================
// AKSI TUKANG PHP - VERSI FINAL DENGAN PENANGANAN JSON POST & PREPARED STATEMENT
// =========================================================================

// 1. Set Header untuk memastikan Response selalu dalam format JSON
header('Content-Type: application/json');

// 2. Sertakan file koneksi database
include("koneksi.php");

// 3. Pengecekan koneksi database
if (!isset($konek) || $konek === false) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal. Periksa koneksi.php.']);
    // Keluar dari skrip
    exit;
}

// 4. Proses Aksi berdasarkan parameter 'act'
if (isset($_GET['act'])) {
    $action = $_GET['act'];

    // ---------------------------------------------------------------------
    ### ➕ Fungsi untuk Menambah Tukang (CREATE) - Menggunakan JSON Input
    // ---------------------------------------------------------------------
    if ($action == 'tambah') {

        // Baca data RAW JSON dari body request (karena dikirim via fetch/AJAX)
        $json_data = file_get_contents("php://input");
        $data = json_decode($json_data, true);

        // Pengecekan validitas JSON
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Format data yang diterima tidak valid (Bukan JSON).']);
            exit;
        }

        // Ambil data dari array $data (hasil decode JSON)
        $nik = $data['nik'] ?? '';
        $nama_tukang = $data['nama_tukang'] ?? '';
        $jenis_kelamin = $data['jenis_kelamin'] ?? '';
        $tgl_masuk = $data['tgl_masuk'] ?? '';
        $status = $data['status'] ?? '';

        // Data Shift Harga (pastikan default 0 jika tidak ada)
        $harga_shift_1 = $data['harga_shift_1'] ?? 0;
        $harga_shift_2 = $data['harga_shift_2'] ?? 0;
        $harga_shift_3 = $data['harga_shift_3'] ?? 0;

        // Bersihkan dan konversi nilai harga ke integer
        $h1 = intval($harga_shift_1);
        $h2 = intval($harga_shift_2);
        $h3 = intval($harga_shift_3);


        // Validasi input Wajib (termasuk minimal satu harga shift harus > 0)
        if (empty($nik) || empty($nama_tukang) || empty($jenis_kelamin) || empty($tgl_masuk) || empty($status) || ($h1 <= 0 && $h2 <= 0 && $h3 <= 0)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi, dan minimal satu Harga Shift harus lebih besar dari 0.']);
            exit; // Penting! Hentikan eksekusi setelah mencetak error
        }

        // Query INSERT dengan 8 placeholder (?)
        $query = "INSERT INTO tukang_nws (nik, nama_tukang, jenis_kelamin, tgl_masuk, status, harga_shift_1, harga_shift_2, harga_shift_3) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepared Statement
        $stmt = mysqli_prepare($konek, $query);

        if ($stmt) {
            // Bind parameter: 8 tipe, 5 string (s) dan 3 integer (i)
            if (mysqli_stmt_bind_param($stmt, 'sssssiii', $nik, $nama_tukang, $jenis_kelamin, $tgl_masuk, $status, $h1, $h2, $h3)) {

                if (mysqli_stmt_execute($stmt)) {
                    // Sukses
                    echo json_encode(['success' => true, 'message' => "Tukang **{$nama_tukang}** berhasil ditambahkan. Anda dapat melihatnya di daftar."]);
                } else {
                    // Gagal Eksekusi: Tangani Error SQL secara spesifik
                    $errorCode = mysqli_stmt_errno($stmt);
                    $errorMessage = mysqli_stmt_error($stmt);

                    if ($errorCode == 1062) { // Kode error SQL untuk Duplicate Entry (NIK)
                        echo json_encode(['success' => false, 'message' => 'NIK (' . $nik . ') sudah terdaftar. Gagal menambahkan tukang.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan tukang: ' . $errorMessage]);
                    }
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal bind parameter: Pastikan tipe data sesuai (5s, 3i).']);
            }
            // Tutup statement
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saat menyiapkan query: ' . mysqli_error($konek)]);
        }

        exit; // Keluar setelah semua proses 'tambah' selesai

    }


    // ---------------------------------------------------------------------
    ### 🗑️ Fungsi untuk Menghapus Tukang (DELETE) - Menggunakan GET Input
    // ---------------------------------------------------------------------
    if ($action == 'delete') {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            exit;
        }

        $query = "DELETE FROM tukang_nws WHERE id = ?";
        $stmt = mysqli_prepare($konek, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id); // Asumsi 'id' adalah integer (i)

            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    echo json_encode(['success' => true, 'message' => 'Tukang berhasil dihapus.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal menghapus tukang: ID tidak ditemukan.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus tukang: ' . mysqli_stmt_error($stmt)]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saat menyiapkan query: ' . mysqli_error($konek)]);
        }

        exit; // Keluar setelah semua proses 'delete' selesai
    }

    // ---------------------------------------------------------------------
    ### ✏️ Fungsi untuk Memperbarui Tukang (UPDATE) - Menggunakan JSON Input
    // ---------------------------------------------------------------------
    if ($action == 'update') {

        // Baca data RAW JSON dari body request
        $json_data = file_get_contents("php://input");
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'Format data yang diterima untuk update tidak valid.']);
            exit;
        }

        // Ambil data dari array $data (hasil decode JSON)
        $id = $data['id'] ?? '';
        $nik_lama = $data['nik_lama'] ?? '';
        $nik = $data['nik'] ?? '';
        $nama_tukang = $data['nama_tukang'] ?? '';
        $jenis_kelamin = $data['jenis_kelamin'] ?? '';
        $tgl_masuk = $data['tgl_masuk'] ?? '';
        $status = $data['status'] ?? '';
        $harga_shift_1 = $data['harga_shift_1'] ?? '';
        $harga_shift_2 = $data['harga_shift_2'] ?? '';
        $harga_shift_3 = $data['harga_shift_3'] ?? '';

        // --- Validasi Input Wajib ---
        if (empty($id) || empty($nik) || empty($nama_tukang) || empty($jenis_kelamin) || empty($tgl_masuk) || empty($status) || $harga_shift_1 === '' || $harga_shift_2 === '' || $harga_shift_3 === '') {
            echo json_encode(['success' => false, 'message' => 'Semua field, termasuk Harga Shift, wajib diisi.']);
            exit;
        }

        $harga_s1_int = (int) $harga_shift_1;
        $harga_s2_int = (int) $harga_shift_2;
        $harga_s3_int = (int) $harga_shift_3;

        // Validasi Harga: minimal satu harus lebih besar dari 0
        if ($harga_s1_int <= 0 && $harga_s2_int <= 0 && $harga_s3_int <= 0) {
            echo json_encode(['success' => false, 'message' => 'Minimal satu Harga Shift harus lebih besar dari 0.']);
            exit;
        }

        // --- Pengecekan NIK Duplikat (Hanya jika NIK Berubah) ---
        // (Kode pengecekan NIK duplikat tetap sama)
        if ($nik != $nik_lama) {
            $cek_query = "SELECT id FROM tukang_nws WHERE nik=? AND id != ?";
            $cek_stmt = mysqli_prepare($konek, $cek_query);

            if ($cek_stmt) {
                mysqli_stmt_bind_param($cek_stmt, 'si', $nik, $id);
                mysqli_stmt_execute($cek_stmt);
                mysqli_stmt_store_result($cek_stmt);

                if (mysqli_stmt_num_rows($cek_stmt) > 0) {
                    mysqli_stmt_close($cek_stmt);
                    echo json_encode(["success" => false, "message" => "NIK $nik sudah terdaftar di tukang lain!"]);
                    exit;
                }
                mysqli_stmt_close($cek_stmt);
            }
        }

        // --- Prepared Statement (UPDATE) ---
        $query = "UPDATE tukang_nws SET 
                nik=?, 
                nama_tukang=?, 
                jenis_kelamin=?, 
                tgl_masuk=?, 
                status=?, 
                harga_shift_1=?, 
                harga_shift_2=?, 
                harga_shift_3=? 
             WHERE id=?";

        $stmt = mysqli_prepare($konek, $query);

        if ($stmt) {
            // Bind parameter: sssssiiis (5 String, 3 Integer, 1 String/Integer ID)
            if (
                mysqli_stmt_bind_param(
                    $stmt,
                    'sssssiiis', // Sesuaikan jika ID Anda adalah integer ('i') atau string ('s')
                    $nik,
                    $nama_tukang,
                    $jenis_kelamin,
                    $tgl_masuk,
                    $status,
                    $harga_s1_int,
                    $harga_s2_int,
                    $harga_s3_int,
                    $id
                )
            ) {
                if (mysqli_stmt_execute($stmt)) {
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        echo json_encode(['success' => true, 'message' => 'Data Tukang dan Harga Shift berhasil diperbarui.']);
                    } else {
                        echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan (Tidak ada perubahan terdeteksi).']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui tukang: ' . mysqli_stmt_error($stmt)]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal bind parameter untuk update.']);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saat menyiapkan query: ' . mysqli_error($konek)]);
        }

        exit;
    }

} else {
    // Penanganan jika parameter 'act' tidak ada
    echo json_encode(['success' => false, 'message' => 'Aksi tidak ditentukan.']);
}

// Tutup koneksi database di akhir skrip (hanya jika exit() belum terpanggil)
// Catatan: Jika ada exit() di atas, kode ini tidak akan dieksekusi, tapi aman untuk diletakkan di sini.
// Pastikan tidak ada karakter lain setelah tag penutup PHP jika Anda menggunakannya!
mysqli_close($konek);