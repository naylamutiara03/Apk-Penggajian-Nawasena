<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'koneksi.php';

// Ambil filter bulan dan tahun dari GET
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$periode = "$tahun-$bulan";

// Daftar nama bulan
$bulanNama = [
    "01" => "Januari", "02" => "Februari", "03" => "Maret",
    "04" => "April", "05" => "Mei", "06" => "Juni",
    "07" => "Juli", "08" => "Agustus", "09" => "September",
    "10" => "Oktober", "11" => "November", "12" => "Desember"
];
$judulPeriode = "Daftar Gaji Tukang Bulan " . ($bulanNama[$bulan] ?? $bulan) . " Tahun $tahun";

// Query data gaji
$q = mysqli_query($konek, "
    SELECT 
        a.nik, 
        t.nama_tukang, 
        t.jabatan, 
        SUM(a.total_hadir) AS total_hadir, 
        j.gapok 
    FROM absensi_tukang a
    JOIN tukang_nws t ON a.nik = t.nik
    JOIN jabatan j ON t.jabatan = j.jabatan
    WHERE DATE_FORMAT(a.tanggal_masuk, '%Y-%m') = '$periode'
    GROUP BY a.nik
");

// Mulai isi HTML
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

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Nama file download
$filename = "Gaji_Tukang_{$bulan}_{$tahun}.pdf";
$dompdf->stream($filename, ["Attachment" => true]); // true = langsung download
exit;
