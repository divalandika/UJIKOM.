<?php
$servername = "localhost";  // Nama host, biasanya 'localhost'
$username = "root";         // Nama pengguna database
$password = "";             // Kata sandi database
$dbname = "webgalleryfoto";  // Nama database yang digunakan

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Memeriksa koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
