<?php
include("koneksi.php");

$act = $_GET['act'];

// ==============================
// FUNGSI: TAMBAH ADMIN
// ==============================
if ($act == 'tambah') {
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    $query = "INSERT INTO admin (username, namalengkap, password) VALUES ('$username', '$namalengkap', '$password')";
    mysqli_query($konek, $query);

    echo "<script>alert('Admin berhasil ditambahkan!'); window.location='data_admin.php';</script>";
}

// ==============================
// FUNGSI: UPDATE ADMIN
// ==============================
elseif ($act == 'update') {
    $id = $_POST['idadmin'];
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE admin SET username='$username', namalengkap='$namalengkap', password='$password' WHERE idadmin='$id'";
    } else {
        $query = "UPDATE admin SET username='$username', namalengkap='$namalengkap' WHERE idadmin='$id'";
    }

    mysqli_query($konek, $query);

    echo "<script>alert('Admin berhasil diperbarui!'); window.location='data_admin.php';</script>";
}

// ==============================
// FUNGSI: HAPUS ADMIN
// ==============================
elseif ($act == 'delete') {
    $id = $_GET['id'];

    $cek = mysqli_query($konek, "SELECT * FROM admin WHERE idadmin='$id'");
    if (mysqli_num_rows($cek) == 0) {
        echo "<script>alert('Admin tidak ditemukan!'); window.location='data_admin.php';</script>";
        exit;
    }

    mysqli_query($konek, "DELETE FROM admin WHERE idadmin='$id'");
    echo "<script>alert('Admin berhasil dihapus!'); window.location='data_admin.php';</script>";
}
?>

buat ketika login pakai admin baru yang ditambahkan itu bisa