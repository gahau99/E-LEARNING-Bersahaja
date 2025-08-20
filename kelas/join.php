<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    die("Akses ditolak!");
}

$id_siswa = $_SESSION['id'];

// ambil daftar kelas yang belum diikuti siswa
$q = mysqli_query($conn, "
    SELECT k.* 
    FROM kelas k 
    WHERE k.id NOT IN (
        SELECT id_kelas FROM kelas_siswa WHERE id_siswa=$id_siswa
    )
    ORDER BY k.nama_kelas
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = intval($_POST['id_kelas']);
    
    // cek apakah sudah gabung
    $cek = mysqli_query($conn, "SELECT * FROM kelas_siswa WHERE id_siswa=$id_siswa AND id_kelas=$id_kelas");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO kelas_siswa (id_siswa, id_kelas) VALUES ($id_siswa, $id_kelas)");
        echo "<p style='color:green'>Berhasil gabung ke kelas!</p>";
    } else {
        echo "<p style='color:red'>Anda sudah terdaftar di kelas ini.</p>";
    }
}
?>

<h2>Gabung Kelas</h2>
<form method="post">
    <label>Pilih Kelas:</label><br>
    <select name="id_kelas" required>
        <option value="">-- Pilih --</option>
        <?php while($row = mysqli_fetch_assoc($q)): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kelas']) ?></option>
        <?php endwhile; ?>
    </select>
    <br><br>
    <button type="submit">Gabung</button>
</form>

<p><a href="list.php">Kembali</a></p>
