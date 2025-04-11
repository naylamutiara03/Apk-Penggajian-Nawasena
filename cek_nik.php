<?php
include("koneksi.php");

if (isset($_GET['nik'])) {
    $nik = $_GET['nik'];
    $query = mysqli_query($konek, "SELECT * FROM karyawan WHERE nik='$nik'");
    $exists = mysqli_num_rows($query) > 0;

    echo json_encode(['exists' => $exists]);
}
?>