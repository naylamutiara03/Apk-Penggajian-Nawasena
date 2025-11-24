<?php
session_start();
include("koneksi.php");

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

if (empty($bulan) || empty($tahun)) {
    die("Filter tidak valid. Pilih bulan dan tahun.");
}

// Nama bulan
$bulan_arr = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$bulan_nama = $bulan_arr[$bulan];

// Header Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Lembur_".$bulan_nama."_".$tahun.".xls");

echo "<h3 style='margin-bottom:5px;'>Laporan Shift Tukang - $bulan_nama $tahun</h3>";

$getTukang = mysqli_query($konek, "SELECT id, nama_tukang FROM tukang_nws ORDER BY nama_tukang ASC");

// LOOP 1–5 MINGGU
for ($minggu = 1; $minggu <= 5; $minggu++) {

    echo "<h4 style='margin:0;'>Minggu ke-$minggu</h4>";

    echo "
    <table border='1' cellpadding='5' cellspacing='0' style='margin-bottom:20px;'>
        <tr style='background:#d4d4d4;font-weight:bold;'>
            <th>No</th>
            <th>Nama Tukang</th>
            <th>Range Tanggal</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Harga Shift (Rp)</th>
            <th>Total Uang (Rp)</th>
        </tr>
    ";

    $no = 1;
    $dataKosongSeminggu = true;

    mysqli_data_seek($getTukang, 0); // Reset pointer hasil SELECT

    while ($t = mysqli_fetch_assoc($getTukang)) {
        $id_tukang = $t['id'];
        $nama_tukang = $t['nama_tukang'];

        // Ambil range minggu tukang
        $qr = mysqli_query($konek, "
            SELECT MIN(tgl_lembur) AS tmin, MAX(tgl_lembur) AS tmax
            FROM lembur_tkg 
            WHERE id_tukang='$id_tukang' 
              AND minggu_ke='$minggu'
              AND MONTH(tgl_lembur)='$bulan'
              AND YEAR(tgl_lembur)='$tahun'
        ");
        $r = mysqli_fetch_assoc($qr);

        if ($r['tmin'] != null) {
            $dataKosongSeminggu = false;
            $range_tgl = date('d', strtotime($r['tmin'])) . "-" . date('d', strtotime($r['tmax'])) . " $bulan_nama $tahun";
        } else {
            $range_tgl = "-";
        }

        // SUM total minggu
        $qt = mysqli_query($konek, "
            SELECT SUM(harga_lembur) AS total_minggu
            FROM lembur_tkg 
            WHERE id_tukang='$id_tukang' 
              AND minggu_ke='$minggu'
              AND MONTH(tgl_lembur)='$bulan'
              AND YEAR(tgl_lembur)='$tahun'
        ");
        $rt = mysqli_fetch_assoc($qt);
        $total_minggu = $rt['total_minggu'] ?? 0;

        // DETAIL SHIFT
        $qd = mysqli_query($konek, "
            SELECT tgl_lembur, detail_shifts, harga_lembur
            FROM lembur_tkg 
            WHERE id_tukang='$id_tukang' 
              AND minggu_ke='$minggu'
              AND MONTH(tgl_lembur)='$bulan'
              AND YEAR(tgl_lembur)='$tahun'
            ORDER BY tgl_lembur ASC
        ");

        if (mysqli_num_rows($qd) == 0) {
            // Baris kosong jika tidak ada lembur minggu ini
            echo "<tr>
                    <td>$no</td>
                    <td>$nama_tukang</td>
                    <td>$range_tgl</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td><b>".number_format($total_minggu, 0, ',', '.')."</b></td>
                  </tr>";
            $no++;
            continue;
        }

        $firstRow = true;
        while ($d = mysqli_fetch_assoc($qd)) {
            $shift_txt = "-";
            if (!empty($d['detail_shifts'])) {
                $js = json_decode($d['detail_shifts'], true);
                if (is_array($js)) {
                    $arr = [];
                    foreach ($js as $sh) {
                        $arr[] = isset($sh['multiplier']) && $sh['multiplier'] < 1 ?
                                 $sh['shift']." (0.5)" :
                                 $sh['shift'];
                    }
                    $shift_txt = implode(", ", $arr);
                }
            }

            echo "<tr>
                    <td>$no</td>
                    <td>$nama_tukang</td>
                    <td>$range_tgl</td>
                    <td>".date('d-m-Y', strtotime($d['tgl_lembur']))."</td>
                    <td>$shift_txt</td>
                    <td>".number_format($d['harga_lembur'], 0, ',', '.')."</td>";

            if ($firstRow) {
                echo "<td rowspan='".mysqli_num_rows($qd)."'><b>".number_format($total_minggu, 0, ',', '.')."</b></td>";
                $firstRow = false;
            }

            echo "</tr>";
        }
        $no++;
    }

    if ($dataKosongSeminggu) {
        echo "<tr><td colspan='7' align='center'><i>Tidak ada data shift minggu ini</i></td></tr>";
    }

    echo "</table>";
}

mysqli_close($konek);
?>
