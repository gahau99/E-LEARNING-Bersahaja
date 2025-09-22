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

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Materi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-purple-100 to-indigo-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="max-w-3xl mx-auto mt-6 sm:mt-10 bg-white rounded-2xl shadow-lg p-4 sm:p-8 flex-grow">
    <h2 class="text-xl sm:text-2xl font-bold text-indigo-600 mb-6">✏️ Edit Materi</h2>

    <form method="post" enctype="multipart/form-data" class="space-y-5">
      <!-- Pilih Kelas -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">Kelas</label>
        <select name="id_kelas" required
          class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300 focus:outline-none">
          <?php while($row = mysqli_fetch_assoc($kelas)): ?>
            <option value="<?= $row['id'] ?>" <?= ($row['id'] == $materi['id_kelas']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($row['nama_kelas']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Judul -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">Judul Materi</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($materi['judul']) ?>" required
          class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300 focus:outline-none">
      </div>

      <!-- Deskripsi -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="4" required
          class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300 focus:outline-none"><?= htmlspecialchars($materi['deskripsi']) ?></textarea>
      </div>

      <!-- File Materi -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">File Materi</label>
        <?php if ($materi['file']): ?>
          <a href="view.php?id=<?= $materi['id'] ?>" target="_blank" 
             class="text-indigo-600 hover:underline block mb-2 text-sm sm:text-base">📄 Lihat File Saat Ini</a>
        <?php endif; ?>
        <input type="file" name="file"
          class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-indigo-300 focus:outline-none">
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti file</p>
      </div>

      <!-- Tombol -->
      <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-0 items-stretch sm:items-center">
        <a href="index.php" 
           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition text-center">
          Kembali
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition">
          Update Materi
        </button>
      </div>
    </form>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
