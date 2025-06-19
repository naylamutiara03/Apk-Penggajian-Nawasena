<?php
include("koneksi.php");

$act = $_GET['act'];

// ==============================
// FUNGSI: TAMBAH ADMIN
// ==============================
if ($act == 'tambah') {
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];

    $cekUsername = mysqli_query($konek, "SELECT * FROM admin WHERE username = '$username'");
    if (mysqli_num_rows($cekUsername) > 0) {
        echo json_encode(["success" => false, "message" => "Username '$username' sudah terdaftar, gunakan username lain."]);
        exit;
    }

    $query = mysqli_query($konek, "INSERT INTO admin (username, namalengkap) VALUES ('$username', '$namalengkap')");

    if ($query) {
        $last_id = mysqli_insert_id($konek);
        echo json_encode(["success" => true, "message" => "Admin berhasil ditambahkan!", "idadmin" => $last_id]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menambahkan admin!"]);
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

    $cekUsername = mysqli_query($konek, "SELECT * FROM admin WHERE username = '$username' AND idadmin != '$id'");
    if (mysqli_num_rows($cekUsername) > 0) {
        echo json_encode(["success" => false, "message" => "Username '$username' sudah digunakan admin lain."]);
        exit;
    }

    $query = "UPDATE admin SET username='$username', namalengkap='$namalengkap' WHERE idadmin='$id'";

    if (mysqli_query($konek, $query)) {
        echo json_encode(["success" => true, "message" => "Admin berhasil diperbarui!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal memperbarui admin!"]);
    }
    exit;
}

// ==============================
// FUNGSI: HAPUS ADMIN
// ==============================
elseif ($act == 'delete') {
    $id = $_GET['id'];

    $cek = mysqli_query($konek, "SELECT * FROM admin WHERE idadmin='$id'");
    if (mysqli_num_rows($cek) == 0) {
        echo json_encode(["success" => false, "message" => "Admin tidak ditemukan!"]);
        exit;
    }

    mysqli_query($konek, "DELETE FROM admin WHERE idadmin='$id'");
    echo json_encode(["success" => true, "message" => "Admin berhasil dihapus!"]);
    exit;
}
?>
