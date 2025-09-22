<?php
session_start();
include "../config.php";

// Cek apakah login
if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Anda harus login dulu.");
}

$id_user = $_SESSION['id'];

// Query semua kelas
$result = mysqli_query($conn, "
    SELECT kelas.*, users.username AS nama_guru
    FROM kelas
    LEFT JOIN users ON kelas.id_guru = users.id
");

// Ambil kelas yang sudah diikuti user
$kelas_saya = [];
$res_kelas_saya = mysqli_query($conn, "SELECT id_kelas FROM kelas_siswa WHERE id_siswa=$id_user");
while($row = mysqli_fetch_assoc($res_kelas_saya)) {
    $kelas_saya[] = $row['id_kelas'];
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Kelas Saya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-6xl mx-auto mt-6 sm:mt-10 bg-white shadow-md rounded-2xl p-4 sm:p-8 w-full">

    <!-- Judul & Tombol -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
      <h2 class="text-lg sm:text-2xl font-bold text-indigo-700 text-center sm:text-left">
        📚 Daftar Kelas Saya
      </h2>
      <a href="../dashboard.php" 
         class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-center w-full sm:w-auto">
        ⬅️ Dashboard
      </a>
    </div>

    <!-- Tabel Responsif -->
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-xs sm:text-sm md:text-base">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-3 sm:px-4 py-2 text-left">ID</th>
            <th class="px-3 sm:px-4 py-2 text-left">Kelas</th>
            <th class="px-3 sm:px-4 py-2 text-left">Deskripsi</th>
            <th class="px-3 sm:px-4 py-2 text-left">Kode</th>
            <th class="px-3 sm:px-4 py-2 text-left">Guru</th>
            <th class="px-3 sm:px-4 py-2 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-3 sm:px-4 py-2"><?= $row['id'] ?></td>
              <td class="px-3 sm:px-4 py-2 font-semibold"><?= htmlspecialchars($row['nama_kelas']) ?></td>
              <td class="px-3 sm:px-4 py-2"><?= htmlspecialchars($row['deskripsi']) ?></td>
              <td class="px-3 sm:px-4 py-2">
                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm">
                  <?= htmlspecialchars($row['kode_kelas']) ?>
                </span>
              </td>
              <td class="px-3 sm:px-4 py-2"><?= htmlspecialchars($row['nama_guru'] ?? '-') ?></td>
              <td class="px-3 sm:px-4 py-2">
                <?php if (in_array($row['id'], $kelas_saya)): ?>
                  <span class="block sm:inline-block w-full sm:w-auto px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs sm:text-sm text-center">
                    ✅ Sudah bergabung
                  </span>
                <?php else: ?>
                  <a href="join.php?id=<?= $row['id'] ?>" 
                     class="block sm:inline-block w-full sm:w-auto px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-xs sm:text-sm text-center">
                     ➕ Gabung
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>
