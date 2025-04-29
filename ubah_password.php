<?php
// Sertakan koneksi ke database lebih dulu
include("koneksi.php");

// Pastikan session sudah aktif
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
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

            <form action="" method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password Lama</label>
                    <input type="password" name="password_lama" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru</label>
                    <input type="password" name="password_baru" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_konfirmasi" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-between mt-6">
                    <a href="data_admin.php"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>