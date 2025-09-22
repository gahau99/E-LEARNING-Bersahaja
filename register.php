<?php
include "config.php";

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role     = $_POST['role']; // guru/siswa

    $insert = mysqli_query($conn, "INSERT INTO users (username,password,role) VALUES ('$username','$password','$role')");
    
    if($insert){
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal daftar!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register E-Learning</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex flex-col bg-gradient-to-br from-blue-100 via-purple-100 to-purple-200">

<!-- Navbar -->
<nav class="bg-blue-600 text-white shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <!-- Logo -->
    <a href="home.php" class="font-bold text-xl text-green-400 hover:text-green-200">Bersahaja</a>

    <!-- Menu Desktop -->
    <ul class="hidden md:flex space-x-6">
      <li><a href="login.php" class="hover:text-gray-200">Login</a></li>
      <li><a href="home.php" class="hover:text-gray-200">Dashboard</a></li>
    </ul>

    <!-- User Dropdown -->
    <div class="relative hidden md:block">
      <button id="userMenuBtn" class="flex items-center space-x-2 focus:outline-none">
        <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div id="userMenu" class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded shadow-lg hidden">
        <a href="logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
      </div>
    </div>

    <!-- Mobile button -->
    <button id="mobileBtn" class="md:hidden focus:outline-none">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>

  <!-- Mobile Menu -->
  <ul id="mobileMenu" class="md:hidden hidden bg-blue-500 px-4 py-2 space-y-2">
    <li><a href="login.php" class="block text-white hover:text-gray-200">Login</a></li>
    <li><a href="home.php" class="block text-white hover:text-gray-200">Dashboard</a></li>
  </ul>
</nav>

<!-- Script toggle -->
<script>
  document.getElementById('userMenuBtn')?.addEventListener('click', () => {
    document.getElementById('userMenu').classList.toggle('hidden');
  });

  document.getElementById('mobileBtn').addEventListener('click', () => {
    document.getElementById('mobileMenu').classList.toggle('hidden');
  });
</script>

<!-- Container utama -->
<main class="flex-grow flex items-center justify-center px-4 py-10">
  <div class="bg-gray-900 text-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6">Register E-Learning</h2>
    
    <form method="post" class="space-y-5">
      <!-- Username -->
      <div>
        <label class="block mb-2 text-sm">Username</label>
        <input type="text" name="username" required
          class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-purple-500 focus:outline-none">
      </div>

      <!-- Password -->
      <div>
        <label class="block mb-2 text-sm">Password</label>
        <input type="password" name="password" required
          class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-purple-500 focus:outline-none">
      </div>

      <!-- Role -->
      <div>
        <label class="block mb-2 text-sm">Daftar sebagai</label>
        <select name="role" required
          class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 focus:ring-2 focus:ring-purple-500 focus:outline-none">
          <option value="guru">Guru</option>
          <option value="siswa">Siswa</option>
        </select>
      </div>

      <!-- Tombol -->
      <button type="submit" name="register"
        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-semibold transition">
        Daftar
      </button>
    </form>

    <!-- Link ke login -->
    <p class="mt-6 text-center text-sm text-gray-400">
      Sudah punya akun? 
      <a href="index.php" class="text-purple-400 hover:underline">Login di sini</a>
    </p>
  </div>
</main>

</body>
</html>
