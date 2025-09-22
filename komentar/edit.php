<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Silakan login.");
}

if (!isset($_GET['id'])) {
    die("ID komentar tidak ditemukan!");
}

$id_komentar = intval($_GET['id']);
$id_user     = $_SESSION['id'];
$role        = $_SESSION['role'];

// Ambil data komentar
$stmt = $conn->prepare("
    SELECT k.id, k.materi_id, k.tugas_id, k.tugas_siswa_id, k.id_user, k.isi, k.dibuat_pada,
           u.username
    FROM komentar k
    LEFT JOIN users u ON k.id_user = u.id
    WHERE k.id = ?
");
$stmt->bind_param("i", $id_komentar);
$stmt->execute();
$result = $stmt->get_result();
$komentar = $result->fetch_assoc();
$stmt->close();

if (!$komentar) {
    die("Komentar tidak ditemukan!");
}

// Cek hak akses
if ($komentar['id_user'] != $id_user && !in_array($role, ['admin','guru'])) {
    die("Anda tidak berhak mengedit komentar ini!");
}

$error = null;

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isi = trim($_POST['isi']);
    if (!empty($isi)) {
        $stmt = $conn->prepare("UPDATE komentar SET isi = ? WHERE id = ?");
        $stmt->bind_param("si", $isi, $id_komentar);
        $stmt->execute();
        $stmt->close();

        // redirect ke halaman asal
        if ($komentar['materi_id']) {
            header("Location: ../materi/view.php?id=" . $komentar['materi_id']);
        } elseif ($komentar['tugas_id']) {
            header("Location: ../tugas/view.php?id=" . $komentar['tugas_id']);
        } elseif ($komentar['tugas_siswa_id']) {
            header("Location: ../tugas_siswa/view.php?id=" . $komentar['tugas_siswa_id']);
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Isi komentar tidak boleh kosong!";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit Komentar</title>
  <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-3xl mx-auto mt-10 bg-white shadow-md rounded-2xl p-8">
    <h2 class="text-2xl font-bold text-indigo-700 mb-6">✏️ Edit Komentar</h2>

    <?php if ($error): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi Komentar</label>
        <textarea id="isi" name="isi" rows="4" required
                  class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"><?= htmlspecialchars($komentar['isi']) ?></textarea>
      </div>

      <div class="flex items-center gap-4">
        <button type="submit"
                class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
          💾 Update
        </button>
        <a href="javascript:history.back()" 
           class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">⬅️ Kembali</a>
      </div>
    </form>

    <p class="mt-6 text-sm text-gray-500">
      Dibuat oleh <span class="font-semibold"><?= htmlspecialchars($komentar['username'] ?? 'Unknown') ?></span>
      pada <?= $komentar['dibuat_pada'] ?>
    </p>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>
