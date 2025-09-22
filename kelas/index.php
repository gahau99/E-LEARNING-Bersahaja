<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa melihat halaman ini.");
}

$result = mysqli_query($conn, "
    SELECT kelas.*, users.username AS nama_guru
    FROM kelas
    LEFT JOIN users ON kelas.id_guru = users.id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Kelas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-blue-100 via-indigo-200 to-purple-200 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="container mx-auto px-4 py-12 pb-80">
    <div class="flex-grow max-w-6xl mx-auto mt-10 bg-white rounded-2xl shadow-lg p-6 sm:p-8">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <h2 class="text-xl sm:text-2xl font-bold text-blue-600">Daftar Kelas</h2>
        <div class="flex flex-wrap gap-2">
          <a href="tambah.php" 
             class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow text-sm sm:text-base">
            + Tambah Kelas
          </a>
          <a href="../dashboard.php" 
             class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow text-sm sm:text-base">
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
              <th class="px-3 sm:px-4 py-2 border-b">Kelas</th>
              <th class="px-3 sm:px-4 py-2 border-b">Deskripsi</th>
              <th class="px-3 sm:px-4 py-2 border-b">Kode</th>
              <th class="px-3 sm:px-4 py-2 border-b">Guru</th>
              <th class="px-3 sm:px-4 py-2 border-b text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-3 sm:px-4 py-2 border-b"><?= $row['id'] ?></td>
              <td class="px-3 sm:px-4 py-2 border-b font-semibold"><?= htmlspecialchars($row['nama_kelas']) ?></td>
              <td class="px-3 sm:px-4 py-2 border-b"><?= htmlspecialchars($row['deskripsi']) ?></td>
              <td class="px-3 sm:px-4 py-2 border-b text-blue-600 font-mono"><?= htmlspecialchars($row['kode_kelas']) ?></td>
              <td class="px-3 sm:px-4 py-2 border-b"><?= htmlspecialchars($row['nama_guru'] ?? '-') ?></td>
              <td class="px-3 sm:px-4 py-2 border-b text-center space-y-1 sm:space-y-0 sm:space-x-2 flex flex-col sm:flex-row justify-center">
                <?php if($_SESSION['role']=='admin' || $_SESSION['id']==$row['id_guru']): ?>
                  <a href="edit.php?id=<?= $row['id'] ?>" 
                     class="px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md text-xs sm:text-sm">
                    Edit
                  </a>
                  <a href="hapus.php?id=<?= $row['id'] ?>" 
                     onclick="return confirm('Yakin ingin menghapus kelas ini?')" 
                     class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs sm:text-sm">
                    Hapus
                  </a>
                <?php else: ?>
                  <span class="text-gray-400 italic">-</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
