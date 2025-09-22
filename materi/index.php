<?php
session_start();
include "../config.php";

// hanya guru dan admin yang bisa mengakses halaman ini
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$result = mysqli_query($conn, "
    SELECT materi.*, kelas.nama_kelas, users.username AS pembuat
    FROM materi
    JOIN kelas ON materi.id_kelas = kelas.id
    JOIN users ON materi.dibuat_oleh = users.id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Materi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-indigo-100 to-blue-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="max-w-6xl mx-auto mt-6 sm:mt-10 bg-white rounded-2xl shadow-lg p-4 sm:p-8 flex-grow">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <h2 class="text-lg sm:text-2xl font-bold text-indigo-600">📚 Daftar Materi</h2>
      <div class="flex flex-wrap gap-2">
        <a href="tambah.php" class="px-3 sm:px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow transition text-xs sm:text-sm md:text-base">
          + Tambah Materi
        </a>
        <a href="../dashboard.php" class="px-3 sm:px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg shadow transition text-xs sm:text-sm md:text-base">
          Dashboard
        </a>
      </div>
    </div>

    <!-- Tabel view (desktop & tablet) -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-[700px] w-full border-collapse text-sm sm:text-base">
        <thead>
          <tr class="bg-indigo-100 text-left">
            <th class="px-3 sm:px-4 py-2 border-b">ID</th>
            <th class="px-3 sm:px-4 py-2 border-b">Kelas</th>
            <th class="px-3 sm:px-4 py-2 border-b">Judul</th>
            <th class="px-3 sm:px-4 py-2 border-b">File</th>
            <th class="px-3 sm:px-4 py-2 border-b">Pembuat</th>
            <th class="px-3 sm:px-4 py-2 border-b">Status</th>
            <th class="px-3 sm:px-4 py-2 border-b text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-3 sm:px-4 py-2 border-b"><?= $row['id'] ?></td>
            <td class="px-3 sm:px-4 py-2 border-b"><?= htmlspecialchars($row['nama_kelas']) ?></td>
            <td class="px-3 sm:px-4 py-2 border-b"><?= htmlspecialchars($row['judul']) ?></td>
            <td class="px-3 sm:px-4 py-2 border-b">
              <?php if($row['file']): ?>
                <a href="view.php?id=<?= $row['id'] ?>" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
              <?php else: ?>
                <span class="text-gray-400">-</span>
              <?php endif; ?>
            </td>
            <td class="px-3 sm:px-4 py-2 border-b"><?= htmlspecialchars($row['pembuat']) ?></td>
            <td class="px-3 sm:px-4 py-2 border-b">
              <?php if($row['status']=='pending'): ?>
                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Pending</span>
              <?php elseif($row['status']=='approved'): ?>
                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Approved</span>
              <?php elseif($row['status']=='rejected'): ?>
                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Rejected</span>
              <?php else: ?>
                <?= htmlspecialchars($row['status']) ?>
              <?php endif; ?>
            </td>
            <td class="px-3 sm:px-4 py-2 border-b text-center">
              <div class="flex justify-center gap-1">
                <?php if($_SESSION['role']=='admin' && $row['status'] === 'pending'): ?>
                  <a href="approve.php?id=<?= $row['id'] ?>" class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md text-xs">Approve</a>
                  <a href="reject.php?id=<?= $row['id'] ?>" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs">Reject</a>
                <?php endif; ?>
                <?php if($_SESSION['role']=='admin' || $_SESSION['id']==$row['dibuat_oleh']): ?>
                  <a href="edit.php?id=<?= $row['id'] ?>" class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md text-xs">Edit</a>
                  <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs">Hapus</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Card view (mobile) -->
    <div class="sm:hidden space-y-4">
      <?php mysqli_data_seek($result, 0); ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
      <div class="bg-white rounded-lg shadow p-4 border">
        <h3 class="font-semibold text-indigo-600"><?= htmlspecialchars($row['judul']) ?></h3>
        <p class="text-sm text-gray-600">Kelas: <?= htmlspecialchars($row['nama_kelas']) ?></p>
        <p class="text-sm text-gray-600">Pembuat: <?= htmlspecialchars($row['pembuat']) ?></p>
        <p class="text-sm mt-1">Status: 
          <?php if($row['status']=='pending'): ?>
            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Pending</span>
          <?php elseif($row['status']=='approved'): ?>
            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Approved</span>
          <?php elseif($row['status']=='rejected'): ?>
            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Rejected</span>
          <?php endif; ?>
        </p>
        <div class="mt-3 flex flex-wrap gap-2">
          <?php if($row['file']): ?>
            <a href="view.php?id=<?= $row['id'] ?>" target="_blank" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-xs">Lihat</a>
          <?php endif; ?>
          <?php if($_SESSION['role']=='admin' && $row['status'] === 'pending'): ?>
            <a href="approve.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md text-xs">Approve</a>
            <a href="reject.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs">Reject</a>
          <?php endif; ?>
          <?php if($_SESSION['role']=='admin' || $_SESSION['id']==$row['dibuat_oleh']): ?>
            <a href="edit.php?id=<?= $row['id'] ?>" class="px-3 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md text-xs">Edit</a>
            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-md text-xs">Hapus</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
