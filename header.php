<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

include "koneksi.php";

// Ambil nama file yang sedang diakses
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white p-4 fixed w-full z-50 top-0 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="assets/img/logo.png" alt="Logo Nawasena" class="h-10">
            </div>

            <!-- Hamburger menu -->
            <button id="menu-btn" class="block md:hidden text-black text-2xl focus:outline-none">
                <ion-icon name="menu-outline"></ion-icon>
            </button>

            <!-- Menu -->
            <ul id="menu"
                class="hidden md:flex text-black md:ml-8 absolute md:relative bg-white md:bg-transparent top-16 md:top-0 left-0 right-0 md:w-auto w-full p-4 md:p-0 md:flex-row flex-col items-center shadow-lg md:shadow-none">

                <li class="w-full text-center">
                    <a href="index.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'index.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Home
                    </a>
                </li>

                <li class="w-full text-center">
                    <a href="data_admin.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'data_admin.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Data Admin
                    </a>
                </li>

                <li class="w-full text-center">
                    <a href="data_jabatan.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'data_jabatan.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Data Jabatan
                    </a>
                </li>

                <li class="w-full text-center">
                    <a href="data_golongan.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'data_golongan.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Data Golongan
                    </a>
                </li>

                <li class="w-full text-center">
                    <a href="data_pegawai.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'data_pegawai.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Tukang
                    </a>
                </li>

                <li class="w-full text-center">
                    <a href="data_kehadiran.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'data_kehadiran.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Kehadiran Tukang
                    </a>
                </li>

                <li class="w-full text-center">
                    <a href="data_penggajian.php"
                        class="block py-2 px-4 rounded-md <?= ($current_page == 'data_penggajian.php') ? 'text-blue-500' : 'hover:text-blue-500'; ?>">
                        Gaji Tukang
                    </a>
                </li>

                <!-- Dropdown Laporan -->
                <li class="relative group w-full text-center md:w-auto">
                    <a href="#" class="block py-2 px-4 flex items-center justify-center rounded-md hover:text-blue-500">
                        Laporan
                        <ion-icon name="chevron-down-outline" class="ml-1"></ion-icon>
                    </a>
                    <ul
                        class="absolute hidden bg-white text-black shadow-lg rounded-lg w-48 group-hover:block md:top-full top-12 md:left-0 left-1/2 transform -translate-x-1/2 md:translate-x-0">
                        <li><a href="cetak_laporan_pegawai.php" class="block px-4 py-2 hover:bg-blue-100">Laporan Data
                                Pegawai</a></li>
                        <li><a href="cetak_laporan_golongan.php" class="block px-4 py-2 hover:bg-blue-100">Laporan Data
                                Golongan</a></li>
                        <li><a href="cetak_laporan_jabatan.php" class="block px-4 py-2 hover:bg-blue-100">Laporan Data
                                Jabatan</a></li>
                        <li><a href="laporan_kehadiran.php" class="block px-4 py-2 hover:bg-blue-100">Laporan Kehadiran
                                Tukang</a></li>
                        <li><a href="laporan_honor.php" class="block px-4 py-2 hover:bg-blue-100">Laporan Honor</a></li>
                    </ul>
                </li>

                <li class="w-full text-center md:hidden">
                    <a href="logout.php" class="block py-2 px-4 text-black hover:text-blue-300">
                        <ion-icon name="log-out-outline" class="text-black text-2xl"></ion-icon>
                    </a>
                </li>
            </ul>

            <div class="ml-auto hidden md:block">
                <a href="logout.php">
                    <ion-icon name="log-out-outline"
                        class="text-black text-2xl hover:text-blue-500 transition duration-300"></ion-icon>
                </a>
            </div>
        </div>
    </nav>

    <script>
        const menuBtn = document.getElementById("menu-btn");
        const menu = document.getElementById("menu");

        menuBtn.addEventListener("click", () => {
            menu.classList.toggle("hidden");
        });
    </script>