<?php
require_once 'db.php';

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("ID organisasi tidak valid.");
}

$id = intval($_GET['id']);

// Ambil data organisasi
$stmt = $conn->prepare("SELECT * FROM organisasi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$org = $result->fetch_assoc();

if (!$org) {
  die("Organisasi tidak ditemukan.");
}

// Ambil dokumentasi
$dokumentasi = $conn->prepare("SELECT * FROM organisasi_foto WHERE organisasi_id = ?");
$dokumentasi->bind_param("i", $id);
$dokumentasi->execute();
$dokResult = $dokumentasi->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($org['nama']) ?> - Detail Organisasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <a href="index.php" class="btn btn-secondary mb-3">&larr; Kembali ke Beranda</a>

  <h2><?= htmlspecialchars($org['nama']) ?></h2>
  <p><strong>Tahun Dibangun:</strong> <?= htmlspecialchars($org['tahun_dibangun']) ?></p>
  <p><?= nl2br(htmlspecialchars($org['deskripsi'])) ?></p>

  <img src="uploads/<?= htmlspecialchars($org['foto_utama']) ?>" class="img-fluid rounded shadow-sm my-3" style="max-height: 300px; object-fit:cover;" alt="Foto Utama Organisasi">

  <h4 class="mt-4">Dokumentasi Kegiatan</h4>
  <div class="row g-3">
    <?php if ($dokResult->num_rows > 0): ?>
      <?php while ($foto = $dokResult->fetch_assoc()): ?>
        <div class="col-md-4">
          <img src="uploads/<?= htmlspecialchars($foto['filename']) ?>" class="img-fluid rounded shadow-sm" alt="Dokumentasi">
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">Belum ada dokumentasi tambahan.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
