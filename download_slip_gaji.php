<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;

session_start();

if (!isset($_SESSION['html_slip'])) {
    echo "Data tidak tersedia untuk didownload.";
    exit;
}

$htmlBody = $_SESSION['html_slip'];

// Ambil nama tukang dan periode dari HTML
preg_match('/<strong>Nama:<\/strong>\s*(.*?)<\/p>/', $htmlBody, $namaMatch);
preg_match('/<strong>Bulan:<\/strong>\s*(.*?)<\/p>/', $htmlBody, $bulanMatch);

$namaTukang = isset($namaMatch[1]) ? trim($namaMatch[1]) : 'Tukang';
$periode = isset($bulanMatch[1]) ? trim($bulanMatch[1]) : 'Periode';

// Encode logo PNG ke base64
$logoPath = __DIR__ . '/assets/img/logo.png'; // Ganti dengan nama file yang kamu upload
$logoBase64 = '';
if (file_exists($logoPath)) {
    $imageData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = 'data:image/png;base64,' . $imageData;
}
// Encode tanda tangan direktur ke base64
$ttdPath = __DIR__ . '/assets/img/ttd-digital.png';
$ttdBase64 = '';
if (file_exists($ttdPath)) {
    $ttdData = base64_encode(file_get_contents($ttdPath));
    $ttdBase64 = 'data:image/png;base64,' . $ttdData;
}


$css = "
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .header { border-bottom: 2px solid black; padding-bottom: 10px; margin-bottom: 20px; }
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .text-block {
        max-width: 80%;
    }
    .company-name {
        font-size: 18px; font-weight: bold;
    }
    .company-sub {
        font-size: 12px; margin-top: 5px;
    }
    .logo-right {
        width: 80px;
    }

    h2.title {
        text-align: center;
        margin: 10px 0 0 0;
        font-size: 14px;
        font-weight: bold;
    }

    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 12px; }
    th { background-color: #f2f2f2; text-align: center; }

    .signature {
        margin-top: 50px;
        width: 100%;
        font-size: 12px;
    }
    .signature td {
        vertical-align: top;
        padding-top: 40px;
        text-align: center;
    }
        .header-table, .header-table td {
    border: none !important;
}

</style>
";


// Header perusahaan
$header = '
<div class="header">
    <table width="100%" class="header-table">
        <tr>
            <td style="width: 85%;">
                <div class="company-name">PT. Nawasena Sinergi Gemilang</div>
                <div class="company-sub">
                    Event & Production<br>
                    Jl. Serua Raya No.1, RW.55, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517
                </div>
            </td>
            <td style="width: 15%; text-align: right;">
                <img src="' . $logoBase64 . '" style="width: 80px;">
            </td>
        </tr>
    </table>
</div>

<h2 class="title">SLIP GAJI BULAN ' . strtoupper($periode) . '</h2>
';


// Footer tanda tangan
$footer = '
<table class="signature">
    <tr>
        <td style="text-align:left;">
            Mengetahui,<br><br>
            <img src="' . $ttdBase64 . '" alt="TTD" style="height:60px;"><br>
            <u>Finance</u><br>
        </td>
        <td style="text-align:right;">
            Diterima Oleh,<br><br><br><br>
            <u>' . htmlspecialchars($namaTukang) . '</u>
        </td>
    </tr>
</table>
';

$finalHtml = $css . $header . $htmlBody . $footer;

// Cetak PDF
$dompdf = new Dompdf(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
$dompdf->loadHtml($finalHtml);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$namaFile = 'Slip-Gaji-' . preg_replace('/[^a-zA-Z0-9]/', '_', $namaTukang) . '-' . str_replace(' ', '_', $periode) . '.pdf';
$dompdf->stream($namaFile, ["Attachment" => true]);
exit;
