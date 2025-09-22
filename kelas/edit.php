<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa edit kelas.");
}

// Pastikan ada ID
if (!isset($_GET['id'])) {
    die("ID kelas tidak ditemukan.");
}
$id_kelas = (int)$_GET['id'];

// Ambil data kelas
$result = mysqli_query($conn, "SELECT * FROM kelas WHERE id='$id_kelas'");
$kelas = mysqli_fetch_assoc($result);
if (!$kelas) {
    die("Kelas tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kode_kelas = mysqli_real_escape_string($conn, $_POST['kode']);

    // Tentukan id_guru
    if ($_SESSION['role'] == 'guru') {
        $id_guru = $_SESSION['id']; // otomatis guru sendiri
    } else {
        $id_guru = $_POST['id_guru']; // admin pilih guru
    }

    $sql = "UPDATE kelas 
            SET nama_kelas='$nama_kelas', deskripsi='$deskripsi', kode_kelas='$kode_kelas', id_guru='$id_guru' 
            WHERE id='$id_kelas'";

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
  <title>Edit Kelas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">

</head>
<body class="bg-gradient-to-br from-green-100 to-blue-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="flex-grow flex items-center justify-center px-4">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-center text-green-600 mb-6">Edit Kelas</h2>

      <form method="POST" class="space-y-5">
        <!-- Nama Kelas -->
        <div>
          <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
          <input type="text" name="nama_kelas" id="nama_kelas" 
                 value="<?= htmlspecialchars($kelas['nama_kelas']) ?>" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none" />
        </div>

        <!-- Deskripsi -->
        <div>
          <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
          <textarea name="deskripsi" id="deskripsi" rows="4" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none"><?= htmlspecialchars($kelas['deskripsi']) ?></textarea>
        </div>

        <!-- Kode Kelas -->
        <div>
          <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Kelas</label>
          <input type="text" name="kode" id="kode" 
                 value="<?= htmlspecialchars($kelas['kode_kelas']) ?>" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none font-mono uppercase" />
        </div>

        <!-- Pilih Guru (hanya admin) -->
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <div>
          <label for="id_guru" class="block text-sm font-medium text-gray-700 mb-1">Pilih Guru</label>
          <select name="id_guru" id="id_guru" required
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-400 focus:outline-none">
            <?php
            $guru = mysqli_query($conn, "SELECT id, username FROM users WHERE role='guru'");
            while ($g = mysqli_fetch_assoc($guru)) {
                $selected = ($g['id'] == $kelas['id_guru']) ? "selected" : "";
                echo "<option value='{$g['id']}' $selected>{$g['username']}</option>";
            }
            ?>
          </select>
        </div>
        <?php endif; ?>

        <!-- Buttons -->
        <div class="flex items-center justify-between space-x-3">
          <a href="index.php"
             class="w-1/2 py-2 px-4 bg-gray-500 hover:bg-gray-600 text-white text-center font-semibold rounded-lg shadow-md transition">
            Kembali
          </a>
          <button type="submit"
                  class="w-1/2 py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
