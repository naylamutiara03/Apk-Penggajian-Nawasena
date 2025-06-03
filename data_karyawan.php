<?php
include("koneksi.php");
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Karyawan</title>
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
                <p class="text-gray-600 mt-2" id="successMessage">Data karyawan berhasil dihapus.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <!-- Modal untuk Pesan Sukses Tambah Karyawan -->
        <div id="successAddModal"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successAddMessage">Data karyawan berhasil ditambahkan.</p>
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
            <h1 class="text-2xl font-bold ml-2">Data Karyawan</h1>
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
                                        <th class="py-4 px-6 text-left">Nama Karyawan</th>
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
                                    $sql = mysqli_query($konek, "SELECT k.id, k.nik, k.nama_karyawan, k.jenis_kelamin, j.jabatan, k.tgl_masuk, k.status
    FROM karyawan k
    JOIN jabatan j ON k.id_jabatan = j.id
    WHERE j.jenis = 'karyawan'
    ORDER BY k.nama_karyawan ASC;");
                                    while ($d = mysqli_fetch_array($sql)) {
                                        // Menggunakan ucwords untuk menampilkan nama karyawan dengan huruf kapital di awal setiap kata
                                        $namaKaryawan = ucwords(strtolower($d['nama_karyawan'])); // Mengubah nama karyawan menjadi format yang diinginkan
                                        $jabatan = ucwords(strtolower($d['jabatan'])); // Mengubah jabatan menjadi format yang diinginkan
                                        echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
                <td class='py-4 px-6 text-center font-bold'>$no</td>
                <td class='py-4 px-6 '>{$d['nik']}</td>
                <td class='py-4 px-6 '>$namaKaryawan</td>
                <td class='py-4 px-6 '>{$d['jenis_kelamin']}</td>
                <td class='py-4 px-6 '>$jabatan</td>
                <td class='py-4 px-6 '>" . date('d F Y', strtotime($d['tgl_masuk'])) . "</td>
                <td class='py-4 px-6 '>{$d['status']}</td>
                <td class='py-4 px-6 text-center flex flex-col lg:flex-row gap-2 justify-center'>
                    <a href='data_karyawan.php?view=edit&id={$d['id']}' class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                        <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                    </a>
                    <a href='#' onclick=\"openDeleteModal('aksi_karyawan.php?act=delete&id={$d['id']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
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
                            <a href="data_karyawan.php?view=tambah"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                                <ion-icon name="person-add-outline" class="mr-1"></ion-icon> Tambah Karyawan
                            </a>
                        </div>
                    </div>
                    <!-- END Tabel Section -->
                </div>
                <?php
                break;

            // ==============================
            // CASE: FORM TAMBAH KARYAWAN
            // ==============================
            case 'tambah':
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Tambah Karyawan</h2>

                        <form id="addKaryawanForm" action="aksi_karyawan.php?act=tambah" method="POST">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">NIK</label>
                                <input type="text" name="nik" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Karyawan</label>
                                <input type="text" name="nama_karyawan" required
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
                                <select name="id_jabatan" required
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih Jabatan</option>
                                    <?php
                                    $jabatanQuery = mysqli_query($konek, "SELECT id, jabatan FROM jabatan WHERE jenis = 'karyawan' ORDER BY jabatan ASC");
                                    while ($jabatan = mysqli_fetch_assoc($jabatanQuery)) {
                                        $selected = ($jabatan['id'] == $data['id_jabatan']) ? 'selected' : '';
                                        echo "<option value='{$jabatan['id']}' $selected>{$jabatan['jabatan']}</option>";
                                    }
                                    ?>
                                </select>
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
                                <a href="data_karyawan.php"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                                    Batal
                                </a>
                                <button type="button" onclick="tambahKaryawan(event)"
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
                    function tambahKaryawan(event) {
                        event.preventDefault(); // Mencegah form submit default

                        let form = document.getElementById("addKaryawanForm");
                        let formData = new FormData(form);

                        // Ambil nilai NIK
                        let nik = formData.get("nik").trim();

                        // Cek apakah NIK sudah terdaftar
                        fetch("cek_nik.php?nik=" + nik)
                            .then(response => response.json())
                            .then(data => {
                                if (data.exists) {
                                    document.getElementById("errorMessage").innerText = "NIK sudah terdaftar.";
                                    document.getElementById("errorModal").classList.remove("hidden");
                                    return;
                                }

                                // Jika NIK tidak terdaftar, lanjutkan dengan validasi dan pengiriman data
                                let nama = formData.get("nama_karyawan").trim();
                                let jk = formData.get("jenis_kelamin").trim();
                                let jabatan = formData.get("id_jabatan").trim();
                                let tglMasuk = formData.get("tgl_masuk").trim();
                                let status = formData.get("status").trim();

                                // Validasi input kosong
                                if (!nik || !nama || !jk || !jabatan || !tglMasuk || !status) {
                                    document.getElementById("errorMessage").innerText = "Semua field wajib diisi.";
                                    document.getElementById("errorModal").classList.remove("hidden");
                                    return;
                                }

                                // Validasi panjang NIK
                                if (nik.length !== 16 || isNaN(nik)) {
                                    document.getElementById("errorMessage").innerText = "NIK harus terdiri dari 16 digit angka.";
                                    document.getElementById("errorModal").classList.remove("hidden");
                                    return;
                                }

                                // Submit form via fetch
                                fetch("aksi_karyawan.php?act=tambah", {
                                    method: "POST",
                                    body: formData
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // Set success message and show success modal
                                            document.getElementById("successAddMessage").innerText = data.message; // Set success message
                                            document.getElementById("successAddModal").classList.remove("hidden"); // Show success modal
                                            // Optionally, you can redirect after a delay
                                            setTimeout(() => {
                                                window.location.href = "data_karyawan.php"; // Redirect setelah sukses
                                            }, 2000); // Redirect after 2 seconds
                                        } else {
                                            document.getElementById("errorMessage").innerText = data.message;
                                            document.getElementById("errorModal").classList.remove("hidden");
                                        }
                                    });
                            });
                    }

                    function closeSuccessAddModal() {
                        document.getElementById('successAddModal').classList.add('hidden'); // Hide success modal
                        window.location.href = "data_karyawan.php"; // Redirect after closing
                    }

                    function closeErrorModal() {
                        document.getElementById("errorModal").classList.add("hidden");
                    }
                </script>

                <?php
                break;

            // ==============================
            // CASE: FORM EDIT KARYAWAN
            // ==============================
            case 'edit':
                $id = $_GET['id'];
                $query = mysqli_query($konek, "SELECT * FROM karyawan WHERE id='$id'");
                $data = mysqli_fetch_array($query);

                if (!$data) {
                    echo "<script>alert('Karyawan tidak ditemukan!'); window.location='data_karyawan.php';</script>";
                    exit;
                }
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Edit Karyawan</h2>

                        <form id="editKaryawanForm" action="aksi_karyawan.php?act=update" method="POST">
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">NIK</label>
                                <input type="text" name="nik" value="<?= $data['nik'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Karyawan</label>
                                <input type="text" name="nama_karyawan" value="<?= $data['nama_karyawan'] ?>" required
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
                                <select name="id_jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    <?php
                                    $jabatanQuery = mysqli_query($konek, "SELECT id, jabatan FROM jabatan WHERE jenis = 'karyawan' ORDER BY jabatan ASC");
                                    while ($jabatan = mysqli_fetch_assoc($jabatanQuery)) {
                                        $selected = ($jabatan['id'] == $data['id_jabatan']) ? 'selected' : '';
                                        echo "<option value='{$jabatan['id']}' $selected>{$jabatan['jabatan']}</option>";
                                    }
                                    ?>
                                </select>
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
                                <a href="data_karyawan.php"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                                    Batal
                                </a>
                                <button type="button" onclick="validasiEditKaryawan(event)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <script>
                    function validasiEditKaryawan(event) {
                        event.preventDefault();

                        let form = document.getElementById("editKaryawanForm");
                        let formData = new FormData(form);

                        let nik = formData.get("nik").trim();
                        let nama = formData.get("nama_karyawan").trim();
                        let jk = formData.get("jenis_kelamin").trim();
                        let jabatan = formData.get("id_jabatan").trim();
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

                        fetch("aksi_karyawan.php?act=update", {
                            method: "POST",
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Set success message and show success modal
                                    document.getElementById("successMessage").innerText = data.message; // Set success message
                                    document.getElementById("successModal").classList.remove("hidden"); // Show success modal
                                    // Optionally, you can redirect after a delay
                                    setTimeout(() => {
                                        window.location.href = "data_karyawan.php"; // Redirect setelah sukses
                                    }, 2000); // Redirect after 2 seconds
                                } else {
                                    alert(data.message); // Show error message
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
                <p>Apakah Anda yakin ingin menghapus karyawan ini?</p>
                <div class="flex justify-end mt-4">
                    <button id="cancelDelete"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                    <button id="confirmDelete"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hapus</button>
                </div>
            </div>
        </div>

        <script>
            // Handle delete confirmation
            let deleteUrl = '';

            function openDeleteModal(url) {
                deleteUrl = url; // Store the URL for deletion
                document.getElementById('deleteModal').classList.remove('hidden'); // Show delete confirmation modal
            }

            document.getElementById('confirmDelete').onclick = function () {
                fetch(deleteUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Hide the delete modal
                            document.getElementById('deleteModal').classList.add('hidden');

                            // Show success modal for delete
                            document.getElementById('successMessage').innerText = data.message; // Set success message
                            document.getElementById('successModal').classList.remove('hidden'); // Show success modal
                        } else {
                            alert(data.message); // Show error message
                        }
                    });
            };

            document.getElementById('cancelDelete').onclick = function () {
                document.getElementById('deleteModal').classList.add('hidden'); // Hide delete modal
            };

            function closeSuccessModal() {
                document.getElementById('successModal').classList.add('hidden'); // Hide success modal
                window.location.href = "data_karyawan.php"; // Redirect after closing
            }

            // Function to close success add modal
            function closeSuccessAddModal() {
                document.getElementById('successAddModal').classList.add('hidden'); // Hide success modal
                window.location.href = "data_karyawan.php"; // Redirect after closing
            }
        </script>

        <?php include 'footer.php'; ?>
    </div>

</body>

</html>