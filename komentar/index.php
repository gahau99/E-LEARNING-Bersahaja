<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa melihat halaman ini.");
}

// =======================
// Filter & Search
// =======================
$filter_parent = $_GET['parent'] ?? '';  // 'materi', 'tugas', 'tugas_siswa'
$search_query  = trim($_GET['q'] ?? '');

// =======================
// Pagination
// =======================
$limit = 10; // komentar per halaman
$page  = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// =======================
// Query utama dengan filter + search + limit
// =======================
$where = [];
if ($filter_parent === 'materi') {
    $where[] = 'k.materi_id IS NOT NULL';
} elseif ($filter_parent === 'tugas') {
    $where[] = 'k.tugas_id IS NOT NULL';
} elseif ($filter_parent === 'tugas_siswa') {
    $where[] = 'k.tugas_siswa_id IS NOT NULL';
}

// Pencarian di komentar atau nama user
if ($search_query !== '') {
    $search_esc = mysqli_real_escape_string($conn, $search_query);
    $where[] = "(k.isi LIKE '%$search_esc%' OR u.username LIKE '%$search_esc%')";
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung total data untuk pagination
$total_sql = "SELECT COUNT(*) AS total 
              FROM komentar k 
              LEFT JOIN users u ON k.id_user = u.id
              $where_sql";
$total_res = mysqli_query($conn, $total_sql);
$total_row = mysqli_fetch_assoc($total_res);
$total_data = $total_row['total'];
$total_page = ceil($total_data / $limit);

// Ambil data komentar
$sql = "
    SELECT k.id, k.materi_id, k.tugas_id, k.tugas_siswa_id, k.id_user, k.isi, k.dibuat_pada, 
           u.username AS nama_user,
           m.judul AS judul_materi,
           t.judul AS judul_tugas
    FROM komentar k
    LEFT JOIN users u ON k.id_user = u.id
    LEFT JOIN materi m ON k.materi_id = m.id
    LEFT JOIN tugas t  ON k.tugas_id = t.id
    $where_sql
    ORDER BY k.dibuat_pada DESC
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($conn, $sql);
if (!$result) die("Query gagal: " . mysqli_error($conn));
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Komentar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <main class="flex-grow max-w-7xl mx-auto mt-6 sm:mt-10 bg-white shadow-md rounded-2xl p-4 sm:p-8">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
      <h2 class="text-xl sm:text-2xl font-bold text-indigo-700">💬 Daftar Komentar</h2>
      <div class="flex flex-wrap gap-2">
        <a href="tambah.php" 
           class="px-3 sm:px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm sm:text-base">
           ➕ Tambah
        </a>
        <a href="../dashboard.php" 
           class="px-3 sm:px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm sm:text-base">
           🏠 Dashboard
        </a>
      </div>
    </div>

    <!-- Filter & Search -->
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end mb-6">
      <!-- Filter -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Filter Parent</label>
        <select name="parent" onchange="this.form.submit()" 
                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Semua</option>
          <option value="materi" <?= $filter_parent=='materi'?'selected':'' ?>>Materi</option>
          <option value="tugas" <?= $filter_parent=='tugas'?'selected':'' ?>>Tugas</option>
          <option value="tugas_siswa" <?= $filter_parent=='tugas_siswa'?'selected':'' ?>>Tugas Siswa</option>
        </select>
      </div>

      <!-- Pencarian -->
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
        <div class="flex">
          <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" 
                 placeholder="Cari komentar/nama user"
                 class="flex-1 border rounded-l-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base">
          <button type="submit" 
                  class="px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 text-sm sm:text-base">
            Cari
          </button>
        </div>
      </div>
    </form>

    <!-- Tabel -->
    <div class="overflow-x-auto">
      <table class="w-full border-collapse border border-gray-300 text-xs sm:text-sm">
        <thead class="bg-indigo-600 text-white">
          <tr>
            <th class="px-2 sm:px-4 py-2 border">ID</th>
            <th class="px-2 sm:px-4 py-2 border">Parent</th>
            <th class="px-2 sm:px-4 py-2 border">Judul</th>
            <th class="px-2 sm:px-4 py-2 border">User</th>
            <th class="px-2 sm:px-4 py-2 border">Isi</th>
            <th class="px-2 sm:px-4 py-2 border">Dibuat Pada</th>
            <th class="px-2 sm:px-4 py-2 border">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-2 sm:px-4 py-2 border text-center"><?= $row['id'] ?></td>
              <td class="px-2 sm:px-4 py-2 border">
                <?php 
                if (!empty($row['materi_id'])) echo "Materi";
                elseif (!empty($row['tugas_id'])) echo "Tugas";
                elseif (!empty($row['tugas_siswa_id'])) echo "Tugas Siswa";
                else echo "-";
                ?>
              </td>
              <td class="px-2 sm:px-4 py-2 border">
                <?php 
                if (!empty($row['materi_id'])) echo htmlspecialchars($row['judul_materi'] ?? '-');
                elseif (!empty($row['tugas_id'])) echo htmlspecialchars($row['judul_tugas'] ?? '-');
                else echo "-";
                ?>
              </td>
              <td class="px-2 sm:px-4 py-2 border"><?= htmlspecialchars($row['nama_user'] ?? '-') ?></td>
              <td class="px-2 sm:px-4 py-2 border"><?= htmlspecialchars($row['isi'] ?? '-') ?></td>
              <td class="px-2 sm:px-4 py-2 border"><?= $row['dibuat_pada'] ?></td>
              <td class="px-2 sm:px-4 py-2 border text-center">
                <?php if($_SESSION['role']=='admin' || $_SESSION['id']==$row['id_user']): ?>
                  <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">✏️ Edit</a> | 
                  <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus komentar ini?')" class="text-red-600 hover:underline">🗑️ Hapus</a>
                <?php else: ?>
                  <span class="text-gray-400">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex flex-wrap justify-center gap-2">
      <?php for($p=1; $p<=$total_page; $p++): ?>
        <a href="?page=<?= $p ?><?= $filter_parent ? '&parent='.$filter_parent : '' ?><?= $search_query ? '&q='.urlencode($search_query) : '' ?>"
           class="px-2 sm:px-3 py-1 rounded <?= $p==$page ? 'bg-indigo-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
          <?= $p ?>
        </a>
      <?php endfor; ?>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>
