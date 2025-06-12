<?php
include("sidebar.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

require 'vendor/autoload.php'; // dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "penggajian");

// Cek apakah form sudah diisi
$nama_tukang = $_GET['nama'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

// Jika semua parameter ada, jalankan proses cetak PDF
if (!empty($nama_tukang) && !empty($bulan) && !empty($tahun)) {

    $query = $koneksi->query("SELECT * FROM data_gaji 
        WHERE nama_tukang = '$nama_tukang' 
        AND bulan = '$bulan' 
        AND tahun = '$tahun' 
        ORDER BY tanggal_awal ASC");

    $data_gaji = [];
    while ($row = $query->fetch_assoc()) {
        $data_gaji[] = $row;
    }

    $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            h2, h3, h4 { text-align: center; margin: 0; }
            .section { margin-top: 15px; }
            .total { font-weight: bold; margin-top: 20px; }
            hr { margin-top: 10px; margin-bottom: 10px; }
        </style>

        <h2>Slip Gaji Tukang</h2>
        <p><strong>Nama:</strong> ' . htmlspecialchars($nama_tukang) . '</p>
        <p><strong>Bulan:</strong> ' . date('F', mktime(0, 0, 0, $bulan, 10)) . ' ' . $tahun . '</p>
        <hr>
    ';

    $total_bulanan = 0;
    $minggu_ke = 1;

    foreach ($data_gaji as $gaji) {
        $tanggal_awal = date('d M Y', strtotime($gaji['tanggal_awal']));
        $tanggal_akhir = date('d M Y', strtotime($gaji['tanggal_akhir']));
        $total_gaji = number_format($gaji['total_gaji'], 0, ',', '.');

        $html .= '
            <div class="section">
                <h4>Minggu ke-' . $minggu_ke++ . '</h4>
                <p>Tanggal: ' . $tanggal_awal . ' s/d ' . $tanggal_akhir . '</p>
                <p>Gaji Minggu Ini: Rp. ' . $total_gaji . '</p>
            </div>
            <hr>
        ';
        $total_bulanan += $gaji['total_gaji'];
    }

    $html .= '
        <p class="total">Total Gaji Bulan Ini: Rp. ' . number_format($total_bulanan, 0, ',', '.') . '</p>
    ';

    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Slip_Gaji_{$nama_tukang}_{$bulan}_{$tahun}.pdf", array("Attachment" => false));
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Slip Gaji Tukang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons.min.js"></script>
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

        <!-- Title & Tanggal Section -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Slip Gaji Tukang</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <!-- END Title & Tanggal Section -->

        <!-- Form Filter Slip Gaji -->
        <div class="w-full lg:max-w-[800px] mx-auto bg-white/80 px-8 py-10 rounded-2xl shadow-xl mt-16">
            <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Filter Slip Gaji Tukang</h2>

            <?php
            $result = $koneksi->query("SELECT DISTINCT nama FROM gaji_tukang ORDER BY nama ASC");
            echo "<pre>";
            while ($row = $result->fetch_assoc()) {
                echo "Nama: " . $row['nama'] . "\n";
            }
            echo "</pre>";
            ?>

            <form action="hasil_slip_gaji.php" method="GET" class="space-y-6">
                <!-- Bulan -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bulan</label>
                    <select name="bulan" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                        <option value="">-- Pilih Bulan --</option>
                        <?php
                        $query_bulan = $koneksi->query("SELECT DISTINCT bulan FROM gaji_tukang ORDER BY bulan ASC");
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
                        while ($row = $query_bulan->fetch_assoc()) {
                            $bln = $row['bulan'];
                            echo "<option value='$bln'>{$bulan_nama[$bln]}</option>";
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
                        $query_tahun = $koneksi->query("SELECT DISTINCT tahun FROM absensi_tukang ORDER BY tahun DESC");
                        while ($row = $query_tahun->fetch_assoc()) {
                            $thn = $row['tahun'];
                            echo "<option value='$thn'>$thn</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Nama Tukang (Dinamis dari data_gaji) -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Tukang</label>
                    <select name="nama_tukang" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-base">
                        <option value="">-- Pilih Nama Tukang --</option>
                        <?php
                        $result = $koneksi->query("SELECT DISTINCT nama FROM gaji_tukang ORDER BY nama ASC");
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['nama']) . '">' . htmlspecialchars($row['nama']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-between mt-8 gap-4 flex-wrap">
                    <!-- Tombol Cetak Slip Gaji -->
                    <button type="submit" formaction="cetak_slip_gaji.php"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 text-white rounded-xl shadow hover:bg-green-700 transition duration-200">
                        <ion-icon name="print-outline" class="text-xl"></ion-icon>
                        Cetak Slip Gaji
                    </button>

                    <!-- Tombol Tampilkan Slip Gaji -->
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition duration-200">
                        Tampilkan Slip Gaji
                    </button>
                </div>
            </form>
        </div>
        <?php include("footer.php"); ?>
    </div>
</body>

</html>