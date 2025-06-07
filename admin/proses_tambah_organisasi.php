<!-- proses_tambah_organisasi.php -->
<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses simpan data
    $fotoUtama = $_FILES['foto_utama']['name'];
    $tmpUtama = $_FILES['foto_utama']['tmp_name'];
    move_uploaded_file($tmpUtama, "../uploads/$fotoUtama");

    $stmt = $conn->prepare("INSERT INTO organisasi (nama, deskripsi, tahun_dibangun, foto_utama) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $_POST['nama'], $_POST['deskripsi'], $_POST['tahun_dibangun'], $fotoUtama);
    $stmt->execute();
    $orgId = $conn->insert_id;

    foreach ($_FILES['dokumentasi']['tmp_name'] as $key => $tmp_name) {
        $filename = $_FILES['dokumentasi']['name'][$key];
        move_uploaded_file($tmp_name, "../uploads/$filename");
        $conn->query("INSERT INTO organisasi_foto (organisasi_id, filename) VALUES ($orgId, '$filename')");
    }

    header("Location: kelola_organisasi.php");
    exit;
}
?>
