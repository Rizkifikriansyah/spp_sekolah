<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siswa') {
  header("Location: ../login.php");
  exit;
}

require_once '../database.php';
include '../includes/header.php';

$user_id = $_SESSION['user']['id'];

// Ambil siswa_id
$stmt = $conn->prepare("SELECT id FROM siswa WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$siswa = $res->fetch_assoc();

if (!$siswa) {
  echo "<div class='alert alert-danger'>Data siswa tidak ditemukan!</div>";
  exit;
}

// Ambil data pembayaran siswa
$stmt2 = $conn->prepare("SELECT * FROM pembayaran WHERE siswa_id = ? ORDER BY tanggal_pembayaran DESC");
$stmt2->bind_param("i", $siswa['id']);
$stmt2->execute();
$result = $stmt2->get_result();
?>

<div class="container mt-4">
  <h2>Riwayat Pembayaran SPP</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Tanggal Pembayaran</th>
        <th>Jumlah</th>
        <th>Status</th>
        <th>Bukti Resi</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['tanggal_pembayaran'] ?></td>
        <td>Rp<?= number_format($row['jumlah'], 0, ',', '.') ?></td>
        <td><?= ucfirst($row['status']) ?></td>
        <td>
          <a href="../uploads/<?= $row['bukti_resi'] ?>" target="_blank">Lihat</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
</div>

<?php include '../includes/footer.php'; ?>
