<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] != 'siswa') {
    die("Akses ditolak!");
}

$id_siswa = $_SESSION['id'];

// ambil kelas siswa
$qKelas = mysqli_query($conn, "SELECT id_kelas FROM kelas_siswa WHERE id_siswa = $id_siswa");
$kelas_ids = [];
while ($r = mysqli_fetch_assoc($qKelas)) {
    $kelas_ids[] = $r['id_kelas'];
}

if (empty($kelas_ids)) {
    die("Anda belum terdaftar di kelas manapun");
}

$kelas_in = implode(",", $kelas_ids);
$tugas = mysqli_query($conn, "SELECT * FROM tugas WHERE id_kelas IN ($kelas_in) ORDER BY deadline ASC");
?>

<h2>Daftar Tugas Saya</h2>

<a href="../dashboard.php">Dashboard</a>

<table border="1" cellpadding="5">
    <tr>
        <th>Judul</th>
        <th>Deskripsi</th>
        <th>Deadline</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($tugas)): ?>
        <?php
        $cek = mysqli_query($conn, "SELECT * FROM tugas_siswa WHERE id_tugas={$row['id']} AND id_siswa=$id_siswa");
        $submit = mysqli_fetch_assoc($cek);
        ?>
        <tr>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></td>
            <td><?= $row['deadline'] ?></td>
            <td>
                <?php if($submit): ?>
                    Sudah Submit (<?= $submit['submitted_at'] ?>)
                    <?php if($submit['nilai'] !== null): ?>
                        | Nilai: <?= $submit['nilai'] ?>
                    <?php endif; ?>
                <?php else: ?>
                    Belum Submit
                <?php endif; ?>
            </td>
            <td>
                <?php if(!$submit): ?>
                    <a href="submit.php?id=<?= $row['id'] ?>">Kumpulkan</a>
                <?php else: ?>
                    <a href="view.php?id=<?= $submit['id'] ?>">Lihat Jawaban</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
