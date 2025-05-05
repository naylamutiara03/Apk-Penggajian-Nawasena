<?php
// Sertakan koneksi ke database lebih dulu
include("koneksi.php");

include("sidebar.php");

// Pastikan admin sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_baru_konfirmasi = $_POST['password_baru_konfirmasi'];

    // Validasi input
    if (empty($password_lama) || empty($password_baru) || empty($password_baru_konfirmasi)) {
        $error_message = "Semua field harus diisi.";
    } elseif ($password_baru !== $password_baru_konfirmasi) {
        $error_message = "Password baru dan konfirmasi password tidak cocok.";
    } elseif (strlen($password_baru) < 8) {
        $error_message = "Password baru harus terdiri dari minimal 8 karakter.";
    } else {
        // Cek password lama
        $query = mysqli_query($konek, "SELECT password FROM admin WHERE username='$username'");
        $data = mysqli_fetch_array($query);

        if ($data && password_verify($password_lama, $data['password'])) {
            // Update password baru
            $password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            mysqli_query($konek, "UPDATE admin SET password='$password_baru_hash' WHERE username='$username'");
            $success_message = "Password berhasil diubah.";
        } else {
            $error_message = "Password lama salah.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <title>Ubah Password</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* style untuk nonaktif icon mata bawaan browser (seperti Chrome pada autofill) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }

        input::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            display: none !important;
        }

        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important;
        }
    </style>

</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
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
            <h1 class="text-2xl font-bold ml-2">Ubah Password</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <!-- END Title & Tanggal Section -->

        <div class="w-full lg:max-w-[700px] mx-auto bg-white/80 px-8 py-10 rounded-2xl shadow-xl mt-16">
            <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Ubah Password</h2>

            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <!-- Password Lama -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password Lama</label>
                    <input type="password" name="password_lama" id="password_lama"
                        class="input-overlay w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 text-base transition duration-200"
                        oninput="toggleEyeIcon('password_lama')" required>
                    <button type="button" class="absolute right-3 top-9 text-gray-500 hidden"
                        onclick="togglePassword('password_lama')" id="toggle_password_lama">
                        <svg id="icon_password_lama" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <!-- Password Baru -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru</label>
                    <input type="password" name="password_baru" id="password_baru"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 text-base transition duration-200"
                        oninput="toggleEyeIcon('password_baru')" required>
                    <button type="button" class="absolute right-3 top-9 text-gray-500 hidden"
                        onclick="togglePassword('password_baru')" id="toggle_password_baru">
                        <svg id="icon_password_baru" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="mb-4 relative">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_konfirmasi" id="password_baru_konfirmasi"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 text-base transition duration-200"
                        oninput="toggleEyeIcon('password_baru_konfirmasi')" required>
                    <button type="button" class="absolute right-3 top-9 text-gray-500 hidden"
                        onclick="togglePassword('password_baru_konfirmasi')" id="toggle_password_baru_konfirmasi">
                        <svg id="icon_password_baru_konfirmasi" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <div class="flex justify-between mt-8">
                    <a href="data_admin.php"
                        class="px-6 py-2 bg-gray-500 text-white rounded-xl shadow hover:bg-gray-600 transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition duration-200">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
        <?php include 'footer.php'; ?>
    </div>
    <script>
        function toggleEyeIcon(id) {
            const input = document.getElementById(id);
            const toggleBtn = document.getElementById('toggle_' + id);
            if (input.value.length > 0) {
                toggleBtn.classList.remove('hidden');
            } else {
                toggleBtn.classList.add('hidden');
            }
        }

        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = document.getElementById('icon_' + id);

            const eyeOpen = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    `;

            const eyeOff = `
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.965 9.965 0 012.442-4.042M6.423 6.423A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.961 9.961 0 01-4.284 5.136M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m6 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3m12 12L3 3" />
    `;

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = eyeOff;
            } else {
                input.type = 'password';
                icon.innerHTML = eyeOpen;
            }
        }
    </script>

</body>

</html>