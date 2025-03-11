<?php
include("koneksi.php"); ?>

<head>
    <title>Data Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <?php
    $view = isset($_GET["view"]) ? $_GET["view"] : null;
    switch ($view) {

        // ==============================
        // CASE: TAMPILAN UTAMA ADMIN
        // ==============================
        default:
            ?>
            <!-- Header Data Admin -->
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-10 rounded-lg shadow-lg flex items-center px-6">
                <a href="index.php" class="text-white text-2xl flex items-center gap-2 hover:text-gray-300 transition ml-16">
                    <ion-icon name="arrow-back-outline"></ion-icon> <!-- Ikon Back -->
                </a>
                <h1 class="text-3xl md:text-4xl font-bold text-center flex-1">Data Admin</h1>
                <span class="w-10"></span> <!-- Spacer untuk keseimbangan -->
            </div>

            <div class="container mx-auto p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300 rounded-xl shadow-lg overflow-hidden">
                        <thead class="bg-blue-600 text-white text-sm uppercase font-semibold">
                            <tr>
                                <th class="py-4 px-6 text-center">No</th>
                                <th class="py-4 px-6 text-left">Username</th>
                                <th class="py-4 px-6 text-left">Nama Lengkap</th>
                                <th class="py-4 px-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm font-medium">
                            <?php
                            $no = 1;
                            $sql = mysqli_query($konek, "SELECT * FROM admin ORDER BY username ASC");

                            while ($d = mysqli_fetch_array($sql)) {
                                echo "<tr class='border-b border-gray-200 odd:bg-gray-100 hover:bg-blue-100 transition-all duration-200'>
                                    <td class='py-4 px-6 text-center'>$no</td>
                                    <td class='py-4 px-6'>{$d['username']}</td>
                                    <td class='py-4 px-6'>{$d['namalengkap']}</td>
                                    <td class='py-4 px-6 text-center flex justify-center gap-2'>
                                        <a href='data_admin.php?view=edit&id={$d['idadmin']}' 
                                           class='flex items-center gap-2 bg-yellow-500 text-white px-3 py-1 rounded-md text-sm font-semibold hover:bg-yellow-600 transition'>
                                           <ion-icon name='pencil-outline'></ion-icon> Edit
                                        </a>
                                        <a href='#' 
                                           onclick=\"openDeleteModal('aksi_admin.php?act=delete&id={$d['idadmin']}')\" 
                                           class='flex items-center gap-2 bg-red-500 text-white px-3 py-1 rounded-md text-sm font-semibold hover:bg-red-600 transition'>
                                           <ion-icon name='trash-outline'></ion-icon> Hapus
                                        </a>
                                    </td>
                                  </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Tambah Admin -->
                <div class="mt-6 flex justify-center">
                    <a href="data_admin.php?view=tambah"
                        class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-lg font-semibold shadow-md hover:bg-green-700 transition">
                        <ion-icon name="person-add-outline"></ion-icon> Tambah Admin
                    </a>
                </div>
            </div>
            <?php
            break;

        // ==============================
        // CASE: FORM TAMBAH ADMIN
        // ==============================
        case 'tambah':
            ?>
            <div class="flex items-center justify-center h-screen bg-gray-100">
                <div class="max-w-lg w-full p-6 bg-white shadow-lg rounded-lg mb-10">
                    <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Tambah Admin</h2>

                    <form action="aksi_admin.php?act=tambah" method="POST">
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

                        <div class="mb-4 relative">
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
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                                Simpan
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
            <div class="flex items-center justify-center h-screen bg-gray-100">
                <div class="max-w-lg w-full p-6 bg-white shadow-lg rounded-lg mb-10">
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

                        <div class="mb-4 relative">
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

    <!-- Modal for Delete Confirmation -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
            <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus admin ini?</p>
            <div class="flex justify-end mt-4">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2">Batal</button>
                <button id="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded-lg">Hapus</button>
            </div>
        </div>
    </div>

    <script>
        let deleteUrl = '';

        function openDeleteModal(url) {
            deleteUrl = url; // Store the URL for deletion
            document.getElementById('deleteModal').classList.remove('hidden'); // Show the modal
        }

        document.getElementById('cancelDelete').onclick = function () {
            document.getElementById('deleteModal').classList.add('hidden'); // Hide the modal
        };

        document.getElementById('confirmDelete').onclick = function () {
            window.location.href = deleteUrl; // Redirect to the delete URL
        };
    </script>
</body>

</html>