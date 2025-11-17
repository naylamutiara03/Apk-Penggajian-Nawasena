<?php
// aksi_lembur.php

// 1. Sertakan koneksi database
include("koneksi.php");

// Pastikan koneksi sukses
if (!$konek) {
    // Jika koneksi gagal, langsung hentikan dengan pesan error
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => "Koneksi database gagal: " . mysqli_connect_error()]);
    exit();
}

// Ambil parameter aksi (act)
$act = $_GET['act'] ?? null;
$response = ['success' => false, 'message' => 'Aksi tidak valid.']; // Default response untuk AJAX

// Asumsi fungsi formatRupiah yang digunakan di blok 'tambah'
// Jika fungsi ini ada di file lain, pastikan sudah di-include
function formatRupiah($angka)
{
    return number_format((int)$angka, 0, '', '.');
}

try {
    
    // =========================================================================
    // A. TINDAKAN TAMBAH DATA LEMBUR (POST Request)
    // =========================================================================
    if ($act == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Ambil dan sanitasi data
        $id_tukang = $_POST['id_tukang'] ?? '';
        $tgl_lembur = $_POST['tgl_lembur'] ?? '';
        $total_harga_lembur = $_POST['total_harga_lembur'] ?? '0';
        $shifts_json = $_POST['shifts_json'] ?? '[]'; 

        // Validasi data dasar
        if (empty($id_tukang) || empty($tgl_lembur) || (int)$total_harga_lembur <= 0) {
             // Jika validasi gagal, kita gunakan redirect dengan status=error
             $pesan_error = urlencode("Input data gagal: ID Tukang, Tanggal, atau Total Harga Lembur tidak valid.");
             header("Location: lembur_tukang.php?status=error&message={$pesan_error}");
             exit();
        }

        $id_tukang_safe = mysqli_real_escape_string($konek, $id_tukang);
        $tgl_lembur_safe = mysqli_real_escape_string($konek, $tgl_lembur);
        $total_harga_lembur_safe = mysqli_real_escape_string($konek, $total_harga_lembur);
        $shifts_json_safe = mysqli_real_escape_string($konek, $shifts_json);

        $shifts_array = json_decode($shifts_json, true);
        $first_shift = !empty($shifts_array) ? $shifts_array[0]['shift'] : 'N/A';
        $first_shift_safe = mysqli_real_escape_string($konek, $first_shift);

        // Query INSERT data lembur
        $query = "INSERT INTO lembur_tkg 
                  (id_tukang, tgl_lembur, shift, harga_lembur, detail_shifts) 
                  VALUES ('$id_tukang_safe', '$tgl_lembur_safe', '$first_shift_safe', '$total_harga_lembur_safe', '$shifts_json_safe')";

        $result = mysqli_query($konek, $query);

        if ($result) {
            // Sukses: Siapkan pesan dan REDIRECT
            $harga_lembur_fmt = formatRupiah((int)$total_harga_lembur);
            $pesan = "Data lembur berhasil ditambahkan. Total: Rp {$harga_lembur_fmt}.";
            $pesan_encoded = urlencode($pesan);
            
            // REDIRECT KE HALAMAN DAFTAR DENGAN PESAN SUKSES
            header("Location: lembur_tukang.php?status=success&message={$pesan_encoded}");
            exit(); // HENTIKAN EKSEKUSI SETELAH REDIRECT
        } else {
             // Gagal INSERT: Redirect dengan error
             $pesan_error = urlencode("Gagal menyimpan data ke database: " . mysqli_error($konek));
             header("Location: lembur_tukang.php?status=error&message={$pesan_error}");
             exit();
        }
    }

    // =========================================================================
    // B. TINDAKAN HAPUS DATA LEMBUR (GET Request - AJAX)
    // =========================================================================
    elseif ($act == 'delete' && $_SERVER['REQUEST_METHOD'] == 'GET') {

        $id_lembur = $_GET['id'] ?? 0;

        if (!is_numeric($id_lembur) || $id_lembur <= 0) {
            throw new Exception("ID data lembur tidak valid.");
        }

        $id_lembur_safe = mysqli_real_escape_string($konek, $id_lembur);

        // Query DELETE
        $query = "DELETE FROM lembur_tkg WHERE id = '$id_lembur_safe'";
        $result = mysqli_query($konek, $query);

        if (mysqli_affected_rows($konek) > 0) {
            $response['success'] = true;
            $response['message'] = "Data lembur ID $id_lembur_safe berhasil dihapus.";
        } elseif (mysqli_error($konek)) {
            throw new Exception("Gagal menghapus data dari database: " . mysqli_error($konek));
        } else {
            // Data tidak ditemukan
            throw new Exception("Data lembur ID $id_lembur_safe tidak ditemukan.");
        }
    }

} catch (Exception $e) {
    // Tangani semua error dan siapkan respons JSON
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 2. Output respons JSON (Hanya dijalankan jika aksi adalah DELETE, atau jika terjadi error)
// Pastikan tidak ada output lain (seperti spasi atau karakter di luar blok PHP) di awal file.
header('Content-Type: application/json');
echo json_encode($response);

// 3. Tutup koneksi dan HENTIKAN EKSEKUSI
mysqli_close($konek);
exit();
?>