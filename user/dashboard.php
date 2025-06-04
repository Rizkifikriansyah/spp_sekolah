<?php
include '../db.php';
session_start();

// Validasi session login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user']['id'];

// Ambil id siswa dari tabel siswa
$stmt = $conn->prepare("SELECT id FROM siswa WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
  echo "Data siswa tidak ditemukan.";
  exit;
}

$siswa_id = $siswa['id'];
$spp_per_semester = 500000;

// -----------------------------
// Ambil TUNGGAKAN PER SEMESTER
// -----------------------------
$tunggakan_query = "
SELECT 
  t.semester,
  MAX(t.jumlah) AS total_tagihan,  -- Ambil 1 nilai tagihan saja
  IFNULL(SUM(p.jumlah), 0) AS total_dibayar
FROM tagihan t
LEFT JOIN pembayaran p 
  ON p.semester = t.semester 
  AND p.siswa_id = t.siswa_id 
  AND p.status = 'disetujui'
WHERE t.siswa_id = ?
GROUP BY t.semester
HAVING total_tagihan > total_dibayar
ORDER BY t.semester

";

$stmt_tunggakan = $conn->prepare($tunggakan_query);
$stmt_tunggakan->bind_param("i", $siswa_id);
$stmt_tunggakan->execute();
$tunggakan_result = $stmt_tunggakan->get_result();

// -----------------------------
// Ambil RIWAYAT PEMBAYARAN TERAKHIR
// -----------------------------
$riwayat_query = "
  SELECT semester, tanggal_pembayaran, jumlah, status 
  FROM pembayaran 
  WHERE siswa_id = ? 
  ORDER BY tanggal_pembayaran DESC 
  LIMIT 5
";

$stmt_riwayat = $conn->prepare($riwayat_query);
$stmt_riwayat->bind_param("i", $siswa_id);
$stmt_riwayat->execute();
$riwayat_result = $stmt_riwayat->get_result();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
      <img src="../img/logo.jpeg" alt="Logo Sekolah" width="40" height="40" class="me-2 rounded-circle" />
      SMAN 1 AMBALAWI
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="form_pembayaran.php">Form Pembayaran</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= htmlspecialchars($_SESSION['user']['username']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Konten utama -->
<div class="container mt-4">
  <h2>Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']) ?></h2>

  <h4 class="mt-4">Tunggakan Semester</h4>
  <?php if ($tunggakan_result->num_rows > 0): ?>
    <table class="table table-bordered table-hover">
      <thead class="table-danger">
        <tr>
          <th>Semester</th>
          <th>Jumlah Tagihan</th>
          <th>Total Dibayar</th>
          <th>Sisa Tunggakan</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $total_sisa = 0;
        while ($row = $tunggakan_result->fetch_assoc()): 
          $sisa = $row['total_tagihan'] - $row['total_dibayar'];
          $total_sisa += $sisa;
        ?>
        <tr>
          <td><?= htmlspecialchars($row['semester']) ?></td>
          <td>Rp<?= number_format($row['total_tagihan'], 0, ',', '.') ?></td>
          <td>Rp<?= number_format($row['total_dibayar'], 0, ',', '.') ?></td>
          <td>Rp<?= number_format($sisa, 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr class="table-warning">
          <th colspan="3">Total Tunggakan</th>
          <th>Rp<?= number_format($total_sisa, 0, ',', '.') ?></th>
        </tr>
      </tfoot>
    </table>
  <?php else: ?>
    <p class="text-success">Tidak ada tunggakan. Semua pembayaran telah lunas 🎉</p>
  <?php endif; ?>

<h4 class="mt-5">Riwayat Pembayaran SPP Terakhir</h4>
<?php if ($riwayat_result->num_rows > 0): ?>
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>Semester</th>
        <th>Tanggal Pembayaran</th>
        <th>Jumlah</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $riwayat_result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['semester']) ?></td>
        <td><?= htmlspecialchars($row['tanggal_pembayaran']) ?></td>
        <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
        <td>
          <?php 
            if ($row['status'] === 'disetujui') {
              echo '<span class="badge bg-success">Disetujui</span>';
            } elseif ($row['status'] === 'pending') {
              echo '<span class="badge bg-warning text-dark">Pending</span>';
            } else {
              echo '<span class="badge bg-secondary">'.htmlspecialchars($row['status']).'</span>';
            }
          ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>Belum ada riwayat pembayaran.</p>
<?php endif; ?>


  <a href="form_pembayaran.php" class="btn btn-primary mt-3">Bayar SPP</a>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
