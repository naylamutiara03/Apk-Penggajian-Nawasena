<?php
include 'koneksi.php'; // Pastikan file ini berisi koneksi ke database

$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act == 'tambah') {
    $jabatan = $_POST['jabatan'];
    $gapok = $_POST['gapok'];
    $tunjangan = $_POST['tunjangan_jabatan'];
    $jenis = $_POST['jenis'];
    $total = $gapok + $tunjangan;

    $query = "INSERT INTO jabatan (jabatan, gapok, tunjangan_jabatan, total, jenis) 
              VALUES ('$jabatan', '$gapok', '$tunjangan', '$total', '$jenis')";
    if (mysqli_query($konek, $query)) {
        header("Location: data_jabatan.php?pesan=berhasil_tambah");
    } else {
        header("Location: data_jabatan.php?pesan=gagal_tambah");
    }

} elseif ($act == 'edit') {
    $id = $_POST['id'];
    $jabatan = $_POST['jabatan'];
    $gapok = $_POST['gapok'];
    $tunjangan = $_POST['tunjangan_jabatan'];
    $jenis = $_POST['jenis'];
    $total = $gapok + $tunjangan;

    $query = "UPDATE jabatan SET 
                jabatan='$jabatan',
                gapok='$gapok',
                tunjangan_jabatan='$tunjangan',
                total='$total',
                jenis='$jenis'
              WHERE id='$id'";
    if (mysqli_query($konek, $query)) {
        header("Location: data_jabatan.php?pesan=berhasil_edit");
    } else {
        header("Location: data_jabatan.php?pesan=gagal_edit");
    }

} elseif ($act == 'delete') {
    $id = $_GET['id'];

    $query = "DELETE FROM jabatan WHERE id='$id'";
    if (mysqli_query($konek, $query)) {
        header("Location: data_jabatan.php?pesan=berhasil_hapus");
    } else {
        header("Location: data_jabatan.php?pesan=gagal_hapus");
    }

} else {
    header("Location: data_jabatan.php?pesan=aksi_tidak_valid");
}
?>