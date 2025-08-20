<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Silakan login dulu!");
}

if (!isset($_GET['id'])) {
    die("ID materi tidak ditemukan!");
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "
    SELECT materi.*, kelas.nama_kelas, users.username AS pembuat
    FROM materi
    JOIN kelas ON materi.id_kelas = kelas.id
    JOIN users ON materi.dibuat_oleh = users.id
    WHERE materi.id = $id
");

$materi = mysqli_fetch_assoc($result);
if (!$materi) {
    die("Materi tidak ditemukan!");
}

if ($materi['status'] !== 'approved' && !in_array($_SESSION['role'], ['admin','guru'])) {
    die("Akses ditolak! Materi ini belum disetujui.");
}

$filePath = $materi['file'] ? "../uploads/" . $materi['file'] : null;
$ext = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;

$backLink = "#"; 

if ($_SESSION['role'] === 'admin') {
    $backLink = "index.php"; // daftar materi admin
} elseif ($_SESSION['role'] === 'guru') {
    $backLink = "index.php"; // daftar materi guru
} elseif ($_SESSION['role'] === 'siswa') {
    $backLink = "list.php"; // halaman materi untuk siswa
}

?>

<h2><?= htmlspecialchars($materi['judul']) ?></h2>
<p>Kelas: <?= htmlspecialchars($materi['nama_kelas']) ?></p>
<p>Pembuat: <?= htmlspecialchars($materi['pembuat']) ?></p>
<hr>

<?php if ($filePath): ?>
    <?php if ($ext === "md"): ?>
        <div id="markdown-content" style="border:1px solid #ddd; padding:15px; background:#fafafa;"></div>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
        fetch("<?= $filePath ?>")
          .then(res => res.text())
          .then(text => {
            document.getElementById("markdown-content").innerHTML = marked.parse(text);
          });
        </script>
    <?php elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
        <img src="<?= $filePath ?>" alt="Materi Gambar" style="max-width:100%; height:auto; border:1px solid #ccc;">
    <?php elseif ($ext === "pdf"): ?>
        <iframe src="<?= $filePath ?>" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
    <?php elseif (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])): ?>
        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode("http://yourdomain.com/uploads/".$materi['file']) ?>" 
                width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
    <?php else: ?>
        <p><a href="<?= $filePath ?>" target="_blank">Lihat file</a></p>
    <?php endif; ?>
<?php else: ?>
    <p><i>Tidak ada file yang diupload.</i></p>
<?php endif; ?>

<br>

<a href="<?= $backLink ?>">Kembali ke daftar materi</a>
