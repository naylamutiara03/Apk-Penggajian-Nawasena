<?php
include("koneksi.php");

$act = $_GET['act'];

// ==============================
// FUNGSI: TAMBAH ADMIN
// ==============================
if ($_GET['act'] == 'tambah') {
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];
    $password = $_POST['password'];

    // Validasi panjang password
    if (strlen($password) < 8) {
        echo json_encode(["success" => false, "message" => "Password harus terdiri dari minimal 8 karakter."]);
        exit;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $query = mysqli_query($konek, "INSERT INTO admin (username, namalengkap, password) VALUES ('$username', '$namalengkap', '$password')");

    if ($query) {
        $last_id = mysqli_insert_id($konek);
        echo json_encode(["success" => true, "idadmin" => $last_id, "username" => $username, "namalengkap" => $namalengkap]);
    } else {
        echo json_encode(["success" => false]);
    }
    exit;
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