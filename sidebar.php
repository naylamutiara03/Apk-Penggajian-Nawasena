<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "User";
?>

<!-- Tombol untuk membuka sidebar di layar kecil -->
<button onclick="openSidebar()" class="sm:block lg:hidden fixed top-4 left-4 bg-gray-900 text-white p-2 rounded">
    <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
</button>

<!-- Sidebar -->
<div id="sidebar"
    class="sidebar fixed top-0 bottom-0 left-0 p-2 w-[300px] lg:w-[300px] overflow-y-auto text-center bg-gray-900 lg:block sm:hidden transition-transform -translate-x-full lg:translate-x-0">
    <div class="text-gray-100 text-xl">
        <div class="p-2.5 mt-1 flex items-center">
            <!-- Ganti icon dengan logo -->
            <img src="assets/img/logo-nawasena.png" alt="Logo Nawasena" class="h-8 w-8 rounded-md">
            <h1 class="font-bold text-gray-200 text-[15px] ml-3">PENGGAJIAN NAWASENA</h1>
            <!-- Tombol untuk menutup sidebar di layar kecil -->
            <i class="bi bi-x cursor-pointer ml-auto lg:hidden" onclick="closeSidebar()"></i>
        </div>
        <div class="my-2 bg-gray-600 h-[1px]"></div>
    </div>

    <div
        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer bg-blue-600 hover:bg-blue-600 text-white">
        <ion-icon name="speedometer-outline" class="text-xl"></ion-icon>
        <a href="dashboard.php" class="text-[15px] ml-4 text-gray-200 font-bold">Dashboard</a>
    </div>
    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white"
        onclick="dropdown()">
        <ion-icon name="folder-outline" class="text-xl"></ion-icon>
        <div class="flex justify-between w-full items-center">
            <span class="text-[15px] ml-4 text-gray-200 font-bold">Master Data</span>
            <span class="text-sm rotate-180" id="arrow">
                <ion-icon name="chevron-down-outline"></ion-icon>
            </span>
        </div>
    </div>
    <div class="text-left text-sm mt-2 w-4/5 mx-auto text-gray-200 font-bold hidden" id="submenu">
        <a href="data_pegawai.php" class="block cursor-pointer p-2 hover:bg-blue-600 rounded-md mt-1">Data Pegawai</a>
        <a href="data_jabatan.php" class="block cursor-pointer p-2 hover:bg-blue-600 rounded-md mt-1">Data Jabatan</a>
        <a href="data_tukang.php" class="block cursor-pointer p-2 hover:bg-blue-600 rounded-md mt-1">Data Tukang</a>
        <a href="data_admin.php" class="block cursor-pointer p-2 hover:bg-blue-600 rounded-md mt-1">Data Admin</a>
    </div>
    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white">
        <ion-icon name="cash-outline" class="text-xl"></ion-icon>
        <a href="transaksi.php" class="text-[15px] ml-4 text-gray-200 font-bold">Transaksi</a>
    </div>
    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white">
        <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
        <a href="laporan.php" class="text-[15px] ml-4 text-gray-200 font-bold">Laporan</a>
    </div>
    <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white">
        <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
        <a href="ubah_password.php" class="text-[15px] ml-4 text-gray-200 font-bold">Ubah Password</a>
    </div>
    <div onclick="openLogoutModal()"
        class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white">
        <ion-icon name="log-out-outline" class="text-xl"></ion-icon>
        <span class="text-[15px] ml-4 text-gray-200 font-bold">Logout</span>
    </div>
</div>

<script>
    function openSidebar() {
        document.getElementById("sidebar").classList.remove("-translate-x-full");
    }

    function closeSidebar() {
        document.getElementById("sidebar").classList.add("-translate-x-full");
    }

    function dropdown() {
        document.querySelector("#submenu").classList.toggle("hidden");
        document.querySelector("#arrow").classList.toggle("rotate-0");
    }

    function openLogoutModal() {
        document.getElementById("logoutModal").classList.remove("hidden");
    }

    function closeLogoutModal() {
        document.getElementById("logoutModal").classList.add("hidden");
    }
</script>