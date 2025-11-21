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
    <title>Data Keterlambatan Karyawan</title>
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
            <h1 class="text-2xl font-bold">Data Keterlambatan Karyawan</h1>
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

        </form>

        <!-- Tabel Keterlambatan -->
        <div class="overflow-x-auto bg-white p-5 rounded-lg shadow">
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-gray-900 text-center">
                        <th class="border px-4 py-2">No</th>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Tanggal</th>
                        <th class="border px-4 py-2">Keterangan</th>
                        <th class="border px-4 py-2">Telat (Menit)</th>
                        <th class="border px-4 py-2">Potongan</th>
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
    $where AND a.keterangan_telat = 'Telat'
    ORDER BY a.tgl_absen DESC
");


                    $no = 1;
                    $total_potongan = 0;

                    while ($d = mysqli_fetch_assoc($query)) {

                        $gapok = $d['gapok'];
                        $per_hari = $gapok / 20;
                        $per_jam = $per_hari / 8;
                        $per_menit = $per_jam / 60;

                        $telat = $d['telat_menit'];

                        if ($telat == "" || $telat == null || $telat == 0) {
                            // Hitung berdasarkan jam datang, misal jam masuk standar 09:00
                            $jam_masuk_standar = strtotime("09:00:00");
                            $jam_datang = strtotime($d['jam_masuk']);

                            if ($jam_datang > $jam_masuk_standar) {
                                $telat = ($jam_datang - $jam_masuk_standar) / 60;
                            } else {
                                $telat = 0;
                            }
                        }

                        $potongan = $telat * $per_menit;
                        $potongan = round($potongan, -3);

                        $total_potongan += $potongan;
                        ?>
                        <tr class="text-center">
                            <td class="border px-4 py-1"><?= $no++; ?></td>
                            <td class="border px-4 py-1"><?= $d['nama_karyawan']; ?></td>
                            <td class="border px-4 py-1"><?= $d['tgl_absen']; ?></td>
                            <td class="border px-4 py-1"><?= $d['keterangan_telat']; ?></td>
                            <td class="border px-4 py-1"><?= $telat; ?> menit</td>
                            <td class="border px-4 py-1 font-semibold text-red-600">Rp
                                <?= number_format($potongan, 0, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <tr class="bg-gray-100 font-bold">
                        <td colspan="5" class="border px-4 py-2 text-right">Total Potongan Semua Karyawan</td>
                        <td class="border px-4 py-2 text-red-700">Rp <?= number_format($total_potongan, 0, ',', '.'); ?>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        <?php include("footer.php"); ?>
    </div>

</body>

</html>