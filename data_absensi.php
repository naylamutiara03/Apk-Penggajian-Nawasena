<?php
include("koneksi.php");
include("sidebar.php");
setlocale(LC_TIME, 'id_ID.UTF-8'); // Atur locale ke Indonesia (format tanggal IDN)
$bulanFilter = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahunFilter = isset($_GET['tahun']) ? $_GET['tahun'] : '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100 text-sm">
    <div class="lg:ml-[300px] p-6">
        <!-- Header -->
        <div class="bg-white p-4 rounded-xl shadow mb-6 flex flex-col lg:flex-row justify-between items-center">
            <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
            <div class="flex items-center gap-2 mt-4 lg:mt-0">
                <span>Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
            </div>
        </div>

        <!-- Title & Date -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Data Absensi Tukang</h2>
            <span class="text-gray-500"><?php echo date('d F Y'); ?></span>
        </div>

        <!-- Main Content -->
        <section class="bg-white p-6 rounded-xl shadow">
            <!-- Filter Section -->
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
                            $selected = (isset($_GET['tahun']) && $_GET['tahun'] == $y) ? "selected" : "";
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
                    <button type="button" id="btnTambah"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-plus"></i> Input Kehadiran
                    </button>
                </div>
            </form>
            <!-- END Filter Section -->

            <!-- Info Text -->
            <div class="bg-blue-100 text-blue-800 rounded px-4 py-2 mb-4 text-sm">
                <?php if ($bulanFilter && $tahunFilter): ?>
                    Menampilkan Data Kehadiran Tukang Bulan:
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
                            <?php
                            $headers = [
                                "NIK",
                                "Nama Karyawan",
                                "Jabatan",
                                "Tanggal Masuk",
                                "Tanggal Keluar",
                                "Jam Masuk",
                                "Jam Keluar",
                                "Hadir",
                                "Aksi"
                            ];
                            foreach ($headers as $head) {
                                echo "<th class='border px-3 py-2 text-center font-semibold'>$head</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryAbsensi = mysqli_query($konek, "
                       SELECT a.*, t.nama_tukang, t.jenis_kelamin, t.jabatan 
                       FROM absensi_tukang a
                       JOIN tukang_nws t ON a.nik = t.nik
                       WHERE " . ($bulanFilter && $tahunFilter ? "MONTH(a.tanggal_masuk) = " . intval($bulanFilter) . " AND YEAR(a.tanggal_masuk) = '$tahunFilter'" : "1") . "
                       ORDER BY a.id DESC
                   ");
                        if (mysqli_num_rows($queryAbsensi) > 0) {
                            while ($row = mysqli_fetch_assoc($queryAbsensi)) {
                                echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
                                    <td class='py-4 px-6 text-center'>{$row['nik']}</td>
                                    <td class='py-4 px-6'>{$row['nama_tukang']}</td>
                                    <td class='py-4 px-6'>{$row['jabatan']}</td>
                                    <td class='py-4 px-6'>" . strftime('%d %B %Y', strtotime($row['tanggal_masuk'])) . "</td>
                                    <td class='py-4 px-6'>" . strftime('%d %B %Y', strtotime($row['tanggal_keluar'])) . "</td>
                                    <td class='py-4 px-6'>" . date('H:i', strtotime($row['jam_masuk'])) . "</td>
                                    <td class='py-4 px-6'>" . date('H:i', strtotime($row['jam_keluar'])) . "</td>
                                    <td class='py-4 px-6'>{$row['total_hadir']} hari</td>
                                    <td class='py-4 px-6 text-center flex gap-2 justify-center'>
                                        <a href='#' onclick=\"openEditModal('{$row['id']}')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                                            <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                                        </a>
                                        <a href='#' onclick=\"openDeleteModal('aksi_absensi.php?act=delete&id={$row['id']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
                                            <ion-icon name='trash-outline' class='mr-1'></ion-icon> Hapus
                                        </a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr>
                                <td colspan='6' class='text-center text-gray-400 py-4'>Data belum tersedia.</td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- END Data Table -->

            <!-- Form Tambah Absensi -->
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
            <!-- END Form Tambah Absensi -->
        </section>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 max -w-sm mx-auto">
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus data absensi ini?</p>
            <div class="flex justify-end mt-4">
                <button id="cancelDelete"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                <button id="confirmDelete"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hapus</button>
            </div>
        </div>
    </div>
    <!-- END Modal Konfirmasi Hapus -->

    <script>
        let deleteUrl = '';

        // script menampilkan modal konfirmasi hapus
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
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        };

        document.getElementById('cancelDelete').onclick = function () {
            document.getElementById('deleteModal').classList.add('hidden');
        };
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

    </script>

</body>

</html>