<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$id_user = $_SESSION['id'];
$role = $_SESSION['role'];

// ambil daftar kelas
if ($role == 'admin') {
    $kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
} else {
    // guru hanya bisa pilih kelas yang dia ampu
    $kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_guru = $id_user ORDER BY nama_kelas ASC");
}

// proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kelas = intval($_POST['id_kelas']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

    $sql = "INSERT INTO tugas (id_kelas, judul, deskripsi, deadline, created_at) 
            VALUES ($id_kelas, '$judul', '$deskripsi', '$deadline', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menambah tugas: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas - Bersahaja</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <?php include '../partials/navbar.php'; ?>

    <main class="flex-grow container mx-auto px-4 py-8">
      

        <!-- Page Title -->
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Tugas</h2>

        <!-- Form Container -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="post" class="space-y-6">
                <!-- Kelas Selection -->
                <div>
                    <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas:</label>
                    <select name="id_kelas" id="id_kelas" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($row = mysqli_fetch_assoc($kelas)): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kelas']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Judul Tugas -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas:</label>
                    <input type="text" name="judul" id="judul" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi:</label>
                    <textarea name="deskripsi" id="deskripsi" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Deadline -->
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">Deadline:</label>
                    <input type="datetime-local" name="deadline" id="deadline" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-4">
                    <a href="index.php" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-150">Kembali</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-150">Simpan</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
  <?php include '../partials/footer.php'; ?>
</body>
</html>