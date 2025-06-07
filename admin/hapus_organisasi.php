<?php
require_once '../db.php';
session_start();

// Cek login admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
  // Hapus data organisasi berdasarkan ID
  $stmt = $conn->prepare("DELETE FROM organisasi WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
}

header("Location: kelola_organisasi.php");
exit;
