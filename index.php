<?php
session_start();
include "config.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_assoc($query);

     if($data){
        $_SESSION['id'] = $data['id'];
        $_SESSION['username'] = $data['username']; 
        $_SESSION['role'] = $data['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Login gagal!";
    }
}
?>
<form method="post">
    <h2>Login E-Learning</h2>
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button type="submit" name="login">Login</button>
</form>
<a href="register.php">Daftar</a>
