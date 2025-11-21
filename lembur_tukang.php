<?php
// lembur_tukang.php
include("koneksi.php");
include("sidebar.php");
setlocale(LC_TIME, 'id_ID.UTF-8'); // Tambahkan locale untuk format tanggal/bulan yang baik

// =========================================================================
// A. DATA SHIFT & FUNGSI (Untuk PHP dan JS)
// =========================================================================

// Informasi tampilan shift
$shift_nama = [
    'shift1' => '09:00 - 17:00 (Shift 1)',
    'shift2' => '17:00 - 01:00 (Shift 2)',
    'shift3' => '01:00 - 09:00 (Shift 3)',
];

// Data default untuk harga shift saat halaman pertama kali dimuat
$default_shift_prices = [
    'shift1' => 0,
    'shift2' => 0,
    'shift3' => 0,
];

// Asumsi fungsi untuk format Rupiah
function formatRupiah($angka)
{
    // Pastikan input adalah integer
    return 'Rp ' . number_format((int) $angka, 0, ',', '.');
}

// =========================================================================
// B. LOGIKA PHP UNTUK MENENTUKAN TAMPILAN & FILTER
// =========================================================================

$view = $_GET['view'] ?? 'list';
$act = $_GET['act'] ?? null;

$status_message = '';
$status_type = '';

// --- LOGIKA FILTER WAKTU ---
$tahunFilter = isset($_GET['tahun']) && is_numeric($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$bulanFilter = isset($_GET['bulan']) && is_numeric($_GET['bulan']) ? $_GET['bulan'] : date('m');
$mingguFilter = isset($_GET['minggu']) && is_numeric($_GET['minggu']) ? $_GET['minggu'] : ''; // Biarkan kosong jika tidak dipilih

// Fungsi untuk menghitung rentang tanggal berdasarkan minggu ke-X dalam bulan/tahun
function getRangeMinggu($bulan, $tahun, $mingguKe)
{
    $startDay = ($mingguKe - 1) * 7 + 1;
    $totalHariBulan = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    $startDate = sprintf("%04d-%02d-%02d", $tahun, $bulan, $startDay);
    $tempEndDay = $startDay + 6;
    if ($tempEndDay > $totalHariBulan) {
        $endDay = $totalHariBulan;
    } else {
        $endDay = $tempEndDay;
    }
    $endDate = sprintf("%04d-%02d-%02d", $tahun, $bulan, $endDay);

    return [$startDate, $endDate];
}

$whereClauses = [];
$whereClauses[] = "YEAR(lt.tgl_lembur) = '$tahunFilter'";
$whereClauses[] = "MONTH(lt.tgl_lembur) = '$bulanFilter'";

// Logika Filter Minggu
$startDate = '';
$endDate = '';
if (!empty($mingguFilter)) {
    // Jika minggu ke- dipilih, filter berdasarkan kolom 'minggu_ke' yang diinput user
    $whereClauses[] = "lt.minggu_ke = '$mingguFilter'";

    // Opsional: Tetapkan startDate/endDate HANYA untuk tampilan info filter, 
    // tapi TIDAK untuk kueri SQL.
    list($startDate, $endDate) = getRangeMinggu($bulanFilter, $tahunFilter, $mingguFilter);
}

$whereSql = "WHERE " . implode(" AND ", $whereClauses);
// --- AKHIR LOGIKA FILTER WAKTU ---


// Data untuk form edit (jika ada)
$data_edit = null;

if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_type = htmlspecialchars($_GET['status']);
    $status_message = htmlspecialchars(urldecode($_GET['message']));
}

// =========================================================================
// C. LOGIKA PENGAMBILAN DATA EDIT
// (Ini dipisahkan agar tidak bentrok dengan logika pesan status di atas)
// =========================================================================
if ($view == 'edit' && isset($_GET['id'])):

    $id_edit = mysqli_real_escape_string($konek, $_GET['id']);

    // 1. Ambil data lembur yang akan di edit
    $query_data = "
        SELECT 
            lt.*, 
            t.nama_tukang,
            t.harga_shift_1, t.harga_shift_2, t.harga_shift_3
        FROM lembur_tkg lt
        JOIN tukang_nws t ON lt.id_tukang = t.id
        WHERE lt.id = '{$id_edit}'";

    $q_data = mysqli_query($konek, $query_data);
    $data_edit = mysqli_fetch_array($q_data);

    if (!$data_edit) {
        // Redirect jika data tidak ditemukan
        $pesan_error = urlencode("Data lembur ID {$id_edit} tidak ditemukan.");
        header("Location: lembur_tukang.php?status=error&message={$pesan_error}");
        exit();
    }

    // Siapkan data untuk form
    $id_tukang_lama = $data_edit['id_tukang'];
    $tgl_lembur_lama = $data_edit['tgl_lembur'];
    $minggu_ke_lama = $data_edit['minggu_ke'];
    // Menggunakan kolom 'harga_lembur' yang benar
    $total_harga_lembur_lama = $data_edit['harga_lembur'];
    $detail_shifts_lama = $data_edit['detail_shifts']; // JSON string

    // Data tukang (digunakan di JavaScript untuk perhitungan harga)
    $harga_shift_1 = $data_edit['harga_shift_1'];
    $harga_shift_2 = $data_edit['harga_shift_2'];
    $harga_shift_3 = $data_edit['harga_shift_3'];

    // Ambil daftar tukang untuk dropdown
    $query_tukang = "SELECT id, nama_tukang, harga_shift_1, harga_shift_2, harga_shift_3 FROM tukang_nws WHERE status='Aktif' ORDER BY nama_tukang ASC";
    $q_tukang = mysqli_query($konek, $query_tukang);
    $tukang_list = mysqli_fetch_all($q_tukang, MYSQLI_ASSOC);

