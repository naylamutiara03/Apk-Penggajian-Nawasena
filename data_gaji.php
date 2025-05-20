<?php
include 'koneksi.php';
include 'sidebar.php';

$bulan_ini = date('Y-m');

// Ambil total hadir (jumlah angka hadir) per NIK di bulan ini
$q = mysqli_query($konek, "
    SELECT 
        a.nik, 
        a.jabatan, 
        SUM(a.total_hadir) AS total_hadir, 
        j.gapok, 
        t.nama_tukang
    FROM absensi_tukang a
    JOIN jabatan j ON a.jabatan = j.jabatan
    JOIN tukang_nws t ON a.nik = t.nik
    WHERE DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = '$bulan_ini'
    GROUP BY a.nik
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Gaji Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang, <strong><?= htmlspecialchars($username) ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Data Gaji Tukang</h1>
            <span class="text-gray-500 mr-2"><?= date('d F Y') ?></span>
        </div>

        <table class="w-full table-auto border border-gray-300 bg-white shadow-sm rounded">
            <thead class="bg-gray-200 text-sm font-semibold text-gray-700">
                <tr>
                    <th class="border p-3">No</th>
                    <th class="border p-3">NIK</th>
                    <th class="border p-3">Nama</th>
                    <th class="border p-3">Jabatan</th>
                    <th class="border p-3">Total Hadir (Hari)</th>
                    <th class="border p-3">Gaji per Hari</th>
                    <th class="border p-3">Total Gaji</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($q)):
                    $total_gaji = $row['total_hadir'] * $row['gapok'];
                ?>
                <tr class="hover:bg-blue-50">
                    <td class="border p-3 text-center"><?= $no++ ?></td>
                    <td class="border p-3"><?= $row['nik'] ?></td>
                    <td class="border p-3"><?= $row['nama_tukang'] ?></td>
                    <td class="border p-3"><?= $row['jabatan'] ?></td>
                    <td class="border p-3 text-center"><?= number_format($row['total_hadir'], 1, ',', '.') ?></td>
                    <td class="border p-3 text-right"><?= number_format($row['gapok'], 0, ',', '.') ?></td>
                    <td class="border p-3 text-right font-bold"><?= number_format($total_gaji, 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($q) == 0): ?>
                <tr>
                    <td colspan="7" class="text-center p-4 text-gray-500">Tidak ada data gaji bulan ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
