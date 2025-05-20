<?php
include 'koneksi.php'; // File koneksi database

$action = isset($_GET['act']) ? $_GET['act'] : '';

if ($action === 'tambah') {
    // Ambil dan sanitasi data input
    $nik = htmlspecialchars($_POST['nik']);
    $bulan = htmlspecialchars($_POST['bulan']);
    $tahun = htmlspecialchars($_POST['tahun']);
    $jamMasuk = htmlspecialchars($_POST['jam_masuk']);
    $jamKeluar = htmlspecialchars($_POST['jam_keluar']);
    $tanggalMasuk = htmlspecialchars($_POST['tanggal_masuk']);
    $tanggalKeluar = htmlspecialchars($_POST['tanggal_keluar']);

    // Validasi tanggal
    if ($tanggalKeluar < $tanggalMasuk) {
        die(json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']));
    }

    // Hitung total_hadir otomatis
    $start = strtotime("$tanggalMasuk $jamMasuk");
    $end = strtotime("$tanggalKeluar $jamKeluar");

    $durasiShift = [
        ['start' => 9, 'end' => 17],  // Shift 1: 09:00–17:00
        ['start' => 18, 'end' => 24],  // Shift 2: 18:00–00:00
        ['start' => 0, 'end' => 6]    // Shift 3: 00:00–06:00
    ];

    $totalHadir = 0;

    if ($end > $start) {
        $selisihJam = ($end - $start) / 3600;

        if ($selisihJam <= 2) {
            $totalHadir = 0;
        } elseif ($selisihJam > 2 && $selisihJam < 5) {
            $totalHadir = 0.5;
        } else {
            $totalHadir = round(($selisihJam / 8) * 2) / 2; // dibulatkan ke 0.5 terdekat
        }
    }

    // Simpan ke database
    $query = mysqli_query($konek, "INSERT INTO absensi_tukang 
        (nik, bulan, tahun, jam_masuk, jam_keluar, total_hadir, tanggal_masuk, tanggal_keluar)
        VALUES 
        ('$nik', '$bulan', '$tahun', '$jamMasuk', '$jamKeluar', '$totalHadir', '$tanggalMasuk', '$tanggalKeluar')");

    if ($query) {
        header("Location: data_absensi.php?bulan=$bulan&tahun=$tahun");
        exit;
    } else {
        echo "Gagal tambah data: " . mysqli_error($konek);
    }

} elseif ($action === 'delete') {
    header('Content-Type: application/json');
    $id = isset($_GET['id']) ? $_GET['id'] : '';

    if (isset($_GET['act']) && $_GET['act'] == 'delete') {
        if (isset($_GET['ids'])) {
            $ids = explode(',', $_GET['ids']);
            $ids = array_map('intval', $ids); // Sanitize input
            $idsList = implode(',', $ids);

            $query = "DELETE FROM absensi_tukang WHERE id IN ($idsList)";
            if (mysqli_query($konek, $query)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus data.']);
            }
        }
    }
} elseif ($action === 'edit') {
    // Ambil dan sanitasi data input
    $id = htmlspecialchars($_POST['id']);
    $nik = htmlspecialchars($_POST['nik']);
    $bulan = htmlspecialchars($_POST['bulan']);
    $tahun = htmlspecialchars($_POST['tahun']);
    $jamMasuk = htmlspecialchars($_POST['jam_masuk']);
    $jamKeluar = htmlspecialchars($_POST['jam_keluar']);
    $tanggalMasuk = htmlspecialchars($_POST['tanggal_masuk']);
    $tanggalKeluar = htmlspecialchars($_POST['tanggal_keluar']);

    // Validasi tanggal
    if ($tanggalKeluar < $tanggalMasuk) {
        die(json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']));
    }

    // Hitung total_hadir otomatis
    $start = strtotime("$tanggalMasuk $jamMasuk");
    $end = strtotime("$tanggalKeluar $jamKeluar");

    $durasiShift = [
        ['start' => 9, 'end' => 17],  // Shift 1: 09:00–17:00
        ['start' => 18, 'end' => 24],  // Shift 2: 18:00–00:00
        ['start' => 0, 'end' => 6]    // Shift 3: 00:00–06:00
    ];

    $totalHadir = 0;

    if ($end > $start) {
        $time = $start;

        while ($time < $end) {
            $jam = (int) date("G", $time);
            $menit = (int) date("i", $time);
            $jamDesimal = $jam + ($menit / 60);

            foreach ($durasiShift as $shift) {
                if ($shift['start'] <= $jamDesimal && $jamDesimal < $shift['end']) {
                    $totalHadir += 1 / ($shift['end'] - $shift['start']);
                    break;
                }
            }

            $time += 3600; // Tambah 1 jam
        }

        $totalHadir = round($totalHadir, 2);

        // Jika jam kerja < 5 jam dan tanggal sama, hitung setengah hari
        $selisihJam = ($end - $start) / 3600;
        if ($tanggalMasuk === $tanggalKeluar && $selisihJam >= 3 && $selisihJam <= 5) {
            $totalHadir = 0.5;
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
        header("Location: data_absensi.php?bulan=$bulan&tahun=$tahun");
        exit;
    } else {
        echo "Gagal edit data: " . mysqli_error($konek);
    }
}

