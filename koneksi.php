<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$db = "penggajian";

$konek = mysqli_connect($host, $user, $pass, $db);

// Periksa koneksi
if (!$konek) {
    die("<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4'>
            <p class='font-bold'>ERROR!</p>
            <p>Koneksi database gagal: " . mysqli_connect_error() . "</p>
         </div>");
}
?>
