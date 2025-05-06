<?php
include("koneksi.php");
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Jabatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <!-- Modal untuk Pesan Sukses -->
        <div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successMessage">Data jabatan berhasil dihapus.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>
        <!-- END Header Section -->

        <!-- Title & Tanggal Section -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Data Jabatan</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <!-- END Title & Tanggal Section -->

        <!-- Filter Dropdown -->
        <div class="flex items-center mb-4">
            <p class="mr-2">Pilih Berdasarkan:</p>
            <select id="filterJabatan" class="border rounded-lg p-2" onchange="filterJabatan()">
                <option value="all">Semua Jabatan</option>
                <option value="karyawan">Karyawan</option>
                <option value="tukang">Tukang</option>
            </select>
        </div>
        <!-- END Filter Dropdown -->

        <div id="content">
            <!-- Tabel Data Jabatan -->
            <div id="tableJabatan">
                <div class="flex justify-center">
                    <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                                <thead class="bg-blue-600 text-white text-sm uppercase font-semibold">
                                    <tr>
                                        <th class="py-4 px-6 text-center">No</th>
                                        <th class="py-4 px-6 text-left">Nama Jabatan</th>
                                        <th class="py-4 px-6 text-left">Gaji Pokok</th>
                                        <th class="py-4 px-6 text-left">Tunjangan Jabatan</th>
                                        <th class="py-4 px-6 text-left">Total</th>
                                        <th class="py-4 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm" id="jabatanData">
                                    <?php
                                    $no = 1;
                                    $sql = mysqli_query($konek, "SELECT * FROM jabatan ORDER BY jabatan ASC");
                                    while ($d = mysqli_fetch_array($sql)) {
                                        echo "<tr class='border-b border-gray-200 hover:bg-blue-100' data-jenis='{$d['jenis']}'>
                                        <td class='py-4 px-6 text-center font-bold'>$no</td>
                                        <td class='py-4 px-6'>{$d['jabatan']}</td>
                                        <td class='py-4 px-6'>" . number_format($d['gapok'], 0, ',', '.') . "</td>
                                        <td class='py-4 px-6'>" . number_format($d['tunjangan_jabatan'], 0, ',', '.') . "</td>
                                        <td class='py-4 px-6'>" . number_format($d['total'], 0, ',', '.') . "</td>
                                        <td class='py-4 px-6 text-center flex flex-col lg:flex-row gap-2 justify-center'>
                                            <a href='#' onclick=\"openEditModal({$d['id']}, '{$d['jabatan']}', {$d['gapok']}, {$d['tunjangan_jabatan']}, '{$d['jenis']}')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                                                <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                                            </a>
                                            <a href='#' onclick=\"openDeleteModal('aksi_jabatan.php?act=delete&id={$d['id']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
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
                            <button onclick="openAddJabatanModal()"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                                <ion-icon name="person-add-outline" class="mr-1"></ion-icon> Tambah Jabatan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function filterJabatan() {
                const filterValue = document.getElementById('filterJabatan').value;
                const rows = document.querySelectorAll('#jabatanData tr');

                rows.forEach(row => {
                    if (filterValue === 'all' || row.getAttribute('data-jenis') === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>

        <!-- Modal konfirmasi hapus -->
        <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
                <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
                <p>Apakah Anda yakin ingin menghapus jabatan ini?</p>
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
                            document.getElementById("successMessage").innerText = data.message;
                            document.getElementById("successModal").classList.remove("hidden");
                            filterJabatan(); // Refresh the table after deletion
                        } else {
                            alert(data.message);
                        }
                });
            };

            document.getElementById('cancelDelete').onclick = function () {
                document.getElementById('deleteModal').classList.add('hidden');
            };

            function closeSuccessModal() {
                document.getElementById('successModal').classList.add('hidden');
                window.location.href = "data_jabatan.php";
            }
        </script>

        <?php include 'footer.php'; ?>
    </div>

</body>

</html>

<!-- Tambah Data Jabatan Modal -->
<div id="addJabatanModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Tambah Data Jabatan</h2>
        <form id="addJabatanForm" action="aksi_jabatan.php?act=tambah" method="POST">
            <div class="mb-4">
                <label for="namaJabatan" class="block text-sm font-medium text-gray-700">Nama Jabatan</label>
                <input type="text" id="namaJabatan" name="jabatan" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="gapok" class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                <input type="number" id="gapok" name="gapok" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="tunjangan" class="block text-sm font-medium text-gray-700">Tunjangan Jabatan</label>
                <input type="number" id="tunjangan" name="tunjangan_jabatan" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis Jabatan</label>
                <select id="jenis" name="jenis" required class="border rounded-lg p-2 w-full">
                    <option value="karyawan">Karyawan</option>
                    <option value="tukang">Tukang</option>
                </select>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeAddJabatanModal()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Tambah</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddJabatanModal() {
        const filter = document.getElementById("filterJabatan").value;
        if (filter === 'karyawan' || filter === 'tukang') {
            document.getElementById("jenis").value = filter;
        } else {
            document.getElementById("jenis").value = "karyawan";
        }

        document.getElementById("addJabatanModal").classList.remove("hidden");
    }

    function closeAddJabatanModal() {
        document.getElementById("addJabatanModal").classList.add("hidden");
    }
</script>

<!-- Edit Data Jabatan Modal -->
<div id="editJabatanModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Edit Data Jabatan</h2>
        <form id="editJabatanForm" action="aksi_jabatan.php?act=edit" method="POST">
            <input type="hidden" name="id" id="editId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama Jabatan</label>
                <input type="text" id="editJabatan" name="jabatan" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                <input type="number" id="editGapok" name="gapok" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tunjangan Jabatan</label>
                <input type="number" id="editTunjangan" name="tunjangan_jabatan" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Jenis Jabatan</label>
                <select id="editJenis" name="jenis" required class="border rounded-lg p-2 w-full">
                    <option value="karyawan">Karyawan</option>
                    <option value="tukang">Tukang</option>
                </select>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeEditJabatanModal()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, jabatan, gapok, tunjangan, jenis) {
        document.getElementById("editId").value = id;
        document.getElementById("editJabatan").value = jabatan;
        document.getElementById("editGapok").value = gapok;
        document.getElementById("editTunjangan").value = tunjangan;
        document.getElementById("editJenis").value = jenis;

        document.getElementById("editJabatanModal").classList.remove("hidden");
    }

    function closeEditJabatanModal() {
        document.getElementById("editJabatanModal").classList.add("hidden");
    }
</script>