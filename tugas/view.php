<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Silakan login dulu!");
}

if (!isset($_GET['id'])) {
    die("ID jawaban tidak ditemukan!");
}

$id_user = (int) $_SESSION['id'];
$role    = $_SESSION['role'];
$id_submit = (int) $_GET['id'];

$sql = "
    SELECT 
        ts.*,
        t.judul           AS tugas_judul,
        t.id_kelas,
        k.nama_kelas,
        k.id_guru,
        u.username        AS nama_siswa
    FROM tugas_siswa ts
    JOIN tugas t   ON ts.id_tugas = t.id
    JOIN kelas k   ON t.id_kelas  = k.id
    JOIN users u   ON ts.id_siswa = u.id
    WHERE ts.id = $id_submit
";
$res = mysqli_query($conn, $sql);
$subm = mysqli_fetch_assoc($res);

if (!$subm) {
    die("Jawaban tidak ditemukan.");
}

/** Akses kontrol */
if ($role === 'siswa' && $subm['id_siswa'] != $id_user) {
    die("Akses ditolak! Ini bukan jawaban Anda.");
}
if ($role === 'guru' && $subm['id_guru'] != $id_user) {
    die("Akses ditolak! Bukan kelas yang Anda ampu.");
}
// admin bebas

// Lokasi file (server path & url)
$relativePath = "../uploads/tugas/" . $subm['file']; // path relatif dari file ini
$fileExists   = $subm['file'] && file_exists($relativePath);
$ext          = $fileExists ? strtolower(pathinfo($relativePath, PATHINFO_EXTENSION)) : null;

// Buat URL absolut untuk Office Viewer / link download
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\'); // naik 1 folder dari /tugas ke root app
$publicUrl = $scheme . '://' . $host . $basePath . '/uploads/tugas/' . rawurlencode($subm['file']);

// Tentukan link kembali
if ($role === 'siswa') {
    $backLink = "list.php";
} else {
    // guru/admin kembali ke daftar jawaban untuk tugas terkait
    $backLink = "index.php?id=" . (int)$subm['id_tugas'];
}
?>
<h2>Jawaban Tugas: <?= htmlspecialchars($subm['tugas_judul']) ?></h2>
<p>Kelas: <?= htmlspecialchars($subm['nama_kelas']) ?></p>
<p>Siswa: <?= htmlspecialchars($subm['nama_siswa']) ?></p>
<p>Dikumpulkan: <?= htmlspecialchars($subm['submitted_at']) ?></p>
<p>Nilai: <?= $subm['nilai'] !== null ? htmlspecialchars($subm['nilai']) : '-' ?></p>
<hr>

<?php if ($fileExists): ?>

    <?php if ($ext === 'md'): ?>
        <!-- Render Markdown dengan marked.js -->
        <div id="markdown-content" style="border:1px solid #ddd; padding:16px; background:#fafafa;"></div>
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script>
        fetch("<?= htmlspecialchars($relativePath) ?>")
          .then(res => res.text())
          .then(text => {
            document.getElementById("markdown-content").innerHTML = marked.parse(text);
          });
        </script>

    <?php elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
        <img src="<?= htmlspecialchars($relativePath) ?>" alt="Jawaban" style="max-width:100%;height:auto;border:1px solid #ccc;">

    <?php elseif ($ext === 'pdf'): ?>
        <iframe src="<?= htmlspecialchars($relativePath) ?>" width="100%" height="650" style="border:1px solid #ccc;"></iframe>

    <?php elseif (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])): ?>
        <!-- Office Viewer butuh URL publik; jika lokal mungkin tidak tampil -->
        <iframe 
          src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($publicUrl) ?>"
          width="100%" height="650" style="border:1px solid #ccc;">
        </iframe>
        <p style="margin-top:8px;">
            Jika pratinjau tidak muncul (server lokal), 
            <a href="<?= htmlspecialchars($relativePath) ?>" target="_blank">buka file</a>.
        </p>

    <?php else: ?>
        <p>Format file tidak bisa dipratinjau. 
           <a href="<?= htmlspecialchars($relativePath) ?>" target="_blank">Buka/Unduh</a>
        </p>
    <?php endif; ?>

    <p style="margin-top:12px;">
        <a href="<?= htmlspecialchars($relativePath) ?>" download>⬇️ Download</a>
    </p>

<?php else: ?>
    <p><i>Tidak ada file yang diupload atau file hilang.</i></p>
<?php endif; ?>

<br>
<a href="<?= htmlspecialchars($backLink) ?>">Kembali</a>
