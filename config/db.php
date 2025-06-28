<?php
$koneksi = mysqli_connect("localhost", "root", "", "spk_topsis");
if (!$koneksi) { die("Koneksi gagal: " . mysqli_connect_error()); }
?>