<?php
// Pastikan koneksi.php sudah di-include
include("koneksi.php");
header('Content-Type: application/json');

// Ambil ID Tukang dari parameter GET
$id_tukang = $_GET['id'] ?? 0;
$id_tukang = (int)$id_tukang;

if ($id_tukang <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID Tukang tidak valid.']);
    exit;
}

// Query untuk mengambil id_jabatan berdasarkan id_tukang
$query = mysqli_query($konek, "SELECT id_jabatan FROM tukang_nws WHERE id = '$id_tukang'");

if ($query && $data = mysqli_fetch_assoc($query)) {
    // Sukses: Mengembalikan ID Jabatan
    echo json_encode(['success' => true, 'id_jabatan' => $data['id_jabatan']]);
} else {
    // Gagal: Tukang tidak ditemukan atau query error
    echo json_encode(['success' => false, 'message' => 'Tukang tidak ditemukan atau database error.']);
}

mysqli_close($konek);
?>