<?php
session_start();
include "../config.php";

// hanya admin yang boleh akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar User - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gradient-to-br from-blue-100 to-purple-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <main class="flex-grow"> <!-- ✅ flex-grow agar isi dorong footer ke bawah -->
    <div class="max-w-5xl mx-auto mt-6 sm:mt-10 bg-white rounded-2xl shadow-lg p-4 sm:p-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <h2 class="text-xl sm:text-2xl font-bold text-blue-600">Daftar User</h2>
        <div class="flex flex-wrap gap-2">
          <a href="tambah.php"
             class="px-3 py-2 sm:px-4 sm:py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow text-sm sm:text-base transition">
            + Tambah User
          </a>
          <a href="../dashboard.php"
             class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow text-sm sm:text-base transition">
            Dashboard
          </a>
        </div>
      </div>

      <!-- Table responsive -->
      <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm sm:text-base">
          <thead>
            <tr class="bg-blue-100 text-left">
              <th class="px-3 sm:px-4 py-2 border-b">ID</th>
              <th class="px-3 sm:px-4 py-2 border-b">Username</th>
              <th class="px-3 sm:px-4 py-2 border-b">Role</th>
              <th class="px-3 sm:px-4 py-2 border-b text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-3 sm:px-4 py-2 border-b"><?= $row['id'] ?></td>
              <td class="px-3 sm:px-4 py-2 border-b"><?= htmlspecialchars($row['username']) ?></td>
              <td class="px-3 sm:px-4 py-2 border-b capitalize"><?= $row['role'] ?></td>
              <td class="px-3 sm:px-4 py-2 border-b text-center flex flex-col sm:flex-row gap-2 justify-center">
                <a href="edit.php?id=<?= $row['id'] ?>"
                   class="px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md text-xs sm:text-sm">
                  Edit
                </a>
                <a href="hapus.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Yakin ingin menghapus user ini?')"
                   class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs sm:text-sm">
                  Hapus
                </a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
