<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

if (!isset($_GET['id'])) {
  header("Location: kelola_siswa.php");
  exit;
}

$id = $_GET['id'];

// Ambil user_id dari siswa
$stmt = $conn->prepare("SELECT user_id FROM siswa WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if ($siswa) {
  $user_id = $siswa['user_id'];
// 1. Hapus tagihan siswa
$stmtHapusTagihan = $conn->prepare("DELETE FROM tagihan WHERE siswa_id = ?");
$stmtHapusTagihan->bind_param("i", $id);
$stmtHapusTagihan->execute();

// 2. Hapus data siswa
$stmtDeleteSiswa = $conn->prepare("DELETE FROM siswa WHERE id = ?");
$stmtDeleteSiswa->bind_param("i", $id);
$stmtDeleteSiswa->execute();

// 3. Hapus user terkait
$stmtDeleteUser = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmtDeleteUser->bind_param("i", $user_id);
$stmtDeleteUser->execute();

}

header("Location: kelola_siswa.php");
exit;
