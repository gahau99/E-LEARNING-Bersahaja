<?php
session_start();
require_once "../config.php";

// --- Cek login
if (!isset($_SESSION['id'])) {
    die("Silakan login dulu!");
}

$id_user = (int) $_SESSION['id'];
$role    = $_SESSION['role'] ?? null;

if (!isset($_GET['id'])) {
    die("ID jawaban tidak ditemukan!");
}
$id_submit = (int) $_GET['id'];

/* =========================
   Ambil detail jawaban
========================= */
$sql = "
    SELECT 
        ts.*,
        t.judul           AS tugas_judul,
        t.id              AS id_tugas,
        t.id_kelas,
        k.nama_kelas,
        k.id_guru,
        u.username        AS nama_siswa
    FROM tugas_siswa ts
    JOIN tugas t   ON ts.id_tugas = t.id
    JOIN kelas k   ON t.id_kelas  = k.id
    JOIN users u   ON ts.id_siswa = u.id
    WHERE ts.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_submit);
$stmt->execute();
$res = $stmt->get_result();
$subm = $res->fetch_assoc();
$stmt->close();

if (!$subm) {
    die("Jawaban tidak ditemukan.");
}

/* =========================
   Cek file jawaban
========================= */
$relativePath = "../uploads/tugas/" . $subm['file'];        
$absolutePath = __DIR__ . "/../uploads/tugas/" . $subm['file'];
$fileExists   = $subm['file'] && file_exists($absolutePath);
$ext          = $fileExists ? strtolower(pathinfo($subm['file'], PATHINFO_EXTENSION)) : null;

$scheme     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host       = $_SERVER['HTTP_HOST'];
$basePath   = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
$publicUrl  = $scheme . '://' . $host . $basePath . '/uploads/tugas/' . rawurlencode($subm['file']);

/* =========================
   Link kembali
========================= */
$backLink = ($role === 'siswa') ? "list.php" : "index.php?id=" . (int)$subm['id_tugas'];

/* =========================
   Proses tambah komentar
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isi'])) {
    $isi = trim($_POST['isi']);
    if ($isi !== "") {
        $stmt = $conn->prepare("INSERT INTO komentar (tugas_siswa_id, id_user, isi) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_submit, $id_user, $isi);
        if ($stmt->execute()) {
            header("Location: view.php?id=" . $id_submit);
            exit;
        }
        $stmt->close();
    }
}

/* =========================
   Ambil komentar
========================= */
$stmt = $conn->prepare("
    SELECT k.*, u.username 
    FROM komentar k
    JOIN users u ON k.id_user = u.id
    WHERE k.tugas_siswa_id=?
    ORDER BY k.dibuat_pada ASC
");
$stmt->bind_param("i", $id_submit);
$stmt->execute();
$komentar = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>View Jawaban</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-5xl mx-auto w-full mt-6 md:mt-10 bg-white shadow-md rounded-2xl p-4 sm:p-8">
    <h2 class="text-xl sm:text-2xl font-bold text-indigo-700 mb-4">👩‍🎓 Jawaban Tugas</h2>

    <!-- Info Jawaban -->
    <div class="mb-6 text-gray-700 grid grid-cols-1 sm:grid-cols-2 gap-2">
      <p><b>Tugas:</b> <?= htmlspecialchars($subm['tugas_judul']) ?></p>
      <p><b>Kelas:</b> <?= htmlspecialchars($subm['nama_kelas']) ?></p>
      <p><b>Siswa:</b> <?= htmlspecialchars($subm['nama_siswa']) ?></p>
      <p><b>Dikumpulkan:</b> <?= htmlspecialchars($subm['submitted_at']) ?></p>
      <p><b>Nilai:</b> <?= $subm['nilai'] !== null ? htmlspecialchars($subm['nilai']) : '-' ?></p>
    </div>

    <hr class="my-6">

    <!-- File Jawaban -->
    <h3 class="text-lg font-semibold text-gray-800 mb-3">📂 File Jawaban</h3>
    <div class="mb-6">
      <?php if ($fileExists): ?>
          <?php if ($ext === 'md'): ?>
              <div id="markdown-content" class="border border-gray-300 bg-gray-50 p-4 rounded overflow-x-auto"></div>
              <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
              <script>
              fetch("<?= htmlspecialchars($relativePath) ?>")
                .then(res => res.text())
                .then(text => {
                  document.getElementById("markdown-content").innerHTML = marked.parse(text);
                });
              </script>

          <?php elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
              <img src="<?= htmlspecialchars($relativePath) ?>" alt="Jawaban" class="max-w-full rounded border mx-auto">

          <?php elseif ($ext === 'pdf'): ?>
              <iframe src="<?= htmlspecialchars($relativePath) ?>" 
                      class="w-full h-[400px] sm:h-[650px] border rounded"></iframe>

          <?php elseif (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])): ?>
              <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($publicUrl) ?>" 
                      class="w-full h-[400px] sm:h-[650px] border rounded"></iframe>
              <p class="mt-2"><a href="<?= htmlspecialchars($relativePath) ?>" target="_blank" class="text-indigo-600 hover:underline">🔗 Buka file</a></p>

          <?php else: ?>
              <p>Format file tidak bisa dipratinjau. <a href="<?= htmlspecialchars($relativePath) ?>" target="_blank" class="text-indigo-600 hover:underline">Unduh</a></p>
          <?php endif; ?>

          <p class="mt-3">
            <a href="<?= htmlspecialchars($relativePath) ?>" download 
               class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
               ⬇️ Download
            </a>
          </p>
      <?php else: ?>
          <p class="italic text-gray-500">Tidak ada file diupload atau file hilang.</p>
      <?php endif; ?>
    </div>

    <!-- Tombol kembali -->
    <a href="<?= htmlspecialchars($backLink) ?>" 
       class="inline-block mb-6 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
       ⬅️ Kembali
    </a>

    <hr class="my-6">

    <!-- Komentar -->
    <h3 class="text-lg font-semibold text-gray-800 mb-3">💬 Komentar</h3>

    <form method="post" class="mb-6 space-y-2">
      <textarea name="isi" rows="3" required 
                class="w-full border rounded p-3 focus:ring-2 focus:ring-indigo-500"></textarea>
      <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Tambah Komentar</button>
    </form>

    <?php if (empty($komentar)): ?>
      <p class="italic text-gray-500">Belum ada komentar.</p>
    <?php else: ?>
      <ul class="space-y-4">
        <?php foreach ($komentar as $k): ?>
          <li class="p-3 border rounded bg-gray-50">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
              <span class="font-semibold text-indigo-700"><?= htmlspecialchars($k['username']) ?></span>
              <small class="text-gray-500"><?= $k['dibuat_pada'] ?></small>
            </div>
            <p class="mt-2 text-gray-700 break-words"><?= nl2br(htmlspecialchars($k['isi'])) ?></p>
            <?php if ($k['id_user'] == $id_user || $role === 'guru'): ?>
              <a href="../komentar/hapus.php?id=<?= $k['id'] ?>&back=<?= urlencode('view.php?id='.$id_submit) ?>" 
                 onclick="return confirm('Hapus komentar ini?')" 
                 class="mt-2 inline-block text-red-600 hover:underline text-sm">🗑️ Hapus</a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>
