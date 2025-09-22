<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    die("Akses ditolak!");
}

$id_siswa = $_SESSION['id'];
$id_tugas = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $dir = "../uploads/tugas/";

    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $filename = time() . "_" . basename($file);
    move_uploaded_file($tmp, $dir . $filename);

    mysqli_query($conn, "INSERT INTO tugas_siswa (id_tugas, id_siswa, file, submitted_at) 
                         VALUES ($id_tugas, $id_siswa, '$filename', NOW())");

    header("Location: list.php");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kumpulkan Tugas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <main class="flex-grow flex items-center justify-center px-4">
    <div class="w-full max-w-lg bg-white shadow-lg rounded-2xl p-8">
      
      <!-- Judul -->
      <h2 class="text-2xl font-bold text-indigo-700 mb-6 text-center">
        📝 Kumpulkan Tugas
      </h2>

      <!-- Form -->
      <form method="post" enctype="multipart/form-data" class="space-y-5">
        
        <!-- Input file -->
        <div>
          <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
            Pilih File Tugas
          </label>
          <input type="file" id="file" name="file" required
            class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer 
                   focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 file:mr-4 
                   file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold 
                   file:bg-indigo-600 file:text-white hover:file:bg-indigo-700"/>
          <p class="mt-2 text-xs text-gray-500">Format: PDF, DOCX, JPG, PNG (max 10MB)</p>
        </div>

        <!-- Tombol submit -->
        <button type="submit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 rounded-lg shadow transition">
          ⬆️ Upload
        </button>
      </form>

      <!-- Link kembali -->
      <div class="mt-6 text-center">
        <a href="list.php" class="text-gray-600 hover:text-indigo-600 font-medium transition">
          ⬅️ Kembali ke daftar tugas
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>

