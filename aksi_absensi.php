<?php
include 'koneksi.php'; // File koneksi

$action = isset($_GET['act']) ? $_GET['act'] : '';

if ($action === 'tambah') {
    $nik           = $_POST['nik'];
    $bulan         = $_POST['bulan'];
    $tahun         = $_POST['tahun'];
    $jam_masuk     = $_POST['jam_masuk'];
    $jam_keluar    = $_POST['jam_keluar'];
    $tanggal_masuk = $_POST['tanggal_masuk'];
    $tanggal_keluar= $_POST['tanggal_keluar'];

    // Hitung total_hadir otomatis
    $start = strtotime("$tanggal_masuk $jam_masuk");
    $end   = strtotime("$tanggal_keluar $jam_keluar");

    $durations = [
        ['start' => 9,  'end' => 17],     // 1 hari: 09:00–17:00
        ['start' => 18, 'end' => 24],     // 1 hari: 18:00–00:00
        ['start' => 0,  'end' => 6]       // 1 hari: 00:00–06:00
    ];

    $total_hadir = 0;

    if ($end > $start) {
        $time = $start;

        while ($time < $end) {
            $jam    = (int)date("G", $time);
            $menit  = (int)date("i", $time);
            $jam_decimal = $jam + ($menit / 60);

            foreach ($durations as $shift) {
                if ($shift['start'] <= $jam_decimal && $jam_decimal < $shift['end']) {
                    $total_hadir += 1 / ($shift['end'] - $shift['start']);
                    break;
                }
            }

            $time += 3600; // tambah 1 jam
        }

        $total_hadir = round($total_hadir, 2);
    } else {
        $total_hadir = 0; // fallback untuk input yang tidak valid (jam keluar <= jam masuk)
    }

    // Simpan ke database
    $query = mysqli_query($konek, "INSERT INTO absensi_tukang 
        (nik, bulan, tahun, jam_masuk, jam_keluar, total_hadir, tanggal_masuk, tanggal_keluar)
        VALUES 
        ('$nik', '$bulan', '$tahun', '$jam_masuk', '$jam_keluar', '$total_hadir', '$tanggal_masuk', '$tanggal_keluar')");

    if ($query) {
        header("Location: absensi_tukang.php?success=tambah");
        exit;
    } else {
        echo "Gagal tambah data: " . mysqli_error($konek);
    }
} elseif ($action === 'delete') {
    $id = isset($_GET['id']) ? $_GET['id'] : '';

    header('Content-Type: application/json');

    if (!empty($id)) {
        $query = "DELETE FROM absensi_tukang WHERE id = '$id'";
        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Data absensi berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus data: ' . mysqli_error($konek)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan.']);
    }
}
