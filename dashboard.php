<?php
session_start(); // Panggil di awal

include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];

include 'sidebar.php'; // Panggil setelah login dicek

// 1. Statistik kartu
$res = mysqli_query($konek, "SELECT COUNT(*) AS total FROM karyawan");
$totalKaryawan = (int) (mysqli_fetch_assoc($res)['total'] ?? 0);

$res = mysqli_query($konek, "SELECT COUNT(*) AS total FROM tukang_nws");
$totalTukang = (int) (mysqli_fetch_assoc($res)['total'] ?? 0);

$res = mysqli_query($konek, "SELECT COUNT(*) AS total FROM absensi_tukang");
$totalAbsensi = (int) (mysqli_fetch_assoc($res)['total'] ?? 0);

$res = mysqli_query($konek, "SELECT COUNT(*) AS total FROM admin");
$totalAdmin = (int) (mysqli_fetch_assoc($res)['total'] ?? 0);

// Grafik kehadiran tukang Jan–Jun
$currentYear = date('Y');
$attendanceByMonth = array_fill(1, 6, 0);

$res = mysqli_query($konek, "
    SELECT MONTH(tanggal_masuk) AS bln, COUNT(*) AS cnt
    FROM absensi_tukang
    WHERE YEAR(tanggal_masuk) = $currentYear
      AND MONTH(tanggal_masuk) BETWEEN 1 AND 6
    GROUP BY MONTH(tanggal_masuk)
");

while ($r = mysqli_fetch_assoc($res)) {
    $m = (int)$r['bln'];
    if ($m >= 1 && $m <= 6) {
        $attendanceByMonth[$m] = (int)$r['cnt'];
    }
}

$barLabels = json_encode(['Jan','Feb','Mar','Apr','Mei','Jun']);
$barData   = json_encode(array_values($attendanceByMonth));
$pieData   = json_encode([$totalKaryawan, $totalAdmin, $totalTukang]);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <!-- Header -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang, <strong><?= htmlspecialchars($username) ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>

        <!-- Title & Tanggal -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <span class="text-gray-500"><?= date('d F Y') ?></span>
        </div>

        <!-- Statistik Kartu -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-semibold text-gray-500">DATA KARYAWAN</h2>
                    <p class="text-2xl font-bold"><?= $totalKaryawan ?></p>
                </div>
                <i class="fas fa-users text-gray-300 text-3xl"></i>
            </div>
            <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-semibold text-gray-500">DATA TUKANG</h2>
                    <p class="text-2xl font-bold"><?= $totalTukang ?></p>
                </div>
                <i class="fas fa-user-cog text-gray-300 text-3xl"></i>
            </div>
            <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-semibold text-gray-500">DATA KEHADIRAN TUKANG</h2>
                    <p class="text-2xl font-bold"><?= $totalAbsensi ?></p>
                </div>
                <i class="fas fa-briefcase text-gray-300 text-3xl"></i>
            </div>
            <div class="bg-white p-4 rounded shadow flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-semibold text-gray-500">DATA ADMIN</h2>
                    <p class="text-2xl font-bold"><?= $totalAdmin ?></p>
                </div>
                <i class="fas fa-comments text-gray-300 text-3xl"></i>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-sm font-semibold text-gray-500 mb-2">Data Kehadiran Tukang (Jan–Jun)</h2>
                <canvas id="barChart" height="200"></canvas>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-sm font-semibold text-gray-500 mb-2">Komposisi Pekerja</h2>
                <canvas id="pieChart" height="200"></canvas>
            </div>
        </div>

        <?php include 'footer.php' ?>
    </div>

    <script>
    // Bar chart
    new Chart(document.getElementById('barChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= $barLabels ?>,
            datasets: [{
                label: 'Kehadiran Tukang',
                data: <?= $barData ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.7)'
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie chart
    new Chart(document.getElementById('pieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Karyawan','Admin','Tukang'],
            datasets: [{
                data: <?= $pieData ?>,
                backgroundColor: [
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(234, 179, 8, 0.7)'
                ]
            }]
        },
        options: { responsive: true }
    });
    </script>
</body>
</html>
