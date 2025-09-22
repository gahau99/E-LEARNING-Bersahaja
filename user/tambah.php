<?php
session_start();
include "../config.php";

// hanya admin yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // ⚠️ sebaiknya gunakan bcrypt/ password_hash() untuk lebih aman
    $role     = $_POST['role'];

    $sql = "INSERT INTO users (username, password, role) 
            VALUES ('$username', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah User - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-blue-100 to-purple-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="flex-grow flex items-center justify-center px-4 py-8 pb-80">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Tambah User</h2>

      <form method="POST" class="space-y-5">
        <!-- Username -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <input type="text" name="username" id="username" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" id="password" required
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <!-- Role -->
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select name="role" id="role" required
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <option value="admin">Admin</option>
            <option value="guru">Guru</option>
            <option value="siswa">Siswa</option>
          </select>
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
          Simpan
        </button>
      </form>

      <!-- Back link -->
      <div class="mt-6 text-center">
        <a href="index.php" class="text-blue-600 hover:underline">← Kembali ke Daftar User</a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
