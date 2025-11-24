<?php
session_start();
include("koneksi.php");

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil filter
$filter_tukang = $_GET['nama'] ?? '';
$filter_bulan  = $_GET['bulan'] ?? '';
$filter_tahun  = $_GET['tahun'] ?? '';
$filter_minggu = $_GET['minggu'] ?? '';

if (empty($filter_tukang) || empty($filter_bulan) || empty($filter_tahun) || empty($filter_minggu)) {
    die("Filter tidak valid. Silakan kembali ke halaman sebelumnya.");
}

// Ambil nama tukang
$q_t = mysqli_query($konek, "SELECT nama_tukang FROM tukang_nws WHERE id = '$filter_tukang'");
$tukang = mysqli_fetch_assoc($q_t);
$nama_tukang = $tukang ? $tukang['nama_tukang'] : "-";

// Ambil detail lembur berdasarkan minggu
$q_data = mysqli_query($konek, "
    SELECT lt.*, t.nama_tukang 
    FROM lembur_tkg lt
    JOIN tukang_nws t ON lt.id_tukang = t.id
    WHERE lt.id_tukang = '$filter_tukang'
      AND MONTH(lt.tgl_lembur) = '$filter_bulan'
      AND YEAR(lt.tgl_lembur) = '$filter_tahun'
      AND lt.minggu_ke = '$filter_minggu'
    ORDER BY lt.tgl_lembur ASC
");

// Hitung total
$q_total = mysqli_query($konek, "
    SELECT COALESCE(SUM(harga_lembur), 0) AS total_lembur
    FROM lembur_tkg
    WHERE id_tukang = '$filter_tukang'
      AND MONTH(tgl_lembur) = '$filter_bulan'
      AND YEAR(tgl_lembur) = '$filter_tahun'
      AND minggu_ke = '$filter_minggu'
");
$d = mysqli_fetch_assoc($q_total);
$total_lembur = (int)$d['total_lembur'];

// Nama file
$filename = "Slip_Gaji_{$nama_tukang}_Minggu_{$filter_minggu}_{$filter_bulan}_{$filter_tahun}.xls";

// Header Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Fungsi rupiah
function formatRupiah($angka){
    return "Rp " . number_format($angka,0,',','.');
}

// Fungsi format shift (½ masih tampil jika ada multiplier < 1)
function getShiftDisplay($json){
    if(empty($json)) return "-";
    $details = json_decode($json, true);
    if(!is_array($details)) return "-";

    $arr = [];
    foreach($details as $sh){
        if(isset($sh['multiplier']) && $sh['multiplier'] < 1){
            $arr[] = $sh['shift'] . " (½)";
        } else {
            $arr[] = $sh['shift'];
        }
    }
    return implode(", ", $arr);
}
?>

<table border="1">
    <tr style="background-color:#d1d5db; font-weight:bold;">
        <td colspan="2" style="text-align:center;">TOTAL HITUNGAN LEMBUR TUKANG</td>
    </tr>
    <tr><td>Nama Tukang</td><td><?= $nama_tukang; ?></td></tr>
    <tr><td>Tahun</td><td><?= $filter_tahun; ?></td></tr>
    <tr><td>Bulan</td><td>
        <?php 
            $bulan_arr = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
            echo $bulan_arr[$filter_bulan];
        ?>
    </td></tr>
    <tr><td>Minggu Ke-</td><td><?= $filter_minggu; ?></td></tr>
</table>

<br>

<table border="1">
    <tr style="background-color:#e5e7eb; font-weight:bold;">
        <td>No</td>
        <td>Tanggal</td>
        <td>Shift</td>
        <td>Jumlah Lembur</td>
    </tr>

<?php
$no = 1;
while($row = mysqli_fetch_assoc($q_data)): ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= date('d/m/Y', strtotime($row['tgl_lembur'])); ?></td>
        <td><?= getShiftDisplay($row['detail_shifts']); ?></td>
        <td><?= formatRupiah($row['harga_lembur']); ?></td>
    </tr>
<?php endwhile; ?>

    <tr style="background-color:#d1fae5; font-weight:bold;">
        <td colspan="3" style="text-align:right;">Total</td>
        <td><?= formatRupiah($total_lembur); ?></td>
    </tr>
</table>

<?php mysqli_close($konek); ?>
