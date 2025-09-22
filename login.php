<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Login gagal!";
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Login E-Learning</title>
    <link rel="icon" type="image/x-icon" href="bersahaja_logo.png">
</head>

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

<script>
  document.getElementById('userMenuBtn')?.addEventListener('click', () => {
    document.getElementById('userMenu').classList.toggle('hidden');
  });

  document.getElementById('mobileBtn').addEventListener('click', () => {
    document.getElementById('mobileMenu').classList.toggle('hidden');
  });
</script>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 via-indigo-200 to-purple-200">
        <div class="bg-white dark:bg-gray-800 rounded-xl px-8 py-10 shadow-2xl ring-1 ring-gray-900/10 w-full max-w-md">
            <form method="post" class="space-y-6">
                <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-6">Login E-Learning</h2>
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:bg-gray-700 dark:text-white" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:bg-gray-700 dark:text-white" required>
                </div>
                <button type="submit" name="login"
                    class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-200">Login</button>
            </form>
            <div class="mt-6 text-center">
                <a href="register.php" class="text-indigo-600 hover:underline font-medium">Daftar akun baru</a>
            </div>
        </div>
    </div>
</body>

</html>