<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    die("Akses ditolak!");
}

$id_siswa = $_SESSION['id'];

// Ambil kelas siswa
$qKelas = mysqli_query($conn, "SELECT id_kelas FROM kelas_siswa WHERE id_siswa = $id_siswa");
$kelas_ids = [];
while ($r = mysqli_fetch_assoc($qKelas)) {
    $kelas_ids[] = $r['id_kelas'];
}

if (empty($kelas_ids)) {
    die("Anda belum terdaftar di kelas manapun");
}

$kelas_in = implode(",", $kelas_ids);
$tugas = mysqli_query($conn, "SELECT * FROM tugas WHERE id_kelas IN ($kelas_in) ORDER BY deadline ASC");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Tugas Saya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-6xl mx-auto mt-6 sm:mt-10 bg-white shadow-md rounded-2xl p-4 sm:p-8 w-full">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
      <h2 class="text-xl sm:text-2xl font-bold text-indigo-700 text-center sm:text-left">
        📝 Daftar Tugas Saya
      </h2>
      <a href="../dashboard.php" 
         class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-center w-full sm:w-auto">
        ⬅️ Dashboard
      </a>
    </div>

    <!-- 📌 Tabel (hanya tampil di layar >= md) -->
    <div class="overflow-x-auto hidden md:block">
      <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-4 py-2 text-left">Judul</th>
            <th class="px-4 py-2 text-left">Deskripsi</th>
            <th class="px-4 py-2 text-left">Deadline</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php while($row = mysqli_fetch_assoc($tugas)): ?>
            <?php
              $cek = mysqli_query($conn, "SELECT * FROM tugas_siswa WHERE id_tugas={$row['id']} AND id_siswa=$id_siswa");
              $submit = mysqli_fetch_assoc($cek);
            ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($row['judul']) ?></td>
              <td class="px-4 py-2"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>
              <td class="px-4 py-2"><?= htmlspecialchars($row['deadline']) ?></td>
              <td class="px-4 py-2">
                <?php if($submit): ?>
                  <span class="px-2 py-1 rounded-lg text-sm bg-green-100 text-green-700">
                    ✅ Sudah Submit (<?= htmlspecialchars($submit['submitted_at']) ?>)
                  </span>
                  <?php if($submit['nilai'] !== null): ?>
                    <br><span class="text-indigo-600 font-medium">Nilai: <?= htmlspecialchars($submit['nilai']) ?></span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="px-2 py-1 rounded-lg text-sm bg-red-100 text-red-700">
                    ❌ Belum Submit
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2">
                <?php if(!$submit): ?>
                  <a href="submit.php?id=<?= $row['id'] ?>"
                     class="px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    📤 Kumpulkan
                  </a>
                <?php else: ?>
                  <a href="view.php?id=<?= $submit['id'] ?>"
                     class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                    👀 Lihat Jawaban
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- 📌 Card (hanya tampil di layar < md) -->
    <div class="space-y-4 md:hidden">
      <?php mysqli_data_seek($tugas, 0); // reset pointer agar bisa dipakai ulang ?>
      <?php while($row = mysqli_fetch_assoc($tugas)): ?>
        <?php
          $cek = mysqli_query($conn, "SELECT * FROM tugas_siswa WHERE id_tugas={$row['id']} AND id_siswa=$id_siswa");
          $submit = mysqli_fetch_assoc($cek);
        ?>
        <div class="p-4 border rounded-lg shadow-sm bg-gray-50">
          <h3 class="font-bold text-lg text-indigo-700"><?= htmlspecialchars($row['judul']) ?></h3>
          <p class="text-gray-700 text-sm mt-1"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
          <p class="text-gray-500 text-sm mt-1">⏰ <?= htmlspecialchars($row['deadline']) ?></p>
          <div class="mt-2">
            <?php if($submit): ?>
              <span class="block px-2 py-1 rounded-lg text-sm bg-green-100 text-green-700">
                ✅ Sudah Submit (<?= htmlspecialchars($submit['submitted_at']) ?>)
              </span>
              <?php if($submit['nilai'] !== null): ?>
                <span class="text-indigo-600 font-medium">Nilai: <?= htmlspecialchars($submit['nilai']) ?></span>
              <?php endif; ?>
            <?php else: ?>
              <span class="block px-2 py-1 rounded-lg text-sm bg-red-100 text-red-700">
                ❌ Belum Submit
              </span>
            <?php endif; ?>
          </div>
          <div class="mt-3">
            <?php if(!$submit): ?>
              <a href="submit.php?id=<?= $row['id'] ?>"
                 class="block w-full px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center text-sm">
                📤 Kumpulkan
              </a>
            <?php else: ?>
              <a href="view.php?id=<?= $submit['id'] ?>"
                 class="block w-full px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-center text-sm">
                👀 Lihat Jawaban
              </a>
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
