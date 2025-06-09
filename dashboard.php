<?php
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "User";
?>

<?php include 'sidebar.php'; ?>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dropdown-content {
            display: none;
        }

        .group:hover .dropdown-content {
            display: block;
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
                    <span class="mr-4">Selamat Datang,
                        <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>

            </div>
        </div>
        <!-- END Header Section -->

        <body class="bg-gray-100">
            <div class="container mx-auto p-4">
                <!-- Title & Tanggal Section -->
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold">
                        Dashboard
                    </h1>
                    <span class="text-gray-500">
                        <?php echo date('d F Y'); ?>
                    </span>
                </div>
                <!-- END Title & Tanggal Section -->

                <!-- Grid Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-500">
                                    DATA KARYAWAN
                                </h2>
                                <p class="text-2xl font-bold">
                                    4
                                </p>
                            </div>
                            <i class="fas fa-users text-gray-300 text-3xl">
                            </i>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-500">
                                    DATA TUKANG
                                </h2>
                                <p class="text-2xl font-bold">
                                    13
                                </p>
                            </div>
                            <i class="fas fa-user-cog text-gray-300 text-3xl">
                            </i>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-500">
                                    DATA KEHADIRAN TUKANG
                                </h2>
                                <p class="text-2xl font-bold">
                                    3
                                </p>
                            </div>
                            <i class="fas fa-briefcase text-gray-300 text-3xl">
                            </i>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-500">
                                    DATA ADMIN
                                </h2>
                                <p class="text-2xl font-bold">
                                    1
                                </p>
                            </div>
                            <i class="fas fa-comments text-gray-300 text-3xl">
                            </i>
                        </div>
                    </div>
                </div>
                <!-- END Grid Section -->

                <!-- Bungkus chart pakai grid 2 kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-sm font-semibold text-gray-500 mb-2">Data Kehadiran Tukang</h2>
                        <canvas id="barChart" height="300"></canvas> <!-- height lebih kecil -->
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-sm font-semibold text-gray-500 mb-2">Total Data</h2>
                        <canvas id="pieChart" height="200"></canvas> <!-- height lebih kecil -->
                    </div>
                </div>

                <?php include 'footer.php'; ?>

                <script>
                    const barCtx = document.getElementById('barChart').getContext('2d');
                    const barChart = new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                            datasets: [{
                                label: 'Kehadiran Tukang',
                                data: [12, 19, 3, 5, 2, 3],
                                backgroundColor: 'rgba(59, 130, 246, 0.7)'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });

                    const pieCtx = document.getElementById('pieChart').getContext('2d');
                    const pieChart = new Chart(pieCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Karyawan', 'Admin', 'Tukang'],
                            datasets: [{
                                label: 'Total Pekerja',
                                data: [4, 1, 13],
                                backgroundColor: [
                                    'rgba(16, 185, 129, 0.7)',
                                    'rgba(59, 130, 246, 0.7)',
                                    'rgba(234, 179, 8, 0.7)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                        }
                    });
                </script>
        </body>

</html>