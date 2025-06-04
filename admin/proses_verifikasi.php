<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
  header("Location: verifikasi_pembayaran.php");
  exit;
}

$id = intval($_GET['id']);
$aksi = $_GET['aksi'];

if ($aksi === 'setujui') {
  $status = 'disetujui';
} elseif ($aksi === 'tolak') {
  $status = 'ditolak';
} else {
  header("Location: verifikasi_pembayaran.php");
  exit;
}

$stmt = $conn->prepare("UPDATE pembayaran SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: verifikasi_pembayaran.php");
exit;
