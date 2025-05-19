<?php
header('Content-Type: application/json');
include("koneksi.php");

if (isset($_GET['nik']) && isset($_GET['table'])) {
    $nik = $_GET['nik'];
    $table = $_GET['table'];
    $exists = false;

    if ($table === 'karyawan') {
        $query = mysqli_query($konek, "SELECT * FROM karyawan WHERE nik='$nik'");
        $exists = mysqli_num_rows($query) > 0;
    } elseif ($table === 'tukang') {
        $query = mysqli_query($konek, "SELECT * FROM tukang_nws WHERE nik='$nik'");
        $exists = mysqli_num_rows($query) > 0;
    }

    echo json_encode(['exists' => $exists, 'table' => $table]);
} else {
    echo json_encode(['error' => 'Parameter nik dan table dibutuhkan.']);
}
?>