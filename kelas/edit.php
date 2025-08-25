<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa edit kelas.");
}

// Pastikan ada ID
if (!isset($_GET['id'])) {
    die("ID kelas tidak ditemukan.");
}
$id_kelas = (int)$_GET['id'];

// Ambil data kelas
$result = mysqli_query($conn, "SELECT * FROM kelas WHERE id='$id_kelas'");
$kelas = mysqli_fetch_assoc($result);
if (!$kelas) {
    die("Kelas tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kode_kelas = mysqli_real_escape_string($conn, $_POST['kode']);

    // Tentukan id_guru
    if ($_SESSION['role'] == 'guru') {
        $id_guru = $_SESSION['id']; // otomatis guru sendiri
    } else {
        $id_guru = $_POST['id_guru']; // admin pilih guru
    }

    $sql = "UPDATE kelas 
            SET nama_kelas='$nama_kelas', deskripsi='$deskripsi', kode_kelas='$kode_kelas', id_guru='$id_guru' 
            WHERE id='$id_kelas'";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<h2>Edit Kelas</h2>
<form method="POST">
    Kelas: <input type="text" name="nama_kelas" value="<?= htmlspecialchars($kelas['nama_kelas']) ?>" required><br><br>
    Deskripsi: <textarea name="deskripsi" id="" cols="30" rows="10" required><?= htmlspecialchars($kelas['deskripsi']) ?></textarea><br><br>
    Kode Kelas: <input type="text" name="kode" value="<?= htmlspecialchars($kelas['kode_kelas']) ?>" required><br><br>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        Pilih Guru:
        <select name="id_guru" required>
            <?php
            $guru = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
            while ($g = mysqli_fetch_assoc($guru)) {
                $selected = ($g['id'] == $kelas['id_guru']) ? "selected" : "";
                echo "<option value='{$g['id']}' $selected>{$g['username']}</option>";
            }
            ?>
        </select><br><br>
    <?php endif; ?>

    <button type="submit">Simpan Perubahan</button>
</form>
<a href="index.php">Kembali</a>
