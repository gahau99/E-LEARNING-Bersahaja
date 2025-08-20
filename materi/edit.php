<?php
session_start();
include "../config.php";

// Cek role (guru/admin saja)
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM materi WHERE id = $id");
$materi = mysqli_fetch_assoc($result);

// Jika materi tidak ditemukan
if (!$materi) {
    die("Materi tidak ditemukan.");
}

// Hanya guru yang buat materi ini ATAU admin yang bisa edit
if ($_SESSION['role'] === 'guru' && $materi['dibuat_oleh'] != $_SESSION['id']) {
    die("Anda tidak berhak mengedit materi ini.");
}

// Ambil daftar kelas
if ($_SESSION['role'] === 'guru') {
    $kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_guru = " . $_SESSION['id']);
} else {
    $kelas = mysqli_query($conn, "SELECT * FROM kelas");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $id_kelas = (int) $_POST['id_kelas'];
    $file = $materi['file']; // default file lama

    // Jika ada upload file baru
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES['file']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file = $file_name;
            // Hapus file lama (jika ada)
            if ($materi['file'] && file_exists($target_dir . $materi['file'])) {
                unlink($target_dir . $materi['file']);
            }
        } else {
            echo "Gagal upload file!";
        }
    }

    // Update data → status kembali ke pending agar dicek ulang
    $query = "UPDATE materi SET 
                id_kelas = '$id_kelas', 
                judul = '$judul', 
                deskripsi = '$deskripsi', 
                file = " . ($file ? "'$file'" : "NULL") . ",
                status = 'pending'
              WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<h2>Edit Materi</h2>
<a href="index.php">Kembali</a>

<form method="post" enctype="multipart/form-data">
    <p>
        <label>Kelas:</label><br>
        <select name="id_kelas" required>
            <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                <option value="<?= $row['id'] ?>" <?= ($row['id'] == $materi['id_kelas']) ? 'selected' : '' ?>>
                    <?= $row['nama_kelas'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </p>
    <p>
        <label>Judul Materi:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($materi['judul']) ?>" required>
    </p>
    <p>
        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" rows="4" cols="40" required><?= htmlspecialchars($materi['deskripsi']) ?></textarea>
    </p>
    <p>
        <label>File Materi:</label><br>
        <?php if ($materi['file']): ?>
            <a href="view.php?id=<?= $materi['id'] ?>" target="_blank">Lihat</a>
        <?php endif; ?>
        <input type="file" name="file">
        <small>(Kosongkan jika tidak ingin mengganti file)</small>
    </p>
    <p>
        <button type="submit">Update</button>
    </p>
</form>
