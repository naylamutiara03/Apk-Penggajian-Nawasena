<head>
    <title>Halaman Utama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>

    <!-- Hero Section -->
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white text-center py-16 relative">
        <!-- Ikon Logout di layar besar -->
        <a href="#" onclick="openLogoutModal()"
            class="absolute top-1/2 right-6 md:right-12 lg:right-16 -translate-y-1/2 text-white text-2xl hover:text-gray-300 transition hidden md:block">
            <ion-icon name="log-out-outline"></ion-icon>
        </a>

        <!-- Judul -->
        <h1 class="text-3xl md:text-4xl font-bold">Aplikasi Penggajian Nawasena</h1>
        <p class="text-lg mt-2">Kelola data gaji karyawan dengan mudah, cepat, dan aman.</p>
        <a href="dashboard.php"
            class="mt-4 inline-block bg-white hover:bg-blue-400 text-blue-800 font-semibold py-2 px-6 rounded-lg shadow-md transition">
            Mulai Sekarang
        </a>

        <!-- Ikon Logout di layar kecil -->
        <a href="#" onclick="openLogoutModal()"
            class="block md:hidden mt-6 text-white text-2xl hover:text-gray-300 transition">
            <ion-icon name="log-out-outline"></ion-icon>
        </a>
    </header>

    <!-- Fitur Unggulan -->
    <section id="features" class="container mx-auto px-4 py-10">
        <h2 class="text-center text-2xl font-bold text-gray-700">Fitur - fitur</h2>
        <div class="grid md:grid-cols-3 gap-6 mt-6">

            <a href="data_admin.php">
                <div class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition">
                    <ion-icon name="analytics-outline" class="text-blue-500 text-5xl"></ion-icon>
                    <h3 class="text-lg font-semibold mt-2">Data Admin</h3>
                    <p class="text-gray-600">Kelola gaji karyawan dengan perhitungan yang akurat dan otomatis.</p>
                </div>
            </a>

            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <ion-icon name="document-text-outline" class="text-blue-500 text-5xl"></ion-icon>
                <h3 class="text-lg font-semibold mt-2">Laporan Keuangan</h3>
                <p class="text-gray-600">Buat laporan penggajian dan catatan keuangan dengan mudah.</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <ion-icon name="people-outline" class="text-blue-500 text-5xl"></ion-icon>
                <h3 class="text-lg font-semibold mt-2">Data Karyawan</h3>
                <p class="text-gray-600">Simpan dan kelola data karyawan dengan sistem yang aman.</p>
            </div>

        </div>
    </section>

    <!-- Modal Logout -->
    <div id="logoutModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
            <h2 class="text-xl font-bold text-gray-800">Konfirmasi Logout</h2>
            <p class="text-gray-600 mt-2">Apakah Anda yakin ingin keluar?</p>
            <div class="mt-4 flex justify-center space-x-4">
                <button onclick="closeLogoutModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Batal</button>
                <a href="logout.php"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Keluar</a>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            document.getElementById("logoutModal").classList.remove("hidden");
        }

        function closeLogoutModal() {
            document.getElementById("logoutModal").classList.add("hidden");
        }
    </script>

</body>

</html>