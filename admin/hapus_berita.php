<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

if (!isset($_GET['id'])) {
  header("Location: kelola_berita.php");
  exit;
}

$id = intval($_GET['id']);

// Hapus gambar lama
$stmt = $conn->prepare("SELECT gambar FROM berita WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$berita = $res->fetch_assoc();

if ($berita && $berita['gambar']) {
  $file = '../uploads/' . $berita['gambar'];
  if (file_exists($file)) unlink($file);
}

// Hapus berita
$stmtDel = $conn->prepare("DELETE FROM berita WHERE id = ?");
$stmtDel->bind_param("i", $id);
$stmtDel->execute();

header("Location: kelola_berita.php");
exit;
