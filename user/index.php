<?php
session_start();
include "../config.php";

// hanya admin yang boleh akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

$result = mysqli_query($conn, "SELECT * FROM users");
?>

<h2>Daftar User</h2>
<a href="tambah.php">Tambah User</a> | <a href="../dashboard.php">Dashboard</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Username</th><th>Role</th><th>Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= $row['role'] ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | 
            <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
