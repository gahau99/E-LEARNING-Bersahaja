<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

// TERIMA banyak kemungkinan nama param: id, id_submit
$id_submit = 0;
if (!empty($_GET['id'])) $id_submit = intval($_GET['id']);
elseif (!empty($_GET['id_submit'])) $id_submit = intval($_GET['id_submit']);
elseif (!empty($_POST['id_submit'])) $id_submit = intval($_POST['id_submit']);

if ($id_submit <= 0) {
    // Friendly error + petunjuk
    echo "<h3 style='color:red'>ID jawaban tidak valid.</h3>";
    echo "<p>Pastikan link untuk memberi nilai memuat parameter <code>?id=&lt;ID_SUBMIT&gt;</code> (contoh: <code>nilai.php?id=12</code>).</p>";
    echo "<p>Jika Anda membuka halaman ini langsung, kembali ke daftar jawaban lalu klik tombol <em>Beri Nilai</em>.</p>";
    echo "<p><a href='javascript:history.back()'>Kembali</a></p>";
    exit;
}

/* Ambil data submission + info tugas, kelas, siswa */
$sql = "
    SELECT 
      ts.*, 
      t.id AS id_tugas, t.judul AS tugas_judul, 
      k.id_guru, k.nama_kelas,
      u.username
    FROM tugas_siswa ts
    JOIN tugas t ON ts.id_tugas = t.id
    JOIN kelas k ON t.id_kelas = k.id
    JOIN users u ON ts.id_siswa = u.id
    WHERE ts.id = $id_submit
";
$res = mysqli_query($conn, $sql);
if (!$res) {
    die("Query error: " . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($res);
if (!$row) {
    die("Jawaban tidak ditemukan.");
}

/* Otorisasi: guru hanya boleh nilai untuk kelas yang dia ampu */
if ($_SESSION['role'] === 'guru' && $row['id_guru'] != $_SESSION['id']) {
    die("Akses ditolak! Bukan kelas yang Anda ampu.");
}

/* Proses POST (simpan nilai) */
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = isset($_POST['nilai']) ? intval($_POST['nilai']) : null;
    if ($nilai === null || $nilai < 0 || $nilai > 100) {
        $error = "Nilai harus antara 0 sampai 100.";
    } else {
        $update = mysqli_query($conn, "UPDATE tugas_siswa SET nilai = $nilai WHERE id = $id_submit");
        if ($update) {
            header("Location: index.php"); // atau header("Location: view.php?id=".$row['id_tugas']);
            exit;
        } else {
            $error = "Gagal menyimpan nilai: " . mysqli_error($conn);
        }
    }
}

/* Preview file (opsional) */
$uploadsDir = "../uploads/tugas/";
$relativePath = $row['file'] ? $uploadsDir . $row['file'] : null;
$fileExists = $row['file'] && file_exists($relativePath);
$ext = $fileExists ? strtolower(pathinfo($relativePath, PATHINFO_EXTENSION)) : null;
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Beri Nilai</title></head><body>
<h2>Beri Nilai</h2>
<p><strong>Tugas:</strong> <?= htmlspecialchars($row['tugas_judul']) ?></p>
<p><strong>Kelas:</strong> <?= htmlspecialchars($row['nama_kelas']) ?></p>
<p><strong>Siswa:</strong> <?= htmlspecialchars($row['username']) ?></p>
<p><strong>Dikirim:</strong> <?= htmlspecialchars($row['submitted_at']) ?></p>

<p><strong>File Jawaban:</strong>
<?php if ($fileExists): ?>
    <a href="<?= htmlspecialchars(string: $relativePath) ?>" target="_blank">Buka</a> |
    <a href="<?= htmlspecialchars($relativePath) ?>" download>Download</a>
<?php else: ?>
    -
<?php endif; ?>
</p>

<?php if ($fileExists && $ext === 'md'): ?>
    <h3>Preview (Markdown)</h3>
    <div id="md" style="border:1px solid #ddd;padding:12px;background:#fafafa;"></div>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
    fetch("<?= "/smkti/e-learning/uploads/tugas/" . rawurlencode($row['file']) ?>")
      .then(r => r.text())
      .then(t => document.getElementById('md').innerHTML = marked.parse(t))
      .catch(e => document.getElementById('md').innerText = "Gagal memuat preview.");
    </script>
<?php endif; ?>

<?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<form method="post">
    <label>Nilai (0–100):</label>
    <input type="number" name="nilai" min="0" max="100" required
           value="<?= $row['nilai'] !== null ? intval($row['nilai']) : '' ?>">
    <input type="hidden" name="id_submit" value="<?= $id_submit ?>">
    <button type="submit">Simpan</button>
</form>

<p><a href="index.php">Kembali ke daftar tugas</a></p>
</body></html>
