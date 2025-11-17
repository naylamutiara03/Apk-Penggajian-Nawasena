<?php
// get_data_tukang.php

// 1. Sertakan koneksi database
include("koneksi.php");

// Set header agar respons selalu dalam format JSON
header('Content-Type: application/json');

// Ambil ID Tukang dari parameter GET
$id_tukang = $_GET['id'] ?? 0;
$id_tukang = (int)$id_tukang;

$response = ['success' => false, 'message' => 'ID Tukang tidak valid.'];

if ($id_tukang <= 0) {
    echo json_encode($response);
    exit;
}

try {
    // Pastikan koneksi sukses
    if (!$konek) {
        throw new Exception("Koneksi database gagal: " . mysqli_connect_error());
    }

    // 2. Query untuk mengambil SEMUA kolom harga shift dan id_jabatan
    // Kolom diambil langsung dari tabel tukang_nws sesuai struktur yang terlihat di gambar
    $query = mysqli_query($konek, "
        SELECT id_jabatan, harga_shift_1, harga_shift_2, harga_shift_3
        FROM tukang_nws 
        WHERE id = '$id_tukang'
    ");

    if ($query && $data = mysqli_fetch_assoc($query)) {
        
        // 3. Sukses: Mengembalikan ID Jabatan dan HARGA-HARGA SHIFT
        $response = [
            'success' => true, 
            'id_jabatan' => $data['id_jabatan'], // Bisa jadi null, biarkan saja
            'prices' => [ // Kirimkan harga shift individual dalam format yang mudah diakses JS
                'shift1' => (int)$data['harga_shift_1'],
                'shift2' => (int)$data['harga_shift_2'],
                'shift3' => (int)$data['harga_shift_3']
            ]
        ];
        
    } else {
        // Gagal: Tukang tidak ditemukan atau query error
        $response['success'] = false;
        $response['message'] = 'Tukang tidak ditemukan atau database error: ' . mysqli_error($konek);
    }

} catch (Exception $e) {
    // Tangani error koneksi atau runtime lainnya
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 4. Output respons JSON
echo json_encode($response);

// 5. Tutup koneksi
mysqli_close($konek);
?>