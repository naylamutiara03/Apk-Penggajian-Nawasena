<?php
include("sidebar.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$koneksi = new mysqli("localhost", "root", "", "penggajian");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Laporan Gaji Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <!-- Header -->
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

        <!-- Title & Tanggal -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Laporan Gaji Karyawan</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>

        <!-- Form Filter -->
        <div class="w-full lg:max-w-[800px] mx-auto bg-white/80 px-8 py-10 rounded-2xl shadow-xl mt-16">
            <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Filter Laporan Gaji Karyawan</h2>

            <form action="hasil_laporan_gaji.php" method="GET" class="space-y-6">
                <!-- Bulan -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bulan</label>
                    <select name="bulan" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                        <option value="">-- Pilih Bulan --</option>
                        <?php
                        $bulan_nama = [
                            "01" => "Januari",
                            "02" => "Februari",
                            "03" => "Maret",
                            "04" => "April",
                            "05" => "Mei",
                            "06" => "Juni",
                            "07" => "Juli",
                            "08" => "Agustus",
                            "09" => "September",
                            "10" => "Oktober",
                            "11" => "November",
                            "12" => "Desember"
                        ];
                        foreach ($bulan_nama as $kode => $nama) {
                            echo "<option value='$kode'>$nama</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Tahun</label>
                    <select name="tahun" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                        <option value="">-- Pilih Tahun --</option>
                        <?php
                        $tahunSekarang = date('Y');
                        for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Tombol -->
                <div class="flex justify-center mt-8">
                    <!-- Tampilkan -->
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition duration-200 flex-grow sm:flex-grow-0 sm:w-auto min-w-[150px]">
                        Tampilkan Laporan Gaji
                    </button>
                </div>
            </form>
        </div>
        <?php include("footer.php"); ?>
    </div>
</body>

</html>