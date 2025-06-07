<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$id = $_POST['id'];
$nama = $_POST['nama'];
$deskripsi = $_POST['deskripsi'];
$tahun = $_POST['tahun_dibangun'];

// Update tanpa ganti gambar
if ($_FILES['foto_utama']['error'] === 4) {
  $stmt = $conn->prepare("UPDATE organisasi SET nama = ?, deskripsi = ?, tahun_dibangun = ? WHERE id = ?");
  $stmt->bind_param("ssii", $nama, $deskripsi, $tahun, $id);
} else {
  // Upload gambar baru
  $foto = $_FILES['foto_utama']['name'];
  $tmp = $_FILES['foto_utama']['tmp_name'];
  $target = "../uploads/" . $foto;
  move_uploaded_file($tmp, $target);

  $stmt = $conn->prepare("UPDATE organisasi SET nama = ?, deskripsi = ?, tahun_dibangun = ?, foto_utama = ? WHERE id = ?");
  $stmt->bind_param("ssisi", $nama, $deskripsi, $tahun, $foto, $id);
}

$stmt->execute();
header("Location: kelola_organisasi.php");
exit;
