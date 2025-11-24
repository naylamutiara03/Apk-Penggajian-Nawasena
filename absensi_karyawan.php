<?php
session_start();
include("koneksi.php");

// Cek Login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Tambah Absensi
if (isset($_POST['simpan'])) {
    $id_karyawan = $_POST['id_karyawan'];
    $tgl = $_POST['tgl'];
    $masuk = $_POST['masuk'];
    $keluar = $_POST['keluar'];

    // RULE KETERLAMBATAN
    $batas = "09:15";
    if ($masuk > $batas) {
        $ket = "Telat";
    } else {
        $ket = "Tepat Waktu";
    }

    mysqli_query($konek, "INSERT INTO absensi_karyawan 
        (id_karyawan, tgl_absen, jam_masuk, jam_keluar, keterangan_telat)
    VALUES 
        ('$id_karyawan','$tgl','$masuk','$keluar','$ket')");

    header("Location: absensi_karyawan.php");
    exit;
}


// Hapus Absensi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($konek, "DELETE FROM absensi_karyawan WHERE id='$id'");
    header("Location: absensi_karyawan.php");
    exit;
}

include("sidebar.php");

// =========== FILTER ===============
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absensi Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="p-6 lg:ml-[300px] flex-grow">

        <!-- HEADER -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang,
                        <strong><?= htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Absensi Karyawan</h1>
            <span class="text-gray-500 mr-2"><?= date('d F Y'); ?></span>
        </div>

        <!-- FORM TAMBAH -->
        <div class="w-full bg-white px-8 py-6 rounded-2xl shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Tambah Absensi</h2>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">

                <!-- PILIH KARYAWAN -->
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Karyawan</label>
                    <select name="id_karyawan" required class="border rounded-lg p-2 w-full">
                        <option value="">-- Pilih Karyawan --</option>
                        <?php
                        $cek = mysqli_query($konek, "SELECT * FROM karyawan ORDER BY nama_karyawan ASC");
                        while ($k = mysqli_fetch_assoc($cek)) {
                            echo "<option value='{$k['id']}'>{$k['nama_karyawan']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- TANGGAL -->
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date" name="tgl" required class="border rounded-lg p-2 w-full">
                </div>

                <!-- JAM MASUK -->
                <div>
                    <label class="block text-sm font-medium mb-1">Jam Masuk</label>
                    <input type="time" name="masuk" required class="border rounded-lg p-2 w-full"
                        placeholder="Jam Masuk">
                </div>

                <!-- JAM KELUAR -->
                <div>
                    <label class="block text-sm font-medium mb-1">Jam Keluar</label>
                    <input type="time" name="keluar" required class="border rounded-lg p-2 w-full"
                        placeholder="Jam Keluar">
                </div>



                <!-- TOMBOL SIMPAN -->
                <div class="flex items-end">
                    <button type="submit" name="simpan"
                        class="bg-blue-600 text-white px-4 py-2 w-full rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>

            </form>
        </div>


        <!-- FILTER BULAN TAHUN + TANGGAL -->
        <div class="bg-white p-4 rounded-xl shadow mb-4">
            <form method="GET" class="flex flex-wrap gap-3 items-center">

                <!-- FILTER HARI (1 s/d max hari di bulan tsb) -->
                <select name="hari" class="border p-2 rounded-lg">
                    <option value="">-- Semua Tanggal --</option>
                    <?php
                    // Tentukan jumlah hari sesuai bulan & tahun yang dipilih
                    $maxHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
                    $hariDipilih = isset($_GET['hari']) ? $_GET['hari'] : "";
                    for ($h = 1; $h <= $maxHari; $h++) {
                        $selHari = ($hariDipilih == $h) ? "selected" : "";
                        echo "<option value='$h' $selHari>$h</option>";
                    }
                    ?>
                </select>

                <select name="bulan" class="border p-2 rounded-lg">
                    <?php
                    $bulanArr = [
                        1 => "Januari",
                        "Februari",
                        "Maret",
                        "April",
                        "Mei",
                        "Juni",
                        "Juli",
                        "Agustus",
                        "September",
                        "Oktober",
                        "November",
                        "Desember"
                    ];

                    foreach ($bulanArr as $i => $nm) {
                        $sel = ($bulan == $i) ? "selected" : "";
                        echo "<option value='$i' $sel>$nm</option>";
                    }
                    ?>
                </select>

                <select name="tahun" class="border p-2 rounded-lg">
                    <?php
                    $now = date("Y");
                    for ($i = 2023; $i <= $now; $i++) {
                        $sel = ($tahun == $i) ? "selected" : "";
                        echo "<option value='$i' $sel>$i</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    Tampilkan
                </button>
            </form>
        </div>


        <!-- TABEL DATA -->
        <div class="bg-white p-6 rounded-2xl shadow">
            <table class="w-full text-sm text-center border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">No</th>
                        <th class="border p-2">Nama</th>
                        <th class="border p-2">Tanggal</th>
                        <th class="border p-2">Jam Masuk</th>
                        <th class="border p-2">Jam Keluar</th>
                        <th class="border p-2">Keterangan</th>
                        <th class="border p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($konek, "
    SELECT a.*, k.nama_karyawan 
    FROM absensi_karyawan a
    JOIN karyawan k ON a.id_karyawan = k.id
    WHERE MONTH(a.tgl_absen)='$bulan' 
    AND YEAR(a.tgl_absen)='$tahun'
    " . (!empty($_GET['hari']) ? " AND DAY(a.tgl_absen)='{$_GET['hari']}'" : "") . "
    ORDER BY a.tgl_absen DESC
");

                    $no = 1;
                    while ($d = mysqli_fetch_assoc($q)) {
                        // CEK KETERANGAN TELAT
                        if ($d['keterangan_telat'] == "Telat") {
                            $ket = "<span class='text-red-600 font-bold flex items-center justify-center gap-1'>
                    <ion-icon name='warning-outline'></ion-icon> Telat
                </span>";
                        } else {
                            $ket = "<span class='text-green-600 font-semibold flex items-center justify-center gap-1'>
                    <ion-icon name='checkmark-circle-outline'></ion-icon> Tepat Waktu
                </span>";
                        }

                        echo "
    <tr>
        <td class='border p-2'>$no</td>
        <td class='border p-2'>{$d['nama_karyawan']}</td>
        <td class='border p-2'>{$d['tgl_absen']}</td>
        <td class='border p-2'>{$d['jam_masuk']} WIB</td>
        <td class='border p-2'>{$d['jam_keluar']} WIB</td>
        <td class='border p-2'>$ket</td>
       <td class='border p-2'>
    <button 
        onclick=\"openModal('{$d['id']}')\" 
        class='text-red-600 text-lg hover:text-red-800'>
        <ion-icon name='trash-outline'></ion-icon>
    </button>
</td>
    </tr>";
                        $no++;
                    }

                    ?>
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById("jamMasuk").addEventListener("change", function () {
                const jamMasuk = this.value;
                const batasWaktu = "09:15";
                const ket = document.getElementById("ketTelat");

                if (jamMasuk > batasWaktu) {
                    ket.textContent = "TELAT";
                    ket.style.color = "red";
                } else if (jamMasuk !== "") {
                    ket.textContent = "Tepat Waktu";
                    ket.style.color = "green";
                } else {
                    ket.textContent = "";
                }
            });

            function openModal(id) {
                document.getElementById("btnDelete").href = "absensi_karyawan.php?hapus=" + id;
                document.getElementById("modalDelete").classList.remove("hidden");
            }

            function closeModal() {
                document.getElementById("modalDelete").classList.add("hidden");
            }
        </script>

        <!-- MODAL DELETE -->
        <div id="modalDelete" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl shadow-lg w-[350px] text-center">
                <h2 class="text-xl font-bold mb-2 text-red-600">Hapus Data?</h2>
                <p class="text-sm text-gray-600 mb-6">Data absensi ini akan dihapus permanen.</p>

                <div class="flex justify-center gap-3">
                    <button onclick="closeModal()" class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400">
                        Batal
                    </button>
                    <a id="btnDelete" href="#" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                        Hapus
                    </a>
                </div>
            </div>
        </div>



        <?php include("footer.php"); ?>
    </div>

</body>

</html>