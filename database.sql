CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM( 'admin', 'guru','siswa') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    kode_kelas VARCHAR(10) UNIQUE,   -- kode join kelas
    id_guru INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_guru) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE kelas_siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_kelas INT NOT NULL,
    id_siswa INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE materi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_kelas INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    file VARCHAR(255), -- untuk upload file/pdf/ppt
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    dibuat_oleh INT NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id) ON DELETE CASCADE,
    FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE tugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_kelas INT NOT NULL,
    judul VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id) ON DELETE CASCADE
);

CREATE TABLE tugas_siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tugas INT NOT NULL,
    id_siswa INT NOT NULL,
    file VARCHAR(255),   -- file jawaban siswa
    nilai INT DEFAULT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tugas) REFERENCES tugas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES users(id) ON DELETE CASCADE
);
