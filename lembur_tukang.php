<?php
include("koneksi.php");
include("sidebar.php");

// =========================================================================
// A. DEFINISI DATA HARGA BERDASARKAN KATEGORI JABATAN
// =========================================================================

// Definisikan shift lembur per kategori/tipe tukang
// HARAP pastikan ID Jabatan ini sesuai dengan data di tabel 'tukang_nws' Anda.
$harga_kategori = [
    'A' => [ // Kategori A: Rinto, Diki, Fendi, Putra, Roni, Wasiman, Muslih (Asumsi id_jabatan 38)
        'shift1' => 110000,
        'shift2' => 110000,
        'shift3' => 120000,
    ],
    'B' => [ // Kategori B: Minto, Anton (Asumsi id_jabatan 39)
        'shift1' => 100000,
        'shift2' => 130000,
        'shift3' => 150000,
    ]
];

// Mapping id_jabatan ke Kategori Harga
$jabatan_ke_kategori = [
    38 => 'A', // ID Jabatan untuk Rinto, Diki, dll.
    39 => 'B', // ID Jabatan untuk Minto, Anton.
    // Tambahkan ID Jabatan lainnya sesuai kategori harga mereka
];

// Informasi tampilan shift
$shift_nama = [
    'shift1' => '09:00 - 17:00 (Shift 1)',
    'shift2' => '17:00 - 01:00 (Shift 2)',
    'shift3' => '01:00 - 09:00 (Shift 3)',
];

