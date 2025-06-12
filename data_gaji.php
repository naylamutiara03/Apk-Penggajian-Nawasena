<?php
include 'koneksi.php';
include 'sidebar.php';

$bulanFilter = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahunFilter = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$mingguFilter = isset($_GET['minggu']) ? $_GET['minggu'] : '';

$periodeFilter = $tahunFilter . '-' . $bulanFilter;
$q = null;

if (!empty($bulanFilter) && !empty($tahunFilter) && !empty($mingguFilter)) {
    // Hapus data gaji_tukang yang tidak punya absensi lagi untuk periode ini
    mysqli_query($konek, "
    DELETE FROM gaji_tukang 
    WHERE CONCAT(tahun, '-', bulan) = '$periodeFilter'
    AND minggu = '$mingguFilter'
    AND nik NOT IN (
        SELECT nik FROM absensi_tukang 
        WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$periodeFilter'
        AND minggu = '$mingguFilter'
    )
");

    $q = mysqli_query($konek, "
        SELECT 
            a.nik, 
            t.nama_tukang, 
            t.id_jabatan, 
            j.jabatan,
            SUM(a.total_hadir) AS total_hadir, 
            j.gapok 
        FROM absensi_tukang a
        JOIN tukang_nws t ON a.nik = t.nik
        JOIN jabatan j ON t.id_jabatan = j.id
        WHERE DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = '$periodeFilter'
            AND a.minggu = '$mingguFilter'
        GROUP BY a.nik
    ");

    if ($q && mysqli_num_rows($q) > 0) {
        $results = [];
        while ($row = mysqli_fetch_assoc($q)) {
            $results[] = $row;
        }

        foreach ($results as $row) {
            $nik = $row['nik'];
            $nama = $row['nama_tukang'];
            $jabatan = $row['id_jabatan'];
            $gapok = $row['gapok'];
            $total_hadir = $row['total_hadir'];
            $total_gaji = $gapok * $total_hadir;

            // Ambil tanggal dan jam dari absensi — PERBAIKAN FILTER!
            $qAbsensi = mysqli_query($konek, "
    SELECT 
        tanggal_masuk,
        tanggal_keluar,
        jam_masuk,
        jam_keluar
    FROM absensi_tukang
    WHERE nik = '$nik'
      AND DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$periodeFilter'
      AND minggu = '$mingguFilter'
");

            $tanggal_masuk_list = [];
            $tanggal_keluar_list = [];
            $jam_masuk_list = [];
            $jam_keluar_list = [];

            while ($absensi = mysqli_fetch_assoc($qAbsensi)) {
                $tanggal_masuk_list[] = $absensi['tanggal_masuk'];
                $tanggal_keluar_list[] = $absensi['tanggal_keluar'];
                $jam_masuk_list[] = $absensi['jam_masuk'];
                $jam_keluar_list[] = $absensi['jam_keluar'];
            }

            // Gabungkan ke dalam string agar bisa disimpan di satu field database
            $tanggal_masuk = implode(', ', $tanggal_masuk_list);
            $tanggal_keluar = implode(', ', $tanggal_keluar_list);
            $jam_masuk = implode(', ', $jam_masuk_list);
            $jam_keluar = implode(', ', $jam_keluar_list);



            // Cek apakah data sudah ada
            $cek = mysqli_query($konek, "SELECT * FROM gaji_tukang WHERE nik='$nik' AND bulan='$bulanFilter' AND tahun='$tahunFilter' AND minggu='$mingguFilter'");
            // Hapus data lama untuk nik yang sama pada periode yang sama agar tidak double
            mysqli_query($konek, "
    DELETE FROM gaji_tukang 
    WHERE nik = '$nik' 
    AND bulan = '$bulanFilter' 
    AND tahun = '$tahunFilter' 
    AND minggu = '$mingguFilter'
");
            if (mysqli_num_rows($cek) == 0) {
                mysqli_query($konek, "INSERT INTO gaji_tukang 
                    (nik, nama, id_jabatan, gapok, total_hadir, total_gaji, bulan, tahun, minggu, tanggal_masuk, tanggal_keluar, jam_masuk, jam_keluar)
                    VALUES 
                    ('$nik', '$nama', '$jabatan', '$gapok', '$total_hadir', '$total_gaji', '$bulanFilter', '$tahunFilter', '$mingguFilter',
                     '$tanggal_masuk', '$tanggal_keluar', '$jam_masuk', '$jam_keluar')");
            }
        }

        // Re-query untuk ditampilkan di tabel
        $q = mysqli_query($konek, "
            SELECT 
                a.nik, 
                t.nama_tukang, 
                t.id_jabatan, 
                j.jabatan,
                SUM(a.total_hadir) AS total_hadir, 
                j.gapok 
            FROM absensi_tukang a
            JOIN tukang_nws t ON a.nik = t.nik
            JOIN jabatan j ON t.id_jabatan = j.id
            WHERE DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = '$periodeFilter'
                AND a.minggu = '$mingguFilter'
            GROUP BY a.nik
        ");
    }
} else {
    // Tampilkan semua data jika belum ada filter
    $q = mysqli_query($konek, "
        SELECT 
            a.nik, 
            t.nama_tukang, 
            t.id_jabatan, 
            j.jabatan,
            SUM(a.total_hadir) AS total_hadir, 
            j.gapok 
        FROM absensi_tukang a
        JOIN tukang_nws t ON a.nik = t.nik
        JOIN jabatan j ON t.id_jabatan = j.id
        GROUP BY a.nik
        ORDER BY a.id DESC
    ");
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Gaji Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
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
                <label class="flex items-center gap-2">
                    Minggu:
                    <select name="minggu" class="border border-gray-300 rounded px-2 py-1" required>
                        <option value="">--Pilih Minggu--</option>
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            $selected = ($mingguFilter == $i) ? "selected" : "";
                            echo "<option value='$i' $selected>Minggu ke-$i</option>";
                        }
                        ?>
                    </select>
                </label>
                <div class="ml-auto flex gap-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-eye"></i> Tampilkan Data
                    </button>
                    <a href="cetak_gaji.php?bulan=<?= $bulanFilter ?>&tahun=<?= $tahunFilter ?>&minggu=<?= $mingguFilter ?>"
                        target="_blank"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-print"></i> Cetak Daftar Gaji
                    </a>
                </div>

            </form>
            <!-- END Filter Section -->

            <!-- Info Text -->
            <div class="bg-blue-100 text-blue-800 rounded px-4 py-2 mb-4 text-sm">
                <?php if ($bulanFilter && $tahunFilter && $mingguFilter): ?>
                    Menampilkan Data Gaji Tukang Bulan:
                    <strong><?= $bulanNama[$bulanFilter] ?? $bulanFilter ?></strong>,
                    Tahun: <strong><?= $tahunFilter ?></strong>,
                    Minggu ke-<strong><?= $mingguFilter ?></strong>
                <?php else: ?>
                    Menampilkan <strong>seluruh data gaji tukang</strong> tanpa filter.
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
                        <?php if ($q): ?>
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
                                    <td colspan="7" class="text-center text-gray-400 py-4">
                                        Tidak ada data gaji untuk bulan dan tahun ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-gray-400 py-4">
                                    Silakan pilih bulan dan tahun untuk menampilkan data.
                                </td>
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