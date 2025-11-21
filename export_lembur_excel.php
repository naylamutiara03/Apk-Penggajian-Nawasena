<?php
include("koneksi.php");

if (!isset($_GET['bulan']) || !isset($_GET['tahun'])) {
    die("Filter tidak valid!");
}

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Lembur_$bulan-$tahun.xls");

$bulanNama = [
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

?>

<h3>Laporan Lembur Karyawan Periode <?= $bulanNama[$bulan] . " " . $tahun; ?></h3>

<table border="1" cellpadding="5">
    <thead>
        <tr style="background:#ddd;">
            <th>No</th>
            <th>Nama Karyawan</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
            <th>Durasi</th>
            <th>Uang Lembur</th>
        </tr>
    </thead>
    <tbody>

<?php
// Ambil data dan urutkan per nama karyawan dulu
$q = mysqli_query($konek, "
    SELECT a.*, k.nama_karyawan, j.gapok 
    FROM absensi_karyawan a
    JOIN karyawan k ON a.id_karyawan = k.id
    JOIN jabatan j ON k.id_jabatan = j.id
    WHERE MONTH(a.tgl_absen)='$bulan' AND YEAR(a.tgl_absen)='$tahun'
    ORDER BY k.nama_karyawan ASC, a.tgl_absen ASC
");

$no = 1;
$grand_total_semua = 0;
$karyawan_sekarang = "";
$total_per_orang = 0;

while ($d = mysqli_fetch_assoc($q)) {

    // Jika nama berubah => tampilkan total per nama sebelumnya
    if ($karyawan_sekarang != "" && $karyawan_sekarang != $d['nama_karyawan']) {
        echo "<tr style='background:#f0f0f0; font-weight:bold;'>
                <td colspan='6' align='right'>TOTAL $karyawan_sekarang</td>
                <td>Rp " . number_format($total_per_orang, 0, ',', '.') . "</td>
             </tr>";
        $total_per_orang = 0; // reset
    }

    $gapok = $d['gapok'];
    $per_hari = $gapok / 20;
    $per_jam = $per_hari / 8;
    $per_menit = $per_jam / 60;

    $jam_keluar = strtotime($d['jam_keluar']);
    $batas_awal_lembur = strtotime("18:31:00");
    $maksimal = strtotime("22:00:00");

    if ($jam_keluar <= $batas_awal_lembur) {
        $lembur_menit = 0;
    } else {
        if ($jam_keluar > $maksimal) $jam_keluar = $maksimal;
        $lembur_detik = $jam_keluar - $batas_awal_lembur;
        $lembur_menit = floor($lembur_detik / 60);
    }

    $uang_lembur = round(($lembur_menit * $per_menit), -3);
    $durasi = $lembur_menit > 0 ? $lembur_menit . " menit" : "-";

    echo "<tr>
            <td>{$no}</td>
            <td>{$d['nama_karyawan']}</td>
            <td>{$d['tgl_absen']}</td>
            <td>{$d['jam_masuk']}</td>
            <td>{$d['jam_keluar']}</td>
            <td>{$durasi}</td>
            <td>Rp " . number_format($uang_lembur, 0, ',', '.') . "</td>
         </tr>";

    $no++;
    $total_per_orang += $uang_lembur;
    $grand_total_semua += $uang_lembur;

    $karyawan_sekarang = $d['nama_karyawan'];
}

// Tampilkan total untuk nama terakhir
if ($karyawan_sekarang != "") {
    echo "<tr style='background:#f0f0f0; font-weight:bold;'>
            <td colspan='6' align='right'>TOTAL $karyawan_sekarang</td>
            <td>Rp " . number_format($total_per_orang, 0, ',', '.') . "</td>
         </tr>";
}
?>

    </tbody>

    <tr style="background:#ddd; font-weight:bold;">
        <td colspan="6" align="right">GRAND TOTAL SEMUA KARYAWAN</td>
        <td>Rp <?= number_format($grand_total_semua, 0, ',', '.'); ?></td>
    </tr>

</table>
