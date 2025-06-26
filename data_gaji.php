<?php
include 'koneksi.php';
include 'sidebar.php';
$username = $_SESSION['username'] ?? 'Guest';

// Delete invalid salary data that no longer has corresponding attendance records
$hapusGajiInvalidQuery = "
    DELETE FROM gaji_tukang
    WHERE CONCAT(tahun, '-', LPAD(bulan, 2, '0')) NOT IN (
        SELECT DISTINCT DATE_FORMAT(tanggal_masuk, '%Y-%m') FROM absensi_tukang
    )
    OR minggu NOT IN (
        SELECT DISTINCT minggu FROM absensi_tukang
    )
    OR nik NOT IN (
        SELECT DISTINCT nik FROM absensi_tukang
    )
";
mysqli_query($konek, $hapusGajiInvalidQuery);

$bulanFilter = isset($_GET['bulan']) ? htmlspecialchars($_GET['bulan']) : '';
$tahunFilter = isset($_GET['tahun']) ? htmlspecialchars($_GET['tahun']) : '';
$mingguFilter = isset($_GET['minggu']) ? htmlspecialchars($_GET['minggu']) : '';

$periodeFilter = '';
if (!empty($tahunFilter) && !empty($bulanFilter)) {
    $periodeFilter = $tahunFilter . '-' . $bulanFilter;
}

$q = null; // Initialize query variable for display