// Asumsi fungsi untuk format Rupiah
function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
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

        <div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successMessage">Operasi berhasil.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="errorModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Error!</h2>
                <p class="text-gray-600 mt-2" id="errorMessage">Pesan error akan ditampilkan di sini.</p>
                <div class="mt-4">
                    <button onclick="closeErrorModal()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
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
        <?php
        $view = isset($_GET["view"]) ? $_GET["view"] : null;
        switch ($view) {
            default:
                ?>
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
                                        SELECT lt.*, tn.nama_tukang 
                                        FROM lembur_tkg lt
                                        JOIN tukang_nws tn ON lt.id_tukang = tn.id
                                        ORDER BY lt.tgl_lembur DESC, tn.nama_tukang ASC
                                    ");

                                    $no = 1;
                                    if (mysqli_num_rows($sql) > 0) {
                                        while ($d = mysqli_fetch_array($sql)) {
                                            $shiftName = $shift_nama[$d['shift']] ?? 'N/A';
                                            $hargaFormatted = formatRupiah($d['harga_lembur']);

                                            echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
                                                <td class='py-4 px-6 text-center font-bold'>$no</td>
                                                <td class='py-4 px-6 '>" . ucwords(strtolower($d['nama_tukang'])) . "</td>
                                                <td class='py-4 px-6 '>" . date('d F Y', strtotime($d['tgl_lembur'])) . "</td>
                                                <td class='py-4 px-6 '>{$shiftName}</td>
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
                <?php
                break;

            // ==============================
            // CASE: FORM TAMBAH LEMBUR
            // ==============================
            case 'tambah':
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Tambah Data Lembur Tukang</h2>

                        <form id="addLemburForm" action="aksi_lembur.php?act=tambah" method="POST">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Tukang</label>
                                <select name="id_tukang" id="id_tukang_select" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Tukang</option>
                                    <?php
                                    // Query untuk mengambil data tukang dari tabel tukang_nws
                                    $tukangQuery = mysqli_query($konek, "SELECT id, nama_tukang FROM tukang_nws ORDER BY nama_tukang ASC");
                                    while ($tukang = mysqli_fetch_assoc($tukangQuery)) {
                                        echo "<option value='" . $tukang['id'] . "'>" . htmlspecialchars(ucwords(strtolower($tukang['nama_tukang']))) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Lembur</label>
                                <input type="date" name="tgl_lembur" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Jam Kerja / Shift Lembur</label>
                                <select name="shift" id="shift_select" required disabled
                                    class="w-full px-4 py-2 border rounded-lg bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    onchange="updateHargaInfo()">
                                    <option value="">Pilih Tukang Dahulu</option>
                                </select>
                                <input type="hidden" name="harga_lembur" id="harga_lembur_value">
                                <p class="mt-2 text-sm text-gray-600">Harga Lembur: <span id="harga_info"
                                        class="font-bold">-</span></p>
                            </div>

                            <div class="flex justify-between mt-6">
                                <a href="lembur_tukang.php"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    // Data shift dan kategori harga dari PHP, disuntikkan ke JavaScript
                    const JS_HARGA_KATEGORI = <?= json_encode($harga_kategori); ?>;
                    const JS_JABATAN_KE_KATEGORI = <?= json_encode($jabatan_ke_kategori); ?>;
                    const JS_SHIFT_NAMA = <?= json_encode($shift_nama); ?>;

                    function formatRupiahJs(angka) {
                        return 'Rp ' + parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }

                    // Fungsi untuk memperbarui harga yang ditampilkan dan hidden input
                    function updateHargaInfo() {
                        const select = document.getElementById('shift_select');
                        const selectedOption = select.options[select.selectedIndex];
                        const hargaInfo = document.getElementById('harga_info');
                        const hargaValueInput = document.getElementById('harga_lembur_value');

                        if (selectedOption && selectedOption.value) {
                            const harga = selectedOption.getAttribute('data-harga');
                            hargaInfo.textContent = formatRupiahJs(harga);
                            hargaValueInput.value = harga; // Simpan nilai INT ke hidden input
                        } else {
                            hargaInfo.textContent = '-';
                            hargaValueInput.value = '';
                        }
                    }

                    // Fungsi utama: Mengisi ulang dropdown shift berdasarkan tukang yang dipilih
                    // Di dalam <script> pada case 'tambah' di lembur_tukang.php
                    function loadShiftPrices(id_tukang) {
                        const shiftSelect = document.getElementById('shift_select');

                        // 1. Reset
                        shiftSelect.innerHTML = '<option value="">Memuat...</option>';
                        shiftSelect.disabled = true;

                        // --- DEBUGGING START ---
                        console.log("Mengambil data untuk ID Tukang:", id_tukang);
                        // --- DEBUGGING END ---

                        // 2. Ambil ID Jabatan dari endpoint AJAX
                        fetch('get_data_tukang.php?id=' + id_tukang)
                            .then(response => {
                                if (!response.ok) throw new Error('Network response was not ok');
                                return response.json();
                            })
                            .then(data => {
                                // --- DEBUGGING START ---
                                console.log("Respons dari get_data_tukang.php:", data);
                                // --- DEBUGGING END ---

                                if (!data.success) {
                                    throw new Error(data.message || 'Gagal mengambil data tukang.');
                                }

                                const id_jabatan = data.id_jabatan;
                                const kategori = JS_JABATAN_KE_KATEGORI[id_jabatan] || 'A'; // Default ke A

                                // --- DEBUGGING START ---
                                console.log("ID Jabatan Ditemukan:", id_jabatan);
                                console.log("Mapping Kategori Harga:", kategori);
                                // --- DEBUGGING END ---

                                const prices = JS_HARGA_KATEGORI[kategori];

                                shiftSelect.innerHTML = '<option value="">Pilih Jam Kerja / Shift</option>';

                                if (prices) {
                                    for (const key in prices) {
                                        const harga = prices[key];
                                        const nama = JS_SHIFT_NAMA[key];
                                        const option = document.createElement('option');
                                        option.value = key;
                                        option.setAttribute('data-harga', harga);
                                        option.textContent = `${nama} (${formatRupiahJs(harga)})`;
                                        shiftSelect.appendChild(option);
                                    }
                                    shiftSelect.disabled = false;
                                } else {
                                    shiftSelect.innerHTML = '<option value="">Kategori Harga Tidak Ditemukan</option>';
                                }
                                updateHargaInfo();
                            })
                            .catch(error => {
                                console.error("Error fetching tukang data:", error);
                                shiftSelect.innerHTML = '<option value="">Gagal Memuat Harga</option>';
                                updateHargaInfo();
                            });
                    }
                    
                    document.addEventListener('DOMContentLoaded', function () {
                        // Event listener saat Tukang dipilih
                        document.getElementById('id_tukang_select').addEventListener('change', function () {
                            if (this.value) {
                                loadShiftPrices(this.value);
                            } else {
                                // Reset jika tidak ada tukang yang dipilih
                                const shiftSelect = document.getElementById('shift_select');
                                shiftSelect.innerHTML = '<option value="">Pilih Tukang Dahulu</option>';
                                shiftSelect.disabled = true;
                                document.getElementById('harga_info').textContent = '-';
                                document.getElementById('harga_lembur_value').value = '';
                            }
                        });

                        // Logika submit form
                        const addLemburForm = document.getElementById('addLemburForm');

                        addLemburForm.addEventListener('submit', function (event) {
                            event.preventDefault();

                            const id_tukang = document.getElementById('id_tukang_select').value.trim();
                            const tgl_lembur = document.querySelector('input[name="tgl_lembur"]').value.trim();
                            const shift = document.getElementById('shift_select').value.trim();
                            const harga_lembur = document.getElementById('harga_lembur_value').value.trim();

                            const errorModal = document.getElementById('errorModal');
                            const errorMessage = document.getElementById('errorMessage');

                            if (!id_tukang || !tgl_lembur || !shift || !harga_lembur) {
                                errorMessage.innerText = 'Semua field wajib diisi, termasuk Harga Lembur (pastikan Tukang dan Shift sudah dipilih).';
                                errorModal.classList.remove('hidden');
                                return;
                            }

                            const formData = new FormData(addLemburForm);
                            // Harga lembur sudah otomatis ada di formData karena menggunakan hidden input name="harga_lembur"

                            // Kirim data ke aksi_lembur.php
                            fetch('aksi_lembur.php?act=tambah', {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById('successMessage').innerText = data.message;
                                        document.getElementById('successModal').classList.remove('hidden');
                                        setTimeout(() => { window.location.href = 'lembur_tukang.php'; }, 1500);
                                    } else {
                                        errorMessage.innerText = data.message || 'Terjadi kesalahan saat menyimpan data lembur.';
                                        errorModal.classList.remove('hidden');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error saat mengirim data:', error);
                                    errorMessage.innerText = 'Gagal mengirim data ke server.';
                                    errorModal.classList.remove('hidden');
                                });
                        });
                    });
                </script>
                <?php
                break;

            case 'edit':
                $id = $_GET['id'] ?? 0;
                $query = mysqli_query($konek, "SELECT * FROM lembur_tkg WHERE id='$id'");
                $data = mysqli_fetch_array($query);

                if (!$data) {
                    echo "<script>alert('Data lembur tidak ditemukan!'); window.location='lembur_tukang.php';</script>";
                    exit;
                }

                echo "<p class='text-center text-xl p-10'>Fitur Edit belum diimplementasikan sepenuhnya. Data yang akan diedit: ID " . htmlspecialchars($data['id']) . "</p>";

                break;
        }
        ?>

        <script>
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
                            document.getElementById('successMessage').innerText = data.message;
                            document.getElementById('successModal').classList.remove('hidden');
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
                window.location.href = "lembur_tukang.php";
            }

            function closeErrorModal() {
                document.getElementById('errorModal').classList.add('hidden');
            }
        </script>

        <?php include 'footer.php'; ?>
    </div>
</body>

</html>