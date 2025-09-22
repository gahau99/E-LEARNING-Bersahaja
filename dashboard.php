<?php
session_start();
if (!isset($_SESSION['id'])) header("Location: index.php");
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="bersahaja_logo.png">
    <title>Dashboard</title>
</head>
<body class="bg-gradient-to-br from-blue-100 via-indigo-200 to-purple-200 min-h-screen flex flex-col">

    <!-- Navbar -->
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-12 flex-grow">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-indigo-700 mb-4">
                Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?> 👋
            </h1>
            <p class="text-gray-600 text-lg">
                Role Anda: <span class="font-semibold text-indigo-600"><?= htmlspecialchars($_SESSION['role']); ?></span>
            </p>
        </div>

        <!-- Quick Stats (khusus admin) -->
        <?php if ($_SESSION['role'] == "admin") { ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                <div class="text-indigo-600 text-3xl mb-2">👤</div>
                <p class="font-bold text-lg">5</p>
                <p class="text-gray-500 text-sm">Total User</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                <div class="text-blue-500 text-3xl mb-2">🏫</div>
                <p class="font-bold text-lg">3</p>
                <p class="text-gray-500 text-sm">Kelas</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                <div class="text-purple-500 text-3xl mb-2">📚</div>
                <p class="font-bold text-lg">5</p>
                <p class="text-gray-500 text-sm">Materi</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition text-center">
                <div class="text-green-500 text-3xl mb-2">✍️</div>
                <p class="font-bold text-lg">3</p>
                <p class="text-gray-500 text-sm">Tugas</p>
            </div>
        </div>
        <?php } ?>

        <!-- Menu Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php if ($_SESSION['role'] == "admin") { ?>
                <a href='user/index.php' class="card-dashboard bg-indigo-600">👤 Kelola User</a>
                <a href='kelas/index.php' class="card-dashboard bg-indigo-500">🏫 Kelola Kelas</a>
                <a href='materi/index.php' class="card-dashboard bg-purple-500">📚 Materi</a>
                <a href='tugas/index.php' class="card-dashboard bg-blue-500">✍️ Tugas</a>
                <a href='komentar/index.php' class="card-dashboard bg-green-500">💬 Komentar</a>
            <?php } elseif ($_SESSION['role'] == "guru") { ?>
                <a href='kelas/index.php' class="card-dashboard bg-indigo-500">🏫 Kelola Kelas</a>
                <a href='materi/index.php' class="card-dashboard bg-purple-500">📚 Materi</a>
                <a href='tugas/index.php' class="card-dashboard bg-blue-500">✍️ Tugas</a>
                <a href='komentar/index.php' class="card-dashboard bg-green-500">💬 Komentar</a>
            <?php } else { ?>
                <a href='kelas/list.php' class="card-dashboard bg-indigo-400">🏫 Daftar Kelas</a>
                <a href='materi/list.php' class="card-dashboard bg-purple-400">📚 Materi</a>
                <a href='tugas/list.php' class="card-dashboard bg-blue-400">✍️ Tugas</a>
            <?php } ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>

    <style>
        .card-dashboard {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border-radius: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            text-decoration: none;
        }
        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
    </style>
</body>
</html>
