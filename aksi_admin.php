<?php
include("koneksi.php");

$act = $_GET['act'];

// ==============================
// FUNGSI: TAMBAH ADMIN
// ==============================
if ($act == 'tambah') {
    $username = $_POST['username'] ?? '';
    $namalengkap = $_POST['namalengkap'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validasi password minimal 8 karakter
    if (strlen($password) < 8) {
        echo json_encode(["success" => false, "message" => "Password harus minimal 8 karakter."]);
        exit;
    }

    // Cek apakah username sudah terdaftar
    $cekUsername = mysqli_query($konek, "SELECT * FROM admin WHERE username = '$username'");
    if (mysqli_num_rows($cekUsername) > 0) {
        echo json_encode(["success" => false, "message" => "Username '$username' sudah terdaftar, gunakan username lain."]);
        exit;
    }

    // Hash password sebelum disimpan
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Simpan ke database
    $query = mysqli_query($konek, "INSERT INTO admin (username, namalengkap, password) VALUES ('$username', '$namalengkap', '$passwordHash')");

    if ($query) {
        $last_id = mysqli_insert_id($konek);
        echo json_encode(["success" => true, "message" => "Admin berhasil ditambahkan!", "idadmin" => $last_id]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menambahkan admin: " . mysqli_error($konek)]);
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
    session_start();
    include("koneksi.php");

    $id = $_GET['id'];
    $adminLogin = $_SESSION['username'];
    $roleLogin = $_SESSION['role'];

    // Ambil data admin yang akan dihapus
    $cek = mysqli_query($konek, "SELECT * FROM admin WHERE idadmin='$id'");
    if (mysqli_num_rows($cek) == 0) {
        echo json_encode(["success" => false, "message" => "Admin tidak ditemukan!"]);
        exit;
    }

    $data = mysqli_fetch_assoc($cek);
    $usernameTarget = $data['username'];

    // Cegah jika bukan superadmin dan ingin hapus akun orang lain
    if ($usernameTarget !== $adminLogin && $roleLogin !== 'superadmin') {
        echo json_encode(["success" => false, "message" => "Anda tidak memiliki izin untuk menghapus akun ini!"]);
        exit;
    }

    // Hapus data admin
    mysqli_query($konek, "DELETE FROM admin WHERE idadmin='$id'");
    echo json_encode(["success" => true, "message" => "Admin berhasil dihapus!"]);
    exit;
}

// ==============================
// FUNGSI: HAPUS AKUN SENDIRI (dengan verifikasi password)
// ==============================
elseif ($act == 'delete_self') {
    session_start();
    include("koneksi.php");

    $username = $_SESSION['username'];
    $passwordInput = $_POST['password'];

    $query = mysqli_query($konek, "SELECT * FROM admin WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if (!$data || !password_verify($passwordInput, $data['password'])) {
        echo json_encode(["success" => false, "message" => "Password salah. Akun tidak dihapus."]);
        exit;
    }

    mysqli_query($konek, "DELETE FROM admin WHERE username='$username'");
    session_destroy(); // Auto logout
    echo json_encode(["success" => true, "message" => "Akun Anda berhasil dihapus."]);
    exit;
}


?>