<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Silakan login.");
}

// ambil semua materi & tugas
$materi = mysqli_query($conn, "SELECT id, judul FROM materi ORDER BY dibuat_pada DESC");
$tugas  = mysqli_query($conn, "SELECT id, judul FROM tugas ORDER BY created_at DESC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $materi_id = intval($_POST['materi_id'] ?? 0) ?: null;
    $tugas_id  = intval($_POST['tugas_id'] ?? 0) ?: null;
    $tugas_siswa_id = null; // jika nanti mau tambah komentar tugas_siswa bisa ditambah
    $isi       = trim($_POST['isi']);
    $id_user   = $_SESSION['id'];

    if ((!$materi_id && !$tugas_id) || empty($isi)) {
        $error = "Silakan pilih Materi atau Tugas dan isi komentar.";
    } else {
        $stmt = $conn->prepare("INSERT INTO komentar (materi_id, tugas_id, tugas_siswa_id, id_user, isi) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $materi_id, $tugas_id, $tugas_siswa_id, $id_user, $isi);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tambah Komentar</title>
  <script src="https://cdn.tailwindcss.com"></script>
   <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>
<div class="container mx-auto px-4 py-12 pb-80">
  <main class="flex-grow w-full max-w-3xl mx-auto mt-6 sm:mt-10 bg-white shadow-md rounded-xl sm:rounded-2xl p-4 sm:p-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
      <h2 class="text-xl sm:text-2xl font-bold text-indigo-700">Tambah Komentar</h2>
      <a href="index.php" 
         class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 shadow text-center">
        ← Kembali
      </a>
    </div>

    <!-- Error -->
    <?php if (isset($error)): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg text-sm sm:text-base">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" class="space-y-5 sm:space-y-6">
      
      <!-- Parent Type -->
      <div>
        <label for="parent_type" class="block text-sm font-medium text-gray-700 mb-1">Parent Type</label>
        <select id="parent_type" required onchange="toggleParentList()" 
                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">-- Pilih --</option>
          <option value="materi">Materi</option>
          <option value="tugas">Tugas</option>
        </select>
      </div>

      <!-- Materi List -->
      <div id="materi_list" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Materi</label>
        <select name="materi_id"
                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">-- Pilih Materi --</option>
          <?php while($m = mysqli_fetch_assoc($materi)): ?>
            <option value="<?= $m['id'] ?>">
              <?= htmlspecialchars($m['judul']) ?> (ID: <?= $m['id'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Tugas List -->
      <div id="tugas_list" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tugas</label>
        <select name="tugas_id"
                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">-- Pilih Tugas --</option>
          <?php while($t = mysqli_fetch_assoc($tugas)): ?>
            <option value="<?= $t['id'] ?>">
              <?= htmlspecialchars($t['judul']) ?> (ID: <?= $t['id'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Isi Komentar -->
      <div>
        <label for="isi" class="block text-sm font-medium text-gray-700 mb-1">Isi Komentar</label>
        <textarea name="isi" id="isi" rows="4" required
                  class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
      </div>

      <!-- Tombol -->
      <div class="flex flex-col sm:flex-row justify-end gap-3">
        <a href="index.php" 
           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 shadow text-center">
          Batal
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow text-center">
          Simpan
        </button>
      </div>
    </form>
  </main>
</div>
  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

  <script>
    function toggleParentList() {
      var type = document.getElementById('parent_type').value;
      document.getElementById('materi_list').classList.toggle('hidden', type !== 'materi');
      document.getElementById('tugas_list').classList.toggle('hidden', type !== 'tugas');
    }
  </script>
</body>
</html>
