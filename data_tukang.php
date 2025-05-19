<?php
include("koneksi.php");
include("sidebar.php");
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
        <!-- Modal untuk Pesan Sukses -->
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

        <!-- Modal untuk Pesan Sukses Tambah Tukang -->
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

        <!-- Header Section -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang,
                        <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>
        <!-- END Header Section -->

        <!-- Title & Tanggal Section -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Data Tukang</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <!-- END Title & Tanggal Section -->

        <?php
        $view = isset($_GET["view"]) ? $_GET["view"] : null;
        switch ($view) {
            default:
                ?>
                <div class="flex justify-center">
                    <!-- Tabel Section -->
                    <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                                <thead class="bg-blue-600 text-white text-sm uppercase font-semibold">
                                    <tr>
                                        <th class="py-4 px-6 text-center">No</th>
                                        <th class="py-4 px-6 text-left">NIK</th>
                                        <th class="py-4 px-6 text-left">Nama Tukang</th>
                                        <th class="py-4 px-6 text-left">Jenis Kelamin</th>
                                        <th class="py-4 px-6 text-left">Jabatan</th>
                                        <th class="py-4 px-6 text-left">Tanggal Masuk</th>
                                        <th class="py-4 px-6 text-left">Status</th>
                                        <th class="py-4 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    <?php
                                    $no = 1;
                                    $sql = mysqli_query($konek, "SELECT * FROM tukang_nws ORDER BY tgl_masuk ASC");
                                    while ($d = mysqli_fetch_array($sql)) {
                                        $namaTukang = ucwords(strtolower($d['nama_tukang']));
                                        $jabatan = ucwords(strtolower($d['jabatan']));
                                        echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
            <td class='py-4 px-6 text-center font-bold'>$no</td>
            <td class='py-4 px-6 '>{$d['nik']}</td>
            <td class='py-4 px-6 '>$namaTukang</td>
            <td class='py-4 px-6 '>{$d['jenis_kelamin']}</td>
            <td class='py-4 px-6 '>$jabatan</td>
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
                    <!-- END Tabel Section -->
                </div>
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

                        <form id="addTukangForm" action="aksi_tukang.php?act=tambah" method="POST">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">NIK</label>
                                <input type="text" name="nik" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Tukang</label>
                                <input type="text" name="nama_tukang" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Jenis Kelamin</label>
                                <select name="jenis_kelamin" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Jabatan</label>
                                <input type="text" name="jabatan" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Masuk</label>
                                <input type="date" name="tgl_masuk" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Status</label>
                                <select name="status" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Status</option>
                                    <option value="Tetap">Tetap</option>
                                    <option value="Tidak Tetap">Tidak Tetap</option>
                                </select>
                            </div>

                            <div class="flex justify-between mt-6">
                                <a href="data_tukang.php"
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

                <!-- Modal untuk Error Message -->
                <div id="errorModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
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
                    document.addEventListener('DOMContentLoaded', function () {
                        const addTukangForm = document.getElementById('addTukangForm');
                        const errorModal = document.getElementById('errorModal');
                        const errorMessage = document.getElementById('errorMessage');
                        const successAddModal = document.getElementById('successAddModal');
                        const successAddMessage = document.getElementById('successAddMessage');

                        addTukangForm.addEventListener('submit', function (event) {
                            event.preventDefault();

                            const formData = new FormData(addTukangForm);
                            const nik = formData.get('nik').trim();

                            fetch(`cek_nik.php?nik=${nik}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data && data.exists) {
                                        errorMessage.innerText = 'NIK sudah terdaftar.';
                                        errorModal.classList.remove('hidden');
                                        return;
                                    }

                                    fetch('aksi_tukang.php?act=tambah', {
                                        method: 'POST',
                                        body: formData
                                    })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error(`HTTP error! status: ${response.status}`);
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            if (data && data.success) {
                                                successAddMessage.innerText = data.message;
                                                successAddModal.classList.remove('hidden');
                                                setTimeout(() => {
                                                    window.location.href = 'data_tukang.php';
                                                }, 2000);
                                            } else if (data && data.message) {
                                                errorMessage.innerText = data.message;
                                                errorModal.classList.remove('hidden');
                                            } else {
                                                errorMessage.innerText = 'Terjadi kesalahan yang tidak diketahui.';
                                                errorModal.classList.remove('hidden');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error saat mengirim data:', error);
                                            errorMessage.innerText = 'Gagal mengirim data ke server.';
                                            errorModal.classList.remove('hidden');
                                        });
                                })
                                .catch(error => {
                                    console.error('Error saat memeriksa NIK:', error);
                                    errorMessage.innerText = 'Gagal menghubungi server untuk memeriksa NIK.';
                                    errorModal.classList.remove('hidden');
                                });
                        });

                        function closeErrorModal() {
                            errorModal.classList.add('hidden');
                        }

                        function closeSuccessAddModal() {
                            successAddModal.classList.add('hidden');
                            window.location.href = 'data_tukang.php';
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
                $query = mysqli_query($konek, "SELECT * FROM tukang_nws WHERE id='$id'");
                $data = mysqli_fetch_array($query);

                if (!$data) {
                    echo "<script>alert('Tukang tidak ditemukan!'); window.location='data_tukang.php';</script>";
                    exit;
                }
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Edit Tukang</h2>

                        <form id="editTukangForm" action="aksi_tukang.php?act=update" method="POST">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">

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

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Jabatan</label>
                                <input type="text" name="jabatan" value="<?= $data['jabatan'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                    <option value="Tetap" <?= $data['status'] == 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Tidak Tetap" <?= $data['status'] == 'Tidak Tetap' ? 'selected' : '' ?>>Tidak
                                        Tetap</option>
                                </select>
                            </div>

                            <div class="flex justify-between mt-6">
                                <a href="data_tukang.php"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                                    Batal
                                </a>
                                <button type="button" onclick="validasiEditTukang(event)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    function validasiEditTukang(event) {
                        event.preventDefault();

                        let form = document.getElementById("editTukangForm");
                        let formData = new FormData(form);

                        let nik = formData.get("nik").trim();
                        let nama = formData.get("nama_tukang").trim();
                        let jk = formData.get("jenis_kelamin").trim();
                        let jabatan = formData.get("jabatan").trim();
                        let tglMasuk = formData.get("tgl_masuk").trim();
                        let status = formData.get("status").trim();

                        if (!nik || !nama || !jk || !jabatan || !tglMasuk || !status) {
                            alert("Semua field wajib diisi.");
                            return;
                        }

                        if (nik.length !== 16 || isNaN(nik)) {
                            alert("NIK harus terdiri dari 16 digit angka.");
                            return;
                        }

                        fetch("aksi_tukang.php?act=update", {
                            method: "POST",
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById("successMessage").innerText = data.message;
                                    document.getElementById("successModal").classList.remove("hidden");
                                    setTimeout(() => {
                                        window.location.href = "data_tukang.php";
                                    }, 2000);
                                } else {
                                    alert(data.message);
                                }
                            });
                    }
                </script>

                <?php
                break;
        }
        ?>

        <!-- Modal konfirmasi hapus -->
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
                            document.getElementById('successMessage').innerText = data.message;
                            document.getElementById('successModal').classList.remove('hidden');
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
                window.location.href = "data_tukang.php";
            }

            function closeSuccessAddModal() {
                document.getElementById('successAddModal').classList.add('hidden');
                window.location.href = "data_tukang.php";
            }
        </script>

        <?php include 'footer.php'; ?>
    </div>

</body>

</html>