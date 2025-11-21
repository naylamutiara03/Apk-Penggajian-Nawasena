<?php
// aksi_lembur.php

// 1. Sertakan koneksi database
include("koneksi.php");

// Pastikan koneksi sukses
if (!$konek) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => "Koneksi database gagal: " . mysqli_connect_error()]);
    exit();
}

// Ambil parameter aksi (act)
$act = $_GET['act'] ?? null;
$response = ['success' => false, 'message' => 'Aksi tidak valid.'];

// Asumsi fungsi formatRupiah
function formatRupiah($angka)
{
    return number_format((int) $angka, 0, ',', '.');
}

// Fungsi pembantu untuk redirect dengan pesan status
function redirectWithError($konek, $message, $location, $extra_params = '')
{
    // Menutup koneksi
    if ($konek) {
        mysqli_close($konek);
    }
    // Mengarahkan ke halaman dengan pesan error
    $pesan_error = urlencode($message);
    header("Location: {$location}?status=error&message={$pesan_error}{$extra_params}");
    exit();
}

try {

    // =========================================================================
    // A. TINDAKAN TAMBAH DATA LEMBUR (POST Request)
    // =========================================================================
    if ($act == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        // Ambil dan sanitasi data
        $id_tukang = $_POST['id_tukang'] ?? '';
        $tgl_lembur = $_POST['tgl_lembur'] ?? '';
        $minggu_ke = $_POST['minggu_ke'] ?? ''; // Diubah dari null ke '' untuk handling form
        $total_harga_lembur = $_POST['total_harga_lembur'] ?? '0';
        $shifts_json = $_POST['shifts_json'] ?? '[]';

        // 🐛 Validasi diperkuat
        if (empty($id_tukang) || empty($tgl_lembur) || !is_numeric($total_harga_lembur) || (int) $total_harga_lembur <= 0) {
            redirectWithError(
                $konek,
                "Input data gagal: ID Tukang, Tanggal, atau Total Harga Lembur tidak valid.",
                "lembur_tukang.php"
            );
        }

        // Sanitasi data
        $id_tukang_safe = mysqli_real_escape_string($konek, $id_tukang);
        $tgl_lembur_safe = mysqli_real_escape_string($konek, $tgl_lembur);
        // Menggunakan NULL jika minggu_ke kosong (dikirikan dari form)
        $minggu_ke_safe = empty($minggu_ke) ? 'NULL' : "'" . mysqli_real_escape_string($konek, $minggu_ke) . "'";
        $total_harga_lembur_safe = (int) $total_harga_lembur;
        $shifts_json_safe = mysqli_real_escape_string($konek, $shifts_json);

        // Ambil ID shift dari elemen pertama yang dipilih
        $shifts_array = json_decode($shifts_json, true);
        $first_shift_id = !empty($shifts_array) ? $shifts_array[0]['id'] : 'N/A';
        $first_shift_safe = mysqli_real_escape_string($konek, $first_shift_id);


        // Query INSERT data lembur
        $query = "INSERT INTO lembur_tkg 
                  (id_tukang, tgl_lembur, minggu_ke, shift, harga_lembur, detail_shifts) 
                  VALUES (
                    '$id_tukang_safe', 
                    '$tgl_lembur_safe', 
                    $minggu_ke_safe, 
                    '$first_shift_safe', 
                    '$total_harga_lembur_safe', 
                    '$shifts_json_safe'
                  )";

        $result = mysqli_query($konek, $query);

        if ($result) {
            $harga_lembur_fmt = formatRupiah((int) $total_harga_lembur);
            $pesan = "Data lembur berhasil ditambahkan. Total: Rp {$harga_lembur_fmt}.";
            $pesan_encoded = urlencode($pesan);

            mysqli_close($konek);
            header("Location: lembur_tukang.php?status=success&message={$pesan_encoded}");
            exit();
        } else {
            redirectWithError(
                $konek,
                "Gagal menyimpan data ke database: " . mysqli_error($konek),
                "lembur_tukang.php"
            );
        }
    }

    // =========================================================================
    // B. TINDAKAN EDIT/UPDATE DATA LEMBUR (POST Request)
    // =========================================================================
    elseif ($act == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        // Ambil dan sanitasi data
        $id_lembur = $_POST['id_lembur'] ?? 0;
        $id_tukang = $_POST['id_tukang'] ?? '';
        $tgl_lembur = $_POST['tgl_lembur'] ?? '';
        $minggu_ke = $_POST['minggu_ke'] ?? ''; // 💡 PERBAIKAN: Ambil sebagai string kosong
        $total_harga_lembur = $_POST['total_harga_lembur'] ?? '0';
        $shifts_json = $_POST['shifts_json'] ?? '[]';

        // Validasi edit TANPA memaksa total lembur > 0
        if (empty($id_tukang) || empty($tgl_lembur) || !is_numeric($total_harga_lembur) || !is_numeric($id_lembur) || (int) $id_lembur <= 0) {
            $id_lembur_param = !empty($id_lembur) ? "&view=edit&id={$id_lembur}" : '';
            redirectWithError(
                $konek,
                "Update data gagal: Data tidak valid. (ID Lembur: {$id_lembur}, Total: {$total_harga_lembur})",
                "lembur_tukang.php",
                $id_lembur_param
            );
        }


        // Sanitasi data
        $id_lembur_safe = mysqli_real_escape_string($konek, $id_lembur);
        $id_tukang_safe = mysqli_real_escape_string($konek, $id_tukang);
        $tgl_lembur_safe = mysqli_real_escape_string($konek, $tgl_lembur);
        // 💡 PERBAIKAN: Menggunakan NULL jika minggu_ke kosong (termasuk jika dikirim dari form dengan value="")
        $minggu_ke_safe = empty($minggu_ke) ? 'NULL' : "'" . mysqli_real_escape_string($konek, $minggu_ke) . "'";
        $total_harga_lembur_safe = (int) $total_harga_lembur;
        $shifts_json_safe = mysqli_real_escape_string($konek, $shifts_json);

        // Ambil ID shift pertama
        $shifts_array = json_decode($shifts_json, true);
        $first_shift_id = (!empty($shifts_array) && isset($shifts_array[0]['id'])) ? $shifts_array[0]['id'] : '';
        $first_shift_safe = mysqli_real_escape_string($konek, $first_shift_id);

        // Query UPDATE data lembur
        $query = "UPDATE lembur_tkg SET 
                      id_tukang = '$id_tukang_safe', 
                      tgl_lembur = '$tgl_lembur_safe', 
                      minggu_ke = $minggu_ke_safe, 
                      shift = '$first_shift_safe', 
                      harga_lembur = '$total_harga_lembur_safe', 
                      detail_shifts = '$shifts_json_safe' 
                  WHERE id = '$id_lembur_safe'";

        $result = mysqli_query($konek, $query);

        if ($result) {
            $harga_lembur_fmt = formatRupiah((int) $total_harga_lembur);
            $pesan = "Data lembur ID {$id_lembur_safe} berhasil diperbarui. Total: Rp {$harga_lembur_fmt}.";
            $pesan_encoded = urlencode($pesan);

            mysqli_close($konek);
            header("Location: lembur_tukang.php?status=success&message={$pesan_encoded}");
            exit();
        } else {
            // Tambahkan id_lembur ke parameter redirect
            $id_lembur_param = !empty($id_lembur) ? "&view=edit&id={$id_lembur}" : '';
            redirectWithError(
                $konek,
                "Gagal memperbarui data ke database: " . mysqli_error($konek),
                "lembur_tukang.php",
                $id_lembur_param
            );
        }
    }


    // =========================================================================
    // C. TINDAKAN HAPUS DATA LEMBUR (GET Request - AJAX)
    // =========================================================================
    elseif ($act == 'hapus' && $_SERVER['REQUEST_METHOD'] == 'GET') {

        $id_lembur = $_GET['id'] ?? 0;
        $id_lembur_safe = 0;

        if (!is_numeric($id_lembur) || (int) $id_lembur <= 0) {
            throw new Exception("ID data lembur tidak valid untuk dihapus.");
        }

        $id_lembur_safe = mysqli_real_escape_string($konek, $id_lembur);

        // Query DELETE
        $query = "DELETE FROM lembur_tkg WHERE id = '$id_lembur_safe'";
        $result = mysqli_query($konek, $query);

        if ($result && mysqli_affected_rows($konek) > 0) {
            $response['success'] = true;
            $response['message'] = "Data lembur ID $id_lembur_safe berhasil dihapus.";
        } elseif ($result && mysqli_affected_rows($konek) == 0) {
            throw new Exception("Data lembur ID $id_lembur_safe tidak ditemukan.");
        } else {
            throw new Exception("Gagal menghapus data dari database: " . mysqli_error($konek));
        }
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// 2. PENANGANAN OUTPUT (JSON atau EXIT)
if ($act == 'hapus') {
    header('Content-Type: application/json');
    echo json_encode($response);
    mysqli_close($konek);
    exit();
} else {
    mysqli_close($konek);
    exit();
}
// Tidak menggunakan tag penutup PHP di akhir file