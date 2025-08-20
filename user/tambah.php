<?php
session_start();
include "../config.php";

// hanya admin yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']); // pakai md5 sederhana, bisa diganti bcrypt untuk lebih aman
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

<h2>Tambah User</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    Role:
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="guru">Guru</option>
        <option value="siswa">Siswa</option>
    </select><br><br>
    <button type="submit">Simpan</button>
</form>
<a href="index.php">Kembali</a>
