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
    echo "<h3 style='color:red'>ID jawaban tidak valid.</h3>";
    echo "<p>Pastikan link memuat parameter <code>?id=&lt;ID_SUBMIT&gt;</code>.</p>";
    echo "<p><a href='javascript:history.back()'>Kembali</a></p>";
    exit;
}

/* Ambil data submission + info tugas */
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
    WHERE ts.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_submit);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Jawaban tidak ditemukan.");
}

/* Otorisasi */
if ($_SESSION['role'] === 'guru' && $row['id_guru'] != $_SESSION['id']) {
    die("Akses ditolak! Bukan kelas yang Anda ampu.");
}

/* Proses POST */
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = isset($_POST['nilai']) ? intval($_POST['nilai']) : null;
    if ($nilai === null || $nilai < 0 || $nilai > 100) {
        $error = "Nilai harus antara 0 sampai 100.";
    } else {
        $update = $conn->prepare("UPDATE tugas_siswa SET nilai = ? WHERE id = ?");
        $update->bind_param("ii", $nilai, $id_submit);
        if ($update->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menyimpan nilai: " . $conn->error;
        }
        $update->close();
    }
}

/* File path */
$uploadsDir   = "../uploads/tugas/";
$relativePath = $row['file'] ? $uploadsDir . $row['file'] : null;
$fileExists   = $row['file'] && file_exists($relativePath);
$ext          = $fileExists ? strtolower(pathinfo($relativePath, PATHINFO_EXTENSION)) : null;

/* URL publik */
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$basePath = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
$publicUrl = $scheme . '://' . $host . $basePath . '/uploads/tugas/' . rawurlencode($row['file']);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Beri Nilai</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- penting untuk responsive -->
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-5xl mx-auto mt-6 sm:mt-10 bg-white shadow-lg rounded-2xl p-4 sm:p-8">
    <h2 class="text-xl sm:text-2xl font-bold text-indigo-700 mb-4">✏️ Beri Nilai</h2>

    <!-- Info Tugas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6 text-sm sm:text-base">
      <p><strong>Tugas:</strong> <?= htmlspecialchars($row['tugas_judul']) ?></p>
      <p><strong>Kelas:</strong> <?= htmlspecialchars($row['nama_kelas']) ?></p>
      <p><strong>Siswa:</strong> <?= htmlspecialchars($row['username']) ?></p>
      <p><strong>Dikirim:</strong> <?= htmlspecialchars($row['submitted_at']) ?></p>
    </div>

    <!-- Preview File -->
    <div class="mb-6">
      <h3 class="font-semibold text-base sm:text-lg mb-2">📄 File Jawaban:</h3>
      <div class="border border-gray-300 rounded-lg p-3 sm:p-4 bg-gray-50">
        <?php if ($fileExists): ?>
          <?php if ($ext === 'md'): ?>
            <div id="markdown-content" class="prose max-w-none text-sm sm:text-base"></div>
            <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
            <script>
              fetch("<?= htmlspecialchars($relativePath) ?>")
                .then(res => res.text())
                .then(text => {
                  document.getElementById("markdown-content").innerHTML = marked.parse(text);
                });
            </script>

          <?php elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
            <img src="<?= htmlspecialchars($relativePath) ?>" alt="Jawaban" class="max-w-full rounded-lg shadow">

          <?php elseif ($ext === 'pdf'): ?>
            <iframe src="<?= htmlspecialchars($relativePath) ?>" 
                    class="w-full h-[400px] sm:h-[650px] border rounded"></iframe>

          <?php elseif (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])): ?>
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($publicUrl) ?>" 
                    class="w-full h-[400px] sm:h-[650px] border rounded"></iframe>
            <p class="mt-2 text-sm"><a href="<?= htmlspecialchars($relativePath) ?>" target="_blank" class="text-blue-600 hover:underline">🔗 Buka file</a></p>

          <?php else: ?>
            <p class="text-sm">Format file tidak bisa dipratinjau. 
              <a href="<?= htmlspecialchars($relativePath) ?>" target="_blank" class="text-blue-600 hover:underline">Unduh</a>
            </p>
          <?php endif; ?>

          <p class="mt-3">
            <a href="<?= htmlspecialchars($relativePath) ?>" download 
               class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition text-sm sm:text-base">⬇️ Download</a>
          </p>
        <?php else: ?>
          <p class="italic text-gray-500">Tidak ada file diupload.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Form Penilaian -->
    <?php if ($error): ?>
      <div class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm sm:text-base"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
      <div>
        <label class="block font-medium mb-1 text-sm sm:text-base">Nilai (0–100):</label>
        <input type="number" name="nilai" min="0" max="100" required
               value="<?= $row['nilai'] !== null ? intval($row['nilai']) : '' ?>"
               class="w-24 sm:w-32 border rounded px-2 sm:px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300 text-sm sm:text-base">
        <input type="hidden" name="id_submit" value="<?= $id_submit ?>">
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <button type="submit" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition text-sm sm:text-base">💾 Simpan</button>
        <a href="index.php" 
           class="px-4 py-2 bg-gray-500 text-white rounded-lg shadow hover:bg-gray-600 transition text-sm sm:text-base">⬅️ Kembali</a>
      </div>
    </form>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>