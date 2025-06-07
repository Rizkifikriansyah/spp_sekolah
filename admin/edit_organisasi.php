<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: ../login.php");
  exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
  header("Location: kelola_organisasi.php");
  exit;
}

$stmt = $conn->prepare("SELECT * FROM organisasi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$org = $result->fetch_assoc();

if (!$org) {
  header("Location: kelola_organisasi.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Organisasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Edit Organisasi</h2>
  <form action="proses_edit_organisasi.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $org['id'] ?>">
    
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Organisasi</label>
      <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($org['nama']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="deskripsi" class="form-label">Deskripsi</label>
      <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($org['deskripsi']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="tahun_dibangun" class="form-label">Tahun Dibangun</label>
      <input type="number" name="tahun_dibangun" id="tahun_dibangun" class="form-control" value="<?= htmlspecialchars($org['tahun_dibangun']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="foto_utama" class="form-label">Foto Utama (biarkan kosong jika tidak diubah)</label>
      <input type="file" name="foto_utama" id="foto_utama" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    <a href="kelola_organisasi.php" class="btn btn-secondary">Kembali</a>
  </form>
</div>
</body>
</html>
