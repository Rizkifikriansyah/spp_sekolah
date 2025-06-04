<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

require_once '../db.php';

$user_id = $_SESSION['user']['id'];

// Ambil id siswa dari tabel siswa
$stmt = $conn->prepare("SELECT id FROM siswa WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$siswa = $result->fetch_assoc();

if (!$siswa) {
  echo "<div class='alert alert-danger'>Data siswa tidak ditemukan!</div>";
  exit;
}
// Ambil semester yang belum lunas (tunggakan)
$semester_query = "
  SELECT 
    t.semester,
    SUM(t.jumlah) AS total_tagihan,
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

$stmt_semester = $conn->prepare($semester_query);
$stmt_semester->bind_param("i", $siswa['id']);
$stmt_semester->execute();
$result_semester = $stmt_semester->get_result();

$semester_belum_lunas = [];
while ($row = $result_semester->fetch_assoc()) {
  $semester_belum_lunas[] = $row['semester'];
}

// Inisialisasi variabel pesan error/success
$error = "";
$success = "";

// Proses jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jumlah = $_POST['jumlah'];
  $tanggal = $_POST['tanggal'];
  $semester = $_POST['semester'] ?? '';
  $resi = $_FILES['bukti_resi'];

  // Validasi sederhana
  if ($jumlah <= 0) {
    $error = "Jumlah pembayaran harus lebih dari 0.";
  } elseif (empty($tanggal)) {
    $error = "Tanggal pembayaran harus diisi.";
  } elseif (empty($semester)) {
    $error = "Semester harus dipilih.";
  } elseif ($resi['error'] !== 0) {
    $error = "File bukti resi harus diupload.";
  } elseif (!in_array($resi['type'], ['image/jpeg', 'image/png'])) {
    $error = "File bukti resi harus berupa JPG atau PNG.";
  } else {
    // Upload file
    $ext = pathinfo($resi['name'], PATHINFO_EXTENSION);
    $namaFile = uniqid() . '.' . $ext;
    $target = '../uploads/' . $namaFile;

    if (move_uploaded_file($resi['tmp_name'], $target)) {
      // Simpan data pembayaran
      $stmtBayar = $conn->prepare("INSERT INTO pembayaran (siswa_id, jumlah, tanggal_pembayaran, bukti_resi, status, semester) VALUES (?, ?, ?, ?, 'menunggu', ?)");
      $stmtBayar->bind_param("idsss", $siswa['id'], $jumlah, $tanggal, $namaFile, $semester);
      if ($stmtBayar->execute()) {
        $success = "Pembayaran berhasil dikirim. Menunggu konfirmasi admin.";
      } else {
        $error = "Gagal menyimpan data pembayaran.";
      }
    } else {
      $error = "Gagal mengupload file bukti resi.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Form Pembayaran SPP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
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
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="form_pembayaran.php">Form Pembayaran</a>
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

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
          <h4>Form Pembayaran SPP</h4>
        </div>
        <div class="card-body">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>

          <?php if (count($semester_belum_lunas) > 0): ?>
            <form method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label">Jumlah Pembayaran (Rp)</label>
                <input type="number" name="jumlah" class="form-control" min="1" required value="<?= isset($_POST['jumlah']) ? htmlspecialchars($_POST['jumlah']) : '' ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Tanggal Pembayaran</label>
                <input type="date" name="tanggal" class="form-control" required value="<?= isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : '' ?>">
              </div>
              <div class="mb-3">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" required>
                  <option value="">-- Pilih Semester --</option>
                  <?php foreach ($semester_belum_lunas as $sem): ?>
                    <option value="<?= $sem ?>" <?= (isset($_POST['semester']) && $_POST['semester'] == $sem) ? 'selected' : '' ?>>
                      Semester <?= $sem ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Bukti Resi (JPG / PNG)</label>
                <input type="file" name="bukti_resi" class="form-control" accept="image/jpeg,image/png" required>
              </div>
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Kirim Pembayaran</button>
                <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
              </div>
            </form>
          <?php else: ?>
            <div class="alert alert-success text-center">
              Semua tagihan telah lunas. Tidak ada pembayaran yang perlu dilakukan 🎉
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
