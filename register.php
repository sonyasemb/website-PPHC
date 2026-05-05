<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn,
        "INSERT INTO users (nama, username, password, role)
         VALUES ('$nama', '$username', '$password', 'user')"
    );

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-page">
    <div class="login-wrap container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 d-flex justify-content-center text-center">
                <div class="brand-box mx-auto mx-lg-0">
                    <img src="ikon/logopphc.png" alt="Logo PPHC" class="brand-logo">
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card login-card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center fw-bold mb-4">Daftar Akun</h2>

<form method="POST">
                            <div class="mb-3">
                                <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>

                        <div class="auth-link mt-3 text-center">
                            Sudah punya akun? <a href="login.php">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
