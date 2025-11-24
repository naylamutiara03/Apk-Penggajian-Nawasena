<?php
// === EXPORT LAPORAN GAJI KARYAWAN KE EXCEL ===
include("koneksi.php");

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

$bulan_nama = [
    "01"=>"Januari","02"=>"Februari","03"=>"Maret","04"=>"April","05"=>"Mei","06"=>"Juni",
    "07"=>"Juli","08"=>"Agustus","09"=>"September","10"=>"Oktober","11"=>"November","12"=>"Desember"
];

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan-Gaji-{$bulan_nama[$bulan]}-$tahun.xls");

echo "<h3>Laporan Gaji Karyawan Bulan {$bulan_nama[$bulan]} $tahun</h3><br>";

$karyawan = $konek->query("SELECT * FROM karyawan ORDER BY nama_karyawan ASC");

while ($data = $karyawan->fetch_assoc()):
$id = $data['id'];

// Gaji pokok & per menit
$get_jab = $konek->query("SELECT gapok FROM jabatan WHERE id='{$data['id_jabatan']}'")->fetch_assoc();
$gaji_pokok = $get_jab ? $get_jab['gapok'] : 0;
$gaji_per_menit = $gaji_pokok / (20 * 8 * 60);

$absensi = $konek->query("
    SELECT * FROM absensi_karyawan
    WHERE id_karyawan='$id'
    AND MONTH(tgl_absen)='$bulan' AND YEAR(tgl_absen)='$tahun'
    AND jam_masuk IS NOT NULL AND jam_keluar IS NOT NULL
    ORDER BY tgl_absen ASC
");

$total_potongan = 0;
$total_lembur_uang = 0;

echo "<b>Nama Karyawan: {$data['nama_karyawan']}</b><br><br>";
echo "<b>Gaji Pokok: Rp ".number_format($gaji_pokok,0,',','.')."</b><br><br>";


echo "
<table border='1' cellpadding='5' cellspacing='0'>
<tr style='background:#ddd;font-weight:bold;'>
    <td>Tanggal</td>
    <td>Telat (Menit)</td>
    <td>Potongan</td>
    <td>Lembur (Menit)</td>
    <td>Uang Lembur</td>
</tr>
";

while ($row = $absensi->fetch_assoc()) {

    // ======== HITUNG TELAT ==========
    $telat = $row['telat_menit'];
    if (!$telat || $telat <= 0) {
        $telat = max(0,(strtotime($row['jam_masuk']) - strtotime("09:00:00"))/60);
    }
    $telat = round($telat);
    $pot_telat = round($telat * $gaji_per_menit, -3);
    $total_potongan += $pot_telat;

    // ======== HITUNG LEMBUR ==========
    $jam_keluar = strtotime($row['jam_keluar']);
    $awal_lembur = strtotime("18:31:00");
    $max_lembur = strtotime("22:00:00");

    if ($jam_keluar <= $awal_lembur) { $lembur = 0; }
    else {
        if ($jam_keluar > $max_lembur) $jam_keluar = $max_lembur;
        $lembur = floor(($jam_keluar - $awal_lembur)/60);
    }

    $uang_lembur = round($lembur * $gaji_per_menit, -3);
    $total_lembur_uang += $uang_lembur;

    echo "
    <tr>
        <td>{$row['tgl_absen']}</td>
        <td>{$telat} Menit</td>
        <td>Rp ".number_format($pot_telat,0,',','.')."</td>
        <td>{$lembur} Menit</td>
        <td>Rp ".number_format($uang_lembur,0,',','.')."</td>
    </tr>
    ";
}

$gaji_akhir = $gaji_pokok + $total_lembur_uang - $total_potongan;

echo "
<tr style='background:#f9f9f9;font-weight:bold;'>
    <td colspan='2'>TOTAL</td>
    <td>Rp ".number_format($total_potongan,0,',','.')."</td>
    <td></td>
    <td>Rp ".number_format($total_lembur_uang,0,',','.')."</td>
</tr>
</table>
<br>
<b>Gaji Akhir = Gaji Pokok + Total Lembur - Total Potongan</b><br>
<b>Gaji Akhir: Rp ".number_format($gaji_akhir,0,',','.')."</b><br><br><hr><br>
";
endwhile;
?>
