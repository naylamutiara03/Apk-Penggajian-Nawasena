<?php
include("koneksi.php");
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <!-- Modal untuk Pesan Sukses -->
        <div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800" id="successTitle">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successMessage">Pesan sukses akan ditampilkan di sini.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>
        <!-- END Modal untuk Pesan Sukses -->

        <!-- Header Section -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang, <?php echo htmlspecialchars($username); ?></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>
        <!-- END Header Section -->

        <!-- Title & Tanggal Section -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">
                Data Admin
            </h1>
            <span class="text-gray-500 mr-2">
                <?php echo date('d F Y'); ?>
            </span>
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
                                        <th class="py-4 px-6 text-left">Username</th>
                                        <th class="py-4 px-6 text-left">Nama Lengkap</th>
                                        <th class="py-4 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm">
                                    <?php
                                    $no = 1;
                                    $sql = mysqli_query($konek, "SELECT * FROM admin ORDER BY idadmin ASC");
                                    while ($d = mysqli_fetch_array($sql)) {
                                        echo "<tr class='border-b border-gray-200 hover:bg-blue-100'>
            <td class='py-4 px-6 text-center font-bold'>$no</td>
            <td class='py-4 px-6 font-bold'>{$d['username']}</td>
            <td class='py-4 px-6 font-bold'>{$d['namalengkap']}</td>
            <td class='py-4 px-6 text-center flex flex-col lg:flex-row gap-2 justify-center'>
                <a href='data_admin.php?view=edit&id={$d['idadmin']}' class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                    <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                </a>
                <a href='#' onclick=\"openDeleteModal('aksi_admin.php?act=delete&id={$d['idadmin']}')\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
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
                            <a href="data_admin.php?view=tambah"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                                <ion-icon name="person-add-outline" class="mr-1"></ion-icon> Tambah Admin
                            </a>
                        </div>
                    </div>
                    <!-- END Tabel Section -->
                </div>
                <?php
                break;

            // ==============================
            // CASE: FORM TAMBAH ADMIN
            // ==============================
            case 'tambah':
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Tambah Admin</h2>

                        <form id="addAdminForm" action="aksi_admin.php?act=tambah" method="POST">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                                <input type="text" name="username" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                                <input type="text" name="namalengkap" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="togglePassword()" class="absolute top-9 right-3 text-gray-500">
                                </button>
                            </div>

                            <div class="flex justify-between mt-6">
                                <a href="data_admin.php"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                                    Batal
                                </a>
                                <button type="button" onclick="tambahAdmin(event)"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal untuk Error Message usn, nama, pass kosong -->
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
                    function tambahAdmin(event) {
                        event.preventDefault(); // Mencegah refresh halaman

                        let formData = new FormData(document.getElementById("addAdminForm"));
                        let username = formData.get("username");
                        let namalengkap = formData.get("namalengkap");
                        let password = formData.get("password");

                        // Validasi input
                        if (!username || !namalengkap || !password) {
                            let message = "Semua field harus diisi.";
                            if (!username) {
                                message = "Username tidak boleh kosong.";
                            } else if (!namalengkap) {
                                message = "Nama Lengkap tidak boleh kosong.";
                            } else if (password.length < 8) {
                                message = "Password harus terdiri dari minimal 8 karakter.";
                            }
                            document.getElementById("errorMessage").innerText = message;
                            document.getElementById("errorModal").classList.remove("hidden"); // Tampilkan modal
                            return; // Hentikan eksekusi jika validasi gagal
                        }

                        fetch("aksi_admin.php?act=tambah", {
                            method: "POST",
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById("successTitle").innerText = "Sukses!";
                                    document.getElementById("successMessage").innerText = data.message;
                                    document.getElementById("successModal").classList.remove("hidden"); // Tampilkan modal sukses
                                    document.getElementById("addAdminForm").reset();
                                    setTimeout(() => {
                                        window.location.href = "data_admin.php"; // Redirect setelah beberapa detik
                                    }, 2000); // Redirect setelah 2 detik
                                } else {
                                    // Ganti alert dengan modal kesalahan
                                    document.getElementById("errorMessage").innerText = data.message;
                                    document.getElementById("errorModal").classList.remove("hidden"); // Tampilkan modal error
                                }
                            });
                    }
                    
                    function closeErrorModal() {
                        document.getElementById("errorModal").classList.add("hidden"); // Hide modal
                    }

                    function closeSuccessModal() {
                        document.getElementById("successModal").classList.add("hidden"); // Hide modal
                    }

                    function togglePassword() {
                        let passwordField = document.getElementById("password");
                        let eyeIcon = document.getElementById("eyeIcon");

                        if (passwordField.type === "password") {
                            passwordField.type = "text";
                            eyeIcon.setAttribute("name", "eye-outline");
                        } else {
                            passwordField.type = "password";
                            eyeIcon.setAttribute("name", "eye-off-outline");
                        }
                    }
                </script>
                <?php
                break;

            // ==============================
            // CASE: FORM EDIT ADMIN
            // ==============================
            case 'edit':
                $id = $_GET['id'];
                $query = mysqli_query($konek, "SELECT * FROM admin WHERE idadmin='$id'");
                $data = mysqli_fetch_array($query);

                if (!$data) {
                    echo "<script>alert('Admin tidak ditemukan!'); window.location='data_admin.php';</script>";
                    exit;
                }
                ?>
                <div class="flex justify-center bg-gray-100">
                    <div class="max-w-7xl w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Edit Admin</h2>

                        <form action="aksi_admin.php?act=update" method="POST">
                            <input type="hidden" name="idadmin" value="<?= $data['idadmin'] ?>">

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Username</label>
                                <input type="text" name="username" value="<?= $data['username'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                                <input type="text" name="namalengkap" value="<?= $data['namalengkap'] ?>" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-semibold mb-2">Password (Kosongkan jika tidak
                                    diubah)</label>
                                <input type="password" id="password" name="password"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="togglePassword()" class="absolute top-9 right-3 text-gray-500">
                                </button>
                            </div>
                            <div class="flex justify-between mt-6">
                                <a href="data_admin.php"
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

                <script>
                    function togglePassword() {
                        let passwordField = document.getElementById("password");
                        let eyeIcon = document.getElementById("eyeIcon");

                        if (passwordField.type === "password") {
                            passwordField.type = "text";
                            eyeIcon.setAttribute("name", "eye-outline");
                        } else {
                            passwordField.type = "password";
                            eyeIcon.setAttribute("name", "eye-off-outline");
                        }
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
                <p>Apakah Anda yakin ingin menghapus admin ini?</p>
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
                            document.getElementById("successTitle").innerText = "Sukses!";
                            document.getElementById("successMessage").innerText = data.message;
                            document.getElementById("successModal").classList.remove("hidden"); // Show success modal

                            // Redirect after a delay
                            setTimeout(() => {
                                window.location.href = "data_admin.php"; // Redirect after 2 seconds
                            }, 2000); // Redirect after 2 seconds
                        } else {
                            alert(data.message); // Show error message
                        }
                    });
            };

            document.getElementById('cancelDelete').onclick = function () {
                document.getElementById('deleteModal').classList.add('hidden'); // Hide delete modal
            };
        </script>
        <?php include 'footer.php'; ?>
    </div>

</body>

</html>