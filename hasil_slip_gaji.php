<?php
include("sidebar.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$koneksi = new mysqli("localhost", "root", "", "penggajian");

$nama_tukang = $_GET['nama_tukang'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

function formatRupiah($angka)
{
    return "Rp. " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Hasil Slip Gaji</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #slip,
            #slip * {
                visibility: visible;
            }

            .no-print {
                display: none !important;
            }

            #slip {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px]">
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang, <strong><?= htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>

        <div id="slip" class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-center mb-4">Slip Gaji Tukang</h2>
            <p><strong>Nama:</strong> <?= htmlspecialchars($nama_tukang); ?></p>
            <p><strong>Bulan:</strong> <?= date('F', mktime(0, 0, 0, $bulan, 10)); ?> <?= $tahun; ?></p>
            <hr class="my-4">

            <?php
            $query = $koneksi->query("SELECT * FROM gaji_tukang
    WHERE nama = '$nama_tukang' 
    AND bulan = '$bulan' 
    AND tahun = '$tahun' 
    ORDER BY tanggal_masuk ASC");

            if ($query->num_rows > 0) {
                echo '
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-300 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">Minggu Ke-</th>
                    <th class="border px-4 py-2">Tanggal</th>
                    <th class="border px-4 py-2">Total Hadir</th>
                    <th class="border px-4 py-2">Gaji Per Hari</th>
                    <th class="border px-4 py-2">Total Gaji</th>
                </tr>
            </thead>
            <tbody>';
                $minggu_ke = 1;
                $total_bulanan = 0;
                while ($row = $query->fetch_assoc()) {
                    $tanggal_awal = date('d M Y', strtotime($row['tanggal_masuk']));
                    $tanggal_akhir = date('d M Y', strtotime($row['tanggal_keluar']));
                    $periode = "$tanggal_awal - $tanggal_akhir";

                    echo '
        <tr class="text-center">
            <td class="border px-4 py-2">Minggu ke-' . $minggu_ke++ . '</td>
            <td class="border px-4 py-2">' . $periode . '</td>
            <td class="border px-4 py-2">' . $row['total_hadir'] . ' Hari</td>
            <td class="border px-4 py-2">' . formatRupiah($row['gapok']) . '</td>
            <td class="border px-4 py-2 font-semibold">' . formatRupiah($row['total_gaji']) . '</td>
        </tr>';
                    $total_bulanan += $row['total_gaji'];
                }

                echo '
    <tr class="bg-gray-100 font-bold text-center">
        <td colspan="4" class="border px-4 py-2">Total Gaji Bulan Ini</td>
        <td class="border px-4 py-2 text-green-600">' . formatRupiah($total_bulanan) . '</td>
    </tr>
    </tbody>
</table>

<!-- Tombol Aksi -->
<div class="mt-6 flex justify-between items-center no-print">
    <!-- Tombol Kembali -->
    <a href="slip_gaji.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
        ← Kembali
    </a>

    <!-- Tombol Cetak -->
    <button onclick="window.print()" class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded shadow">
        🖨 Cetak Slip Gaji
    </button>
</div>
    </div>';
            } else {
                echo '<p class="text-red-500 mt-4">Data slip gaji tidak ditemukan untuk bulan dan nama tukang yang dipilih.</p>';
                echo '
    <!-- Tombol Kembali -->
    <div class="mt-6 text-center">
        <a href="slip_gaji.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
            ← Kembali
        </a>
    </div>';
            }
            ?>
        </div>
    </div>
</body>

</html>