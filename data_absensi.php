<?php
include("koneksi.php");
include("sidebar.php");
setlocale(LC_TIME, 'id_ID.UTF-8'); // Atur locale ke Indonesia (format tanggal IDN)

// define variabel untuk filter bulan dan tahun
$bulanFilter = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahunFilter = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// define variabel default (untuk fungsi edit) - These are now primarily used for initial filter display
// The actual values for edit modal come from data attributes on the edit button
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>
        /* Styles for the success pop-up */
        .success-popup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            /* Green */
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .success-popup.show {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-100 text-sm">
    <div id="successMessage" class="success-popup hidden"></div>

    <div class="lg:ml-[300px] p-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6 flex flex-col lg:flex-row justify-between items-center">
            <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
            <div class="flex items-center gap-2 mt-4 lg:mt-0">
                <span>Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Data Absensi Tukang</h2>
            <span class="text-gray-500"><?php echo date('d F Y'); ?></span>
        </div>

        <section class="bg-white p-6 rounded-xl shadow">
            <div class="bg-blue-600 text-white text-sm font-semibold rounded px-3 py-2 mb-4">
                Filter Data Kehadiran Tukang
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
                        $bulanFilter = $_GET['bulan'] ?? '';
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
                        $tahunFilter = $_GET['tahun'] ?? '';
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
                        $mingguFilter = $_GET['minggu'] ?? '';
                        for ($m = 1; $m <= 5; $m++) {
                            $selected = ($mingguFilter == $m) ? "selected" : "";
                            echo "<option value='$m' $selected>Minggu ke-$m</option>";
                        }
                        ?>
                    </select>
                </label>

                <div class="ml-auto flex gap-2 mb-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-eye"></i> Tampilkan Data
                    </button>
                    <button type="button" id="btnTambah"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-plus"></i> Input Kehadiran
                    </button>
                </div>
            </form>
            <!-- Container baru: kiri search, kanan tombol hapus -->
            <div class="flex justify-between items-center mb-4">
                <input type="text" id="searchInput" placeholder="Cari Nama Karyawan..."
                    class="border border-gray-300 rounded px-2 py-1 w-64">

                <button id="deleteSelected" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ml-4">
                    Hapus Terpilih
                </button>
            </div>

            <div class="bg-blue-100 text-blue-800 rounded px-4 py-2 mb-4 text-sm">
                <?php if ($bulanFilter && $tahunFilter && $mingguFilter): ?>
                    Menampilkan Data Kehadiran Tukang Bulan:
                    <strong><?= $bulanNama[$bulanFilter] ?? $bulanFilter ?></strong>,
                    Tahun: <strong><?= $tahunFilter ?></strong>,
                    Minggu ke-<strong><?= $mingguFilter ?></strong>
                <?php else: ?>
                    Silakan pilih bulan, tahun, dan minggu untuk menampilkan data.
                <?php endif; ?>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-gray-600 border border-gray-300">
                    <thead class="bg-gray-100 text-gray-500">
                        <tr>
                            <th class="border px-3 py-2 text-center font-semibold"><input type="checkbox" id="selectAll"
                                    class="cursor-pointer"></th>
                            <th class='border px-3 py-2 text-center font-semibold'>NIK</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Nama Karyawan</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Jabatan</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Tanggal Masuk</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Tanggal Keluar</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Jam Masuk</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Jam Keluar</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Hadir</th>
                            <th class='border px-3 py-2 text-center font-semibold'>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($bulanFilter && $tahunFilter && $mingguFilter) {
                            // Query dengan filter bulan, tahun dan minggu (asumsi ada kolom minggu di tabel absensi_tukang)
                            $bulanInt = intval($bulanFilter);
                            $mingguInt = intval($mingguFilter);

                            $queryAbsensi = mysqli_query($konek, "
                        SELECT a.*, t.nama_tukang, t.jenis_kelamin, t.jabatan
                        FROM absensi_tukang a
                        JOIN tukang_nws t ON a.nik = t.nik
                        WHERE MONTH(a.tanggal_masuk) = $bulanInt
                        AND YEAR(a.tanggal_masuk) = '$tahunFilter'
                        AND a.minggu = $mingguInt
                        ORDER BY a.id DESC
                    ");

                            if (mysqli_num_rows($queryAbsensi) > 0) {
                                while ($row = mysqli_fetch_assoc($queryAbsensi)) {
                                    $id = htmlspecialchars($row['id'], ENT_QUOTES);
                                    $nik = htmlspecialchars($row['nik'], ENT_QUOTES);
                                    $nama_tukang = htmlspecialchars(ucwords($row['nama_tukang']), ENT_QUOTES);
                                    $jabatan = htmlspecialchars(ucwords($row['jabatan']), ENT_QUOTES);
                                    $tanggal_masuk = htmlspecialchars($row['tanggal_masuk'], ENT_QUOTES);
                                    $tanggal_keluar = htmlspecialchars($row['tanggal_keluar'], ENT_QUOTES);
                                    $jam_masuk = htmlspecialchars($row['jam_masuk'], ENT_QUOTES);
                                    $jam_keluar = htmlspecialchars($row['jam_keluar'], ENT_QUOTES);
                                    $total_hadir = htmlspecialchars($row['total_hadir'], ENT_QUOTES);

                                    echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
    <td class='py-4 px-6 text-center'>
        <input type='checkbox' class='selectRow' value='{$id}'>
    </td>
    <td class='py-4 px-6 text-center'>{$nik}</td>
    <td class='py-4 px-6'>{$nama_tukang}</td>
    <td class='py-4 px-6'>{$jabatan}</td>
    <td class='py-4 px-6'>" . strftime('%d %B %Y', strtotime($tanggal_masuk)) . "</td>
    <td class='py-4 px-6'>" . strftime('%d %B %Y', strtotime($tanggal_keluar)) . "</td>
    <td class='py-4 px-6'>" . date('H:i', strtotime($jam_masuk)) . "</td>
    <td class='py-4 px-6'>" . date('H:i', strtotime($jam_keluar)) . "</td>
    <td class='py-4 px-6'>{$total_hadir} hari</td>
    <td class='py-4 px-6 text-center flex gap-2 justify-center'>
        <a href='#' class='edit-button bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'
            data-id='{$id}'
            data-nik='{$nik}'
            data-nama='{$nama_tukang}' 
            data-bulan='{$bulanFilter}'
            data-tahun='{$tahunFilter}'
            data-minggu='{$mingguFilter}'
            data-minggu='{$mingguFilter}'
            data-jam-masuk='{$jam_masuk}'
            data-jam-keluar='{$jam_keluar}'
            data-tanggal-masuk='{$tanggal_masuk}'
            data-tanggal-keluar='{$tanggal_keluar}'>
            <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
        </a>
        <a href='#' onclick=\"openDeleteModal('aksi_absensi.php?act=delete&id={$id}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
            <ion-icon name='trash-outline' class='mr-1'></ion-icon> Hapus
        </a>
    </td>
</tr>";
                                }
                            } else {
                                echo "<tr>
                            <td colspan='10' class='text-center text-gray-400 py-4'>Data belum tersedia untuk filter tersebut.</td>
                        </tr>";
                            }
                        } else {
                            echo "<tr>
                        <td colspan='10' class='text-center text-gray-400 py-4'>Silakan pilih bulan, tahun, dan minggu terlebih dahulu.</td>
                    </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div id="formTambah" class="mt-6 hidden bg-gray-50 p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-4">Tambah Data Absensi Tukang</h3>
                <form action="aksi_absensi.php?act=tambah" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1">Nama Karyawan</label>
                        <select name="nik" class="w-full border px-3 py-1 rounded" required>
                            <option value="">-- Pilih Karyawan --</option>
                            <?php
                            $queryTukang = mysqli_query($konek, "SELECT nik, nama_tukang FROM tukang_nws WHERE status = 'Tetap'");
                            while ($tukang = mysqli_fetch_assoc($queryTukang)) {
                                echo "<option value='" . htmlspecialchars($tukang['nik']) . "'>" . htmlspecialchars($tukang['nama_tukang']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Bulan</label>
                        <select name="bulan" class="w-full border px-3 py-1 rounded" required>
                            <option value="">-- Pilih Bulan --</option>
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
                                echo "<option value='$key'>$nama</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Tambahan input minggu -->
                    <div>
                        <label class="block mb-1">Minggu Ke-</label>
                        <input type="number" name="minggu" class="w-full border px-3 py-1 rounded" min="1" max="5"
                            required placeholder="1 - 5">
                    </div>
                    <div>
                        <label class="block mb-1">Tahun</label>
                        <input type="text" name="tahun" class="w-full border px-3 py-1 rounded"
                            value="<?php echo date('Y'); ?>" required>
                    </div>
                    <div>
                        <label class="block mb-1">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="w-full border px-3 py-1 rounded" required>
                    </div>
                    <div>
                        <label class="block mb-1">Tanggal Keluar</label>
                        <input type="date" name="tanggal_keluar" class="w-full border px-3 py-1 rounded" required>
                    </div>

                    <div>
                        <label class="block mb-1">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="w-full border px-3 py-1 rounded" required>
                    </div>
                    <div>
                        <label class="block mb-1">Jam Keluar</label>
                        <input type="time" name="jam_keluar" class="w-full border px-3 py-1 rounded" required>
                    </div>
                    <div class="col-span-2 flex justify-end gap-2 mt-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                        <button type="button" id="btnBatal"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Batal</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <!-- Modal Edit Absensi -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-xl relative">
            <h2 class="text-xl font-bold mb-4">Edit Absensi</h2>
            <form action="aksi_absensi.php?act=edit" method="post">
                <input type="hidden" id="edit_id" name="id">

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-2">
                        <label class="block mb-1">Nama Karyawan</label>
                        <input type="text" id="edit_nama" name="nama"
                            class="w-full border px-3 py-2 rounded bg-gray-100" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">NIK</label>
                        <input type="text" name="nik" value="<?= htmlspecialchars($nik) ?>"
                            class="w-full border px-3 py-2 rounded bg-gray-100" readonly>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Bulan</label>
                        <select id="edit_bulan" name="bulan" class="w-full border px-3 py-2 rounded" required>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Tahun</label>
                        <select id="edit_tahun" name="tahun" class="w-full border px-3 py-2 rounded" required>
                            <?php
                            $yearNow = date('Y');
                            for ($i = $yearNow; $i >= 2020; $i--) {
                                echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Minggu Ke-</label>
                        <input type="number" id="edit_minggu" name="minggu" class="w-full border px-3 py-2 rounded"
                            min="1" max="5" required>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Jam Masuk</label>
                        <input type="time" id="edit_jam_masuk" name="jam_masuk" class="w-full border px-3 py-2 rounded"
                            required>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Jam Keluar</label>
                        <input type="time" id="edit_jam_keluar" name="jam_keluar"
                            class="w-full border px-3 py-2 rounded" required>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Tanggal Masuk</label>
                        <input type="date" id="edit_tanggal_masuk" name="tanggal_masuk"
                            class="w-full border px-3 py-2 rounded" required>
                    </div>

                    <div class="mb-2">
                        <label class="block mb-1">Tanggal Keluar</label>
                        <input type="date" id="edit_tanggal_keluar" name="tanggal_keluar"
                            class="w-full border px-3 py-2 rounded" required>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeEditModal()"
                        class="mr-3 bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
            <button onclick="closeEditModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
        </div>
    </div>

    <!-- Modal Edit Absensi -->

    <div id="deleteModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-80">
            <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
            <p class="mb-4">Apakah anda yakin ingin menghapus data ini?</p>
            <div class="flex justify-end gap-2">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                <button id="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            </div>
        </div>
    </div>
    <div id="deleteSelectedModal"
        class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-80">
            <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus Terpilih</h2>
            <p class="mb-4">Apakah anda yakin ingin menghapus data yang dipilih?</p>
            <div class="flex justify-end gap-2">
                <button id="cancelDeleteSelected" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                <button id="confirmDeleteSelected"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            </div>
        </div>
    </div>
    <script>
        let deleteUrl = '';
        const successMessageDiv = document.getElementById('successMessage');

        function showSuccessPopup(message) {
            successMessageDiv.textContent = message;
            successMessageDiv.classList.remove('hidden');
            successMessageDiv.classList.add('show');

            setTimeout(() => {
                successMessageDiv.classList.remove('show');
                setTimeout(() => {
                    successMessageDiv.classList.add('hidden');
                }, 500); // Wait for fade-out animation to complete
            }, 3000); // Message visible for 3 seconds
        }

        // Menampilkan modal konfirmasi untuk single delete
        function openDeleteModal(url) {
            deleteUrl = url;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        // Konfirmasi hapus untuk single delete
        document.getElementById('confirmDelete').addEventListener('click', function () {
            fetch(deleteUrl, {
                method: 'GET', // Or POST if you prefer
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('deleteModal').classList.add('hidden');
                        showSuccessPopup(data.message); // Show success pop-up
                        // Optional: Show a success message to the user before reloading
                        setTimeout(() => {
                            location.reload(); // Reload after the pop-up is shown
                        }, 1000); // Adjust delay as needed
                    } else {
                        alert(data.message || 'Gagal menghapus data.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data.');
                });
        });

        // Batal hapus untuk single delete
        document.getElementById('cancelDelete').addEventListener('click', function () {
            document.getElementById('deleteModal').classList.add('hidden');
        });
        // END script menampilkan modal konfirmasi hapus

        // script menampilkan form tambah absensi
        document.getElementById("btnTambah").addEventListener("click", function () {
            document.getElementById("formTambah").classList.remove("hidden");
        });

        document.getElementById("btnBatal").addEventListener("click", function () {
            document.getElementById("formTambah").classList.add("hidden");
        });
        // END script menampilkan form tambah absensi

        const formTambah = document.getElementById('formTambah');
        const bulanSelect = formTambah.querySelector('select[name="bulan"]');
        const tahunInput = formTambah.querySelector('input[name="tahun"]');
        const tanggalMasuk = formTambah.querySelector('input[name="tanggal_masuk"]');
        const tanggalKeluar = formTambah.querySelector('input[name="tanggal_keluar"]');


        function pad(num) {
            return num.toString().padStart(2, '0');
        }

        function updateTanggal() {
            const bulan = bulanSelect.value;
            const tahun = tahunInput.value;

            if (bulan && tahun) {
                const tanggalAwal = `${tahun}-${bulan}-01`;
                tanggalMasuk.value = tanggalAwal;

                // Tentukan jumlah hari dalam bulan tersebut
                const lastDay = new Date(tahun, parseInt(bulan), 0).getDate(); // 0 artinya hari terakhir bulan sebelumnya
                const tanggalAkhir = `${tahun}-${bulan}-${pad(lastDay)}`;
                tanggalKeluar.value = tanggalAkhir;
            }
        }

        bulanSelect.addEventListener('change', updateTanggal);
        tahunInput.addEventListener('input', updateTanggal);

        // Scroll ke form dan fokus ke input pertama
        document.getElementById("btnTambah").addEventListener("click", function () {
            const form = document.getElementById("formTambah");
            form.classList.remove("hidden");

            // Scroll ke form
            form.scrollIntoView({ behavior: 'smooth' });

            // Fokus ke input pertama
            const firstInput = form.querySelector("select[name='nik']");
            if (firstInput) firstInput.focus();

            updateTanggal(); // otomatis isi tanggal jika bulan/tahun sudah dipilih
        });

        // Script untuk menampilkan modal edit absensi
        function openEditModal(id, nik, bulan, tahun, minggu, jamMasuk, jamKeluar, tanggalMasuk, tanggalKeluar, nama) {
            // Format bulan jadi 2 digit jika perlu
            if (bulan.toString().length === 1) {
                bulan = bulan.toString().padStart(2, '0');
            }

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nama').value = nama;
            document.querySelector('input[name="nik"]').value = nik; // <- tambahkan ini

            document.getElementById('edit_bulan').value = padZero(bulan);
            document.getElementById('edit_tahun').value = tahun;
            document.getElementById('edit_minggu').value = minggu;
            document.getElementById('edit_jam_masuk').value = jamMasuk;
            document.getElementById('edit_jam_keluar').value = jamKeluar;
            document.getElementById('edit_tanggal_masuk').value = tanggalMasuk;
            document.getElementById('edit_tanggal_keluar').value = tanggalKeluar;

            document.getElementById('editModal').classList.remove('hidden');

            function padZero(value) {
                return value.toString().padStart(2, '0');
            }

        }

        document.getElementById('editModal').querySelector('form').addEventListener('submit', function (event) {
            event.preventDefault(); // Cegah submit default

            const form = this;
            const formData = new FormData(form);
            const url = form.action;

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessPopup(data.message);
                        closeEditModal();

                        // Redirect ke URL yang diberikan dalam respons
                        setTimeout(() => {
                            window.location.href = data.redirect; // Redirect ke URL yang benar
                        }, 1000);
                    } else {
                        alert(data.message || 'Gagal menyimpan perubahan.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan perubahan.');
                });
        });

        // modal pop up untuk menyimpan perubahan edit absensi
        document.querySelector('form[action="aksi_absensi.php?act=edit"]').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network error: ' + res.status);
                    return res.text();
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            showSuccessPopup(data.message);
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        } else {
                            showSuccessPopup(data.message || 'Gagal menyimpan perubahan.');
                        }
                    } catch (err) {
                        console.error('Failed to parse JSON:', err, 'Response:', text);
                        showSuccessPopup('Respons server tidak valid.');
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    showSuccessPopup('Terjadi kesalahan saat menyimpan perubahan.');
                });
        });

        // END modal pop up untuk menyimpan perubahan edit absensi

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Add event listeners to all edit buttons
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault(); // Cegah reload halaman
                openEditModal(
                    button.dataset.id,
                    button.dataset.nik,
                    button.dataset.bulan,
                    button.dataset.tahun,
                    button.dataset.minggu,
                    button.dataset.jamMasuk,
                    button.dataset.jamKeluar,
                    button.dataset.tanggalMasuk,
                    button.dataset.tanggalKeluar,
                    button.dataset.nama
                );
            });
        });


        // Script fitur search
        document.getElementById('searchInput').addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase(); // Ambil nilai input pencarian dan ubah ke huruf kecil
            const rows = document.querySelectorAll('tbody tr'); // Ambil semua baris dalam tbody

            rows.forEach(row => {
                let found = false; // Flag untuk menandai apakah ada kecocokan di baris ini
                // Loop melalui semua sel dalam setiap baris (kecuali kolom Aksi)
                // Start from index 1 to skip the checkbox column, and go up to length - 1 to skip the action column
                for (let i = 1; i < row.cells.length - 1; i++) {
                    const cellText = row.cells[i].textContent.toLowerCase(); // Ambil teks dari setiap sel dan ubah ke huruf kecil
                    if (cellText.includes(searchValue)) {
                        found = true; // Jika ada kecocokan, set flag menjadi true
                        break; // Keluar dari loop sel karena sudah ditemukan kecocokan
                    }
                }
                if (found) {
                    row.style.display = ''; // Tampilkan baris jika ada kecocokan di salah satu sel
                } else {
                    row.style.display = 'none'; // Sembunyikan baris jika tidak ada kecocokan
                }
            });
        });

        // Script untuk menghapus beberapa data absensi
        // Pilih semua checkbox
        document.getElementById('selectAll').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.selectRow');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Show delete selected modal
        document.getElementById('deleteSelected').addEventListener('click', function () {
            const selectedIds = Array.from(document.querySelectorAll('.selectRow:checked')).map(checkbox => checkbox.value);
            if (selectedIds.length === 0) {
                alert('Silakan pilih data yang ingin dihapus.'); // Use standard alert for simplicity
                return;
            }
            document.getElementById('deleteSelectedModal').classList.remove('hidden');
        });

        // Konfirmasi hapus terpilih
        document.getElementById('confirmDeleteSelected').addEventListener('click', function () {
            const selectedIds = Array.from(document.querySelectorAll('.selectRow:checked')).map(checkbox => checkbox.value);
            fetch('aksi_absensi.php?act=delete&ids=' + selectedIds.join(','))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessPopup(data.message); // Show success pop-up
                        document.getElementById('deleteSelectedModal').classList.add('hidden');
                        setTimeout(() => {
                            location.reload(); // Reload the page to see the changes
                        }, 1000); // Adjust delay as needed
                    } else {
                        alert(data.message || 'Gagal menghapus data.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data.');
                });
        });

        // Batal hapus terpilih
        document.getElementById('cancelDeleteSelected').addEventListener('click', function () {
            document.getElementById('deleteSelectedModal').classList.add('hidden');
        });

    </script>
</body>

</html>