<?php
include 'koneksi.php'; // File koneksi database

$action = isset($_GET['act']) ? $_GET['act'] : '';

if ($action === 'tambah') {
    // Ambil dan sanitasi data input
    $nik = mysqli_real_escape_string($konek, $_POST['nik']);
    $bulan = mysqli_real_escape_string($konek, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($konek, $_POST['tahun']);
    $jamMasuk = mysqli_real_escape_string($konek, $_POST['jam_masuk']);
    $jamKeluar = mysqli_real_escape_string($konek, $_POST['jam_keluar']);
    $tanggalMasuk = mysqli_real_escape_string($konek, $_POST['tanggal_masuk']);
    $tanggalKeluar = mysqli_real_escape_string($konek, $_POST['tanggal_keluar']);

    // Tangkap dan validasi minggu
    $minggu = isset($_POST['minggu']) ? (int) mysqli_real_escape_string($konek, $_POST['minggu']) : 1;
    if ($minggu < 1)
        $minggu = 1;
    if ($minggu > 5)
        $minggu = 5;

    // Validasi tanggal
    if ($tanggalKeluar < $tanggalMasuk) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        exit;
    }

    // Hitung total_hadir otomatis
    $start = strtotime("$tanggalMasuk $jamMasuk");
    $end = strtotime("$tanggalKeluar $jamKeluar");

    $totalHadir = 0;

    if ($end > $start) {
        $selisihDetik = $end - $start;
        $selisihJam = $selisihDetik / 3600;

        if ($selisihJam <= 2) {
            $totalHadir = 0;
        } elseif ($selisihJam > 2 && $selisihJam < 5) {
            $totalHadir = 0.5;
        } else {
            $totalHadir = round(($selisihJam / 8) * 2) / 2;
        }
    }

    // Simpan ke database termasuk minggu
    $query = mysqli_query($konek, "INSERT INTO absensi_tukang
    (nik, bulan, tahun, minggu, jam_masuk, jam_keluar, total_hadir, tanggal_masuk, tanggal_keluar)
    VALUES
    ('$nik', '$bulan', '$tahun', '$minggu', '$jamMasuk', '$jamKeluar', '$totalHadir', '$tanggalMasuk', '$tanggalKeluar')");

    if ($query) {
        // Redirect ke halaman data dengan filter bulan, tahun, minggu
        header("Location: data_absensi.php?bulan=$bulan&tahun=$tahun&minggu=$minggu");
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Gagal tambah data: ' . mysqli_error($konek)]);
        exit;
    }

} elseif ($action === 'delete') {
    header('Content-Type: application/json');

    if (isset($_GET['ids'])) {
        $ids = explode(',', $_GET['ids']);
        $cleanedIds = array_map(function ($id) use ($konek) {
            return (int) mysqli_real_escape_string($konek, trim($id));
        }, $ids);
        $cleanedIds = array_filter($cleanedIds, function ($value) {
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
        $id = (int) mysqli_real_escape_string($konek, $_GET['id']);
        $query = "DELETE FROM absensi_tukang WHERE id = $id";
        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Data absensi berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . mysqli_error($konek)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Parameter ID atau IDs tidak ditemukan.']);
    }
    exit;

} elseif ($action === 'edit') {
    header('Content-Type: application/json');

    $id = (int) mysqli_real_escape_string($konek, $_POST['id']);
    $nik = mysqli_real_escape_string($konek, $_POST['nik']);
    $bulan = mysqli_real_escape_string($konek, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($konek, $_POST['tahun']);
    $jamMasuk = mysqli_real_escape_string($konek, $_POST['jam_masuk']);
    $jamKeluar = mysqli_real_escape_string($konek, $_POST['jam_keluar']);
    $tanggalMasuk = mysqli_real_escape_string($konek, $_POST['tanggal_masuk']);
    $tanggalKeluar = mysqli_real_escape_string($konek, $_POST['tanggal_keluar']);

    // Tangkap dan validasi minggu
    $minggu = isset($_POST['minggu']) ? (int) $_POST['minggu'] : 1;
    if ($minggu < 1)
        $minggu = 1;
    if ($minggu > 5)
        $minggu = 5;

    if ($tanggalKeluar < $tanggalMasuk) {
        echo json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        exit;
    }

    $start = strtotime("$tanggalMasuk $jamMasuk");
    $end = strtotime("$tanggalKeluar $jamKeluar");

    $totalHadir = 0;

    if ($end > $start) {
        $selisihDetik = $end - $start;
        $selisihJam = $selisihDetik / 3600;

        if ($selisihJam <= 2) {
            $totalHadir = 0;
        } elseif ($selisihJam > 2 && $selisihJam < 5) {
            $totalHadir = 0.5;
        } else {
            $totalHadir = round(($selisihJam / 8) * 2) / 2;
        }
    }

    $query = mysqli_query($konek, "UPDATE absensi_tukang SET
        nik = '$nik',
        bulan = '$bulan',
        tahun = '$tahun',
        jam_masuk = '$jamMasuk',
        jam_keluar = '$jamKeluar',
        tanggal_masuk = '$tanggalMasuk',
        tanggal_keluar = '$tanggalKeluar',
        total_hadir = '$totalHadir',
        minggu = '$minggu'
        WHERE id = '$id'");

    if ($query) {
        echo json_encode([
            'success' => true,
            'message' => 'Data absensi berhasil diperbarui.',
            'redirect' => "data_absensi.php?bulan=$bulan&tahun=$tahun&minggu=$minggu"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal edit data: ' . mysqli_error($konek)]);
    }
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
    exit;
}
?>