// --- BACKEND LOGIC: PROCESS AND SAVE SALARY DATA ---
// This section only runs if ALL period filters (month, year, week) are selected
// Always reprocess salary data when filter is applied, even without clicking "refresh"
if (!empty($bulanFilter) && !empty($tahunFilter) && !empty($mingguFilter)) {
    // Langsung jalankan proses perhitungan dan simpan ke gaji_tukang...
    // Start transaction to ensure data integrity
    mysqli_begin_transaction($konek);

    try {
        // 1. Delete 'gaji_tukang' data that no longer has attendance for this period.
        // This cleans up old salary data that might no longer be valid.
        $delete_query = "
            DELETE FROM gaji_tukang
            WHERE CONCAT(tahun, '-', bulan) = ?
            AND minggu = ?
            AND nik NOT IN (
                SELECT nik FROM absensi_tukang
                WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?
                AND minggu = ?
            )
        ";
        $stmt_delete = mysqli_prepare($konek, $delete_query);
        if (!$stmt_delete) {
            throw new Exception("Error preparing delete statement: " . mysqli_error($konek));
        }
        mysqli_stmt_bind_param($stmt_delete, "ssss", $periodeFilter, $mingguFilter, $periodeFilter, $mingguFilter);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);

        // 2. Query to get attendance data, name, position, and basic salary
        // for the filtered period. This is the SOURCE data for salary calculation.
        $main_query = "
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
    WHERE DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = ?
        AND a.minggu = ?
    GROUP BY a.nik, t.nama_tukang, t.id_jabatan, j.jabatan, j.gapok
    ORDER BY t.nama_tukang ASC
";
        $stmt_main = mysqli_prepare($konek, $main_query);
        if (!$stmt_main) {
            throw new Exception("Error preparing main query statement: " . mysqli_error($konek));
        }
        mysqli_stmt_bind_param($stmt_main, "ss", $periodeFilter, $mingguFilter);
        mysqli_stmt_execute($stmt_main);
        $result_main_query = mysqli_stmt_get_result($stmt_main);

        if ($result_main_query && mysqli_num_rows($result_main_query) > 0) {
            $results_to_process = [];
            while ($row = mysqli_fetch_assoc($result_main_query)) {
                $results_to_process[] = $row;
            }
            mysqli_free_result($result_main_query); // Free main query result
            mysqli_stmt_close($stmt_main); // Close main statement

            foreach ($results_to_process as $row) {
                $nik = $row['nik'];
                $nama = $row['nama_tukang'];
                $jabatan_id = $row['id_jabatan'];
                $gapok = $row['gapok'];
                $total_hadir = $row['total_hadir'];
                $total_gaji = $gapok * $total_hadir;

                // 3. Get detailed attendance dates and times for this NIK and period
                $absensi_detail_query = "
                    SELECT
                        tanggal_masuk,
                        tanggal_keluar,
                        jam_masuk,
                        jam_keluar
                    FROM absensi_tukang
                    WHERE nik = ?
                        AND DATE_FORMAT(tanggal_masuk, '%Y-%m') = ?
                        AND minggu = ?
                    ORDER BY tanggal_masuk ASC
                ";
                $stmt_absensi_detail = mysqli_prepare($konek, $absensi_detail_query);
                if (!$stmt_absensi_detail) {
                    throw new Exception("Error preparing absensi detail statement: " . mysqli_error($konek));
                }
                mysqli_stmt_bind_param($stmt_absensi_detail, "sss", $nik, $periodeFilter, $mingguFilter);
                mysqli_stmt_execute($stmt_absensi_detail);
                $result_absensi_detail = mysqli_stmt_get_result($stmt_absensi_detail);

                $tanggal_masuk_list = [];
                $tanggal_keluar_list = [];
                $jam_masuk_list = [];
                $jam_keluar_list = [];

                while ($absensi = mysqli_fetch_assoc($result_absensi_detail)) {
                    $tanggal_masuk_list[] = $absensi['tanggal_masuk'];
                    $tanggal_keluar_list[] = $absensi['tanggal_keluar'];
                    $jam_masuk_list[] = $absensi['jam_masuk'];
                    $jam_keluar_list[] = $absensi['jam_keluar'];
                }
                mysqli_free_result($result_absensi_detail); // Free absensi detail query result
                mysqli_stmt_close($stmt_absensi_detail); // Close absensi detail statement

                // Concatenate into strings to store in a single database field
                $tanggal_masuk_str = implode(', ', $tanggal_masuk_list);
                $tanggal_keluar_str = implode(', ', $tanggal_keluar_list);
                $jam_masuk_str = implode(', ', $jam_masuk_list);
                $jam_keluar_str = implode(', ', $jam_keluar_list);

                // 4. Insert or Update salary data using prepared statements
                $insert_update_query = "
                    INSERT INTO gaji_tukang
                    (nik, nama, id_jabatan, gapok, total_hadir, total_gaji, bulan, tahun, minggu, tanggal_masuk, tanggal_keluar, jam_masuk, jam_keluar)
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        nama=?,
                        id_jabatan=?,
                        gapok=?,
                        total_hadir=?,
                        total_gaji=?,
                        tanggal_masuk=?,
                        tanggal_keluar=?,
                        jam_masuk=?,
                        jam_keluar=?
                ";
                $stmt_insert_update = mysqli_prepare($konek, $insert_update_query);
                if (!$stmt_insert_update) {
                    throw new Exception("Error preparing insert/update statement: " . mysqli_error($konek));
                }
                // Bind parameters for INSERT and UPDATE parts
                mysqli_stmt_bind_param(
                    $stmt_insert_update,
                    "ssididssisssssididssss", // <-- Corrected type string as per your original code
                    $nik,
                    $nama,
                    $jabatan_id,
                    $gapok,
                    $total_hadir,
                    $total_gaji,
                    $bulanFilter,
                    $tahunFilter,
                    $mingguFilter,
                    $tanggal_masuk_str,
                    $tanggal_keluar_str,
                    $jam_masuk_str,
                    $jam_keluar_str, // For INSERT (13 vars)
                    $nama,
                    $jabatan_id,
                    $gapok,
                    $total_hadir,
                    $total_gaji,
                    $tanggal_masuk_str,
                    $tanggal_keluar_str,
                    $jam_masuk_str,
                    $jam_keluar_str  // For UPDATE (9 vars)
                );
                mysqli_stmt_execute($stmt_insert_update);
                mysqli_stmt_close($stmt_insert_update);
            }
        }
        mysqli_commit($konek); // Commit transaction if all operations succeed

    } catch (Exception $e) {
        mysqli_rollback($konek); // Rollback if an error occurs
        error_log("Error processing salary data: " . $e->getMessage()); // Log error to server
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
        echo "<strong class='font-bold'>Terjadi Kesalahan!</strong>";
        echo "<span class='block sm:inline'> Gagal memproses data gaji. Silakan coba lagi atau hubungi administrator.</span>";
        echo "</div>";
    }

    // --- FRONTEND LOGIC: RETRIEVE DATA FOR DISPLAY ---
    // After data is saved/updated, retrieve from 'gaji_tukang' table for display
    $display_query = "
    SELECT 
        g.nik,
        t.nama_tukang,
        j.jabatan,
        g.total_hadir,
        j.gapok,
        g.total_gaji
    FROM gaji_tukang g
    JOIN tukang_nws t ON g.nik = t.nik
    JOIN jabatan j ON t.id_jabatan = j.id
    WHERE CONCAT(g.tahun, '-', LPAD(g.bulan, 2, '0')) = ?
    AND g.minggu = ?
    ORDER BY t.nama_tukang ASC
