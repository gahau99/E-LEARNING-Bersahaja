<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'siswa') {
    die("Akses hanya untuk siswa!");
}

$id_user = $_SESSION['id'];

// Ambil kelas & materi yang diikuti siswa
$result = mysqli_query($conn, "
    SELECT materi.*, kelas.nama_kelas
    FROM materi
    JOIN kelas ON materi.id_kelas = kelas.id
    JOIN kelas_siswa ks ON ks.id_kelas = kelas.id
    WHERE ks.id_siswa = $id_user AND materi.status = 'approved'
");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Materi Saya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-6xl mx-auto mt-6 sm:mt-10 bg-white shadow-md rounded-2xl p-4 sm:p-8 w-full">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
      <h2 class="text-lg sm:text-2xl font-bold text-indigo-700">📖 Materi Saya</h2>
      <a href="../dashboard.php" 
         class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-center w-full sm:w-auto">
        ⬅️ Dashboard
      </a>
    </div>

    <!-- Tabel untuk layar medium ke atas -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-4 py-2 text-left">ID</th>
            <th class="px-4 py-2 text-left">Kelas</th>
            <th class="px-4 py-2 text-left">Judul</th>
            <th class="px-4 py-2 text-left">Deskripsi</th>
            <th class="px-4 py-2 text-left">File</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2"><?= $row['id'] ?></td>
              <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($row['nama_kelas']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['judul']) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['deskripsi']) ?></td>
              <td class="px-4 py-2 space-x-2">
                <?php if($row['file']): ?>
                  <a href="view.php?id=<?= $row['id'] ?>" target="_blank"
                     class="px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-xs">
                    👀 Lihat
                  </a>
                  <a href="../uploads/<?= htmlspecialchars($row['file']) ?>" target="_blank"
                     class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-xs">
                    ⬇️ Download
                  </a>
                <?php else: ?>
                  <span class="text-gray-500">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Card view untuk layar kecil -->
    <div class="sm:hidden space-y-4">
      <?php mysqli_data_seek($result, 0); // reset pointer data ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="border border-gray-200 rounded-lg p-4 shadow-sm bg-gray-50">
          <p class="text-sm text-gray-500">ID: <?= $row['id'] ?></p>
          <h3 class="text-base font-bold text-indigo-700"><?= htmlspecialchars($row['judul']) ?></h3>
          <p class="text-sm text-gray-700"><span class="font-semibold">Kelas:</span> <?= htmlspecialchars($row['nama_kelas']) ?></p>
          <p class="text-sm text-gray-700"><span class="font-semibold">Deskripsi:</span> <?= htmlspecialchars($row['deskripsi']) ?></p>
          <div class="mt-3 flex gap-2">
            <?php if($row['file']): ?>
              <a href="view.php?id=<?= $row['id'] ?>" target="_blank"
                 class="flex-1 px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center text-sm">
                👀 Lihat
              </a>
              <a href="../uploads/<?= htmlspecialchars($row['file']) ?>" target="_blank"
                 class="flex-1 px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center text-sm">
                ⬇️ Download
              </a>
            <?php else: ?>
              <span class="text-gray-500">Tidak ada file</span>
            <?php endif; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>
