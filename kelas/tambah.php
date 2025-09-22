<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa menambah kelas.");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kode_kelas = mysqli_real_escape_string($conn, $_POST['kode']);

    // Tentukan id_guru
    if ($_SESSION['role'] == 'guru') {
        $id_guru = $_SESSION['id']; // otomatis
    } else {
        $id_guru = $_POST['id_guru']; // admin pilih guru dari form
    }

    $sql = "INSERT INTO kelas (nama_kelas, deskripsi, kode_kelas, id_guru) 
            VALUES ('$nama_kelas','$deskripsi','$kode_kelas','$id_guru')";

    if (mysqli_query($conn, $sql)) {
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
  <title>Tambah Kelas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-blue-100 to-purple-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="flex-grow flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg p-6 sm:p-8">
      <h2 class="text-xl sm:text-2xl font-bold text-center text-blue-600 mb-6">Tambah Kelas</h2>

      <form method="POST" class="space-y-5">
        <!-- Nama Kelas -->
        <div>
          <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
          <input type="text" name="nama_kelas" id="nama_kelas" required
                 class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base" />
        </div>

        <!-- Deskripsi -->
        <div>
          <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea name="deskripsi" id="deskripsi" rows="4" required
                    class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base"></textarea>
        </div>

        <!-- Kode Kelas -->
        <div>
          <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas</label>
          <input type="text" name="kode" id="kode" required
                 class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none font-mono uppercase text-sm sm:text-base" />
        </div>

        <!-- Pilih Guru (hanya admin) -->
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <div>
          <label for="id_guru" class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru</label>
          <select name="id_guru" id="id_guru" required
                  class="w-full px-3 sm:px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none text-sm sm:text-base">
            <?php
            $guru = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
            while ($g = mysqli_fetch_assoc($guru)) {
                echo "<option value='{$g['id']}'>{$g['username']}</option>";
            }
            ?>
          </select>
        </div>
        <?php endif; ?>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
          <a href="index.php"
             class="w-full sm:w-1/2 py-2 px-4 bg-gray-500 hover:bg-gray-600 text-white text-center font-semibold rounded-lg shadow-md transition text-sm sm:text-base">
            Kembali
          </a>
          <button type="submit"
                  class="w-full sm:w-1/2 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition text-sm sm:text-base">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
