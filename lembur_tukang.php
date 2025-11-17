<?php
// lembur_tukang.php
include("koneksi.php");
include("sidebar.php");

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
// B. LOGIKA PHP UNTUK MENENTUKAN TAMPILAN
// =========================================================================

$view = $_GET['view'] ?? 'list';
$act = $_GET['act'] ?? null; // Digunakan di aksi_lembur.php

$status_message = '';
$status_type = ''; //

// Data untuk form edit (jika ada)
$data_edit = null;
if ($view == 'edit' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $query_edit = mysqli_query($konek, "SELECT * FROM lembur_tkg WHERE id='$id'");
    $data_edit = mysqli_fetch_array($query_edit);
}

if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_type = htmlspecialchars($_GET['status']);
    $status_message = htmlspecialchars(urldecode($_GET['message']));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Lembur Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">

        <div id="successModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successMessage">Operasi berhasil.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="errorModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Error!</h2>
                <p class="text-gray-600 mt-2" id="errorMessage">Pesan error akan ditampilkan di sini.</p>
                <div class="mt-4">
                    <button onclick="closeErrorModal()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="detailModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-2xl w-full max-w-lg mx-4">
                <h2 class="text-xl font-bold text-blue-700 mb-3">Detail Lembur</h2>
                <p class="text-gray-700 mb-4">
                    Tukang: <strong id="detailTukang"></strong>
                    <br>
                    Tanggal: <strong id="detailTanggal"></strong>
                </p>

                <h3 class="font-semibold mb-2 text-gray-800 border-b pb-1">Daftar Shift yang Diambil:</h3>
                <div id="detailShiftList" class="max-h-60 overflow-y-auto">
                </div>

                <div class="mt-6">
                    <button onclick="document.getElementById('detailModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
                <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
                <p>Apakah Anda yakin ingin menghapus data lembur ini?</p>
                <div class="flex justify-end mt-4">
                    <button id="cancelDelete"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                    <button id="confirmDelete"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hapus</button>
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
            <h1 class="text-2xl font-bold ml-2">Data Lembur Tukang</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <?php if ($view == 'tambah'): ?>

            <div class="flex justify-center">
                <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                    <h2 class="text-2xl font-bold mb-4">Input Data Lembur Tukang</h2>
                    <hr class="mb-4">

                    <form method="POST" action="aksi_lembur.php?act=tambah" id="lembur_form">
                        <input type="hidden" name="total_harga_lembur" id="total_harga_lembur_value" value="0">
                        <input type="hidden" name="shifts_json" id="shifts_json_value" value="[]">

                        <div class="mb-4">
                            <label for="tgl_lembur" class="block text-sm font-medium text-gray-700">Tanggal Lembur:</label>
                            <input type="date" name="tgl_lembur" id="tgl_lembur" value="<?= date('Y-m-d'); ?>" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
                            <h3 class="text-lg font-semibold mb-3">Pilih Shift Lembur</h3>
                            <div id="shift_options_container">
                                <?php
                                // Tampilkan shift dengan harga default Rp 0 sebelum tukang dipilih
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
                            <p class="text-xl font-bold text-blue-800">Total Harga Lembur: <span
                                    id="total_harga_info"><?= formatRupiah(0); ?></span></p>
                        </div>

                        <div class="mt-6 flex space-x-4">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700">Simpan
                                Lembur</button>
                            <button type="button" onclick="window.location.href='lembur_tukang.php'"
                                class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php elseif ($view == 'edit' && $data_edit): ?>

            <div class="flex justify-center">
                <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                    <h2 class="text-2xl font-bold mb-4">Edit Data Lembur Tukang (ID: <?= $data_edit['id']; ?>)</h2>
                    <p class="text-red-500">Fitur Edit penuh belum diimplementasikan. Silakan hapus dan input ulang jika
                        perlu.</p>
                    <hr class="my-4">
                    <p>Data yang akan diedit:</p>
                    <ul>
                        <li>Tanggal: <?= htmlspecialchars($data_edit['tgl_lembur']); ?></li>
                        <li>Total Harga: <?= formatRupiah($data_edit['harga_lembur']); ?></li>
                        <li>Detail Shift: <?= htmlspecialchars($data_edit['detail_shifts']); ?></li>
                    </ul>
                    <div class="mt-4">
                        <a href="lembur_tukang.php"
                            class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md hover:bg-gray-600">Kembali ke
                            Daftar</a>
                    </div>
                </div>
            </div>

        <?php else: ?>

            <div class="flex justify-center">
                <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                    <div class="overflow-x-auto">
                        <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                            <thead class="bg-blue-600 text-white text-sm uppercase font-semibold">
                                <tr>
                                    <th class="py-4 px-6 text-center">No</th>
                                    <th class="py-4 px-6 text-left">Nama Tukang</th>
                                    <th class="py-4 px-6 text-left">Tanggal Lembur</th>
                                    <th class="py-4 px-6 text-left">Jam Lembur</th>
                                    <th class="py-4 px-6 text-left">Harga Lembur</th>
                                    <th class="py-4 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm">
                                <?php
                                $sql = mysqli_query($konek, "
        SELECT lt.*, tn.nama_tukang, lt.detail_shifts 
        FROM lembur_tkg lt
        JOIN tukang_nws tn ON lt.id_tukang = tn.id
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
                    <a href='#' onclick=\"openDeleteModal('aksi_lembur.php?act=delete&id={$d['id']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
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
                            <ion-icon name="add-circle-outline" class="mr-1"></ion-icon> Tambah Lembur
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

            // --- FUNGSI MODAL POP-UP DETAIL SHIFT (Perbaikan Error 'Object') ---
            function openDetailModal(nama_tukang, tgl_lembur, detail_shifts_js_string) {
                const detailModal = document.getElementById('detailModal');
                const detailTukang = document.getElementById('detailTukang');
                const detailTanggal = document.getElementById('detailTanggal');
                const detailShiftList = document.getElementById('detailShiftList');

                try {
                    // --- HANYA SATU KALI PARSE (SOLUSI) ---
                    // detail_shifts_js_string sekarang adalah string JSON mentah.
                    const detail_shifts = JSON.parse(detail_shifts_js_string);
                    // ------------------------------------

                    detailTukang.textContent = nama_tukang;

                    // Format tanggal
                    const dateObj = new Date(tgl_lembur);
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    detailTanggal.textContent = dateObj.toLocaleDateString('id-ID', options);

                    let html = '<ul class="space-y-2">';

                    if (detail_shifts && detail_shifts.length > 0) {
                        detail_shifts.forEach(data => {
                            const key = data.shift;
                            const multiplier = data.multiplier;
                            const harga = data.harga;

                            const nama_shift_display = JS_SHIFT_NAMA[key] || key;
                            const label = (multiplier == 1) ? 'Penuh' : 'Setengah (0.5x)';
                            const hargaFormatted = formatRupiahJs(harga);

                            html += `<li class="p-3 border rounded bg-gray-50 text-sm">
                                <span class="font-medium">${nama_shift_display}</span>
                                <span class="text-gray-500"> (${hargaFormatted})</span>
                                <span class="float-right font-bold text-blue-600">${label}</span>
                            </li>`;
                        });
                    } else {
                        html += '<li class="text-red-500 italic">Detail shift tidak ditemukan.</li>';
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
            // --- END FUNGSI DETAIL MODAL ---
        </script>

        <?php include 'footer.php'; ?>
    </div>
</body>

</html>