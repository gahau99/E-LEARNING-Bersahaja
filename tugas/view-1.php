<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$id_tugas = intval($_GET['id']);
$result = mysqli_query($conn, "
    SELECT ts.*, u.username 
    FROM tugas_siswa ts
    JOIN users u ON ts.id_siswa = u.id
    WHERE ts.id_tugas=$id_tugas
");
?>

<h2>Jawaban Siswa</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Nama Siswa</th>
        <th>File</th>
        <th>Nilai</th>
        <th>Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><a href="../uploads/tugas/<?= $row['file'] ?>" target="_blank">Lihat File</a></td>
        <td><?= $row['nilai'] ?? '-' ?></td>
        <td>
            <a href="nilai.php?id=<?= $row['id'] ?>">Beri Nilai</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
