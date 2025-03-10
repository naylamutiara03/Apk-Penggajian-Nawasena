<?php include("header.php"); ?>

    <!-- Hero Section -->
    <header class="bg-blue-500 text-white text-center py-16 mt-16">
        <h1 class="text-3xl md:text-4xl font-bold">Aplikasi Penggajian Nawasena</h1>
        <p class="text-lg mt-2">Kelola data gaji karyawan dengan mudah, cepat, dan aman.</p>
        <a href="dashboard.php"
            class="mt-4 inline-block bg-white hover:bg-yellow-300 text-blue-800 font-semibold py-2 px-6 rounded-lg shadow-md transition">
            Mulai Sekarang
        </a>
    </header>

    <!-- Fitur Unggulan -->
    <section id="features" class="container mx-auto px-4 py-10">
        <h2 class="text-center text-2xl font-bold text-gray-700">Fitur - fitur</h2>
        <div class="grid md:grid-cols-3 gap-6 mt-6">

            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <ion-icon name="cash-outline" class="text-blue-500 text-5xl"></ion-icon>
                <h3 class="text-lg font-semibold mt-2">Manajemen Gaji</h3>
                <p class="text-gray-600">Kelola gaji karyawan dengan perhitungan yang akurat dan otomatis.</p>
            </div>

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

    <?php include("footer.php"); ?>

</body>

</html>