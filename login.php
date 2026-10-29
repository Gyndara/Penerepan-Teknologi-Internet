
<?php
require 'config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    periksaCSRF();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT * FROM users WHERE username = ?'
        );
        $stmt->execute([$username]);
        $akun = $stmt->fetch();

        if ($akun &&
            password_verify($password, $akun['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $akun['id'];
            $_SESSION['username'] = $akun['username'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang === 'id' ? 'id' : 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= e($teks[$lang]['login']) ?></title>
</head>
<body>
    <h1><?= e($teks[$lang]['judul']) ?></h1>

    <p>
        <a href="login.php?lang=id">Bahasa Indonesia</a> |
        <a href="login.php?lang=en">English</a>
    </p>

    <h2><?= e($teks[$lang]['login']) ?></h2>

    <?php if ($error !== ''): ?>
        <p><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token"
               value="<?= e($_SESSION['csrf_token']) ?>">

        <label><?= e($teks[$lang]['username']) ?></label><br>
        <input type="text" name="username" required>
        <br><br>

        <label><?= e($teks[$lang]['password']) ?></label><br>
        <input type="password" name="password" required>
        <br><br>

        <button type="submit">
            <?= e($teks[$lang]['login']) ?>
        </button>
    </form>
    <p>
    Belum punya akun?
    <a href="register.php">Buat akun baru</a>
</p>
</body>
</html>
