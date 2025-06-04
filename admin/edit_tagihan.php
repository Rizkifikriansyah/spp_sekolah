<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}
require_once '../db.php';

$id = $_GET['id'] ?? null;
$siswa_id = $_GET['siswa_id'] ?? null;
if (!$id || !$siswa_id) die("Data tidak valid.");

$stmt = $conn->prepare("SELECT * FROM tagihan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tagihan = $stmt->get_result()->fetch_assoc();

if (!$tagihan) die("Tagihan tidak ditemukan.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $semester = $_POST['semester'];
  $jumlah = $_POST['jumlah'];
  $tanggal = $_POST['tanggal'];
  $keterangan = $_POST['keterangan'];

  $stmt = $conn->prepare("UPDATE tagihan SET semester=?, jumlah=?, tanggal=?, keterangan=? WHERE id=?");
  $stmt->bind_param("sissi", $semester, $jumlah, $tanggal, $keterangan, $id);
  $stmt->execute();

  header("Location: kelola_tagihan.php?siswa_id=$siswa_id");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Tagihan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
  <h2>Edit Tagihan</h2>
  <form method="POST">
    <div class="mb-3">
      <label for="semester" class="form-label">Semester</label>
      <input type="text" class="form-control" name="semester" value="<?= htmlspecialchars($tagihan['semester']) ?>" required>
    </div>
    <div class="mb-3">
      <label for="jumlah" class="form-label">Jumlah (Rp)</label>
      <input type="number" class="form-control" name="jumlah" value="<?= $tagihan['jumlah'] ?>" required>
    </div>
    <div class="mb-3">
      <label for="tanggal" class="form-label">Tanggal Tagihan</label>
      <input type="date" class="form-control" name="tanggal" value="<?= $tagihan['tanggal'] ?>" required>
    </div>
    <div class="mb-3">
      <label for="keterangan" class="form-label">Keterangan</label>
      <textarea class="form-control" name="keterangan"><?= htmlspecialchars($tagihan['keterangan']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="kelola_tagihan.php?siswa_id=<?= $siswa_id ?>" class="btn btn-secondary">Batal</a>
  </form>
</div>
</body>
</html>
