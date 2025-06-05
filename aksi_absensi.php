<?php
include 'koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 0); // Matikan tampilan error agar JSON bersih

// Fungsi perhitungan total hadir berdasarkan shift yang sudah diperbaiki
// Fungsi hitungTotalHadir yang sudah benar:
function hitungTotalHadir($start, $end)
{
    $shifts = [
        ['start' => '09:00', 'end' => '17:00'],
        ['start' => '18:00', 'end' => '23:59'],
        ['start' => '00:00', 'end' => '06:00']
    ];

    $totalHari = 0;
    $checkedShifts = [];

    $tanggalSekarang = $start;

    while (strtotime(date('Y-m-d', $tanggalSekarang)) <= strtotime(date('Y-m-d', $end))) {
        foreach ($shifts as $shift) {
            $shiftDate = date('Y-m-d', $tanggalSekarang);
            if ($shift['end'] === '06:00') {
                $shiftDate = date('Y-m-d', strtotime('+1 day', strtotime($shiftDate)));
            }
            $shiftStart = strtotime($shiftDate . ' ' . $shift['start']);
            $shiftEnd = strtotime($shiftDate . ' ' . $shift['end']);

            $shiftKey = $shiftDate . '_' . $shift['start'] . '_' . $shift['end'];
            if (in_array($shiftKey, $checkedShifts))
                continue;

            $overlapStart = max($start, $shiftStart);
            $overlapEnd = min($end, $shiftEnd);

            if ($overlapEnd > $overlapStart) {
                $durasiJam = ($overlapEnd - $overlapStart) / 3600;

                if ($durasiJam >= 4) {
                    $totalHari += 1;
                } elseif ($durasiJam >= 2) {
                    $totalHari += 0.5;
                }

                $checkedShifts[] = $shiftKey;
            }
        }

        $tanggalSekarang = strtotime('+1 day', $tanggalSekarang);
    }

    return $totalHari;
}

$action = isset($_GET['act']) ? $_GET['act'] : '';

if ($action === 'tambah') {
    $nik = mysqli_real_escape_string($konek, $_POST['nik'] ?? '');
    $bulan = mysqli_real_escape_string($konek, $_POST['bulan'] ?? '');
    $tahun = mysqli_real_escape_string($konek, $_POST['tahun'] ?? '');
    $jamMasuk = mysqli_real_escape_string($konek, $_POST['jam_masuk'] ?? '');
    $jamKeluar = mysqli_real_escape_string($konek, $_POST['jam_keluar'] ?? '');
    $tanggalMasuk = mysqli_real_escape_string($konek, $_POST['tanggal_masuk'] ?? '');
    $tanggalKeluar = mysqli_real_escape_string($konek, $_POST['tanggal_keluar'] ?? '');

    $minggu = isset($_POST['minggu']) ? (int) $_POST['minggu'] : 1;
    $minggu = max(1, min($minggu, 5));

    if ($tanggalKeluar < $tanggalMasuk) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        exit;
    }

    $cekDuplikat = mysqli_query($konek, "SELECT id FROM absensi_tukang 
        WHERE nik = '$nik' 
        AND tanggal_masuk = '$tanggalMasuk' 
        AND jam_masuk = '$jamMasuk' 
        AND jam_keluar = '$jamKeluar'");

    if (mysqli_num_rows($cekDuplikat) > 0) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'type' => 'duplicate',
            'message' => 'Data absensi dengan waktu dan tanggal yang sama sudah ada.'
        ]);
        exit;
    }

    $startTimestamp = strtotime("$tanggalMasuk $jamMasuk");
    $endTimestamp = strtotime("$tanggalKeluar $jamKeluar");
    $totalHadir = hitungTotalHadir($startTimestamp, $endTimestamp);

    $query = mysqli_query($konek, "INSERT INTO absensi_tukang
        (nik, bulan, tahun, minggu, jam_masuk, jam_keluar, total_hadir, tanggal_masuk, tanggal_keluar)
        VALUES
        ('$nik', '$bulan', '$tahun', '$minggu', '$jamMasuk', '$jamKeluar', '$totalHadir', '$tanggalMasuk', '$tanggalKeluar')");

    if ($query) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Data absensi berhasil ditambahkan.',
            'redirect' => "data_absensi.php?bulan=$bulan&tahun=$tahun&minggu=$minggu"
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Gagal tambah data: ' . mysqli_error($konek)
        ]);
    }
    exit;
}

if ($action === 'edit') {
    header('Content-Type: application/json');
    $id = (int) mysqli_real_escape_string($konek, $_POST['id']);
    $nik = mysqli_real_escape_string($konek, $_POST['nik']);
    $bulan = mysqli_real_escape_string($konek, $_POST['bulan']);
    $tahun = mysqli_real_escape_string($konek, $_POST['tahun']);
    $jamMasuk = mysqli_real_escape_string($konek, $_POST['jam_masuk']);
    $jamKeluar = mysqli_real_escape_string($konek, $_POST['jam_keluar']);
    $tanggalMasuk = mysqli_real_escape_string($konek, $_POST['tanggal_masuk']);
    $tanggalKeluar = mysqli_real_escape_string($konek, $_POST['tanggal_keluar']);
    $minggu = max(1, min((int) ($_POST['minggu'] ?? 1), 5));

    if ($tanggalKeluar < $tanggalMasuk) {
        echo json_encode(['success' => false, 'message' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        exit;
    }

    $startTimestamp = strtotime("$tanggalMasuk $jamMasuk");
    $endTimestamp = strtotime("$tanggalKeluar $jamKeluar");
    $totalHadir = hitungTotalHadir($startTimestamp, $endTimestamp);

    $query = mysqli_query($konek, "UPDATE absensi_tukang SET
        nik = '$nik',
        bulan = '$bulan',
        tahun = '$tahun',
        jam_masuk = '$jamMasuk',
        jam_keluar = '$jamKeluar',
        tanggal_masuk = '$tanggalMasuk',
        tanggal_keluar = '$tanggalKeluar',
        minggu = '$minggu',
        total_hadir = '$totalHadir'
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
}

// Bagian tampilan data (jika digunakan dalam file ini)
if (isset($bulanFilter) && isset($tahunFilter) && isset($mingguFilter)) {
    $bulanInt = intval($bulanFilter);
    $mingguInt = intval($mingguFilter);

    $queryAbsensi = mysqli_query($konek, "
        SELECT a.*, t.nama_tukang, t.jenis_kelamin, t.id_jabatan
        FROM absensi_tukang a
        JOIN tukang_nws t ON a.nik = t.nik
        WHERE MONTH(a.tanggal_masuk) = $bulanInt
        AND YEAR(a.tanggal_masuk) = '$tahunFilter'
        AND a.minggu = $mingguInt
        ORDER BY a.id DESC
    ");

    if (mysqli_num_rows($queryAbsensi) > 0) {
        while ($row = mysqli_fetch_assoc($queryAbsensi)) {
            // Tampilkan data di tabel
        }
    } else {
        echo "<tr>
            <td colspan='10' class='text-center text-gray-400 py-4'>Data belum tersedia untuk filter tersebut.</td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='10' class='text-center text-gray-400 py-4'>Silakan pilih bulan, tahun, dan minggu terlebih dahulu.</td>
    </tr>";
}
?>