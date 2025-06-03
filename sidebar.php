<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "User ";
?>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
$is_transaksi_active = in_array($current_page, ['data_absensi.php', 'data_gaji.php']);
$master_data_pages = ['data_karyawan.php', 'data_jabatan.php', 'data_tukang.php', 'data_admin.php'];
$is_master_data_active = in_array($current_page, $master_data_pages);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons.min.js"></script>
    <style>
        .loader {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 4s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Loading Spinner -->
    <div id="loading" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="loader"></div>
    </div>

    <!-- Tombol untuk membuka sidebar di layar kecil -->
    <button onclick="openSidebar()"
        class="sm:block lg:hidden fixed top-4 left-4 bg-gray-900 text-white p-2 rounded z-50">
        <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
    </button>

    <!-- Backdrop untuk menutup sidebar -->
    <div id="backdrop" class="hidden fixed inset-0 bg-black opacity-50 z-40" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed top-0 bottom-0 left-0 p-2 w-[300px] lg:w-[300px] overflow-y-auto text-center bg-gray-900 
    lg:block sm:hidden transition-transform -translate-x-full lg:translate-x-0 z-50">
        <div class="text-gray-100 text-xl">
            <div class="p-2.5 mt-1 flex items-center">
                <img src="assets/img/logo-nawasena.png" alt="Logo Nawasena" class="h-8 w-8 rounded-md">
                <h1 class="font-bold text-gray-200 text-[15px] ml-3">PENGGAJIAN NAWASENA</h1>
                <ion-icon name="close-outline" class="text-white text-xl cursor-pointer ml-auto lg:hidden"
                    onclick="closeSidebar()"></ion-icon>
            </div>
            <div class="my-2 bg-gray-600 h-[1px]"></div>
        </div>

        <!-- Menu Dashboard -->
        <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer 
    <?php echo ($current_page == 'dashboard.php') ? 'bg-blue-700' : 'hover:bg-blue-600'; ?> text-white">
            <ion-icon name="speedometer-outline" class="text-xl"></ion-icon>
            <a href="dashboard.php" class="text-[15px] ml-4 text-gray-200 font-bold loading-link">Dashboard</a>
        </div>

        <!-- Menu Master Data -->
        <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer 
<?php echo ($is_master_data_active) ? 'bg-blue-700' : 'hover:bg-blue-600'; ?> text-white"
            onclick="toggleDropdown('submenuMaster', 'arrowMaster')">
            <ion-icon name="folder-outline" class="text-xl"></ion-icon>
            <div class="flex justify-between w-full items-center">
                <span class="text-[15px] ml-4 text-gray-200 font-bold">Master Data</span>
                <span class="text-sm <?php echo ($is_master_data_active) ? ' rotate-180' : ''; ?>" id="arrowMaster">
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </span>
            </div>
        </div>

        <!-- SubMenu Master Data -->
        <div class="text-left text-sm mt-2 w-4/5 mx-auto text-gray-200 font-bold 
<?php echo ($is_master_data_active) ? '' : 'hidden'; ?>" id="submenuMaster">
            <a href="data_karyawan.php" class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link
    <?php echo ($current_page == 'data_karyawan.php') ? 'bg-blue-600' : ''; ?>">Data
                Karyawan</a>
            <a href="data_jabatan.php" class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link
    <?php echo ($current_page == 'data_jabatan.php') ? 'bg-blue-600' : ''; ?>">Data Jabatan</a>
            <a href="data_tukang.php" class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link
    <?php echo ($current_page == 'data_tukang.php') ? 'bg-blue-600' : ''; ?>">Data Tukang</a>
            <a href="data_admin.php" class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link
    <?php echo ($current_page == 'data_admin.php') ? 'bg-blue-600' : ''; ?>">Data Admin</a>
        </div>

        <!-- Menu Transaksi -->
        <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer 
    <?php echo ($is_transaksi_active) ? 'bg-blue-700' : 'hover:bg-blue-600'; ?> text-white"
            onclick="toggleDropdown('submenuTransaksi', 'arrowTransaksi')">
            <ion-icon name="cash-outline" class="text-xl"></ion-icon>
            <div class="flex justify-between w-full items-center">
                <span class="text-[15px] ml-4 text-gray-200 font-bold">Transaksi</span>
                <span class="text-sm <?php echo ($is_transaksi_active) ? 'rotate-180' : ''; ?>" id="arrowTransaksi">
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </span>
            </div>
        </div>

        <!-- Submenu Transaksi -->
        <div class="text-left text-sm mt-2 w-4/5 mx-auto text-gray-200 font-bold 
<?php echo ($is_transaksi_active) ? '' : 'hidden'; ?>" id="submenuTransaksi">
            <a href="data_absensi.php" class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link 
    <?php echo ($current_page == 'data_absensi.php') ? 'bg-blue-600' : ''; ?>">
                Data Absensi Tukang
            </a>
            <a href="data_gaji.php" class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link 
    <?php echo ($current_page == 'data_gaji.php') ? 'bg-blue-600' : ''; ?>">
                Data Gaji Tukang
            </a>
        </div>


        <!-- Menu Laporan -->
        <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer 
    <?php echo ($current_page == 'laporan.php' || $current_page == 'laporan_gaji.php' || $current_page == 'slip_gaji.php') ? 'bg-blue-700' : 'hover:bg-blue-600'; ?> text-white"
            onclick="toggleDropdown('submenuLaporan', 'arrowLaporan')">
            <ion-icon name="document-text-outline" class="text-xl"></ion-icon>
            <div class="flex justify-between w-full items-center">
                <a href="laporan.php" class="text-[15px] ml-4 text-gray-200 font-bold"
                    onclick="showLoading()">Laporan</a>
                <span
                    class="text-sm <?php echo ($current_page == 'laporan.php' || $current_page == 'laporan_gaji.php' || $current_page == 'slip_gaji.php') ? 'rotate-180' : ''; ?>"
                    id="arrowLaporan">
                    <ion-icon name="chevron-down-outline"></ion-icon>
                </span>
            </div>
        </div>

        <!-- Submenu for Laporan -->
        <div id="submenuLaporan" class="hidden text-left text-sm mt-2 w-4/5 mx-auto text-gray-200 font-bold">
            <a href="slip_gaji.php"
                class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link <?php echo ($current_page == 'slip_gaji.php') ? 'bg-blue-600' : ''; ?>">
                Slip Gaji Tukang
            </a>
            <a href="laporan_gaji_karyawan.php"
                class="block cursor-pointer p-2 rounded-md mt-1 hover:bg-blue-600 loading-link <?php echo ($current_page == 'slip_gaji_karyawan.php') ? 'bg-blue-600' : ''; ?>">
                Laporan Gaji Karyawan
            </a>
        </div>

        <!-- Menu Ubah Password -->
        <div class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white
    <?php echo ($current_page == 'ubah_password.php') ? 'bg-blue-600' : ''; ?>">
            <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
            <a href="ubah_password.php" class="loading-link text-[15px] ml-4 text-gray-200 font-bold">Ubah Password</a>
        </div>

        <!-- Menu Logout -->
        <div onclick="openLogoutModal()"
            class="p-2.5 mt-3 flex items-center rounded-md px-4 duration-300 cursor-pointer hover:bg-blue-600 text-white">
            <ion-icon name="log-out-outline" class="text-xl"></ion-icon>
            <span class="text-[15px] ml-4 text-gray-200 font-bold">Logout</span>
        </div>
    </div>

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

    <!-- Script -->
    <script>
        function openSidebar() {
            document.getElementById("sidebar").classList.remove("-translate-x-full");
            document.getElementById("backdrop").classList.remove("hidden");
        }

        function closeSidebar() {
            document.getElementById("sidebar").classList.add("-translate-x-full");
            document.getElementById("backdrop").classList.add("hidden");
        }

        function toggleDropdown(submenuId, arrowId) {
            document.getElementById(submenuId).classList.toggle("hidden");
            document.getElementById(arrowId).classList.toggle("rotate-180");
        }

        document.addEventListener("click", function (event) {
            const sidebar = document.getElementById("sidebar");
            const backdrop = document.getElementById("backdrop");
            const button = document.querySelector("button[onclick='openSidebar()']");
            if (!sidebar.contains(event.target) && !button.contains(event.target)) {
                closeSidebar();
            }
        });

        function openLogoutModal() {
            document.getElementById("logoutModal").classList.remove("hidden");
        }

        function closeLogoutModal() {
            document.getElementById("logoutModal").classList.add("hidden");
        }

        function showLoading() {
            document.getElementById("loading").classList.remove("hidden");
        }
    </script>

    <script>
        document.querySelectorAll('.loading-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault(); // cegah pindah langsung
                const url = this.getAttribute('href');
                document.getElementById("loading").classList.remove("hidden");

                setTimeout(() => {
                    window.location.href = url;
                }, 1000);
            });
        });
    </script>


</body>

</html>