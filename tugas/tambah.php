<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$id_user = $_SESSION['id'];
$role = $_SESSION['role'];

// ambil daftar kelas
if ($role == 'admin') {
    $kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
} else {
    // guru hanya bisa pilih kelas yang dia ampu
    $kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_guru = $id_user ORDER BY nama_kelas ASC");
}

// proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = intval($_POST['id_kelas']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

    $sql = "INSERT INTO tugas (id_kelas, judul, deskripsi, deadline, created_at) 
            VALUES ($id_kelas, '$judul', '$deskripsi', '$deadline', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menambah tugas: " . mysqli_error($conn);
    }
}
?>

<h2>Tambah Tugas</h2>
<a href="index.php">Kembali</a>
<form method="post">
    <label>Kelas:</label><br>
    <select name="id_kelas" required>
        <option value="">-- Pilih Kelas --</option>
        <?php while ($row = mysqli_fetch_assoc($kelas)): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kelas']) ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Judul Tugas:</label><br>
    <input type="text" name="judul" required><br><br>

    <label>Deskripsi:</label><br>
    <textarea name="deskripsi" rows="5" cols="40"></textarea><br><br>

    <label>Deadline:</label><br>
    <input type="datetime-local" name="deadline" required><br><br>

    <button type="submit">Simpan</button>
</form>
