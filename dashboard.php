<?php

session_start();
if (!isset($_SESSION['id'])) header("Location: index.php");
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>Dashboard</title>
</head>
<body class="bg-gradient-to-br from-blue-100 via-indigo-200 to-purple-200 min-h-screen">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white dark:bg-gray-800 rounded-xl px-8 py-10 shadow-2xl ring-1 ring-gray-900/10 w-full max-w-lg">
            <h2 class="text-3xl font-bold text-center text-indigo-700 dark:text-white mb-4">Dashboard</h2>
            <p class="text-center text-lg text-gray-700 dark:text-gray-300 mb-8">
                Halo, <span class="font-semibold"><?php echo $_SESSION['username']; ?></span>
                <span class="text-sm text-gray-500">(role: <?php echo $_SESSION['role']; ?>)</span>
            </p>
            <div class="flex flex-col gap-4 mb-8">
                
                <?php if ($_SESSION['role'] == "admin") { ?>
                    <a href='user/index.php' class="block py-2 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-center font-medium">Kelola User</a>
                    <a href='kelas/index.php' class="block py-2 px-4 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 text-center font-medium">Kelola Kelas</a>
                    <a href='materi/index.php' class="block py-2 px-4 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-center font-medium">Materi</a>
                    <a href='tugas/index.php' class="block py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-center font-medium">Tugas</a>
                <?php } elseif ($_SESSION['role'] == "guru") { ?>
                    <a href='kelas/index.php' class="block py-2 px-4 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 text-center font-medium">Kelola Kelas</a>
                    <a href='materi/index.php' class="block py-2 px-4 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-center font-medium">Materi</a>
                    <a href='tugas/index.php' class="block py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-center font-medium">Tugas</a>
                <?php } else { ?>
                    <a href='kelas/list.php' class="block py-2 px-4 bg-indigo-400 text-white rounded-lg hover:bg-indigo-500 text-center font-medium">Daftar Kelas</a>
                    <a href='materi/list.php' class="block py-2 px-4 bg-purple-400 text-white rounded-lg hover:bg-purple-500 text-center font-medium">Materi</a>
                    <a href='tugas/list.php' class="block py-2 px-4 bg-blue-400 text-white rounded-lg hover:bg-blue-500 text-center font-medium">Tugas</a>
                <?php } ?>
            </div>
            <div class="text-center">
                <a href='logout.php' class="text-red-600 hover:underline font-medium">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>