<?php
session_start();
include("koneksi.php");
include("sidebar.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); exit;
}

// Variabel GET
$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

$bulan_nama = [
    "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni",
    "07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Laporan Gaji Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="p-6 lg:ml-[300px] flex-grow">

    <!-- HEADER -->
    <div class="bg-white px-4 py-3 rounded shadow mb-6 text-center">
        <h1 class="text-2xl font-bold">LAPORAN GAJI KARYAWAN</h1>
        <p class="mt-1 text-gray-700 font-semibold">
            Bulan: <?= $bulan_nama[$bulan] ?> <?= $tahun ?>
        </p>
    </div>

    <!-- Tombol -->
    <div class="flex justify-between mb-5">
        <a href="laporan_gaji_karyawan.php"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
           ← Kembali
        </a>
        <a href="export_gaji_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
           Export Excel
        </a>
    </div>

<?php
// ======================== DATA KARYAWAN ========================
$karyawan = $konek->query("SELECT * FROM karyawan ORDER BY nama_karyawan ASC");

while ($data = $karyawan->fetch_assoc()):
$id_karyawan = $data['id'];

// gaji pokok
$get_jab = $konek->query("SELECT gapok FROM jabatan WHERE id='{$data['id_jabatan']}'")->fetch_assoc();
$gaji_pokok = $get_jab ? $get_jab['gapok'] : 0;

// Gaji per menit
$gaji_per_menit = $gaji_pokok / (20 * 8 * 60);

// Ambil absensi bulan & tahun
$absensi = $konek->query("
    SELECT * FROM absensi_karyawan
    WHERE id_karyawan='$id_karyawan'
    AND MONTH(tgl_absen)='$bulan'
    AND YEAR(tgl_absen)='$tahun'
    AND jam_masuk IS NOT NULL AND jam_keluar IS NOT NULL
    ORDER BY tgl_absen ASC
");

$total_potongan = 0;
$total_lembur = 0;
?>

<!-- CARD PER KARYAWAN -->
<div class="bg-white shadow rounded-lg p-6 mb-6 border border-gray-200">

    <!-- Nama -->
    <div class="flex justify-between mb-3">
        <h2 class="text-xl font-bold text-blue-700"><?= $data['nama_karyawan'] ?></h2>
        <span class="text-lg font-semibold text-green-700">
            Gaji Pokok: Rp <?= number_format($gaji_pokok,0,',','.') ?>
        </span>
    </div>

    <!-- TABEL GAJI -->
    <table class="w-full text-sm border border-gray-300">
        <thead class="bg-gray-200 font-semibold text-gray-800">
            <tr>
                <th class="border p-2">Tanggal</th>
                <th class="border p-2">Telat</th>
                <th class="border p-2">Potongan</th>
                <th class="border p-2">Lembur</th>
                <th class="border p-2">Uang Lembur</th>
            </tr>
        </thead>
        <tbody>

        <?php if ($absensi->num_rows == 0): ?>
            <tr><td colspan="5" class="text-center text-gray-500 p-3">Tidak ada absensi bulan ini.</td></tr>
        <?php endif; ?>

        <?php while ($row = $absensi->fetch_assoc()):
            // HITUNG TELAT
            $telat = $row['telat_menit'];
            if (!$telat || $telat <= 0) {
                $telat = max(0,(strtotime($row['jam_masuk']) - strtotime("09:00:00"))/60);
            }
            $telat = round($telat);
            $pot_telat = round($telat * $gaji_per_menit,-3);
            $total_potongan += $pot_telat;

            // HITUNG LEMBUR
            $jam_keluar = strtotime($row['jam_keluar']);
            $awal_lembur = strtotime("18:31:00");
            $max_lembur = strtotime("22:00:00");

            if ($jam_keluar <= $awal_lembur) { $lembur = 0; }
            else {
                if ($jam_keluar > $max_lembur) $jam_keluar = $max_lembur;
                $lembur = floor(($jam_keluar - $awal_lembur)/60);
            }
            $uang_lembur = round($lembur * $gaji_per_menit,-3);
            $total_lembur += $uang_lembur;
        ?>
            <tr class="text-center">
                <td class="border p-2"><?= $row['tgl_absen'] ?></td>
                <td class="border p-2"><?= $telat ?> menit</td>
                <td class="border p-2 text-red-600">Rp <?= number_format($pot_telat,0,',','.') ?></td>
                <td class="border p-2"><?= $lembur ?> menit</td>
                <td class="border p-2 text-green-600">Rp <?= number_format($uang_lembur,0,',','.') ?></td>
            </tr>
        <?php endwhile; ?>

        </tbody>
    </table>

    <!-- TOTAL -->
    <div class="mt-4 font-bold text-lg">
        <p>Total Potongan: <span class="text-red-600">Rp <?= number_format($total_potongan,0,',','.') ?></span></p>
        <p>Total Uang Lembur: <span class="text-green-600">Rp <?= number_format($total_lembur,0,',','.') ?></span></p>
        <p class="mt-2 text-blue-800">Gaji Akhir:
            <span class="text-black font-extrabold">
                Rp <?= number_format(($gaji_pokok + $total_lembur - $total_potongan),0,',','.') ?>
            </span>
        </p>
    </div>
</div>

<?php endwhile; ?>

<?php include("footer.php"); ?>
</div>
</body>
</html>
