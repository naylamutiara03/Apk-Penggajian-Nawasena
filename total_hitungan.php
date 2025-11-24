<?php
session_start();
include("koneksi.php");

// Cek Sesi
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

// Fungsi Format Rupiah
function formatRupiah($angka)
{
    return 'Rp ' . number_format((int) $angka, 0, ',', '.');
}

// Fungsi untuk menentukan range tanggal per minggu
function getRangeTanggalDB($tahun, $bulan, $minggu, $id_tukang, $konek)
{
    $where = [];
    $where[] = "id_tukang = '$id_tukang'";
    $where[] = "YEAR(tgl_lembur) = '$tahun'";
    if (!empty($bulan))
        $where[] = "MONTH(tgl_lembur) = '$bulan'";
    if (!empty($minggu))
        $where[] = "minggu_ke = '$minggu'";
    $where_clause = implode(" AND ", $where);

    $q = mysqli_query($konek, "SELECT MIN(tgl_lembur) AS tgl_min, MAX(tgl_lembur) AS tgl_max FROM lembur_tkg WHERE $where_clause");
    $r = mysqli_fetch_assoc($q);

    if ($r['tgl_min'] && $r['tgl_max']) {
        $tgl_min = date('d', strtotime($r['tgl_min']));
        $tgl_max = date('d', strtotime($r['tgl_max']));
        $bulan_str = date('F', strtotime($r['tgl_min']));
        return "$tgl_min-$tgl_max $bulan_str $tahun";
    }
    return '';
}


// Ambil Filter
$filter_tukang = $_GET['nama'] ?? '';
$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? '';
$filter_minggu = $_GET['minggu'] ?? '';

// Ambil Data Tukang
$list_tukang = mysqli_query($konek, "SELECT id, nama_tukang FROM tukang_nws ORDER BY nama_tukang ASC");

// Ambil Tahun dari Database
$q_years = mysqli_query($konek, "SELECT DISTINCT YEAR(tgl_lembur) AS tahun FROM lembur_tkg ORDER BY tahun DESC");
$available_years = [];
while ($row = mysqli_fetch_assoc($q_years)) {
    $available_years[] = $row['tahun'];
}
if (!in_array(date('Y'), $available_years)) {
    $available_years[] = date('Y');
    rsort($available_years);
}

// ======================================================
//  MENYUSUN QUERY HANYA KETIKA FILTER TERISI
// ======================================================
$data_ada = false;
$result_akumulasi = null;
$info_range = '';

if (!empty($filter_tukang) && !empty($filter_tahun)) {
    $where = ["lt.id_tukang = '$filter_tukang'"];
    $where[] = "YEAR(lt.tgl_lembur) = '$filter_tahun'";

    if (!empty($filter_bulan))
        $where[] = "MONTH(lt.tgl_lembur) = '$filter_bulan'";
    if (!empty($filter_minggu))
        $where[] = "lt.minggu_ke = '$filter_minggu'";

    $where_clause = implode(" AND ", $where);

    // Query detail lembur per hari
    $query_detail = "
        SELECT lt.*, t.nama_tukang 
        FROM lembur_tkg lt
        JOIN tukang_nws t ON lt.id_tukang = t.id
        WHERE $where_clause
        ORDER BY lt.tgl_lembur DESC
    ";
    $result_detail = mysqli_query($konek, $query_detail);

    if (mysqli_num_rows($result_detail) > 0) {
        $data_ada = true;
        $info_range = getRangeTanggalDB($filter_tahun, $filter_bulan, $filter_minggu, $filter_tukang, $konek);
    }
}


