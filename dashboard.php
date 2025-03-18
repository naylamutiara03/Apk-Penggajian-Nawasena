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
                    <span class="mr-4">Selamat Datang, <?php echo htmlspecialchars($username); ?></span>
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
                                    DATA PEGAWAI
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
                                    DATA ADMIN
                                </h2>
                                <p class="text-2xl font-bold">
                                    1
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
                                    DATA JABATAN
                                </h2>
                                <p class="text-2xl font-bold">
                                    4
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
                                    DATA KEHADIRAN
                                </h2>
                                <p class="text-2xl font-bold">
                                    13
                                </p>
                            </div>
                            <i class="fas fa-comments text-gray-300 text-3xl">
                            </i>
                        </div>
                    </div>
                </div>
                <!-- END Grid Section -->

                <!-- Chart Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-sm font-semibold text-gray-500 mb-2">
                            Data Pegawai Berdasarkan Jenis Kelamin
                        </h2>
                        <img alt="Bar chart showing data of employees based on gender" height="400"
                            src="https://storage.googleapis.com/a1aa/image/Ph1zHBqhCX_1YOPPaSx_QZRpjcmUol1m1y39g41BIGs.jpg"
                            width="600" />
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-sm font-semibold text-gray-500 mb-2">
                            Status Pegawai
                        </h2>
                        <img alt="Pie chart showing status of employees" height="400"
                            src="https://storage.googleapis.com/a1aa/image/zjkEmBhlo4yNJJqWDz_8y7UAXZQnnLsL5OlnERLOVt8.jpg"
                            width="600" />
                    </div>
                </div>
                <!-- END Chart Section -->
                 
                <?php include 'footer.php'; ?>
        </body>

</html>