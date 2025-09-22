<?php
if (!isset($_SESSION)) { session_start(); }

// Kalau sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'guru') {
        header("Location: views/dashboard/guru.php");
        exit;
    } else {
        header("Location: views/dashboard/siswa.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Bersahaja - Belajar Lebih Mudah</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="bersahaja_logo.png">
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <header class="bg-blue-600 shadow sticky top-0 z-50">
  <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
    
    <!-- Logo -->
    <div class="flex items-center gap-2">
      <span class="font-bold text-xl text-white">Bersahaja</span>
    </div>

    <!-- Menu -->
    <div class="flex items-center gap-4">
      <a href="login.php" 
        class="px-5 py-2 bg-white text-green-600 font-semibold rounded-lg shadow hover:bg-gray-100 transition">
        Login
      </a>

      <a href="register.php" 
        class="px-5 py-2 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-green-600 transition">
        Register
      </a>
    </div>
    
  </div>
</header>


  <!-- Hero Section -->
  <section 
  class="relative bg-cover bg-center text-white py-20" 
  style="background-image: url('bacgroud.jpg');"
>
  <!-- Overlay gelap biar teks tetap jelas -->
  <div class="absolute inset-0 bg-black/50"></div>

  <div class="relative max-w-4xl mx-auto text-center px-6">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4">
      Belajar Lebih Mudah dengan Bersahaja
    </h1>
    <p class="text-lg md:text-xl mb-6">
      Platform e-learning untuk siswa dan guru, mengelola kelas, materi, dan tugas dalam satu tempat.
    </p>
    <div class="flex justify-center gap-4">
      <a href="login.php" 
         class="px-6 py-3 bg-white text-green-600 font-semibold rounded-xl shadow hover:bg-gray-100">
        Mulai Sekarang
      </a>
      <a href="#fitur" 
         class="px-6 py-3 border border-white font-semibold rounded-xl hover:bg-green-600">
        Lihat Fitur
      </a>
    </div>
  </div>
</section>


  <!-- Fitur -->
  <section id="fitur" class="py-16 bg-gray-100">
    <div class="max-w-6xl mx-auto px-6 text-center">
      <h2 class="text-3xl font-bold mb-10 text-gray-800">Fitur Unggulan</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
          <div class="text-green-600 text-4xl mb-4">📚</div>
          <h3 class="text-xl font-semibold mb-2">Kelas Online</h3>
          <p class="text-gray-600">Guru dapat membuat kelas, siswa dapat bergabung dan belajar bersama.</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
          <div class="text-green-600 text-4xl mb-4">📝</div>
          <h3 class="text-xl font-semibold mb-2">Tugas & Nilai</h3>
          <p class="text-gray-600">Upload, koreksi, dan nilai tugas lebih cepat dan transparan.</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
          <div class="text-green-600 text-4xl mb-4">💬</div>
          <h3 class="text-xl font-semibold mb-2">Diskusi</h3>
          <p class="text-gray-600">Komentar dan diskusi antar siswa dan guru di dalam kelas.</p>
        </div>

      </div>
    </div>
  </section>

<!-- Section Logo & Motto -->
<section id="tentang" class="py-20 bg-gray-50">
  <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 items-center gap-10 px-6">
    
    <!-- Logo di kiri -->
    <div class="flex justify-center md:justify-end">
      <img src="bersahaja_logo.png" alt="Logo Bersahaja" class="w-40 h-40 object-contain">
    </div>

    <!-- Keterangan di kanan -->
    <div>
      <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
        Bersahaja
      </h2>
      <p class="text-lg text-gray-600 mb-4">
        Platform e-learning yang sederhana namun bermakna.  
        Dirancang untuk membantu siswa dan guru mengelola kelas, materi, serta tugas dengan cara yang lebih efektif.
      </p>
      <p class="italic text-green-600 font-semibold text-xl">
        "Belajar Lebih Mudah, Setiap Hari"
      </p>
    </div>

  </div>
</section>


  <!-- Footer -->
  <?php include __DIR__ . '../partials/footer.php'; ?>

</body>
</html>
