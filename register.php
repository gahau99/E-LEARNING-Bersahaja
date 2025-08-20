<?php
include "config.php";

if(isset($_POST['register'])){
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role     = $_POST['role']; // guru/siswa

    $insert = mysqli_query($conn, "INSERT INTO users (username,password,role) VALUES ('$username','$password','$role')");
    
    if($insert){
        echo "Registrasi berhasil! <a href='index.php'>Login</a>";
    } else {
        echo "Gagal daftar!";
    }
}
?>
<form method="post">
    <h2>Register</h2>
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <select name="role">
        <option value="guru">Guru</option>
        <option value="siswa">Siswa</option>
    </select><br>
    <button type="submit" name="register">Daftar</button>
</form>
