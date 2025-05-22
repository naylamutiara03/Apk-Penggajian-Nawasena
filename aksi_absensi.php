<?php
include 'koneksi.php'; // File koneksi database

// Set header to indicate JSON response for all AJAX requests
// This should be at the very top before any output, but only if it's an AJAX request.
// For regular form submissions that redirect, don't set this header.
// A simple way to check if it's an AJAX request is to look for X-Requested-With header,
// but since your current JS already expects JSON for deletes and POST for edits,
// we'll explicitly set it for delete and edit actions.

$action = isset($_GET['act']) ? $_GET['act'] : '';

if ($action === 'tambah') {
    // This action still performs a redirect after successful insertion, so no JSON header here.
    // However, if there's an error, we should exit with JSON.
    // Ambil dan sanitasi data input
    $nik = mysqli_real_escape_string($konek, $_POST['nik']);
    $bulan = mysqli_real_escape_string($konek, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($konek, $_POST['tahun']);
    $jamMasuk = mysqli_real_escape_string($konek, $_POST['jam_masuk']);
    $jamKeluar = mysqli_real_escape_string($konek, $_POST['jam_keluar']);
    $tanggalMasuk = mysqli_real_escape_string($konek, $_POST['tanggal_masuk']);
    $tanggalKeluar = mysqli_real_escape_string($konek, $_POST['tanggal_keluar']);

    // Validasi tanggal
    if ($tanggalKeluar < $tanggalMasuk) {
        header('Content-Type: application/json'); // Set JSON header for error response
        echo json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        exit;
    }

    // Hitung total_hadir otomatis
    $start = strtotime("$tanggalMasuk $jamMasuk");
    $end = strtotime("$tanggalKeluar $jamKeluar");

    $totalHadir = 0;

    if ($end > $start) {
        $selisihDetik = $end - $start;
        $selisihJam = $selisihDetik / 3600; // Convert to hours

        if ($selisihJam <= 2) {
            $totalHadir = 0;
        } elseif ($selisihJam > 2 && $selisihJam < 5) {
            $totalHadir = 0.5;
        } else {
            // Assuming 8 hours is a full day, and rounding to nearest 0.5
            $totalHadir = round(($selisihJam / 8) * 2) / 2;
        }
    }

    // Simpan ke database
    $query = mysqli_query($konek, "INSERT INTO absensi_tukang
        (nik, bulan, tahun, jam_masuk, jam_keluar, total_hadir, tanggal_masuk, tanggal_keluar)
        VALUES
        ('$nik', '$bulan', '$tahun', '$jamMasuk', '$jamKeluar', '$totalHadir', '$tanggalMasuk', '$tanggalKeluar')");

    if ($query) {
        // Redirect on success
        header("Location: data_absensi.php?bulan=$bulan&tahun=$tahun");
        exit;
    } else {
        // Output error in JSON format if insertion fails, although typically this would redirect as well
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Gagal tambah data: ' . mysqli_error($konek)]);
        exit;
    }

} elseif ($action === 'delete') {
    // This action always returns JSON, so set the header here
    header('Content-Type: application/json');

    if (isset($_GET['ids'])) {
        // Multiple deletion
        $ids = explode(',', $_GET['ids']);
        // Sanitize each ID individually using mysqli_real_escape_string
        $cleanedIds = array_map(function ($id) use ($konek) {
            // Ensure IDs are numeric or cast them
            return (int) mysqli_real_escape_string($konek, trim($id));
        }, $ids);

        // Filter out any non-numeric or empty values after sanitization
        $cleanedIds = array_filter($cleanedIds, function($value) {
            return is_numeric($value) && $value > 0;
        });

        $idsList = implode(',', $cleanedIds);

        if (!empty($idsList)) {
            $query = "DELETE FROM absensi_tukang WHERE id IN ($idsList)";
            if (mysqli_query($konek, $query)) {
                echo json_encode(['success' => true, 'message' => 'Data absensi terpilih berhasil dihapus.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus data absensi terpilih: ' . mysqli_error($konek)]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tidak ada ID yang valid dipilih untuk dihapus.']);
        }
    } elseif (isset($_GET['id'])) {
        // Single deletion
        $id = (int) mysqli_real_escape_string($konek, $_GET['id']); // Ensure ID is integer
        $query = "DELETE FROM absensi_tukang WHERE id = $id";
        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Data absensi berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . mysqli_error($konek)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Parameter ID atau IDs tidak ditemukan.']);
    }
    exit; // Always exit after JSON response for AJAX delete
} elseif ($action === 'edit') {
    // This action should also return JSON, so set the header here
    header('Content-Type: application/json');

    // Ambil dan sanitasi data input
    $id = (int) mysqli_real_escape_string($konek, $_POST['id']);
    $nik = mysqli_real_escape_string($konek, $_POST['nik']);
    $bulan = mysqli_real_escape_string($konek, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($konek, $_POST['tahun']);
    $jamMasuk = mysqli_real_escape_string($konek, $_POST['jam_masuk']);
    $jamKeluar = mysqli_real_escape_string($konek, $_POST['jam_keluar']);
    $tanggalMasuk = mysqli_real_escape_string($konek, $_POST['tanggal_masuk']);
    $tanggalKeluar = mysqli_real_escape_string($konek, $_POST['tanggal_keluar']);

    // Validasi tanggal
    if ($tanggalKeluar < $tanggalMasuk) {
        echo json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        exit;
    }

    // Hitung total_hadir otomatis (re-using the logic from 'tambah' for consistency)
    $start = strtotime("$tanggalMasuk $jamMasuk");
    $end = strtotime("$tanggalKeluar $jamKeluar");

    $totalHadir = 0;

    if ($end > $start) {
        $selisihDetik = $end - $start;
        $selisihJam = $selisihDetik / 3600; // Convert to hours

        if ($selisihJam <= 2) {
            $totalHadir = 0;
        } elseif ($selisihJam > 2 && $selisihJam < 5) {
            $totalHadir = 0.5;
        } else {
            // Assuming 8 hours is a full day, and rounding to nearest 0.5
            $totalHadir = round(($selisihJam / 8) * 2) / 2;
        }
    }


    // Update database
    $query = mysqli_query($konek, "UPDATE absensi_tukang SET
        nik = '$nik',
        bulan = '$bulan',
        tahun = '$tahun',
        jam_masuk = '$jamMasuk',
        jam_keluar = '$jamKeluar',
        tanggal_masuk = '$tanggalMasuk',
        tanggal_keluar = '$tanggalKeluar',
        total_hadir = '$totalHadir'
        WHERE id = '$id'");

    if ($query) {
        echo json_encode(['success' => true, 'message' => 'Data absensi berhasil diperbarui.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal edit data: ' . mysqli_error($konek)]);
    }
    exit; // Always exit after JSON response for AJAX edit
} else {
    // Default response for invalid action, typically not accessed directly
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
    exit;
}
?>