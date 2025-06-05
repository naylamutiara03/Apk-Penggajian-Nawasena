<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'koneksi.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$minggu = $_GET['minggu'] ?? '';
$periode = "$tahun-$bulan";

$bulanNama = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret",
    "04" => "April", "05" => "Mei", "06" => "Juni",
    "07" => "Juli", "08" => "Agustus", "09" => "September",
    "10" => "Oktober", "11" => "November", "12" => "Desember"
];

$tanggalAwal = '';
$tanggalAkhir = '';
$judulMinggu = '';
$whereTanggal = '';

if (!empty($minggu)) {
    $jumlahHari = cal_days_in_month(CAL_GREGORIAN, (int)$bulan, (int)$tahun);
    $mingguInt = (int)$minggu;

    $tanggalAwal = date("Y-m-d", strtotime("$tahun-$bulan-01 + " . ($mingguInt - 1) * 7 . " days"));
    $tanggalAkhir = date("Y-m-d", strtotime("$tanggalAwal + 6 days"));

    if ((int)date('d', strtotime($tanggalAkhir)) > $jumlahHari) {
        $tanggalAkhir = "$tahun-$bulan-$jumlahHari";
    }

    $judulMinggu = " (Minggu ke-$minggu: " . date('d/m/Y', strtotime($tanggalAwal)) . " - " . date('d/m/Y', strtotime($tanggalAkhir)) . ")";
    $whereTanggal = "a.tanggal_masuk BETWEEN '$tanggalAwal' AND '$tanggalAkhir'";
} else {
    $whereTanggal = "DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = '$periode'";
}

$judulPeriode = "Daftar Gaji Tukang Bulan " . ($bulanNama[$bulan] ?? $bulan) . " Tahun $tahun" . $judulMinggu;

$q = mysqli_query($konek, "
    SELECT 
        a.nik, 
        t.nama_tukang, 
        j.jabatan, 
        SUM(a.total_hadir) AS total_hadir, 
        j.gapok 
    FROM absensi_tukang a
    JOIN tukang_nws t ON a.nik = t.nik
    JOIN jabatan j ON t.id_jabatan = j.id
    WHERE $whereTanggal
    GROUP BY a.nik
");

$html = "<h2 style='text-align:center;'>$judulPeriode</h2><br>";
$html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
    <thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Total Hadir</th>
            <th>Gaji per Hari</th>
            <th>Total Gaji</th>
        </tr>
    </thead>
    <tbody>";

$no = 1;
while ($row = mysqli_fetch_assoc($q)) {
    $totalGaji = $row['total_hadir'] * $row['gapok'];
    $html .= "<tr>
        <td align='center'>{$no}</td>
        <td>{$row['nik']}</td>
        <td>{$row['nama_tukang']}</td>
        <td>{$row['jabatan']}</td>
        <td align='center'>" . number_format($row['total_hadir'], 1, ',', '.') . "</td>
        <td align='right'>" . number_format($row['gapok'], 0, ',', '.') . "</td>
        <td align='right'><strong>" . number_format($totalGaji, 0, ',', '.') . "</strong></td>
    </tr>";
    $no++;
}

if ($no === 1) {
    $html .= "<tr><td colspan='7' align='center'><em>Tidak ada data.</em></td></tr>";
}

$html .= "</tbody></table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$filename = "Gaji_Tukang_{$bulan}_{$tahun}" . (!empty($minggu) ? "_Minggu{$minggu}" : "") . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;