";
    $stmt_display = mysqli_prepare($konek, $display_query);
    if ($stmt_display) {
        mysqli_stmt_bind_param($stmt_display, "ss", $periodeFilter, $mingguFilter);
        mysqli_stmt_execute($stmt_display);
        $q = mysqli_stmt_get_result($stmt_display);
        mysqli_stmt_close($stmt_display);
    } else {
        error_log("Error preparing display query: " . mysqli_error($konek));
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
        echo "<strong class='font-bold'>Terjadi Kesalahan!</strong>";
        echo "<span class='block sm:inline'> Gagal mengambil data untuk ditampilkan.</span>";
        echo "</div>";
    }

} else {
    // Display real-time data (total attendance & total salary) directly from attendance if no filters are applied
    $display_latest_query = "
    SELECT
        a.nik,
        t.nama_tukang,
        j.jabatan,
        j.gapok,
        SUM(a.total_hadir) AS total_hadir,
        (SUM(a.total_hadir) * j.gapok) AS total_gaji
    FROM absensi_tukang a
    JOIN tukang_nws t ON a.nik = t.nik
    JOIN jabatan j ON t.id_jabatan = j.id
    GROUP BY a.nik, t.nama_tukang, j.jabatan, j.gapok
    ORDER BY t.nama_tukang ASC
    ";

    $q = mysqli_query($konek, $display_latest_query);

    if (!$q) {
        error_log("Error fetching latest real-time salary data: " . mysqli_error($konek));
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>";
        echo "<strong class='font-bold'>Terjadi Kesalahan!</strong>";
        echo "<span class='block sm:inline'> Gagal mengambil data gaji real-time dari absensi.</span>";
        echo "</div>";
    }
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

        <section class="bg-white p-6 rounded-xl shadow">
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
                            echo "<option value='" . htmlspecialchars($key) . "' $selected>" . htmlspecialchars($nama) . "</option>";
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
                            echo "<option value='" . htmlspecialchars($y) . "' $selected>" . htmlspecialchars($y) . "</option>";
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
                            echo "<option value='" . htmlspecialchars($i) . "' $selected>Minggu ke-" . htmlspecialchars($i) . "</option>";
                        }
                        ?>
                    </select>
                </label>
                <div class="ml-auto flex gap-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-eye"></i> Tampilkan Data
                    </button>
                    <?php if ($bulanFilter && $tahunFilter && $mingguFilter): ?>
                        <a href="cetak_gaji.php?bulan=<?= htmlspecialchars($bulanFilter) ?>&tahun=<?= htmlspecialchars($tahunFilter) ?>&minggu=<?= htmlspecialchars($mingguFilter) ?>"
                            target="_blank"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded flex items-center gap-1">
                            <i class="fas fa-print"></i> Cetak Daftar Gaji
                        </a>
                    <?php else: ?>
                        <button type="button"
                            onclick="alert('Silakan pilih bulan, tahun, dan minggu terlebih dahulu sebelum mencetak daftar gaji.')"
                            class="bg-gray-400 cursor-not-allowed text-white px-3 py-1 rounded flex items-center gap-1"
                            disabled>
                            <i class="fas fa-print"></i> Cetak Daftar Gaji
                        </button>
                    <?php endif; ?>
                </div>

            </form>
            <div class="bg-blue-100 text-blue-800 rounded px-4 py-2 mb-4 text-sm">
                <?php if ($bulanFilter && $tahunFilter && $mingguFilter): ?>
                    Menampilkan Data Gaji Tukang Bulan:
                    <strong><?= htmlspecialchars($bulanNama[$bulanFilter] ?? $bulanFilter) ?></strong>,
                    Tahun: <strong><?= htmlspecialchars($tahunFilter) ?></strong>,
                    Minggu ke-<strong><?= htmlspecialchars($mingguFilter) ?></strong>
                <?php else: ?>
                    Silakan pilih bulan, tahun, dan minggu untuk cetak daftar gaji.
                <?php endif; ?>
            </div>

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
                        <?php if ($q && mysqli_num_rows($q) > 0): ?>
                            <?php
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($q)):
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-blue-100">
                                    <td class="py-4 px-6 text-center"><?= $no++ ?></td>
                                    <td class="py-4 px-6"><?= htmlspecialchars($row['nik']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_tukang']) ?></td>
                                    <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                    <td class="py-4 px-6 text-center"><?= number_format($row['total_hadir'], 1, '.', '.') ?>
                                    </td>
                                    <td>Rp <?= number_format($row['gapok'], 0, ',', '.') ?></td>
                                    <td class="py-4 px-6 text-right font-bold">
                                        Rp <?= number_format($row['total_gaji'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-gray-400 py-4">
                                    <?php if ($bulanFilter && $tahunFilter && $mingguFilter): ?>
                                        Tidak ada data gaji untuk periode ini.
                                    <?php else: ?>
                                        Silakan pilih bulan, tahun, dan minggu untuk menampilkan data spesifik.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>

</html>