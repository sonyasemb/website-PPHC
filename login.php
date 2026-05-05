<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,
        "SELECT * FROM users WHERE username='$username' LIMIT 1"
    );
    $user = mysqli_fetch_assoc($query);

    if ($user) {

        // PASSWORD PLAIN TEXT → HASH
        if ($user['password'] === $password) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn,
                "UPDATE users SET password='$newHash' WHERE id_user='{$user['id_user']}'"
            );
            $user['password'] = $newHash;
        }

        // CEK HASH
        if (password_verify($password, $user['password'])) {

            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['role']    = trim(strtolower($user['role']));

            $checkLastLogin = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'last_login_at'");
            if ($checkLastLogin && mysqli_num_rows($checkLastLogin) > 0) {
                $uid = (int)($user['id_user'] ?? 0);
                if ($uid > 0) {
                    mysqli_query($conn, "UPDATE users SET last_login_at=NOW() WHERE id_user={$uid}");
                }
            }

            if ($_SESSION['role'] === 'admin') {
                header("Location: admin.php");
            } elseif ($_SESSION['role'] === 'psikolog') {
                header("Location: psikolog.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }
    }

    header("Location: login.php?error=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css?v=<?php echo filemtime(__DIR__ . '/assets/css/login.css'); ?>">
</head>
<body>

<div class="login-page">
    <div class="login-wrap container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 d-flex justify-content-center text-center">
                <div class="brand-box mx-auto mx-lg-0">
                    <img src="ikon/logopphc.png?v=<?php echo filemtime(__DIR__ . '/ikon/logopphc.png'); ?>" alt="Logo PPHC" class="brand-logo">
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card login-card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center fw-bold mb-4">Login</h2>

                        <?php if (isset($_GET['error'])): ?>
                            <p class="error">Username atau password salah</p>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>

                        <div class="auth-link mt-3 text-center">
                            Belum punya akun? <a href="register.php">Daftar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
