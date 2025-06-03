<?php
include("koneksi.php");

if (isset($_GET['act'])) {
    $action = $_GET['act'];

    // Fungsi untuk menambah tukang
    if ($action == 'tambah') {
        $nik = mysqli_real_escape_string($konek, $_POST['nik']);
        $nama_tukang = mysqli_real_escape_string($konek, $_POST['nama_tukang']);
        $jenis_kelamin = mysqli_real_escape_string($konek, $_POST['jenis_kelamin']);
        $jabatan = mysqli_real_escape_string($konek, $_POST['id_jabatan']);
        $tgl_masuk = mysqli_real_escape_string($konek, $_POST['tgl_masuk']);
        $status = mysqli_real_escape_string($konek, $_POST['status']);

        // Validasi input
        if (empty($nik) || empty($nama_tukang) || empty($jenis_kelamin) || empty($jabatan) || empty($tgl_masuk) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
            exit;
        }

        // Query untuk menambah tukang
        $query = "INSERT INTO tukang_nws (nik, nama_tukang, jenis_kelamin, id_jabatan, tgl_masuk, status) VALUES ('$nik', '$nama_tukang', '$jenis_kelamin', '$jabatan', '$tgl_masuk', '$status')";

        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Tukang berhasil ditambahkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan tukang: ' . mysqli_error($konek)]);
        }
    }

    // Fungsi untuk menghapus tukang
    if ($action == 'delete') {
        $id = $_GET['id'];

        // Validasi ID
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            exit;
        }

        // Query untuk menghapus tukang
        $query = "DELETE FROM tukang_nws WHERE id='$id'";
        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Tukang berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus tukang: ' . mysqli_error($konek)]);
        }
    }

    // Fungsi untuk memperbarui tukang
    if ($action == 'update') {
        $id = mysqli_real_escape_string($konek, $_POST['id']);
        $nik = mysqli_real_escape_string($konek, $_POST['nik']);
        $nama_tukang = mysqli_real_escape_string($konek, $_POST['nama_tukang']);
        $jenis_kelamin = mysqli_real_escape_string($konek, $_POST['jenis_kelamin']);
        $jabatan = mysqli_real_escape_string($konek, $_POST['id_jabatan']);
        $tgl_masuk = mysqli_real_escape_string($konek, $_POST['tgl_masuk']);
        $status = mysqli_real_escape_string($konek, $_POST['status']);

        // Validasi input
        if (empty($id) || empty($nik) || empty($nama_tukang) || empty($jenis_kelamin) || empty($jabatan) || empty($tgl_masuk) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
            exit;
        }

        // Query untuk memperbarui tukang
        $query = "UPDATE tukang_nws SET nik='$nik', nama_tukang='$nama_tukang', jenis_kelamin='$jenis_kelamin', id_jabatan='$jabatan', tgl_masuk='$tgl_masuk', status='$status' WHERE id='$id'";

        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Tukang berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui tukang: ' . mysqli_error($konek)]);
        }
    }
}
?>