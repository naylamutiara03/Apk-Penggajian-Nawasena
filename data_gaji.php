<?php
include 'koneksi.php';
include 'sidebar.php';

// Ambil filter bulan dan tahun dari parameter GET
$bulanFilter = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahunFilter = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Format ke YYYY-MM
$periodeFilter = $tahunFilter . '-' . $bulanFilter;

// Ambil total hadir (jumlah angka hadir) per NIK di bulan yang dipilih
$q = mysqli_query($konek, "
    SELECT 
        a.nik, 
        t.nama_tukang, 
        t.jabatan, 
        SUM(a.total_hadir) AS total_hadir, 
        j.gapok 
    FROM absensi_tukang a
    JOIN tukang_nws t ON a.nik = t.nik
    JOIN jabatan j ON t.jabatan = j.jabatan
    WHERE DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = '$periodeFilter'
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

        <!-- Main Content -->
        <section class="bg-white p-6 rounded-xl shadow">
            <!-- Filter Section -->
            <div class="bg-blue-600 text-white text-sm font-semibold rounded px-3 py-2 mb-4">
                Filter Data Gaji Tukang
            </div>
            <form method="GET" action="" class="flex flex-wrap items-center gap-4 mb-6">
                <label class="flex items-center gap-2">
                    Bulan:
                    <select name="bulan" class="border border-gray-300 rounded px-2 py-1" required>
                        <option value="">--Pilih Bulan--</option>
                        <?php
                        $bulanNama = [
                            "01" => "Januari",
                            "02" => "Februari",
                            "03" => "Maret",
                            "04" => "April",
                            "05" => "Mei",
                            "06" => "Juni",
                            "07" => "Juli",
                            "08" => "Agustus",
                            "09" => "September",
                            "10" => "Oktober",
                            "11" => "November",
                            "12" => "Desember"
                        ];

                        foreach ($bulanNama as $key => $nama) {
                            $selected = ($bulanFilter == $key) ? "selected" : "";
                            echo "<option value='$key' $selected>$nama</option>";
                        }
                        ?>
                    </select>
                </label>
                <label class="flex items-center gap-2">
                    Tahun:
                    <select name="tahun" class="border border-gray-300 rounded px-2 py-1" required>
                        <option value="">--Pilih Tahun--</option>
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear; $y >= 2020; $y--) {
                            $selected = ($tahunFilter == $y) ? "selected" : "";
                            echo "<option value='$y' $selected>$y</option>";
                        }
                        ?>
                    </select>
                </label>
                <div class="ml-auto flex gap-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-eye"></i> Tampilkan Data
                    </button>
                </div>
            </form>
            <!-- END Filter Section -->

            <!-- Info Text -->
            <div class="bg-blue-100 text-blue-800 rounded px-4 py-2 mb-4 text-sm">
                <?php if ($bulanFilter && $tahunFilter): ?>
                    Menampilkan Data Gaji Tukang Bulan:
                    <strong><?= $bulanNama[$bulanFilter] ?? $bulanFilter ?></strong>, Tahun:
                    <strong><?= $tahunFilter ?></strong>
                <?php else: ?>
                    Silakan pilih bulan dan tahun untuk menampilkan data.
                <?php endif; ?>
            </div>
            <!-- END Info Text -->

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-gray-600 border border-gray-300">
                    <thead class="bg-gray-100 text-gray-500">
                        <tr>
                            <th class="border px-3 py-2 text-center font-semibold">No</th>
                            <th class="border px-3 py-2 text-center font-semibold">NIK</th>
                            <th class="border px-3 py-2 text-center font-semibold">Nama</th>
                            <th class="border px-3 py-2 text-center font-semibold">Jabatan</th>
                            <th class="border px-3 py-2 text-center font-semibold">Total Hadir (Hari)</th>
                            <th class="border px-3 py-2 text-center font-semibold">Gaji per Hari</th>
                            <th class="border px-3 py-2 text-center font-semibold">Total Gaji</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($q)):
                            $total_gaji = $row['total_hadir'] * $row['gapok'];
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-blue-100">
                                <td class="py-4 px-6 text-center"><?= $no++ ?></td>
                                <td class="py-4 px-6"><?= htmlspecialchars($row['nik']) ?></td>
                                <td class="py-4 px-6"><?= htmlspecialchars($row['nama_tukang']) ?></td>
                                <td class="py-4 px-6"><?= htmlspecialchars($row['jabatan']) ?></td>
                                <td class="py-4 px-6 text-center"><?= number_format($row['total_hadir'], 1, ',', '.') ?>
                                </td>
                                <td class="py-4 px-6 text-right"><?= number_format($row['gapok'], 0, ',', '.') ?></td>
                                <td class="py-4 px-6 text-right font-bold"><?= number_format($total_gaji, 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($q) == 0): ?>
                            <tr>
                                <td colspan="7" class="text-center text-gray-400 py-4">Tidak ada data gaji untuk bulan dan
                                    tahun ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <!-- END Data Table -->
        </section>
    </div>
</body>

</html>