endif; // <--- Menggunakan endif untuk menutup if ($view == 'edit')
// --- AKHIR LOGIKA PENGAMBILAN DATA EDIT ---

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Shift Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">

        <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Konfirmasi Hapus</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus data shift ini secara
                            permanen?</p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            Hapus
                        </button>
                        <button id="cancelDelete"
                            class="mt-3 px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="successModal"
            class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Berhasil!</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" id="successMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="button" onclick="closeSuccessModal()"
                            class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="errorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Error!</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" id="errorMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="button" onclick="closeErrorModal()"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="detailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 border-b pb-2">Detail Shift Lembur</h3>
                    <div class="mt-4 space-y-2">
                        <p class="text-sm"><strong>Tukang:</strong> <span id="detailTukang" class="font-medium"></span>
                        </p>
                        <p class="text-sm"><strong>Tanggal:</strong> <span id="detailTanggal"
                                class="font-medium"></span></p>
                        <hr>
                        <h4 class="font-semibold mt-3 text-gray-700">Rincian Shift:</h4>
                        <div id="detailShiftList">
                        </div>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button type="button" onclick="document.getElementById('detailModal').classList.add('hidden')"
                            class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang,
                        <strong><?php echo htmlspecialchars($username ?? 'User'); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Data Shift Tukang</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <?php if ($view == 'tambah'): ?>
            <div class="flex justify-center">
                <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                    <h2 class="text-2xl font-bold mb-4">Input Data Shift Tukang</h2>
                    <hr class="mb-4">

                    <form method="POST" action="aksi_lembur.php?act=tambah" id="lembur_form">
                        <input type="hidden" name="total_harga_lembur" id="total_harga_lembur_value" value="0">
                        <input type="hidden" name="shifts_json" id="shifts_json_value" value="[]">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="tgl_lembur" class="block text-sm font-medium text-gray-700">Tanggal
                                    Shift:</label>
                                <input type="date" name="tgl_lembur" id="tgl_lembur" value="<?= date('Y-m-d'); ?>" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="mb-4">
                                <label for="minggu_ke_input" class="block text-sm font-medium text-gray-700">Minggu Ke-
                                    (Untuk Laporan):</label>
                                <select name="minggu_ke" id="minggu_ke_input"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Minggu Ke- --</option>
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo "<option value='{$i}'>Minggu ke-{$i}</option>";
                                    }
                                    ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1 italic">Nilai ini opsional dan disesuaikan dengan
                                    perhitungan kantor.</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="id_tukang_select" class="block text-sm font-medium text-gray-700">Pilih
                                Tukang:</label>
                            <select name="id_tukang" id="id_tukang_select" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="loadShiftPrices(this.value)">
                                <option value="">-- Pilih Tukang --</option>
                                <?php
                                $q_tukang = mysqli_query($konek, "SELECT id, nama_tukang FROM tukang_nws ORDER BY nama_tukang ASC");
                                while ($data_tukang = mysqli_fetch_assoc($q_tukang)) {
                                    echo "<option value='{$data_tukang['id']}'>{$data_tukang['nama_tukang']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div id="shift_options_container_parent" class="mt-6 border p-4 rounded-lg bg-gray-50">
                            <h3 class="text-lg font-semibold mb-3">Pilih Shift</h3>
                            <div id="shift_options_container">
                                <?php
                                foreach ($default_shift_prices as $key => $harga) {
                                    $nama_shift_display = $shift_nama[$key] ?? $key;
                                    echo '
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="shift_' . $key . '" name="shifts[]" value="' . $key . '" 
                                    data-harga="' . $harga . '" 
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                    onchange="calculateTotalHarga()" disabled>
                                <label for="shift_' . $key . '" class="ml-3 text-sm font-medium text-gray-500">
                                    ' . $nama_shift_display . ' (' . formatRupiah($harga) . ') - Penuh
                                </label>
                            </div>';
                                }
                                ?>
                                <p class="text-red-500 italic mt-2" id="initial_warning">Pilih Tukang terlebih dahulu untuk
                                    memuat harga shift.</p>
                            </div>
                        </div>

                        <div id="half_shift_container" class="mt-4 border p-4 rounded-lg bg-gray-50 hidden">
                            <h3 class="text-lg font-semibold mb-3">Setengah Shift Tambahan (0.5x)</h3>
                            <select id="half_shift_select"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="calculateTotalHarga()">
                                <option value="">Tidak ada setengah shift tambahan</option>
                            </select>
                        </div>

                        <div class="mt-6 p-4 bg-blue-100 rounded-lg">
                            <p class="text-xl font-bold text-blue-800">Total Harga Shift:
                                <span id="total_harga_info"><?= formatRupiah(0); ?></span>
                            </p>
                        </div>

                        <div class="mt-6 flex space-x-4">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">
                                Simpan Lembur
                            </button>
                            <button type="button" onclick="window.location.href='lembur_tukang.php'"
                                class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- ==========================
     FORM EDIT LEMBUR TUKANG
=========================== -->
        <?php elseif ($view == 'edit' && isset($_GET['id'])): ?>
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6 text-indigo-600">📝 Edit Data Shift Tukang (ID: <?= $id_edit; ?>)</h2>

                <form action="aksi_lembur.php?act=edit" method="POST" class="bg-white p-6 rounded-lg shadow-lg">

                    <!-- Hidden Input -->
                    <input type="hidden" name="id_lembur" value="<?= $id_edit; ?>">
                    <input type="hidden" name="shifts_json" id="shifts_json"
                        value="<?= htmlspecialchars($detail_shifts_lama); ?>">
                    <input type="hidden" name="total_harga_lembur" id="total_harga_lembur_input"
                        value="<?= $total_harga_lembur_lama; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pilih Tukang:</label>
                            <select name="id_tukang" id="id_tukang" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500">
                                <option value="">-- Pilih Tukang --</option>
                                <?php foreach ($tukang_list as $tukang): ?>
                                    <option value="<?= $tukang['id']; ?>" data-harga1="<?= $tukang['harga_shift_1']; ?>"
                                        data-harga2="<?= $tukang['harga_shift_2']; ?>"
                                        data-harga3="<?= $tukang['harga_shift_3']; ?>" <?= ($tukang['id'] == $id_tukang_lama) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($tukang['nama_tukang']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Shift:</label>
                            <input type="date" name="tgl_lembur" id="tgl_lembur" required value="<?= $tgl_lembur_lama; ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Minggu Ke-:</label>
                            <select name="minggu_ke" id="minggu_ke" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500">
                                <option value="">-- Pilih Minggu --</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i; ?>" <?= ($i == $minggu_ke_lama) ? 'selected' : ''; ?>>
                                        Minggu ke-<?= $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ==================
     SHIFT LIST 
=================== -->
                    <h3 class="text-lg font-semibold border-b pb-2 mb-4">Pilih Shift</h3>
                    <div id="shifts-container" class="space-y-2 mb-6 p-4 border rounded-md bg-gray-50">
                        <p class="text-sm text-gray-500">Pilih tukang terlebih dahulu...</p>
                    </div>

                    <!-- ==================
 HALF SHIFT OPTION
=================== -->
                    <div id="half_shift_container" class="mt-4 border p-4 rounded-lg bg-gray-50 hidden">
                        <h3 class="text-lg font-semibold mb-3">Setengah Shift Tambahan (0.5x)</h3>
                        <select id="half_shift_select"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Tidak ada setengah shift tambahan</option>
                        </select>
                    </div>


                    <script>
                        document.addEventListener("DOMContentLoaded", function () {

                            const tukangSelect = document.getElementById("id_tukang");
                            const container = document.getElementById("shifts-container");
                            const displayTotal = document.getElementById("total_harga_display");
                            const inputTotal = document.getElementById("total_harga_lembur_input");
                            const jsonInput = document.getElementById("shifts_json");

                            let saved = [];
                            try { saved = JSON.parse(jsonInput.value || "[]"); } catch (e) { saved = []; }

                            function formatRP(n) { return "Rp " + (parseInt(n) || 0).toLocaleString("id-ID"); }

                            function renderShifts() {
                                const opt = tukangSelect.options[tukangSelect.selectedIndex];
                                if (!opt || !opt.dataset.harga1) {
                                    container.innerHTML = '<p class="text-sm text-gray-500">Pilih tukang terlebih dahulu...</p>';
                                    setTotal(0);
                                    return;
                                }

                                const list = [
                                    { id: 1, label: "Shift 1 (09:00 - 17:00)", harga: parseFloat(opt.dataset.harga1) },
                                    { id: 2, label: "Shift 2 (17:00 - 01:00)", harga: parseFloat(opt.dataset.harga2) },
                                    { id: 3, label: "Shift 3 (01:00 - 09:00)", harga: parseFloat(opt.dataset.harga3) }
                                ].filter(s => s.harga > 0);

                                let html = "";
                                list.forEach(s => {
                                    const cek = saved.some(v => parseInt(v.shift) === s.id) ? "checked" : "";
                                    html += `
            <div class="flex items-center">
                <input type="checkbox" class="shift-check h-4 w-4 text-indigo-600"
                    value="${s.id}" data-harga="${s.harga}" ${cek}>
                <label class="ml-2 text-sm text-gray-700">${s.label} (${formatRP(s.harga)})</label>
            </div>`;
                                });

                                container.innerHTML = html;
                                renderHalfShift();
                                addListeners();
                                calcTotal();
                            }

                            function addListeners() {
                                document.querySelectorAll(".shift-check").forEach(e => {
                                    e.addEventListener("change", calcTotal);
                                });
                            }

                            function setTotal(n) {
                                displayTotal.textContent = formatRP(n);
                                inputTotal.value = n;
                            }

                            function calcTotal() {
                                let t = 0;
                                let data = [];

                                // ===== SHIFT FULL NORMAL =====
                                document.querySelectorAll(".shift-check:checked").forEach(c => {
                                    const h = parseFloat(c.dataset.harga);
                                    t += h;
                                    data.push({
                                        shift: c.value,
                                        harga: h,
                                        multiplier: 1
                                    });
                                });

                                // ===== HALF SHIFT =====
                                const half = document.getElementById("half_shift_select");
                                if (half && half.value !== "") {
                                    const h = parseFloat(half.options[half.selectedIndex].dataset.harga);
                                    t += h;
                                    data.push({
                                        shift: half.value,
                                        harga: h,
                                        multiplier: 0.5 // <--- PENTING
                                    });
                                }

                                // Simpan ke input tersembunyi
                                jsonInput.value = JSON.stringify(data);
                                setTotal(t);
                            }


                            /* ============ TAMBAHAN: HALF SHIFT ============ */
                            function renderHalfShift() {
                                const opt = tukangSelect.options[tukangSelect.selectedIndex];
                                const halfBox = document.getElementById("half_shift_container");
                                const halfSelect = document.getElementById("half_shift_select");

                                if (!opt || !opt.dataset.harga1) {
                                    halfBox.classList.add("hidden");
                                    halfSelect.innerHTML = '<option value="">Tidak ada setengah shift tambahan</option>';
                                    return;
                                }

                                halfBox.classList.remove("hidden");

                                const halfList = [
                                    { id: 1, label: "Setengah Shift 1", harga: parseFloat(opt.dataset.harga1) / 2 },
                                    { id: 2, label: "Setengah Shift 2", harga: parseFloat(opt.dataset.harga2) / 2 },
                                    { id: 3, label: "Setengah Shift 3", harga: parseFloat(opt.dataset.harga3) / 2 }
                                ].filter(s => s.harga > 0);

                                halfSelect.innerHTML = '<option value="">Tidak ada setengah shift tambahan</option>';

                                halfList.forEach(s => {
                                    const selected = saved.some(v => parseInt(v.shift) === s.id && v.multiplier && v.multiplier < 1);
                                    halfSelect.innerHTML += `<option value="${s.id}" data-harga="${s.harga}" ${selected}>
            ${s.label} (Rp ${s.harga.toLocaleString("id-ID")})
        </option>`;
                                });

                                halfSelect.addEventListener("change", calcTotal);
                            }


                            tukangSelect.addEventListener("change", () => { saved = []; renderShifts(); renderHalfShift(); });

                            renderShifts(); // initial load
                        });
                    </script>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-xl font-bold">Total Harga Shift:
                            <span id="total_harga_display" class="text-blue-600">
                                <?= "Rp " . number_format($total_harga_lembur_lama, 0, ',', '.'); ?>
                            </span>
                        </h4>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="lembur_tukang.php"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>


            <?php if ($view != 'tambah' && $view != 'edit'): ?>
                <div class="p-6">
                    <?php if ($status_message): ?>
                        <div
                            class="mb-4 p-3 rounded-lg <?= $status_type == 'success' ? 'bg-green-100 text-green-700 border-green-500' : 'bg-red-100 text-red-700 border-red-500'; ?> border-l-4">
                            <p class="font-semibold"><?= ucfirst($status_type); ?>:</p>
                            <p><?= $status_message; ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Daftar Data Lembur Tukang</h2>
                        <a href="?view=tambah"
                            class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">Tambah
                            Data</a>
                    </div>

                    <form method="GET" class="flex space-x-3 items-end bg-gray-50 p-4 rounded-lg shadow-inner mb-6">
                        <input type="hidden" name="view" value="list">

                        <div>
                            <label for="tahun_filter" class="block text-sm font-medium text-gray-700">Tahun:</label>
                            <select name="tahun" id="tahun_filter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                                    $selected = ($tahunFilter == $y) ? 'selected' : '';
                                    echo "<option value='{$y}' {$selected}>{$y}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label for="bulan_filter" class="block text-sm font-medium text-gray-700">Bulan:</label>
                            <select name="bulan" id="bulan_filter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <?php
                                $bulan_nama = [
                                    '01' => 'Januari',
                                    '02' => 'Februari',
                                    '03' => 'Maret',
                                    '04' => 'April',
                                    '05' => 'Mei',
                                    '06' => 'Juni',
                                    '07' => 'Juli',
                                    '08' => 'Agustus',
                                    '09' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember'
                                ];
                                foreach ($bulan_nama as $num => $nama) {
                                    $num_padded = str_pad($num, 2, '0', STR_PAD_LEFT);
                                    $selected = ($bulanFilter == $num_padded) ? 'selected' : '';
                                    echo "<option value='{$num_padded}' {$selected}>{$nama}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div>
                            <label for="minggu_filter" class="block text-sm font-medium text-gray-700">Minggu Ke-:</label>
                            <select name="minggu" id="minggu_filter"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Minggu</option>
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    $selected = ($mingguFilter == $i) ? 'selected' : '';
                                    echo "<option value='{$i}' {$selected}>Minggu ke-{$i}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 h-10">Terapkan
                            Filter</button>

                        <?php if (!empty($mingguFilter) || $tahunFilter != date('Y') || $bulanFilter != date('m')): ?>
                            <a href="lembur_tukang.php"
                                class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600 h-10 flex items-center">Reset</a>
                        <?php endif; ?>
                    </form>
                    <?php if (!empty($mingguFilter)): ?>
                        <div class="mb-4 p-3 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                            <p class="font-semibold">Filter Aktif:</p>
                            <p class="text-sm">Menampilkan data lembur Tahun **<?= $tahunFilter; ?>**, Bulan
                                **<?= $bulan_nama[$bulanFilter]; ?>**, dan **Minggu Ke-<?= $mingguFilter; ?>** (Berdasarkan input
                                data).</p>
                        </div>
                    <?php endif; ?>

                    <div class="bg-white p-4 shadow-lg rounded-lg overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Shift</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Minggu Ke-</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Tukang</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Harga</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                // Menggabungkan tabel lembur_tkg (lt) dengan tukang_nws (t)
                                // C:\xampp\htdocs\apppenggajian\lembur_tukang.php (Sekitar Baris 611)
                                $query_lembur = "
    SELECT 
        lt.*, 
        t.nama_tukang 
    FROM lembur_tkg lt
    JOIN tukang_nws t ON lt.id_tukang = t.id
    $whereSql
    ORDER BY lt.tgl_lembur DESC";

                                $q_lembur = mysqli_query($konek, $query_lembur);
                                // ...
                        
                                $q_lembur = mysqli_query($konek, $query_lembur);

                                if (mysqli_num_rows($q_lembur) > 0):
                                    while ($data_lembur = mysqli_fetch_array($q_lembur)):
                                        ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= strftime('%A, %d %B %Y', strtotime($data_lembur['tgl_lembur'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= $data_lembur['minggu_ke'] ?? '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($data_lembur['nama_tukang']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold text-blue-600">
                                                <?= formatRupiah($data_lembur['total_harga_lembur']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button type="button"
                                                    onclick="openDetailModal('<?= htmlspecialchars($data_lembur['nama_tukang']); ?>', '<?= $data_lembur['tgl_lembur']; ?>', '<?= htmlspecialchars(json_encode($data_lembur['detail_shifts'])); ?>')"
                                                    class="text-blue-600 hover:text-blue-900 mr-2">Detail</button>
                                                <a href="?view=edit&id=<?= $data_lembur['id']; ?>"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                                <button type="button"
                                                    onclick="openDeleteModal('aksi_lembur.php?act=hapus&id=<?= $data_lembur['id']; ?>')"
                                                    class="text-red-600 hover:text-red-900">Hapus</button>
                                            </td>
                                        </tr>
                                        <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada data lembur yang ditemukan untuk filter ini.
                                        </td>
                                    </tr>
                                    <?php
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>

            <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl mb-6">
                <h2 class="text-xl font-bold mb-3">Filter Data Shift</h2>
                <form method="GET" action="lembur_tukang.php" class="flex flex-col md:flex-row gap-4 items-end">

                    <div class="w-full md:w-1/3">
                        <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun:</label>
                        <select name="tahun" id="tahun"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                            <?php
                            $currentYear = date('Y');
                            for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                                $selected = ($y == $tahunFilter) ? 'selected' : '';
                                echo "<option value='{$y}' {$selected}>{$y}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="w-full md:w-1/3">
                        <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan:</label>
                        <select name="bulan" id="bulan"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                            <?php
                            $months = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember'
                            ];
                            foreach ($months as $num => $name) {
                                $selected = ($num == $bulanFilter) ? 'selected' : '';
                                // Tambahkan angka nol di depan untuk format bulan (01, 02, ...)
                                $monthNum = sprintf("%02d", $num);
                                echo "<option value='{$monthNum}' {$selected}>{$name}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="w-full md:w-1/3">
                        <label for="minggu" class="block text-sm font-medium text-gray-700">Minggu Ke-:</label>
                        <select name="minggu" id="minggu"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Minggu</option>
                            <?php
                            // Logika sederhana: Hardcode Minggu ke-1 sampai Minggu ke-5
                            for ($w = 1; $w <= 5; $w++) {
                                // Cek apakah nilai perulangan ($w) sama dengan filter yang aktif ($mingguFilter)
                                $selected = ($w == $mingguFilter) ? 'selected' : '';

                                // Label sederhana (HANYA menampilkan "Minggu ke-X")
                                $displayLabel = "Minggu ke-{$w}";

                                // Output opsi HTML
                                echo "<option value='{$w}' {$selected}>{$displayLabel}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full md:w-auto px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">Filter</button>
                    <a href="lembur_tukang.php"
                        class="w-full md:w-auto px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition text-center">Reset</a>
                </form>
            </div>
            <div class="flex justify-center">
                <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">

                    <div class="flex justify-center">
                        <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                            <div class="overflow-x-auto">
                                <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                                    <thead class="bg-blue-600 text-white text-sm uppercase font-semibold">
                                        <tr>
                                            <th class="py-4 px-6 text-center">No</th>
                                            <th class="py-4 px-6 text-left">Nama Tukang</th>
                                            <th class="py-4 px-6 text-left">Tanggal Shift</th>
                                            <th class="py-4 px-6 text-left">Jam Shift</th>
                                            <th class="py-4 px-6 text-left">Harga Shift</th>
                                            <th class="py-4 px-6 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700 text-sm">
                                        <?php
                                        // Query SQL baru (Ganti yang lama dengan ini)
                                        $sql = mysqli_query($konek, "
    SELECT lt.*, tn.nama_tukang, lt.detail_shifts 
    FROM lembur_tkg lt
    JOIN tukang_nws tn ON lt.id_tukang = tn.id
    {$whereSql}  /* <-- Penambahan WHERE clause dari filter */
    ORDER BY lt.tgl_lembur DESC, tn.nama_tukang ASC
");

                                        $no = 1;
                                        if (mysqli_num_rows($sql) > 0) {
                                            while ($d = mysqli_fetch_array($sql)) {
                                                $hargaFormatted = formatRupiah($d['harga_lembur']);

                                                // Siapkan data JSON untuk diteruskan ke JavaScript
                                                // Kita harus pastikan string JSON sudah di-escape dengan benar agar valid di atribut onclick
                                                $detail_shifts_json_escaped = htmlspecialchars(json_encode($d['detail_shifts']), ENT_QUOTES, 'UTF-8');
                                                $tukang_name = ucwords(strtolower($d['nama_tukang']));

                                                echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
                <td class='py-4 px-6 text-center font-bold'>$no</td>
                <td class='py-4 px-6 '>{$tukang_name}</td>
                <td class='py-4 px-6 '>" . date('d F Y', strtotime($d['tgl_lembur'])) . "</td>
                
                <td class='py-4 px-6 text-center'>
                    <button 
                        type='button'
                        onclick=\"openDetailModal('{$tukang_name}', '{$d['tgl_lembur']}', {$detail_shifts_json_escaped})\"
                        class='bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition'
                    >
                        Lihat Detail
                    </button>
                </td>
                
                <td class='py-4 px-6 '>{$hargaFormatted}</td>
                <td class='py-4 px-6 text-center flex flex-col lg:flex-row gap-2 justify-center'>
                    <a href='lembur_tukang.php?view=edit&id={$d['id']}' class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                        <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                    </a>
                   <a href='#' onclick=\"openDeleteModal('aksi_lembur.php?act=hapus&id={$d['id']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
                        <ion-icon name='trash-outline' class='mr-1'></ion-icon> Hapus
                    </a>
                </td>
            </tr>";
                                                $no++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='py-4 px-6 text-center'>Tidak ada data lembur.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 flex justify-center">
                                <a href="lembur_tukang.php?view=tambah"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                                    <ion-icon name="add-circle-outline" class="mr-1"></ion-icon> Tambah Data
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; // End of view switch ?>

                <script>
                    // Data shift nama dari PHP (hanya untuk tampilan)
                    const JS_SHIFT_NAMA = <?= json_encode($shift_nama); ?>;

                    function formatRupiahJs(angka) {
                        return 'Rp ' + parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }

                    function calculateTotalHarga() {
                        const container = document.getElementById('shift_options_container');
                        const checkboxes = container ? container.querySelectorAll('input[name="shifts[]"]:checked') : [];
                        const halfShiftSelect = document.getElementById('half_shift_select');
                        const totalHargaInfo = document.getElementById('total_harga_info');
                        const totalHargaValueInput = document.getElementById('total_harga_lembur_value');
                        const shiftsJsonInput = document.getElementById('shifts_json_value');

                        let totalHarga = 0;
                        let selectedShifts = [];

                        // 1. Hitung shift penuh
                        checkboxes.forEach(checkbox => {
                            const harga = parseInt(checkbox.dataset.harga);
                            const shiftKey = checkbox.value;
                            totalHarga += harga;
                            selectedShifts.push({ shift: shiftKey, multiplier: 1, harga: harga });
                        });

                        // 2. Hitung setengah shift tambahan (0.5x)
                        const halfShiftKey = halfShiftSelect ? halfShiftSelect.value : null;
                        if (halfShiftKey) {
                            const hargaPenuh = parseInt(halfShiftSelect.options[halfShiftSelect.selectedIndex].dataset.harga);
                            const hargaHalf = Math.round(hargaPenuh / 2); // Pembulatan ke integer terdekat
                            totalHarga += hargaHalf;

                            selectedShifts.push({ shift: halfShiftKey, multiplier: 0.5, harga: hargaHalf });
                        }

                        if (totalHargaInfo) totalHargaInfo.textContent = formatRupiahJs(totalHarga);
                        if (totalHargaValueInput) totalHargaValueInput.value = totalHarga;
                        if (shiftsJsonInput) shiftsJsonInput.value = JSON.stringify(selectedShifts);
                    }

                    function loadShiftPrices(id_tukang) {
                        const shiftOptionsContainer = document.getElementById('shift_options_container');
                        const halfShiftContainer = document.getElementById('half_shift_container');
                        const halfShiftSelect = document.getElementById('half_shift_select');
                        const initialWarning = document.getElementById('initial_warning');

                        // 1. Reset tampilan
                        if (shiftOptionsContainer) shiftOptionsContainer.innerHTML = '';
                        if (halfShiftSelect) halfShiftSelect.innerHTML = '<option value="">Tidak ada setengah shift tambahan</option>';
                        if (halfShiftContainer) halfShiftContainer.classList.add('hidden');
                        if (initialWarning) {
                            initialWarning.classList.remove('hidden');
                            initialWarning.textContent = 'Memuat data harga...';
                        }
                        calculateTotalHarga();

                        if (!id_tukang || id_tukang === "") {
                            if (initialWarning) initialWarning.textContent = 'Pilih Tukang terlebih dahulu untuk memuat harga shift.';
                            return;
                        }

                        // 2. Ambil Harga dari endpoint AJAX (get_data_tukang.php)
                        fetch('get_data_tukang.php?id=' + id_tukang)
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.json();
                            })
                            .then(data => {
                                if (!data.success || !data.prices) {
                                    throw new Error(data.message || 'Data harga shift tidak ditemukan.');
                                }

                                const prices = data.prices;
                                const shiftKeys = ['shift1', 'shift2', 'shift3'];

                                if (shiftOptionsContainer) shiftOptionsContainer.innerHTML = '';
                                if (initialWarning) initialWarning.classList.add('hidden');
                                if (halfShiftContainer) halfShiftContainer.classList.remove('hidden');

                                let shiftsAvailable = false;

                                shiftKeys.forEach(key => {
                                    const harga = prices[key];
                                    const nama_shift_display = JS_SHIFT_NAMA[key] ?? key;

                                    if (harga > 0 && shiftOptionsContainer && halfShiftSelect) {
                                        shiftsAvailable = true;
                                        const div = document.createElement('div');
                                        div.classList.add('flex', 'items-center', 'mb-2');
                                        div.innerHTML = `
                                    <input type="checkbox" id="shift_${key}" name="shifts[]" value="${key}" 
                                        data-harga="${harga}" 
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" 
                                        onchange="calculateTotalHarga()">
                                    <label for="shift_${key}" class="ml-3 text-sm font-medium text-gray-700">
                                        ${nama_shift_display} (${formatRupiahJs(harga)}) - Penuh
                                    </label>`;
                                        shiftOptionsContainer.appendChild(div);

                                        const optionHalf = document.createElement('option');
                                        optionHalf.value = key;
                                        optionHalf.setAttribute('data-harga', harga);
                                        optionHalf.textContent = `${nama_shift_display} (${formatRupiahJs(Math.round(harga / 2))}) - Setengah`;
                                        halfShiftSelect.appendChild(optionHalf);
                                    }
                                });

                                if (!shiftsAvailable && initialWarning) {
                                    initialWarning.textContent = 'Harga Shift untuk tukang ini belum diatur (> Rp 0).';
                                    initialWarning.classList.remove('hidden');
                                    if (halfShiftContainer) halfShiftContainer.classList.add('hidden');
                                }

                                if (halfShiftSelect) halfShiftSelect.onchange = calculateTotalHarga;
                                calculateTotalHarga();
                            })
                            .catch(error => {
                                console.error("Error fetching tukang data:", error);
                                if (initialWarning) {
                                    initialWarning.textContent = 'Gagal Memuat Harga: ' + error.message;
                                    initialWarning.classList.remove('hidden');
                                }
                                if (shiftOptionsContainer) shiftOptionsContainer.innerHTML = '';
                                if (halfShiftContainer) halfShiftContainer.classList.add('hidden');
                                calculateTotalHarga();
                            });
                    }

                    // MODAL & GLOBAL HANDLERS
                    let deleteUrl = '';

                    function openDeleteModal(url) {
                        deleteUrl = url;
                        document.getElementById('deleteModal').classList.remove('hidden');
                    }

                    document.getElementById('cancelDelete').onclick = function () {
                        document.getElementById('deleteModal').classList.add('hidden');
                    };

                    document.getElementById('confirmDelete').onclick = function () {
                        fetch(deleteUrl)
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('deleteModal').classList.add('hidden');
                                if (data.success) {
                                    // Tampilkan modal sukses dari operasi AJAX delete
                                    openSuccessModal(data.message);
                                    // Tunggu sebentar sebelum refresh halaman setelah modal ditutup
                                    setTimeout(() => { window.location.href = "lembur_tukang.php"; }, 1500);
                                } else {
                                    document.getElementById('errorMessage').innerText = data.message || 'Gagal menghapus data lembur.';
                                    document.getElementById('errorModal').classList.remove('hidden');
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                document.getElementById('deleteModal').classList.add('hidden');
                                document.getElementById('errorMessage').innerText = 'Gagal menghubungi server untuk menghapus.';
                                document.getElementById('errorModal').classList.remove('hidden');
                            });
                    };

                    function closeSuccessModal() {
                        document.getElementById('successModal').classList.add('hidden');
                        window.location.href = "lembur_tukang.php"; // Refresh daftar
                    }

                    function closeErrorModal() {
                        document.getElementById('errorModal').classList.add('hidden');
                    }

                    // Inisialisasi pada DOM Load
                    document.addEventListener('DOMContentLoaded', function () {
                        const selectElement = document.getElementById('id_tukang_select');
                        if (selectElement && selectElement.value) {
                            loadShiftPrices(selectElement.value);
                        }

                        // Tangkap pesan dari PHP (setelah redirect dari aksi_lembur.php)
                        const statusType = '<?= $status_type; ?>';
                        const statusMessage = '<?= $status_message; ?>';

                        if (statusType === 'success' && statusMessage !== '') {
                            openSuccessModal(statusMessage);
                            // Hapus status/message dari URL setelah ditampilkan agar tidak muncul lagi saat refresh
                            history.replaceState(null, '', 'lembur_tukang.php');
                        } else if (statusType === 'error' && statusMessage !== '') {
                            document.getElementById('errorMessage').innerText = statusMessage;
                            document.getElementById('errorModal').classList.remove('hidden');
                            history.replaceState(null, '', 'lembur_tukang.php');
                        }
                    });

                    function openSuccessModal(message) {
                        document.getElementById('successMessage').innerText = message;
                        document.getElementById('successModal').classList.remove('hidden');
                    }

                    function closeSuccessModal() {
                        document.getElementById('successModal').classList.add('hidden');
                        // Tidak perlu refresh/redirect di sini jika dipanggil dari tombol 'Tutup'
                    }

                    function closeErrorModal() {
                        document.getElementById('errorModal').classList.add('hidden');
                    }

                    function openDetailModal(nama_tukang, tgl_lembur, detail_shifts_js_string) {
                        const detailModal = document.getElementById('detailModal');
                        const detailTukang = document.getElementById('detailTukang');
                        const detailTanggal = document.getElementById('detailTanggal');
                        const detailShiftList = document.getElementById('detailShiftList');

                        try {
                            const detail_shifts = JSON.parse(detail_shifts_js_string);

                            detailTukang.textContent = nama_tukang;

                            const dateObj = new Date(tgl_lembur);
                            const options = { day: 'numeric', month: 'long', year: 'numeric' };
                            detailTanggal.textContent = dateObj.toLocaleDateString('id-ID', options);

                            let html = '<ul class="space-y-2">';

                            if (detail_shifts && detail_shifts.length > 0) {
                                detail_shifts.forEach(data => {

                                    // --- Normalisasi value shift apapun formatnya ---
                                    let rawShift = data.shift ?? data.id_shift ?? data.nama_shift ?? 0;
                                    let key = parseInt(String(rawShift).replace(/\D/g, "")); // "shift1" -> 1

                                    // Jika masih NaN (data buruk), fallback ke 1
                                    if (isNaN(key)) key = 1;

                                    // Multiplier jika tidak ada → default 1 (penuh)
                                    const multiplier = data.multiplier ?? 1;

                                    // Nama shift berdasarkan mapping
                                    let baseName = JS_SHIFT_NAMA[key] || ("Shift " + key);

                                    // Jika setengah, tambahan teks "Setengah"
                                    if (multiplier < 1) {
                                        baseName = "Setengah " + baseName;
                                    }

                                    // Harga lembur (fallback ke 0)
                                    const harga = data.harga ?? 0;
                                    const hargaFormatted = formatRupiahJs(harga);

                                    const label = (multiplier == 1) ? "Penuh" : "Setengah (0.5x)";

                                    html += `
        <li class="p-3 border rounded bg-gray-50 text-sm">
            <span class="font-medium">${baseName}</span>
            <span class="text-gray-500"> (${hargaFormatted})</span>
            <span class="float-right font-bold text-blue-600">${label}</span>
        </li>`;
                                });

                            } else {
                                html += `<li class="text-red-500 italic">Detail shift tidak ditemukan.</li>`;
                            }

                            html += '</ul>';
                            detailShiftList.innerHTML = html;
                            detailModal.classList.remove('hidden');

                        } catch (e) {
                            console.error("Gagal mem-parse data shift:", e);
                            document.getElementById('errorMessage').innerText = 'Gagal memuat detail shift: data tidak valid.';
                            document.getElementById('errorModal').classList.remove('hidden');
                        }
                    }

                </script>

                <?php include 'footer.php'; ?>
            </div>
</body>

</html>