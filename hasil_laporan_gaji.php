<?php
include("sidebar.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$koneksi = new mysqli("localhost", "root", "", "penggajian");

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
    <title>Hasil Laporan Gaji</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-center mb-4">Laporan Gaji Karyawan</h2>
            <p><strong>Bulan:</strong> <?= date('F', mktime(0, 0, 0, $bulan, 10)); ?> <?= $tahun; ?></p>
            <hr class="my-4">

            <?php
            $query = $koneksi->query("
    SELECT 
        k.nama_karyawan,
        j.jabatan,
        j.gapok,
        j.tunjangan_jabatan,
        (j.gapok + j.tunjangan_jabatan) AS total_gaji
    FROM karyawan k
    JOIN jabatan j ON k.id_jabatan = j.id
    ORDER BY k.nama_karyawan ASC
");

            if ($query->num_rows > 0) {
                echo '
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border border-gray-300 text-sm">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">Nama Karyawan</th>
                                <th class="border px-4 py-2">Jabatan</th>
                                <th class="border px-4 py-2">Gaji Pokok</th>
                                <th class="border px-4 py-2">Tunjangan Jabatan</th>
                                <th class="border px-4 py-2">Total Gaji</th>
                            </tr>
                        </thead>
                        <tbody>';
                while ($row = $query->fetch_assoc()) {
                    echo '
                    <tr class="text-center">
                        <td class="border px-4 py-2">' . htmlspecialchars($row['nama_karyawan']) . '</td>
                        <td class="border px-4 py-2">' . htmlspecialchars($row['jabatan']) . '</td>
                        <td class="border px-4 py-2">' . formatRupiah($row['gapok']) . '</td>
                        <td class="border px-4 py-2">' . formatRupiah($row['tunjangan_jabatan']) . '</td>
                        <td class="border px-4 py-2 font-semibold text-green-600">' . formatRupiah($row['total_gaji']) . '</td>
                    </tr>';
                }
                echo '
                        </tbody>
                    </table>

                    <div class="mt-6 flex justify-between items-center no-print">
                        <a href="laporan_gaji_karyawan.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                            ← Kembali
                        </a>

                        <form action="cetak_laporan_gaji.php" method="GET" target="_blank">
                            <input type="hidden" name="bulan" value="' . htmlspecialchars($bulan) . '">
                            <input type="hidden" name="tahun" value="' . htmlspecialchars($tahun) . '">
                            <button type="submit" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded shadow">
                                🖨 Cetak Laporan
                            </button>
                        </form>
                    </div>
                </div>';
            } else {
                echo '<p class="text-red-500 mt-4">Tidak ada data karyawan yang ditemukan.</p>
                <div class="mt-6 text-center">
                    <a href="laporan_gaji_karyawan.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                        ← Kembali
                    </a>
                </div>';
            }
            ?>
        </div>
    </div>
</body>

</html>