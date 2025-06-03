<?php
include("koneksi.php"); // File koneksi

if (isset($_GET['act'])) {
    $action = $_GET['act'];

    // Fungsi untuk menambah karyawan
    if ($action == 'tambah') {
        $nik = mysqli_real_escape_string($konek, $_POST['nik']);
        $nama_karyawan = mysqli_real_escape_string($konek, $_POST['nama_karyawan']);
        $jenis_kelamin = mysqli_real_escape_string($konek, $_POST['jenis_kelamin']);
        $jabatan = mysqli_real_escape_string($konek, $_POST['id_jabatan']);
        $tgl_masuk = mysqli_real_escape_string($konek, $_POST['tgl_masuk']);
        $status = mysqli_real_escape_string($konek, $_POST['status']);

        // Validasi input
        if (empty($nik) || empty($nama_karyawan) || empty($jenis_kelamin) || empty($jabatan) || empty($tgl_masuk) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
            exit;
        }

        // Query untuk menambah karyawan
        $query = "INSERT INTO karyawan (nik, nama_karyawan, jenis_kelamin, id_jabatan, tgl_masuk, status) VALUES ('$nik', '$nama_karyawan', '$jenis_kelamin', '$jabatan', '$tgl_masuk', '$status')";

        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Karyawan berhasil ditambahkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan karyawan: ' . mysqli_error($konek)]);
        }
    }

    // Fungsi untuk menghapus karyawan
    if ($action == 'delete') {
        $id = $_GET['id'];

        // Validasi ID
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID tidak valid.']);
            exit;
        }

        // Query untuk menghapus karyawan
        $query = "DELETE FROM karyawan WHERE id='$id'";
        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Karyawan berhasil dihapus.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus karyawan: ' . mysqli_error($konek)]);
        }
    }

    // Fungsi untuk memperbarui karyawan
    if ($action == 'update') {
        $id = mysqli_real_escape_string($konek, $_POST['id']);
        $nik = mysqli_real_escape_string($konek, $_POST['nik']);
        $nama_karyawan = mysqli_real_escape_string($konek, $_POST['nama_karyawan']);
        $jenis_kelamin = mysqli_real_escape_string($konek, $_POST['jenis_kelamin']);
        $jabatan = mysqli_real_escape_string($konek, $_POST['id_jabatan']);
        $tgl_masuk = mysqli_real_escape_string($konek, $_POST['tgl_masuk']);
        $status = mysqli_real_escape_string($konek, $_POST['status']);

        // Validasi input
        if (empty($id) || empty($nik) || empty($nama_karyawan) || empty($jenis_kelamin) || empty($jabatan) || empty($tgl_masuk) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Semua field harus diisi.']);
            exit;
        }

        // Query untuk memperbarui karyawan
        $query = "UPDATE karyawan SET nik='$nik', nama_karyawan='$nama_karyawan', jenis_kelamin='$jenis_kelamin', id_jabatan='$jabatan', tgl_masuk='$tgl_masuk', status='$status' WHERE id='$id'";

        if (mysqli_query($konek, $query)) {
            echo json_encode(['success' => true, 'message' => 'Karyawan berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui karyawan: ' . mysqli_error($konek)]);
        }
    }
}
?>