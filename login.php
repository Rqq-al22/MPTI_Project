<?php
session_start();
require 'config.php';

if (isset($_POST['login'])) {
    $username = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($db, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        if (password_verify($password, $data['password'])) {
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['role'] = $data['id_role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <div class="left">
        <div class="circle">
            <img src="https://cdn-icons-png.flaticon.com/512/5087/5087579.png" alt="User">
        </div>
    </div>

    <div class="right">
        <h2>Login</h2>

        <?php if (isset($error)) : ?>
            <p style="color:red"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn-login">LOGIN</button>
        </form>

    </div>
</div>

</body>
</html>
