<?php
session_start();
include("koneksi.php");
include("sidebar.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Lembur Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons.min.js"></script>
</head>

<body class="bg-gray-100">

    <div class="p-6 lg:ml-[300px] flex-grow">

        <!-- Header -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang,
                        <strong><?= htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>

        <!-- Judul Page -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Data Lembur Karyawan</h1>
            <span class="text-gray-500"><?= date('d F Y'); ?></span>
        </div>

        <!-- Filter Bulan & Tahun -->
        <form method="GET" class="flex flex-col md:flex-row gap-3 mb-5">

            <select name="bulan" class="border rounded-lg p-2" required>
                <option value="">-- Pilih Bulan --</option>
                <?php
                $bulanNama = [
                    1 => "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember"
                ];
                for ($i = 1; $i <= 12; $i++) {
                    $sel = (@$_GET['bulan'] == $i) ? "selected" : "";
                    echo "<option value='$i' $sel>$bulanNama[$i]</option>";
                }
                ?>
            </select>

            <select name="tahun" class="border rounded-lg p-2" required>
                <option value="">-- Pilih Tahun --</option>
                <?php
                for ($t = date('Y') - 3; $t <= date('Y'); $t++) {
                    $sel = (@$_GET['tahun'] == $t) ? "selected" : "";
                    echo "<option value='$t' $sel>$t</option>";
                }
                ?>
            </select>

            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Tampilkan
            </button>

            <?php if (isset($_GET['bulan']) && isset($_GET['tahun'])) { ?>
                <a href="export_lembur_excel.php?bulan=<?= $_GET['bulan']; ?>&tahun=<?= $_GET['tahun']; ?>"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Export Excel
                </a>
            <?php } ?>
        </form>


        <!-- Tabel Lembur -->
        <div class="overflow-x-auto bg-white p-5 rounded-lg shadow">
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-gray-900">
                        <th class="border px-4 py-2">No</th>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Tanggal</th>
                        <th class="border px-4 py-2">Jam Masuk</th>
                        <th class="border px-4 py-2">Jam Keluar</th>
                        <th class="border px-4 py-2">Durasi Lembur</th>
                        <th class="border px-4 py-2">Uang Lembur</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $where = "";
                    if (isset($_GET['bulan']) && isset($_GET['tahun'])) {
                        $bulan = $_GET['bulan'];
                        $tahun = $_GET['tahun'];
                        $where = "WHERE MONTH(a.tgl_absen)='$bulan' AND YEAR(a.tgl_absen)='$tahun'";
                    }

                    $query = mysqli_query($konek, "
SELECT a.*, k.nama_karyawan, j.gapok 
FROM absensi_karyawan a
JOIN karyawan k ON a.id_karyawan = k.id
JOIN jabatan j ON k.id_jabatan = j.id
$where
ORDER BY a.tgl_absen DESC
");


                    $no = 1;
                    $total_semua = 0;

                    while ($d = mysqli_fetch_assoc($query)) {
                        $gapok = $d['gapok'];       // Gaji pokok
                        $per_hari = $gapok / 20;       // Gaji per hari (20 hari kerja)
                        $per_jam = $per_hari / 8;     // Gaji per jam (8 jam kerja)
                        $per_menit = $per_jam / 60;     // Gaji per menit
                        $jam_keluar = strtotime($d['jam_keluar']);
                        $batas_awal_lembur = strtotime("18:31:00");
                        $maksimal = strtotime("22:00:00");

                        // Hitung lembur
                        if ($jam_keluar <= $batas_awal_lembur) {
                            $lembur_menit = 0;
                        } else {
                            if ($jam_keluar > $maksimal)
                                $jam_keluar = $maksimal;
                            $lembur_detik = $jam_keluar - $batas_awal_lembur;
                            $lembur_menit = floor($lembur_detik / 60); // Tidak dibulatkan ke atas
                        }


                        // Uang lembur murni tanpa tambahan bonus
                        $uang_lembur = $lembur_menit * $per_menit;

                        // Pembulatan ke ribuan terdekat
                        $uang_lembur = round($uang_lembur, -3);

                        // Tampilkan durasi
                        $durasi = $lembur_menit > 0 ? $lembur_menit . " menit" : "-";

                        $total_semua += $uang_lembur;

                        ?>
                        <tr class="text-center">
                            <td class="border px-4 py-1"><?= $no++; ?></td>
                            <td class="border px-4 py-1"><?= $d['nama_karyawan']; ?></td>
                            <td class="border px-4 py-1"><?= $d['tgl_absen']; ?></td>
                            <td class="border px-4 py-1"><?= $d['jam_masuk']; ?></td>
                            <td class="border px-4 py-1"><?= $d['jam_keluar']; ?></td>
                            <td class="border px-4 py-1"><?= $durasi; ?></td>
                            <td class="border px-4 py-1 font-semibold text-green-600">Rp
                                <?= number_format($uang_lembur, 0, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <tr class="bg-gray-100 font-bold">
                        <td colspan="6" class="border px-4 py-2 text-right">Total Lembur Semua Karyawan</td>
                        <td class="border px-4 py-2 text-green-700">Rp <?= number_format($total_semua, 0, ',', '.'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php include("footer.php"); ?>
    </div>

</body>

</html>