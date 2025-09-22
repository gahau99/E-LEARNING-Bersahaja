<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Silakan login.");
}

$id_user = $_SESSION['id'];
$role = $_SESSION['role'];

// kalau siswa → redirect
if ($role == 'siswa') {
    header("Location: list.php");
    exit;
}

// admin → lihat semua tugas
if ($role == 'admin') {
    $sql = "SELECT t.*, k.nama_kelas, u.username as guru 
            FROM tugas t
            JOIN kelas k ON t.id_kelas = k.id
            JOIN users u ON k.id_guru = u.id
            ORDER BY t.created_at DESC";
}

// guru → lihat tugas kelas yang dia ampu
if ($role == 'guru') {
    $sql = "SELECT t.*, k.nama_kelas 
            FROM tugas t
            JOIN kelas k ON t.id_kelas = k.id
            WHERE k.id_guru = $id_user
            ORDER BY t.created_at DESC";
}

$tugas = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Tugas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- biar responsive -->
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gradient-to-br from-indigo-100 to-indigo-200 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="max-w-6xl mx-auto mt-10 bg-white rounded-2xl shadow-lg p-6 flex-grow">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
      <h2 class="text-2xl font-bold text-indigo-700">📚 Daftar Tugas</h2>
      <div class="flex flex-wrap gap-2">
        <a href="tambah.php" 
           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow transition">
          + Tambah Tugas
        </a>
        <a href="../dashboard.php" 
           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition">
          Dashboard
        </a>
      </div>
    </div>

    <!-- Table (desktop) -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg shadow-sm">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-4 py-2 text-left">Judul</th>
            <th class="px-4 py-2 text-left">Kelas</th>
            <th class="px-4 py-2 text-left">Deadline</th>
            <?php if ($role == 'admin'): ?>
              <th class="px-4 py-2 text-left">Guru</th>
            <?php endif; ?>
            <th class="px-4 py-2 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php while ($row = mysqli_fetch_assoc($tugas)): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2"><?= htmlspecialchars($row['judul']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['nama_kelas']) ?></td>
              <td class="px-4 py-2"><?= $row['deadline'] ?></td>
              <?php if ($role == 'admin'): ?>
                <td class="px-4 py-2"><?= htmlspecialchars($row['guru']) ?></td>
              <?php endif; ?>
              <td class="px-4 py-2 text-center space-x-2">
                <a href="view.php?id=<?= $row['id'] ?>" 
                   class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded shadow text-sm">Lihat</a>
                <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')"
                   class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded shadow text-sm">Hapus</a>
                <a href="nilai.php?id=<?= $row['id'] ?>" 
                   class="px-3 py-1 bg-emerald-500 hover:bg-emerald-600 text-white rounded shadow text-sm">Beri Nilai</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Card Layout (mobile) -->
    <div class="md:hidden space-y-4">
      <?php mysqli_data_seek($tugas, 0); // reset pointer hasil query ?>
      <?php while ($row = mysqli_fetch_assoc($tugas)): ?>
        <div class="bg-white border rounded-lg shadow p-4">
          <h3 class="text-lg font-semibold text-indigo-700"><?= htmlspecialchars($row['judul']) ?></h3>
          <p class="text-sm text-gray-600">Kelas: <?= htmlspecialchars($row['nama_kelas']) ?></p>
          <p class="text-sm text-gray-600">Deadline: <?= $row['deadline'] ?></p>
          <?php if ($role == 'admin'): ?>
            <p class="text-sm text-gray-600">Guru: <?= htmlspecialchars($row['guru']) ?></p>
          <?php endif; ?>
          <div class="mt-3 flex flex-wrap gap-2">
            <a href="view.php?id=<?= $row['id'] ?>" 
               class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded shadow text-sm">Lihat</a>
            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')"
               class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded shadow text-sm">Hapus</a>
            <a href="nilai.php?id=<?= $row['id'] ?>" 
               class="px-3 py-1 bg-emerald-500 hover:bg-emerald-600 text-white rounded shadow text-sm">Beri Nilai</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
