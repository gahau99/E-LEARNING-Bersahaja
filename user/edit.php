<?php
session_start();
include "../config.php";

// Hanya admin yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

// Ambil ID user dari GET
if (!isset($_GET['id'])) {
    die("ID user tidak ditemukan!");
}
$id_user = intval($_GET['id']);

// Ambil data user saat ini
$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User tidak ditemukan!");
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // kosong = tidak diubah
    $role     = $_POST['role'];

    if ($username == "") {
        $error = "Username tidak boleh kosong!";
    } else {
        if ($password !== "") {
            $password_hashed = md5($password); // ⚠️ sebaiknya gunakan password_hash untuk lebih aman
            $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $password_hashed, $role, $id_user);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
            $stmt->bind_param("ssi", $username, $role, $id_user);
        }

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal update: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit User - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="../bersahaja_logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="bg-gradient-to-br from-blue-100 to-purple-100 min-h-screen flex flex-col font-sans">

  <!-- Navbar -->
  <?php include '../partials/navbar.php'; ?>

  <!-- Container -->
  <div class="flex-grow flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">Edit User</h2>

      <!-- Error message -->
      <?php if (isset($error)): ?>
        <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="space-y-5">
        <!-- Username -->
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
          <input type="text" name="username" id="username" required
                 value="<?= htmlspecialchars($user['username']) ?>"
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diubah"
                 class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>

        <!-- Role -->
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select name="role" id="role" required
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <option value="admin" <?= $user['role']=='admin' ? 'selected' : '' ?>>Admin</option>
            <option value="guru" <?= $user['role']=='guru' ? 'selected' : '' ?>>Guru</option>
            <option value="siswa" <?= $user['role']=='siswa' ? 'selected' : '' ?>>Siswa</option>
          </select>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-between space-x-3">
          <a href="index.php"
             class="w-1/2 py-2 px-4 bg-gray-500 hover:bg-gray-600 text-white text-center font-semibold rounded-lg shadow-md transition">
            Kembali
          </a>
          <button type="submit"
                  class="w-1/2 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <?php include '../partials/footer.php'; ?>

</body>
</html>
