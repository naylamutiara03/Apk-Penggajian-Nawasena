<?php
include 'koneksi.php';

$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act == 'tambah') {
    $jenis = $_POST['jenis'];
    $jabatan = $_POST['jabatan'];

    if ($jenis == 'tukang') {
        $gaji_per_hari = $_POST['gaji_per_hari'];

        $query = "INSERT INTO jabatan (jabatan, gaji_per_hari, jenis) 
                  VALUES ('$jabatan', '$gaji_per_hari', '$jenis')";
    } else {
        $gapok = $_POST['gapok'];
        $tunjangan = $_POST['tunjangan_jabatan'];
        $total = $gapok;

        $query = "INSERT INTO jabatan (jabatan, gapok, tunjangan_jabatan, total, jenis) 
                  VALUES ('$jabatan', '$gapok', '$tunjangan', '$total', '$jenis')";
    }

    if (mysqli_query($konek, $query)) {
        header("Location: data_jabatan.php?pesan=berhasil_tambah");
    } else {
        header("Location: data_jabatan.php?pesan=gagal_tambah");
    }

} elseif ($act == 'edit') {
    $id = $_POST['id'];
    $jenis = $_POST['jenis'];
    $jabatan = $_POST['jabatan'];

    if ($jenis == 'tukang') {
        $gaji_per_hari = $_POST['gaji_per_hari'];

        $query = "UPDATE jabatan SET 
                    jabatan='$jabatan',
                    gaji_per_hari='$gaji_per_hari',
                    gapok=NULL,
                    tunjangan_jabatan=NULL,
                    total=NULL,
                    jenis='$jenis'
                  WHERE id='$id'";
    } else {
        $gapok = $_POST['gapok'];
        $tunjangan = $_POST['tunjangan_jabatan'];
        $total = $gapok + $tunjangan;

        $query = "UPDATE jabatan SET 
                    jabatan='$jabatan',
                    gapok='$gapok',
                    tunjangan_jabatan='$tunjangan',
                    total='$total',
                    gaji_per_hari=NULL,
                    jenis='$jenis'
                  WHERE id='$id'";
    }

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
