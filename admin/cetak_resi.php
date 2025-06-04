<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
  exit('ID tidak ditemukan.');
}

$id = (int) $_GET['id'];
$stmt = $conn->prepare("SELECT p.*, s.nama, s.kelas FROM pembayaran p JOIN siswa s ON p.siswa_id = s.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
  exit('Data tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Resi Pembayaran</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: #f9f9f9;
    }

    .resi {
      position: relative;
      border: 1px solid #000;
      padding: 40px;
      width: 700px;
      margin: auto;
      background-color: white;
    }

    .resi::before {
      content: "";
      background-image: url('../img/logo.jpeg'); /* Perbaikan path */
      background-repeat: no-repeat;
      background-position: center;
      background-size: 200px;
      opacity: 0.07;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }

    .content {
      position: relative;
      z-index: 1;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .field {
      margin: 10px 0;
      font-size: 16px;
    }

    .signature-section {
      display: flex;
      justify-content: space-between;
      margin-top: 60px;
      font-size: 14px;
    }

    .signature-box {
      text-align: center;
      width: 45%;
    }

    .signature-box .space {
      margin-top: 60px;
      border-top: 1px solid #000;
      width: 100%;
    }

    @media print {
      body {
        background: none;
      }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="resi">
    <div class="content">
      <h2>Resi Pembayaran SPP</h2>
      <div class="field"><strong>Nama:</strong> <?= htmlspecialchars($data['nama']) ?></div>
      <div class="field"><strong>Kelas:</strong> <?= htmlspecialchars($data['kelas']) ?></div>
      <div class="field"><strong>Tanggal Bayar:</strong> <?= date('d M Y', strtotime($data['tanggal_pembayaran'])) ?></div>
      <div class="field"><strong>Jumlah:</strong> Rp<?= number_format($data['jumlah'], 0, ',', '.') ?></div>
      <div class="field"><strong>Status:</strong> <?= htmlspecialchars($data['status']) ?></div>

      <p style="margin-top: 30px;">Terima kasih atas pembayaran Anda.</p>

      <div class="signature-section">
        <div class="">
          <div class="space"></div>
        </div>
        <div class="signature-box">
          Bendahara Sekolah<br><br>
          <div class="space">Tanda Tangan</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
