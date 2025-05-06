<?php
include("koneksi.php");
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100 text-sm">
    <div class="lg:ml-[300px] p-6">
        <!-- Header -->
        <div class="bg-white p-4 rounded-xl shadow mb-6 flex flex-col lg:flex-row justify-between items-center">
            <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
            <div class="flex items-center gap-2 mt-4 lg:mt-0">
                <span>Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong></span>
                <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
            </div>
        </div>

        <!-- Title & Date -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Data Absensi Tukang</h2>
            <span class="text-gray-500"><?php echo date('d F Y'); ?></span>
        </div>

        <!-- Main Content -->
        <section class="bg-white p-6 rounded-xl shadow">
            <!-- Filter Section -->
            <div class="bg-blue-600 text-white text-sm font-semibold rounded px-3 py-2 mb-4">
                Filter Data Kehadiran Tukang
            </div>
            <form class="flex flex-wrap items-center gap-4 mb-6">
                <label class="flex items-center gap-2">
                    Bulan:
                    <select class="border border-gray-300 rounded px-2 py-1">
                        <option>--Pilih Bulan--</option>
                    </select>
                </label>
                <label class="flex items-center gap-2">
                    Tahun:
                    <select class="border border-gray-300 rounded px-2 py-1">
                        <option>--Pilih Tahun--</option>
                    </select>
                </label>
                <div class="ml-auto flex gap-2">
                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-eye"></i> Tampilkan Data
                    </button>
                    <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded flex items-center gap-1">
                        <i class="fas fa-plus"></i> Input Kehadiran
                    </button>
                </div>
            </form>

            <!-- Info Text -->
            <div class="bg-blue-100 text-blue-800 rounded px-4 py-2 mb-4 text-sm">
                Menampilkan Data Kehadiran Tukang Bulan: <strong>09</strong>, Tahun: <strong>2020</strong>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-gray-600 border border-gray-300">
                    <thead class="bg-gray-100 text-gray-500">
                        <tr>
                            <?php
                            $headers = [
                                "NIK", "Nama Karyawan", "Jenis Kelamin",
                                "Jabatan", "Hadir", "Action"
                            ];
                            foreach ($headers as $head) {
                                echo "<th class='border px-3 py-2 text-center font-semibold'>$head</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Kosong -->
                        <tr>
                            <td colspan="9" class="text-center text-gray-400 py-4">Data belum tersedia.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
