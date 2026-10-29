
<?php
require 'config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$berhasil = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    periksaCSRF();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    // Validasi kelengkapan data
    if ($username === '' || $password === '' || $konfirmasi === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,50}$/', $username)) {
        $error = 'Username harus 4-50 karakter, hanya huruf, angka, dan underscore.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        // Periksa apakah username sudah digunakan
        $stmt = $pdo->prepare(
            'SELECT id FROM users WHERE username = ?'
        );
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'Username sudah digunakan.';
        } else {
            // Hash password sebelum disimpan
            $hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO users (username, password)
                     VALUES (?, ?)'
                );
                $stmt->execute([$username, $hash]);

                $berhasil = 'Akun berhasil dibuat. Silakan login.';
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    $error = 'Username sudah digunakan.';
                } else {
                    throw $e;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang === 'id' ? 'id' : 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title>Buat Akun</title>
</head>
<body>
    <h1><?= e($teks[$lang]['judul']) ?></h1>

    <p>
        <a href="register.php?lang=id">Bahasa Indonesia</a> |
        <a href="register.php?lang=en">English</a>
    </p>

    <h2><?= $lang === 'id' ? 'Registrasi Akun' : 'Create Account' ?></h2>

    <?php if ($error !== ''): ?>
        <p><?= e($error) ?></p>
    <?php endif; ?>

    <?php if ($berhasil !== ''): ?>
        <p><?= e($berhasil) ?></p>
        <p><a href="login.php">
            <?= e($teks[$lang]['login']) ?>
        </a></p>
    <?php else: ?>
        <form method="POST" action="register.php">
            <input type="hidden" name="csrf_token"
                   value="<?= e($_SESSION['csrf_token']) ?>">

            <label><?= e($teks[$lang]['username']) ?></label><br>
            <input type="text" name="username"
                   minlength="4" maxlength="50" required>
            <br><br>

            <label><?= e($teks[$lang]['password']) ?></label><br>
            <input type="password" name="password"
                   minlength="8" required>
            <br><br>

            <label>
                <?= $lang === 'id'
                    ? 'Konfirmasi Password'
                    : 'Confirm Password' ?>
            </label><br>
            <input type="password" name="konfirmasi"
                   minlength="8" required>
            <br><br>

            <button type="submit">
                <?= $lang === 'id' ? 'Buat Akun' : 'Register' ?>
            </button>
        </form>
    <?php endif; ?>

    <p>
        <?= $lang === 'id'
            ? 'Sudah punya akun?'
            : 'Already have an account?' ?>
        <a href="login.php"><?= e($teks[$lang]['login']) ?></a>
    </p>
</body>
</html>
