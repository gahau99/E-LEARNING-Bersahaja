<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa menambah kelas.");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kode_kelas = mysqli_real_escape_string($conn, $_POST['kode']);

    // Tentukan id_guru
    if ($_SESSION['role'] == 'guru') {
        $id_guru = $_SESSION['id']; // otomatis
    } else {
        $id_guru = $_POST['id_guru']; // admin pilih guru dari form
    }

    $sql = "INSERT INTO kelas (nama_kelas, deskripsi, kode_kelas, id_guru) 
            VALUES ('$nama_kelas','$deskripsi','$kode_kelas','$id_guru')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<h2>Tambah Kelas</h2>
<form method="POST">
    Kelas: <input type="text" name="nama_kelas" required><br><br>
    Deskripsi: <textarea name="deskripsi" id="" cols="30" rows="10" required></textarea><br><br>
    Kode Kelas: <input type="text" name="kode" required><br><br>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        Pilih Guru:
        <select name="id_guru" required>
            <?php
            $guru = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
            while ($g = mysqli_fetch_assoc($guru)) {
                echo "<option value='{$g['id']}'>{$g['username']}</option>";
            }
            ?>
        </select><br><br>
    <?php endif; ?>

    <button type="submit">Simpan</button>
</form>
<a href="index.php">Kembali</a>
