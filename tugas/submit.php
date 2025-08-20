<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    die("Akses ditolak!");
}

$id_siswa = $_SESSION['id'];
$id_tugas = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];
    $dir = "../uploads/tugas/";

    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $filename = time() . "_" . basename($file);
    move_uploaded_file($tmp, $dir . $filename);

    mysqli_query($conn, "INSERT INTO tugas_siswa (id_tugas, id_siswa, file, submitted_at) 
                         VALUES ($id_tugas, $id_siswa, '$filename', NOW())");

    header("Location: list.php");
    exit;
}
?>

<h2>Kumpulkan Tugas</h2>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" required><br><br>
    <button type="submit">Upload</button>
</form>
