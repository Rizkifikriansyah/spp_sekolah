<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

$id = $_GET['id'] ?? null;
$siswa_id = $_GET['siswa_id'] ?? null;
if (!$id || !$siswa_id) {
  die("Data tidak lengkap.");
}

$stmt = $conn->prepare("DELETE FROM tagihan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: kelola_tagihan.php?siswa_id=$siswa_id");
exit;
