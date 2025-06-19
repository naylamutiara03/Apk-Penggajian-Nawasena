<?php
include 'koneksi.php'; // Make sure this path is correct

// Set content type to JSON for all responses
header('Content-Type: application/json');

$act = isset($_GET['act']) ? $_GET['act'] : '';

if ($act == 'tambah') {
    // Sanitize and validate input
    $jabatan = mysqli_real_escape_string($konek, $_POST['jabatan']);
    $gapok = (int) str_replace('.', '', $_POST['gapok']);
    $tunjangan = (int) str_replace('.', '', $_POST['tunjangan_jabatan']);
    $jenis = mysqli_real_escape_string($konek, $_POST['jenis']);

    if ($jenis == 'karyawan') {
        $total = $gapok + $tunjangan;
    } else {
        $total = $gapok;
        $tunjangan = 0;
    }

    // ✅ Cek apakah sudah ada jabatan dengan jenis yang sama
    $cek = mysqli_query($konek, "SELECT * FROM jabatan WHERE jabatan = '$jabatan' AND jenis = '$jenis'");
    if (mysqli_num_rows($cek) > 0) {
        echo json_encode([
            'success' => false,
            'message' => "Jabatan '$jabatan' untuk jenis '$jenis' sudah ada."
        ]);
        exit;
    }

    // ✅ Eksekusi jika belum ada
    $query = "INSERT INTO jabatan (jabatan, gapok, tunjangan_jabatan, total, jenis) 
              VALUES ('$jabatan', '$gapok', '$tunjangan', '$total', '$jenis')";

    if (mysqli_query($konek, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data jabatan berhasil ditambahkan.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan data jabatan: ' . mysqli_error($konek)]);
    }
    exit();
} elseif ($act == 'edit') {
    // Sanitize and validate input
    $id = (int) $_POST['id'];
    $jabatan = mysqli_real_escape_string($konek, $_POST['jabatan']);
    // Remove dots for currency conversion
    $gapok = (int) str_replace('.', '', $_POST['gapok']);
    $tunjangan = (int) str_replace('.', '', $_POST['tunjangan_jabatan']);
    $jenis = mysqli_real_escape_string($konek, $_POST['jenis']);

    // Recalculate total based on type
    if ($jenis == 'karyawan') {
        $total = $gapok + $tunjangan;
    } else { // tukang
        $total = $gapok; // Gaji per hari is the total for tukang
        $tunjangan = 0; // Ensure tunjangan is 0 for tukang in the database
    }

    $query = "UPDATE jabatan SET 
                jabatan='$jabatan',
                gapok='$gapok',
                tunjangan_jabatan='$tunjangan',
                total='$total',
                jenis='$jenis'
              WHERE id='$id'";
    
    if (mysqli_query($konek, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data jabatan berhasil diupdate.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupdate data jabatan: ' . mysqli_error($konek)]);
    }
    exit();

} elseif ($act == 'delete') {
    $id = (int) $_GET['id']; // Cast to integer for security

    $query = "DELETE FROM jabatan WHERE id='$id'";
    
    if (mysqli_query($konek, $query)) {
        echo json_encode(['success' => true, 'message' => 'Data jabatan berhasil dihapus.', 'id' => $id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data jabatan: ' . mysqli_error($konek)]);
    }
    exit();

} else {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
    exit();
}
?>