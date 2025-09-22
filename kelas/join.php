<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    die("Akses ditolak!");
}

$id_siswa = $_SESSION['id'];

// ambil daftar kelas yang belum diikuti siswa
$q = mysqli_query($conn, "
    SELECT k.* 
    FROM kelas k 
    WHERE k.id NOT IN (
        SELECT id_kelas FROM kelas_siswa WHERE id_siswa=$id_siswa
    )
    ORDER BY k.nama_kelas
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = intval($_POST['id_kelas']);
    
    // cek apakah sudah gabung
    $cek = mysqli_query($conn, "SELECT * FROM kelas_siswa WHERE id_siswa=$id_siswa AND id_kelas=$id_kelas");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO kelas_siswa (id_siswa, id_kelas) VALUES ($id_siswa, $id_kelas)");
        echo "<p style='color:green'>Berhasil gabung ke kelas!</p>";
    } else {
        echo "<p style='color:red'>Anda sudah terdaftar di kelas ini.</p>";
    }
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Gabung Kelas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <main class="flex-grow flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-8">
      
      <!-- Judul -->
      <h2 class="text-2xl font-bold text-indigo-700 mb-6 text-center">
        📘 Gabung Kelas
      </h2>

      <!-- Form -->
      <form method="post" class="space-y-5">
        <!-- Select -->
        <div>
          <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-2">
            Pilih Kelas
          </label>
          <select id="id_kelas" name="id_kelas" required
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Pilih --</option>
            <?php while($row = mysqli_fetch_assoc($q)): ?>
              <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['nama_kelas']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Tombol -->
        <button type="submit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg shadow transition">
          Gabung
        </button>
      </form>

      <!-- Link kembali -->
      <div class="mt-6 text-center">
        <a href="list.php" class="text-gray-600 hover:text-indigo-600 font-medium transition">
          ⬅️ Kembali ke daftar kelas
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>
