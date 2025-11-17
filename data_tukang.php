<?php
include("koneksi.php");
include("sidebar.php");
// $username diasumsikan didefinisikan di 'sidebar.php' atau 'koneksi.php'
// Pastikan $konek sudah terkoneksi ke database
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successMessage">Data tukang berhasil dihapus.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="successAddModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successAddMessage">Data tukang berhasil ditambahkan.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessAddModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div id="errorModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-red-600">Terjadi Kesalahan!</h2>
                <p class="text-gray-600 mt-2" id="errorMessage"></p>
                <div class="mt-4">
                    <button onclick="closeErrorModal()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang,
                        <strong><?php echo htmlspecialchars($username ?? 'User'); // Tambahkan penanganan jika $username tidak terdefinisi ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Data Tukang</h1>
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
                                        <th class="py-4 px-6 text-left">NIK</th>
                                        <th class="py-4 px-6 text-left">Nama Tukang</th>
                                        <th class="py-4 px-6 text-left">Jenis Kelamin</th>
                                        <th class="py-4 px-6 text-center">Detail Shift & Harga</th>
                                        <th class="py-4 px-6 text-left">Tanggal Masuk</th>
                                        <th class="py-4 px-6 text-left">Status</th>
                                        <th class="py-4 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    <?php
                                    $no = 1;
                                    // QUERY DIBIARKAN SAMA, tapi kita akan mengakses harga_shift_X
                                    $sql = mysqli_query($konek, "
                                SELECT * FROM tukang_nws 
                                ORDER BY tgl_masuk ASC
                            ");
                                    while ($d = mysqli_fetch_array($sql)) {
                                        $namaTukang = ucwords(strtolower($d['nama_tukang']));

                                        // Siapkan data Shift untuk modal (pastikan kolom ini ada di DB Anda)
                                        $shiftData = [
                                            // Pastikan nilai-nilai ini adalah angka atau null, BUKAN string kosong
                                            '1' => $d['harga_shift_1'] === null ? 0 : (float) $d['harga_shift_1'],
                                            '2' => $d['harga_shift_2'] === null ? 0 : (float) $d['harga_shift_2'],
                                            '3' => $d['harga_shift_3'] === null ? 0 : (float) $d['harga_shift_3'],
                                        ];

                                        // Encode data shift menjadi string JSON yang aman untuk dimasukkan ke atribut data HTML
                                        $jsonShift = htmlspecialchars(json_encode($shiftData), ENT_QUOTES, 'UTF-8');

                                        echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
                                    <td class='py-4 px-6 text-center font-bold'>$no</td>
                                    <td class='py-4 px-6 '>{$d['nik']}</td>
                                    <td class='py-4 px-6 '>$namaTukang</td>
                                    <td class='py-4 px-6 '>{$d['jenis_kelamin']}</td>
                                    
                                    <td class='py-4 px-6 text-center'>
                                        <button onclick=\"openShiftDetailModal('$namaTukang', '$jsonShift')\" 
                                                class='bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs transition'>
                                            <ion-icon name='eye-outline' class='mr-1'></ion-icon> Lihat Detail
                                        </button>
                                    </td>
                                    
                                    <td class='py-4 px-6 '>" . date('d F Y', strtotime($d['tgl_masuk'])) . "</td>
                                    <td class='py-4 px-6 '>{$d['status']}</td>
                                    <td class='py-4 px-6 text-center flex flex-col lg:flex-row gap-2 justify-center'>
                                        <a href='data_tukang.php?view=edit&id={$d['id']}' class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                                            <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                                        </a>
                                        <a href='#' onclick=\"openDeleteModal('aksi_tukang.php?act=delete&id={$d['id']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
                                            <ion-icon name='trash-outline' class='mr-1'></ion-icon> Hapus
                                        </a>
                                    </td>
                                </tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 flex justify-center">
                            <a href="data_tukang.php?view=tambah"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                                <ion-icon name="person-add-outline" class="mr-1"></ion-icon> Tambah Tukang
                            </a>
                        </div>
                    </div>
                </div>

                <div id="shiftDetailModal"
                    class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
                    <div class="bg-white p-6 rounded-lg shadow-2xl w-full max-w-md">
                        <h2 class="text-xl font-bold text-gray-800 mb-4" id="modalTukangName">Detail Shift Gaji</h2>

                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Shift</th>
                                    <th
                                        class="py-3 px-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Harga (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="shiftDetailTableBody">
                            </tbody>
                        </table>

                        <div class="mt-6 text-center">
                            <button onclick="closeShiftDetailModal()"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Tutup</button>
                        </div>
                    </div>
                </div>

                <script>
                    /**
                     * Fungsi untuk memformat angka menjadi format Rupiah.
                     * @param {number|string} angka - Angka yang akan diformat.
                     * @returns {string} - String dalam format 'Rp X.XXX.XXX'.
                     */
                    function formatRupiah(angka) {
                        const number_string = angka.toString().replace(/[^,\d]/g, ''),
                            split = number_string.split(','),
                            sisa = split[0].length % 3;

                        // GANTI const MENJADI let untuk variabel yang akan diubah!
                        let rupiah = split[0].substr(0, sisa),
                            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                        if (ribuan) {
                            let separator = sisa ? '.' : ''; // GANTI const MENJADI let
                            rupiah += separator + ribuan.join('.');
                        }

                        return 'Rp ' + (rupiah || 0);
                    }

                    /**
                     * Membuka modal detail shift dan mengisi tabel dengan data harga shift.
                     * @param {string} nama - Nama tukang.
                     * @param {string} jsonShift - String JSON yang berisi data harga shift (sudah di-htmlspecialchars).
                     */
                    function openShiftDetailModal(nama, jsonShift) {
                        const modal = document.getElementById('shiftDetailModal');
                        const tableBody = document.getElementById('shiftDetailTableBody');
                        const tukangNameTitle = document.getElementById('modalTukangName');

                        let shiftData = {};

                        // --- PENANGANAN ERROR PARSING JSON ---
                        try {
                            // Mengganti &quot; kembali menjadi " agar JSON.parse dapat bekerja
                            const decodedJson = jsonShift.replace(/&quot;/g, '"');
                            shiftData = JSON.parse(decodedJson);
                        } catch (e) {
                            // Jika parsing gagal, log error dan gunakan data default
                            console.error("Gagal parse JSON Shift Data:", e);
                            console.log("String JSON yang gagal diparse:", jsonShift);

                            // Gunakan data default agar modal tetap bisa ditampilkan
                            shiftData = {
                                '1': 0,
                                '2': 0,
                                '3': 0
                            };
                        }
                        // -------------------------------------

                        // Set judul modal
                        tukangNameTitle.innerText = `Detail Shift Gaji - ${nama}`;

                        // Kosongkan isi tabel sebelumnya
                        tableBody.innerHTML = '';

                        // --- LOGIKA PENGISIAN TABEL ---
                        let dataFound = false;

                        // Iterasi melalui objek shiftData (Shift 1, 2, 3)
                        for (const shiftNumber in shiftData) {
                            // Pastikan harga adalah angka atau 0 jika null/undefined dari database
                            const harga = parseFloat(shiftData[shiftNumber]) || 0;

                            dataFound = true;
                            const row = tableBody.insertRow();
                            row.className = 'border-b border-gray-100';

                            // Kolom Shift
                            let cellShift = row.insertCell();
                            cellShift.className = 'py-2 px-4 whitespace-nowrap text-sm font-medium text-gray-900';
                            cellShift.innerText = `Shift ${shiftNumber}`;

                            // Kolom Harga
                            let cellHarga = row.insertCell();
                            cellHarga.className = 'py-2 px-4 whitespace-nowrap text-sm text-gray-700 text-right';

                            if (harga > 0) {
                                cellHarga.innerText = formatRupiah(harga);
                            } else {
                                // Tampilkan pesan 'Tidak aktif' jika harga 0
                                cellHarga.innerHTML = '<span class="text-red-500">Rp 0 (Tidak aktif)</span>';
                            }
                        }

                        if (!dataFound && Object.keys(shiftData).length === 0) {
                            // Jika setelah parsing data masih kosong (tidak terduga)
                            const row = tableBody.insertRow();
                            const cell = row.insertCell();
                            cell.colSpan = 2;
                            cell.className = 'py-4 px-4 text-center text-sm text-gray-500';
                            cell.innerText = 'Data harga shift tidak ditemukan.';
                        }
                        // ------------------------------

                        // Tampilkan Modal
                        modal.classList.remove('hidden');
                    }

                    /**
                     * Menutup modal detail shift.
                     */
                    function closeShiftDetailModal() {
                        document.getElementById('shiftDetailModal').classList.add('hidden');
                    }
                </script>

                <?php
                break;

            // ==============================
            // CASE: FORM TAMBAH TUKANG
            // ==============================
            case 'tambah':
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Tambah Tukang</h2>

                        <form id="addTukangForm" method="POST">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 border p-4 rounded-lg">
                                <h3 class="text-lg font-bold text-gray-800 col-span-full mb-2">Data Pribadi</h3>

                                <div class="mb-4">
                                    <label for="nik" class="block text-gray-700 text-sm font-semibold mb-2">NIK <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="nik" name="nik" required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="nama_tukang" class="block text-gray-700 text-sm font-semibold mb-2">Nama Tukang
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" id="nama_tukang" name="nama_tukang" required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="jenis_kelamin" class="block text-gray-700 text-sm font-semibold mb-2">Jenis
                                        Kelamin <span class="text-red-500">*</span></label>
                                    <select id="jenis_kelamin" name="jenis_kelamin" required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="tgl_masuk" class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Masuk
                                        <span class="text-red-500">*</span></label>
                                    <input type="date" id="tgl_masuk" name="tgl_masuk" required
                                        value="<?php echo date('Y-m-d'); ?>"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label for="status" class="block text-gray-700 text-sm font-semibold mb-2">Status <span
                                            class="text-red-500">*</span></label>
                                    <select id="status" name="status" required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="Aktif">Aktif</option>
                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                    </select>
                                </div>

                            </div>

                            <div class="mb-6 border p-4 rounded-lg bg-yellow-50">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Gaji Per Shift (Isi 0 jika tidak berlaku)</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                    <div class="mb-2">
                                        <label for="harga_shift_1" class="block text-gray-700 text-sm font-semibold mb-2">Harga
                                            Shift 1 (Rp)</label>
                                        <input type="number" id="harga_shift_1" name="harga_shift_1" min="0" value="0"
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div class="mb-2">
                                        <label for="harga_shift_2" class="block text-gray-700 text-sm font-semibold mb-2">Harga
                                            Shift 2 (Rp)</label>
                                        <input type="number" id="harga_shift_2" name="harga_shift_2" min="0" value="0"
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>

                                    <div class="mb-2">
                                        <label for="harga_shift_3" class="block text-gray-700 text-sm font-semibold mb-2">Harga
                                            Shift 3 (Rp)</label>
                                        <input type="number" id="harga_shift_3" name="harga_shift_3" min="0" value="0"
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <a href="data_tukang.php"
                                    class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Batal</a>
                                <button type="submit" id="submitButton"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    Simpan Data
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                <script>
                    // ==============================================
                    // FUNGSI JAVASCRIPT UNTUK TAMBAH TUKANG (FINAL)
                    // ==============================================

                    // Deklarasikan semua elemen hanya sekali.
                    const addTukangForm = document.getElementById('addTukangForm');
                    const errorModal = document.getElementById('errorModal');
                    const errorMessage = document.getElementById('errorMessage');
                    const successAddModal = document.getElementById('successAddModal');
                    const successAddMessage = document.getElementById('successAddMessage');
                    const submitButton = document.getElementById('submitButton');


                    // Fungsi global untuk menutup modal Error (diperlukan karena dipanggil dari onclick HTML)
                    function closeErrorModal() {
                        if (errorModal) {
                            errorModal.classList.add('hidden');
                        }
                    }

                    // Fungsi global untuk menutup modal Sukses
                    function closeSuccessAddModal() {
                        if (successAddModal) {
                            successAddModal.classList.add('hidden');
                            window.location.href = 'data_tukang.php';
                        }
                    }

                    // Mengambil data formulir menjadi objek
                    function getFormData(form) {
                        const formData = new FormData(form);
                        const data = {};
                        for (const [key, value] of formData.entries()) {
                            data[key] = value;
                        }
                        return data;
                    }

                    // Fungsi validasi utama dan AJAX
                    function validasiTambahTukang(event) {
                        event.preventDefault();

                        // Pengecekan dasar elemen (jika ada yang null, hentikan)
                        if (!errorModal || !errorMessage || !successAddModal) {
                            console.error("Kesalahan DOM: Modal/Pesan Error tidak ditemukan.");
                            alert("Terjadi kesalahan sistem: Modal pesan tidak ditemukan.");
                            return;
                        }

                        const nikInput = document.getElementById('nik');
                        const nik = nikInput.value.trim();

                        if (nik === "" || document.getElementById('nama_tukang').value.trim() === "") {
                            errorMessage.innerText = 'NIK dan Nama Tukang wajib diisi.';
                            errorModal.classList.remove('hidden');
                            return;
                        }

                        submitButton.disabled = true;
                        submitButton.innerText = 'Menyimpan...';


                        // --- STEP 1: Pengecekan NIK di cek_nik.php ---
                        fetch(`cek_nik.php?nik=${nik}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.exists) {
                                    errorMessage.innerText = 'NIK sudah terdaftar. Mohon gunakan NIK lain.';
                                    errorModal.classList.remove('hidden');
                                    submitButton.disabled = false;
                                    submitButton.innerText = 'Simpan Data';
                                    return;
                                }

                                // Jika NIK OK, lanjutkan ke Step 2: Kirim Data
                                const formData = getFormData(addTukangForm);

                                // --- STEP 2: Kirim data ke aksi_tukang.php ---
                                fetch('aksi_tukang.php?act=tambah', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(formData)
                                })
                                    .then(response => {
                                        const contentType = response.headers.get("content-type");
                                        if (contentType && contentType.indexOf("application/json") !== -1) {
                                            return response.json();
                                        } else {
                                            // Tangani jika respon bukan JSON (Fatal Error PHP)
                                            return response.text().then(text => {
                                                console.error("Server Response (Non-JSON / PHP Fatal Error):", text);
                                                // Kembalikan objek error yang bisa ditangkap oleh .then(data)
                                                return { success: false, message: 'Respon server tidak valid (bukan JSON). Cek console untuk detail error PHP.' };
                                            });
                                        }
                                    })
                                    .then(data => {
                                        submitButton.disabled = false;
                                        submitButton.innerText = 'Simpan Data';

                                        if (data && data.success) {
                                            successAddMessage.innerText = data.message;
                                            successAddModal.classList.remove('hidden');
                                        } else {
                                            // Menangani pesan error dari server (termasuk duplikat NIK dari aksi_tukang.php)
                                            errorMessage.innerText = data.message || 'Terjadi kesalahan saat menyimpan data.';
                                            errorModal.classList.remove('hidden');
                                        }
                                    })
                                    .catch(error => {
                                        submitButton.disabled = false;
                                        submitButton.innerText = 'Simpan Data';

                                        console.error('Error saat mengirim data:', error);
                                        errorMessage.innerText = 'Gagal mengirim data atau ada kesalahan jaringan: ' + error.message;
                                        errorModal.classList.remove('hidden');
                                    });

                            })
                            .catch(error => {
                                // Catching errors from cek_nik.php (misalnya gagal koneksi)
                                submitButton.disabled = false;
                                submitButton.innerText = 'Simpan Data';
                                console.error('Error saat cek NIK:', error);
                                errorMessage.innerText = 'Gagal melakukan pengecekan NIK: ' + error.message;
                                errorModal.classList.remove('hidden');
                            });
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        // Set nilai default 0 untuk input number
                        const numberInputs = document.querySelectorAll('input[type="number"][min="0"]');
                        numberInputs.forEach(input => {
                            if (input.value === "") {
                                input.value = "0";
                            }
                        });

                        // Tambahkan event listener ke form
                        if (addTukangForm) {
                            addTukangForm.addEventListener('submit', validasiTambahTukang);
                        }
                    });
                </script>
                <?php
                break;

            // ==============================
// CASE: FORM EDIT TUKANG
// ==============================
            case 'edit':
                $id = $_GET['id'];
                // Ambil semua kolom, termasuk harga_shift_1, harga_shift_2, harga_shift_3
                $query = mysqli_query($konek, "SELECT * FROM tukang_nws WHERE id='$id'");
                $data = mysqli_fetch_array($query);

                if (!$data) {
                    echo "<script>alert('Tukang tidak ditemukan!'); window.location = 'data_tukang.php';</script>";
                    exit;
                }

                // Variabel Harga Shift yang sudah ada di database
                $harga_shift_1 = $data['harga_shift_1'] ?? 0;
                $harga_shift_2 = $data['harga_shift_2'] ?? 0;
                $harga_shift_3 = $data['harga_shift_3'] ?? 0;

                // --- Penyesuaian Status untuk Tampilan ---
                // Mapping nilai database ke nilai tampilan
                $status_aktif = ($data['status'] == 'Tetap' || $data['status'] == 'Aktif') ? 'Aktif' : 'Tidak Aktif';
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Edit Tukang</h2>

                        <form id="editTukangForm" onsubmit="validasiEditTukang(event)">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            <input type="hidden" name="nik_lama" value="<?= $data['nik'] ?>">

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">NIK</label>
                                <input type="text" name="nik" value="<?= $data['nik'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Tukang</label>
                                <input type="text" name="nama_tukang" value="<?= $data['nama_tukang'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Laki-laki" <?= $data['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>
                                        Laki-laki</option>
                                    <option value="Perempuan" <?= $data['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>
                                        Perempuan</option>
                                </select>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-4 border-b pb-1">Pengaturan Harga Shift</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-semibold mb-2">Harga Shift 1 (Rp)</label>
                                    <input type="number" name="harga_shift_1" value="<?= $harga_shift_1 ?>" required min="0"
                                        step="1000"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-semibold mb-2">Harga Shift 2 (Rp)</label>
                                    <input type="number" name="harga_shift_2" value="<?= $harga_shift_2 ?>" required min="0"
                                        step="1000"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-semibold mb-2">Harga Shift 3 (Rp)</label>
                                    <input type="number" name="harga_shift_3" value="<?= $harga_shift_3 ?>" required min="0"
                                        step="1000"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Masuk</label>
                                <input type="date" name="tgl_masuk" value="<?= $data['tgl_masuk'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                                <select name="status" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Aktif" <?= $status_aktif == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="Tidak Aktif" <?= $status_aktif == 'Tidak Aktif' ? 'selected' : '' ?>>Tidak Aktif
                                    </option>
                                </select>
                            </div>

                            <div class="flex justify-between mt-6">
                                <a href="data_tukang.php"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                                    Batal
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="successModal"
                    class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm text-center">
                        <h2 class="text-xl font-semibold text-green-600 mb-2">Berhasil!</h2>
                        <p id="successMessage" class="text-gray-700">Data tukang berhasil diperbarui.</p>
                        <button onclick="closeModal()"
                            class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            OK
                        </button>
                    </div>
                </div>

                <div id="errorModal"
                    class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                        <h2 class="text-lg font-bold text-gray-800">Error!</h2>
                        <p class="text-gray-600 mt-2" id="errorMessage">Pesan error akan ditampilkan di sini.</p>
                        <div class="mt-4">
                            <button onclick="closeErrorModal()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                        </div>
                    </div>
                </div>

                <script>
                    function closeErrorModal() {
                        document.getElementById("errorModal").classList.add("hidden");
                    }

                    function closeModal() {
                        document.getElementById("successModal").classList.add("hidden");
                        window.location.href = "data_tukang.php";
                    }

                    function validasiEditTukang(event) {
                        event.preventDefault();

                        let form = document.getElementById("editTukangForm");
                        let formData = new FormData(form);

                        // Ambil semua data dan konversi ke objek JSON untuk Fetch
                        let dataJSON = {};
                        for (let [key, value] of formData.entries()) {
                            dataJSON[key] = value.trim();
                        }

                        // Konversi harga ke angka (untuk validasi)
                        let harga_s1 = parseFloat(dataJSON.harga_shift_1);
                        let harga_s2 = parseFloat(dataJSON.harga_shift_2);
                        let harga_s3 = parseFloat(dataJSON.harga_shift_3);

                        const errorModal = document.getElementById("errorModal");
                        const errorMessage = document.getElementById("errorMessage");

                        // Validasi wajib isi
                        if (!dataJSON.nik || !dataJSON.nama_tukang || !dataJSON.jenis_kelamin || !dataJSON.tgl_masuk || !dataJSON.status ||
                            dataJSON.harga_shift_1 === "" || dataJSON.harga_shift_2 === "" || dataJSON.harga_shift_3 === "") {
                            errorMessage.innerText = "Semua field, termasuk Harga Shift 1, 2, dan 3, wajib diisi.";
                            errorModal.classList.remove("hidden");
                            return;
                        }

                        // Validasi NIK
                        if (dataJSON.nik.length !== 16 || isNaN(dataJSON.nik)) {
                            errorMessage.innerText = "NIK harus terdiri dari 16 digit angka.";
                            errorModal.classList.remove("hidden");
                            return;
                        }

                        // Validasi Harga Multi-Shift harus positif dan minimal satu > 0
                        if (isNaN(harga_s1) || harga_s1 < 0 || isNaN(harga_s2) || harga_s2 < 0 || isNaN(harga_s3) || harga_s3 < 0) {
                            errorMessage.innerText = `Harga Shift harus berupa angka positif.`;
                            errorModal.classList.remove('hidden');
                            return;
                        }

                        if (harga_s1 <= 0 && harga_s2 <= 0 && harga_s3 <= 0) {
                            errorMessage.innerText = "Minimal satu Harga Shift harus lebih besar dari 0.";
                            errorModal.classList.remove('hidden');
                            return;
                        }

                        // Pengecekan NIK Duplikat (hanya jika NIK berubah)
                        if (dataJSON.nik !== dataJSON.nik_lama) {
                            fetch(`cek_nik.php?nik=${dataJSON.nik}&id_tukang=${dataJSON.id}`)
                                .then(response => {
                                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                                    return response.json();
                                })
                                .then(data => {
                                    if (data && data.exists) {
                                        errorMessage.innerText = 'NIK sudah terdaftar pada tukang lain.';
                                        errorModal.classList.remove('hidden');
                                        return;
                                    }
                                    // Lanjutkan submit jika NIK unik
                                    submitEditForm(dataJSON);
                                })
                                .catch(error => {
                                    console.error('Error saat memeriksa NIK:', error);
                                    errorMessage.innerText = 'Gagal menghubungi server untuk memeriksa NIK.';
                                    errorModal.classList.remove('hidden');
                                });
                        } else {
                            // Langsung submit jika NIK tidak berubah
                            submitEditForm(dataJSON);
                        }
                    }

                    function submitEditForm(dataJSON) {
                        const errorModal = document.getElementById("errorModal");
                        const errorMessage = document.getElementById("errorMessage");

                        fetch("aksi_tukang.php?act=update", {
                            method: "POST",
                            headers: {
                                'Content-Type': 'application/json' // Kirim sebagai JSON
                            },
                            body: JSON.stringify(dataJSON) // Konversi objek JS menjadi string JSON
                        })
                            .then(response => {
                                if (response.headers.get("content-type") && response.headers.get("content-type").includes("application/json")) {
                                    return response.json();
                                } else {
                                    return response.text().then(text => {
                                        console.error("Non-JSON Server Response:", text);
                                        throw new Error("Respon server tidak valid (Bukan JSON). Cek console PHP.");
                                    });
                                }
                            })
                            .then(data => {
                                if (data.success) {
                                    document.getElementById("successMessage").innerText = data.message;
                                    document.getElementById("successModal").classList.remove("hidden");
                                } else {
                                    errorMessage.innerText = data.message || "Terjadi kesalahan saat menyimpan.";
                                    errorModal.classList.remove("hidden");
                                }
                            })
                            .catch(error => {
                                console.error("Fetch Error:", error);
                                errorMessage.innerText = error.message || "Gagal mengirim data ke server. Cek koneksi atau log server.";
                                errorModal.classList.remove("hidden");
                            });
                    }
                </script>

                <?php
                break;
        }
        ?>

        <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
                <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
                <p>Apakah Anda yakin ingin menghapus tukang ini?</p>
                <div class="flex justify-end mt-4">
                    <button id="cancelDelete"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                    <button id="confirmDelete"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hapus</button>
                </div>
            </div>
        </div>

        <script>
            let deleteUrl = '';

            function openDeleteModal(url) {
                deleteUrl = url;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            document.getElementById('confirmDelete').onclick = function () {
                fetch(deleteUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('deleteModal').classList.add('hidden');
                            // Menggunakan Modal Sukses Delete/Umum
                            document.getElementById('successMessage').innerText = data.message;
                            document.getElementById('successModal').classList.remove('hidden');
                        } else {
                            // Menggunakan alert karena modal error yang spesifik untuk halaman ini belum ada di bagian bawah
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error saat menghapus:', error);
                        alert('Gagal menghapus data: Kesalahan jaringan/server.');
                    });
            };

            document.getElementById('cancelDelete').onclick = function () {
                document.getElementById('deleteModal').classList.add('hidden');
            };

            function closeSuccessModal() {
                document.getElementById('successModal').classList.add('hidden');
                window.location.href = "data_tukang.php";
            }

            // Fungsi ini digunakan di modal successAddModal yang ada di bagian atas kode.
            function closeSuccessAddModal() {
                document.getElementById('successAddModal').classList.add('hidden');
                window.location.href = "data_tukang.php";
            }
        </script>

        <?php include 'footer.php'; ?>
    </div>

</body>

</html>