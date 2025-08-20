<?php
session_start();
if (!isset($_SESSION['id'])) header("Location: index.php");

echo "<h2>Dashboard</h2>";
echo "Halo, " . $_SESSION['username'] . " (role: " . $_SESSION['role'] . ")<br><br>";

// Menu khusus per role admin, guru, siswa.
if ($_SESSION['role'] == "admin") {
    echo "<a href='user/index.php'>Kelola User</a> | 
          <a href='kelas/index.php'>Kelola Kelas</a> | 
          <a href='materi/index.php'>Materi</a> | 
          <a href='tugas/index.php'>Tugas</a>";
} elseif ($_SESSION['role'] == "guru") {
    echo "<a href='kelas/index.php'>Kelola Kelas</a> | 
          <a href='materi/index.php'>Materi</a> | 
          <a href='tugas/index.php'>Tugas</a>";
} else {
    echo "<a href='kelas/list.php'>Daftar Kelas</a> | 
          <a href='materi/list.php'>Materi</a> | 
          <a href='tugas/list.php'>Tugas</a>";
}

echo "<br><br><a href='logout.php'>Logout</a>";
?>
