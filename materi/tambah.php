<?php
session_start();
include "../config.php";

// Cek role
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru/admin yang bisa tambah materi.");
}

// Ambil daftar kelas (hanya kelas yang dibuat guru terkait atau semua kelas kalau admin)
if ($_SESSION['role'] === 'guru') {
    $id_guru = $_SESSION['id'];
    $kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_guru = $id_guru");
} else {
    $kelas = mysqli_query($conn, "SELECT * FROM kelas");
}

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $id_kelas = (int) $_POST['id_kelas'];
    $dibuat_oleh = $_SESSION['id'];
    $file = null;

    // Upload file jika ada
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES['file']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file = $file_name;
        } else {
            echo "Gagal upload file!";
        }
    }

    // Simpan ke database (status pending)
    $query = "INSERT INTO materi (id_kelas, judul, deskripsi, file, status, dibuat_oleh) 
              VALUES ('$id_kelas', '$judul', '$deskripsi', " . ($file ? "'$file'" : "NULL") . ", 'pending', '$dibuat_oleh')";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<h2>Tambah Materi</h2>
<a href="index.php">Kembali</a>

<form method="post" enctype="multipart/form-data">
    <p>
        <label>Kelas:</label><br>
        <select name="id_kelas" required>
            <option value="">-- Pilih Kelas --</option>
            <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nama_kelas'] ?></option>
            <?php endwhile; ?>
        </select>
    </p>
    <p>
        <label>Judul Materi:</label><br>
        <input type="text" name="judul" required>
    </p>
    <p>
        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="4" cols="40" required></textarea>
    </p>
    <p>
        <label>File Materi (pdf, markdown lebih bagus):</label><br>
        <input type="file" name="file">
    </p>
    <p>
        <button type="submit">Simpan</button>
    </p>
</form>
