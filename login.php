<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOGIN APLIKASI PENGGAJIAN PT. NAWASENA SINERGI GEMILANG</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            background: url('assets/img/background-buram.jpg') no-repeat center fixed;
            background-size: cover;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
        <div class="bg-gray-900 text-white text-center py-4 rounded-t-lg flex items-center justify-center gap-2">
            <ion-icon name="lock-closed-outline" class="text-2xl"></ion-icon>
            <h3 class="text-lg font-semibold">LOGIN APLIKASI PENGGAJIAN</h3>
        </div>

        <div class="p-6">
            <div class="flex justify-center mb-4">
                <img src="assets/img/logo-nawasena.png" alt="Logo Nawasena" class="h-20">
            </div>

            <?php
            session_start();
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                include "koneksi.php";

                $user = trim($_POST['username']);
                $pass = trim($_POST['password']);


                if (empty($user) || empty($pass)) {
                    echo '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-4">
                <p class="font-bold">Warning!</p>
                <p>Form belum lengkap.</p>
              </div>';
                } else {
                    // Ambil data admin berdasarkan username
                    $sqlLogin = mysqli_query($konek, "SELECT * FROM admin WHERE username='$user'");
                    $d = mysqli_fetch_assoc($sqlLogin);

                    if ($d && password_verify($pass, $d['password'])) { // Cek password dengan password_verify
                        $_SESSION['login'] = TRUE;
                        $_SESSION['id'] = $d['idadmin'];
                        $_SESSION['username'] = $d['username'];
                        $_SESSION['namalengkap'] = $d['namalengkap'];
                        $_SESSION['role'] = $d['role']; 
            
                        header('Location: ./dashboard.php');
                        exit;
                    } else {
                        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4">
                    <p class="font-bold">ERROR!</p>
                    <p>Username atau password salah!</p>
                  </div>';
                    }
                }
            }
            ?>

            <form action="" method="post">
                <div class="mb-4">
                    <input type="text" name="username" placeholder="Username"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="mb-4">
                    <input type="password" name="password" placeholder="Password"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="mt-4">
                    <button type="submit"
                        class="w-full bg-gray-900 hover:bg-blue-600 text-white font-semibold py-2 rounded-md transition duration-300">Login</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>