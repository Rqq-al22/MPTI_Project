
<?php
session_start();
require 'config.php';

if (isset($_POST['login'])) {
    $username = trim($_POST['email']);
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($db, "SELECT id_user, username, password, id_role FROM users WHERE username = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($data) {
            // Password can be stored as plain text or hashed. Support both for migration.
            $isValid = false;
            if (!empty($data['password']) && password_verify($password, $data['password'])) {
                $isValid = true;
            } elseif ($password === $data['password']) {
                $isValid = true;
            }

            if ($isValid) {
                // If password was plain text, re-hash it for better security
                if (!password_get_info($data['password'])['algo']) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = mysqli_prepare($db, "UPDATE users SET password = ? WHERE id_user = ?");
                    if ($updateStmt) {
                        mysqli_stmt_bind_param($updateStmt, 'si', $hashed, $data['id_user']);
                        mysqli_stmt_execute($updateStmt);
                        mysqli_stmt_close($updateStmt);
                    }
                }

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
    } else {
        $error = "Terjadi kesalahan pada query. Coba lagi nanti.";
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

