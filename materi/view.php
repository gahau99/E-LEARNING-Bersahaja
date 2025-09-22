<?php
session_start();
include "../config.php";

// pastikan partials diakses dari folder atas
include "../partials/navbar.php"; 

if (!isset($_SESSION['id'])) {
    die("Silakan login dulu!");
}

if (!isset($_GET['id'])) {
    die("ID materi tidak ditemukan!");
}

$id = intval($_GET['id']);

// ========================
// Ambil detail materi
// ========================
$sqlMateri = "
    SELECT m.*, k.nama_kelas, u.username AS pembuat, u.role AS role_pembuat
    FROM materi m
    JOIN kelas k ON m.id_kelas = k.id
    JOIN users u ON m.dibuat_oleh = u.id
    WHERE m.id = ?
";
if ($stmtM = mysqli_prepare($conn, $sqlMateri)) {
    mysqli_stmt_bind_param($stmtM, "i", $id);
    mysqli_stmt_execute($stmtM);
    $resM = mysqli_stmt_get_result($stmtM);
    $materi = mysqli_fetch_assoc($resM);
    mysqli_stmt_close($stmtM);
} else {
    die("Query gagal: " . mysqli_error($conn));
}

if (!$materi) {
    die("Materi tidak ditemukan!");
}

if ($materi['status'] !== 'approved' && !in_array($_SESSION['role'], ['admin','guru'])) {
    die("Akses ditolak! Materi ini belum disetujui.");
}

$filePath = $materi['file'] ? "../uploads/" . $materi['file'] : null;
$ext = $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null;

// ========================
// Link kembali
// ========================
$backLink = "#"; 
if ($_SESSION['role'] === 'admin') {
    $backLink = "index.php";
} elseif ($_SESSION['role'] === 'guru') {
    $backLink = "index.php";
} elseif ($_SESSION['role'] === 'siswa') {
    $backLink = "list.php";
}

// ========================
// KOMENTAR
// ========================
$id_user = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['isi']))) {
    $isi_raw = trim($_POST['isi']);
    $sqlIns = "INSERT INTO komentar (materi_id, id_user, isi) VALUES (?, ?, ?)";
    if ($stmtIns = mysqli_prepare($conn, $sqlIns)) {
        mysqli_stmt_bind_param($stmtIns, "iis", $id, $id_user, $isi_raw);
        mysqli_stmt_execute($stmtIns);
        mysqli_stmt_close($stmtIns);
    }
    header("Location: view.php?id=$id");
    exit;
}

// Ambil komentar
$sql = "
    SELECT k.id, k.materi_id, k.id_user, k.isi, k.dibuat_pada,
           u.username AS commenter_name,
           u.role AS commenter_role
    FROM komentar k
    LEFT JOIN users u ON k.id_user = u.id
    WHERE k.materi_id = ?
    ORDER BY k.dibuat_pada DESC
";

$komentar = [];
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $komentar[] = $r;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($materi['judul']) ?> - E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../bersahaja_logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gray-100">

<div class="max-w-4xl mx-auto p-4 sm:p-6 bg-white rounded-lg shadow mt-4 sm:mt-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-2"><?= htmlspecialchars($materi['judul']) ?></h2>
    <p class="text-gray-600 text-sm sm:text-base">
        Kelas: <span class="font-medium"><?= htmlspecialchars($materi['nama_kelas']) ?></span>
    </p>
    <p class="text-gray-600 text-sm sm:text-base">
        Pembuat: <span class="font-medium"><?= htmlspecialchars($materi['pembuat']) ?></span> (<?= htmlspecialchars($materi['role_pembuat'] ?? '-') ?>)
    </p>
    <hr class="my-4">

    <!-- File Materi -->
    <?php if ($filePath): ?>
        <div class="border rounded-lg p-3 sm:p-4 bg-gray-50 mb-4">
            <?php if ($ext === "md"): ?>
                <div id="markdown-content" class="prose max-w-none text-sm sm:text-base"></div>
                <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
                <script>
                fetch("<?= $filePath ?>")
                  .then(res => res.text())
                  .then(text => {
                    document.getElementById("markdown-content").innerHTML = marked.parse(text);
                  });
                </script>
            <?php elseif (in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
                <img src="<?= $filePath ?>" alt="Materi Gambar" class="w-full max-w-full h-auto rounded-md shadow">
            <?php elseif ($ext === "pdf"): ?>
                <iframe src="<?= $filePath ?>" class="w-full h-[400px] sm:h-[600px] rounded-md border"></iframe>
            <?php elseif (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])): ?>
                <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode("http://yourdomain.com/uploads/".$materi['file']) ?>" 
                        class="w-full h-[400px] sm:h-[600px] rounded-md border"></iframe>
            <?php else: ?>
                <a href="<?= $filePath ?>" target="_blank" class="text-blue-600 hover:underline">Lihat file</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500 italic">Tidak ada file yang diupload.</p>
    <?php endif; ?>

    <a href="<?= $backLink ?>" class="inline-block bg-blue-600 text-white px-3 sm:px-4 py-2 rounded hover:bg-blue-700 text-xs sm:text-sm">⬅ Kembali</a>

    <hr class="my-6">

    <!-- Komentar -->
    <h3 class="text-lg sm:text-xl font-semibold mb-3">Komentar</h3>

    <!-- Form -->
    <form method="POST" class="mb-6">
        <textarea name="isi" rows="3" required placeholder="Tulis komentar..." 
                  class="w-full p-2 sm:p-3 border rounded-lg focus:ring focus:ring-blue-300 text-sm sm:text-base"></textarea>
        <button type="submit" class="mt-2 bg-green-600 text-white px-3 sm:px-4 py-2 rounded hover:bg-green-700 text-xs sm:text-sm">Kirim</button>
    </form>

    <!-- Daftar komentar -->
    <?php if (count($komentar) > 0): ?>
        <div class="space-y-3 sm:space-y-4">
            <?php foreach($komentar as $k):
                $username = !empty($k['commenter_name']) ? htmlspecialchars($k['commenter_name']) : '<i>Pengguna tidak ditemukan</i>';
                $commenter_role = !empty($k['commenter_role']) ? htmlspecialchars($k['commenter_role']) : '-';
                $waktu = !empty($k['dibuat_pada']) ? $k['dibuat_pada'] : '-';
            ?>
                <div class="p-3 sm:p-4 border rounded-lg bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-1">
                        <span class="font-semibold text-sm sm:text-base"><?= $username ?></span>
                        <span class="text-xs text-gray-500"><?= $waktu ?></span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-600 mb-1">(<?= $commenter_role ?>)</p>
                    <p class="text-gray-800 text-sm sm:text-base"><?= nl2br(htmlspecialchars($k['isi'])) ?></p>
                    <?php if ($_SESSION['id'] == $k['id_user'] || in_array($_SESSION['role'], ['admin','guru'])): ?>
                        <div class="mt-2 text-xs sm:text-sm flex gap-2">
                            <a href="../komentar/hapus.php?id=<?= $k['id'] ?>&back=<?= urlencode("view.php?id=$id") ?>" 
                               class="text-red-600 hover:underline" 
                               onclick="return confirm('Hapus komentar ini?')">Hapus</a>
                            <a href="../komentar/edit.php?id=<?= $k['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">Belum ada komentar.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '../../partials/footer.php'; ?>

</body>
</html>