// Array Bulan
$bulan_arr = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Total Hitungan Lembur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <?php include("sidebar.php"); ?>

    <div class="p-6 lg:ml-[300px]">

        <!-- Header -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">PT. Nawa Sena Sinergi Gemilang</h1>
                <div class="flex items-center">
                    <span class="mr-2">Selamat Datang, <b><?= $username; ?></b></span>
                </div>
            </div>
        </div>

        <!-- Title -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-indigo-700">📊 Total Shift Tukang</h1>
            <span class="text-gray-500"><?= date('d F Y'); ?></span>
        </div>

        <!-- Filter -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold border-b pb-1">Filter Data</h2>
                <p class="text-sm text-gray-500 italic">
                    <span class="font-semibold text-red-500">Notes:</span> pilih Tahun dan Bulan saja untuk melihat
                    seluruh data di bulan tersebut.
                </p>
            </div>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">

                <div>
                    <label class="text-sm font-medium">Nama Tukang:</label>
                    <select name="nama" class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
                        <option value="">-- Pilih Tukang --</option>
                        <?php while ($t = mysqli_fetch_assoc($list_tukang)): ?>
                            <option value="<?= $t['id']; ?>" <?= ($filter_tukang == $t['id']) ? 'selected' : ''; ?>>
                                <?= $t['nama_tukang']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Tahun:</label>
                    <select name="tahun" class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
                        <option value="">-- Pilih Tahun --</option>
                        <?php foreach ($available_years as $y): ?>
                            <option value="<?= $y; ?>" <?= ($filter_tahun == $y) ? 'selected' : ''; ?>><?= $y; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Bulan:</label>
                    <select name="bulan" class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
                        <option value="">-- Semua Bulan --</option>
                        <?php foreach ($bulan_arr as $b => $n): ?>
                            <option value="<?= $b; ?>" <?= ($filter_bulan == $b) ? 'selected' : ''; ?>><?= $n; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">Minggu Ke:</label>
                    <select name="minggu" class="w-full border rounded p-2 focus:ring focus:ring-indigo-300">
                        <option value="">-- Semua Minggu --</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($filter_minggu == $i) ? 'selected' : ''; ?>>Minggu ke-<?= $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex flex-wrap gap-2 items-end">

                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-sm">
                        Filter
                    </button>

                    <a href="total_hitungan.php"
                        class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 shadow-sm">
                        Reset
                    </a>

                    <!-- === EXPORT EXCEL === -->
                    <?php
                    // Export Per Bulan (Semua Tukang)
                    if (!empty($filter_bulan) && !empty($filter_tahun) && empty($filter_tukang) && empty($filter_minggu)) { ?>
                        <a href="export_hitungan_bulan.php?bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-sm">
                            Export Excel Bulanan
                        </a>
                    <?php } ?>

                    <?php
                    // Export Per Tukang + Minggu
                    if (!empty($filter_bulan) && !empty($filter_tahun) && !empty($filter_tukang) && !empty($filter_minggu)) { ?>
                        <a href="export_hitungan_minggu.php?nama=<?= $filter_tukang ?>&bulan=<?= $filter_bulan ?>&tahun=<?= $filter_tahun ?>&minggu=<?= $filter_minggu ?>"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm">
                            Export Excel Per Minggu
                        </a>
                    <?php } ?>

                </div>
            </form>
        </div>


        <!-- INFO RANGE -->
        <?php if ($data_ada): ?>
            <div class="mb-4 p-4 bg-blue-50 rounded border-l-4 border-blue-400">
                <p><b>Tukang:</b>
                    <?= mysqli_fetch_assoc(mysqli_query($konek, "SELECT nama_tukang FROM tukang_nws WHERE id='$filter_tukang'"))['nama_tukang']; ?>
                </p>
                <p><b>Tahun:</b> <?= $filter_tahun; ?></p>
                <p><b>Bulan:</b> <?= !empty($filter_bulan) ? $bulan_arr[$filter_bulan] : 'Semua Bulan'; ?></p>
                <p><b>Minggu:</b> <?= !empty($filter_minggu) ? $filter_minggu : 'Semua Minggu'; ?></p>
                <?php if (!empty($filter_minggu)): ?>
                    <p><b>Range Tanggal:</b> <?= $info_range; ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- TABLE -->
        <?php if ($data_ada): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <table class="min-w-full border">
                    <tr class="bg-gray-100 font-bold">
                        <td class="px-4 py-2 border">No</td>
                        <td class="px-6 py-2 border">Nama Tukang</td>
                        <td class="px-6 py-2 border">Tanggal</td>
                        <td class="px-6 py-2 border text-center">Shift</td>
                        <td class="px-6 py-2 border text-right">Jumlah Lembur</td>
                    </tr>
                    <?php
                    $no = 1;
                    $grand_total = 0;
                    while ($d = mysqli_fetch_assoc($result_detail)):

                        $grand_total += $d['harga_lembur'];

                        // tampilkan shift apa adanya, tidak diubah
                        $shift_display = '-';
                        if (!empty($d['detail_shifts'])) {
                            $details = json_decode($d['detail_shifts'], true);
                            if (is_array($details)) {
                                $arr = [];
                                foreach ($details as $sh) {
                                    // Jika ada multiplier < 1 maka ditampilkan setengah (contoh: 0.5)
                                    if (isset($sh['multiplier']) && $sh['multiplier'] < 1) {
                                        $arr[] = $sh['shift'] . " (½)";
                                    } else {
                                        $arr[] = $sh['shift'];
                                    }
                                }
                                $shift_display = implode(", ", $arr);
                            }
                        }

                        ?>
                        <tr>
                            <td class="px-4 py-2 border"><?= $no++; ?></td>
                            <td class="px-6 py-2 border"><?= $d['nama_tukang']; ?></td>
                            <td class="px-6 py-2 border"><?= date('d F Y', strtotime($d['tgl_lembur'])); ?></td>
                            <td class="px-6 py-2 border text-center"><?= $shift_display; ?></td>
                            <td class="px-6 py-2 border text-right text-blue-600 font-bold">
                                <?= formatRupiah($d['harga_lembur']); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="4" class="px-6 py-2 border text-right">Grand Total</td>
                        <td class="px-6 py-2 border text-right text-green-600">
                            <?= formatRupiah($grand_total); ?>
                        </td>
                    </tr>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>

<?php mysqli_close($konek); ?>