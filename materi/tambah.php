<?php
session_start();
include "../config.php";

// Cek role
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru/admin yang bisa tambah materi.");
}

// Ambil daftar kelas (hanya kelas milik guru atau semua kelas jika admin)
if ($_SESSION['role'] === 'guru') {
    $id_guru = $_SESSION['id'];
    $kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_guru = $id_guru");
} else {
    $kelas = mysqli_query($conn, "SELECT * FROM kelas");
}

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $id_kelas = (int) $_POST['id_kelas'];
    $dibuat_oleh = $_SESSION['id'];
    $file = null;

    // Upload file jika ada
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES['file']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $file = $file_name;
        } else {
            echo "Gagal upload file!";
        }
    }

    // Simpan ke database (status pending)
    $query = "INSERT INTO materi (id_kelas, judul, deskripsi, file, status, dibuat_oleh) 
              VALUES ('$id_kelas', '$judul', '$deskripsi', " . ($file ? "'$file'" : "NULL") . ", 'pending', '$dibuat_oleh')";
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
  <title>Tambah Materi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-green-100 to-emerald-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="max-w-3xl mx-auto mt-6 sm:mt-10 bg-white rounded-2xl shadow-lg p-4 sm:p-8 flex-grow">
    <h2 class="text-xl sm:text-2xl font-bold text-emerald-600 mb-6">➕ Tambah Materi</h2>

    <form method="post" enctype="multipart/form-data" class="space-y-5">
      <!-- Pilih Kelas -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">Kelas</label>
        <select name="id_kelas" required
          class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring focus:ring-emerald-300 focus:outline-none">
          <option value="">-- Pilih Kelas --</option>
          <?php while($row = mysqli_fetch_assoc($kelas)): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kelas']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Judul -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">Judul Materi</label>
        <input type="text" name="judul" required
          class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring focus:ring-emerald-300 focus:outline-none">
      </div>

      <!-- Deskripsi -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="4" required
          class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring focus:ring-emerald-300 focus:outline-none"></textarea>
      </div>

      <!-- File Materi -->
      <div>
        <label class="block text-gray-700 font-medium mb-1">File Materi</label>
        <input type="file" name="file"
          class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-emerald-300 focus:outline-none">
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
          Disarankan format <strong>PDF</strong> atau <strong>Markdown</strong>
        </p>
      </div>

      <!-- Tombol -->
      <div class="flex flex-col sm:flex-row justify-between gap-3 sm:gap-0 items-stretch sm:items-center">
        <a href="index.php" 
           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition text-center">
          Kembali
        </a>
        <button type="submit" 
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow transition">
          Simpan Materi
        </button>
      </div>
    </form>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
