
<?php
require 'config.php';
wajibLogin();

$error = '';

$nim = '';
$nama = '';
$prodi = '';
$email = '';
$jk = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    periksaCSRF();

    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $jk = $_POST['jenis_kelamin'] ?? '';

    if ($nim === '' || $nama === '' ||
        $prodi === '' || $email === '' || $jk === '') {
        $error = $teks[$lang]['kosong'];
    } elseif (strlen($nim) > 20 || strlen($nama) > 100 ||
              strlen($prodi) > 100 || strlen($email) > 100) {
        $error = 'Panjang data melebihi batas.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (!in_array($jk, ['Laki-laki', 'Perempuan'], true)) {
        $error = 'Jenis kelamin tidak valid.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO mahasiswa
                (nim, nama, prodi, email, jenis_kelamin)
                VALUES (?, ?, ?, ?, ?)'
            );

            $stmt->execute([$nim, $nama, $prodi, $email, $jk]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'NIM sudah terdaftar.';
            } else {
                throw $e;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang === 'id' ? 'id' : 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= e($teks[$lang]['tambah']) ?></title>
</head>
<body>
    <h1><?= e($teks[$lang]['tambah']) ?></h1>

    <p>
        <a href="tambah.php?lang=id">Bahasa Indonesia</a> |
        <a href="tambah.php?lang=en">English</a>
    </p>

    <?php if ($error !== ''): ?>
        <p><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="tambah.php">
        <input type="hidden" name="csrf_token"
               value="<?= e($_SESSION['csrf_token']) ?>">

        <label><?= e($teks[$lang]['nim']) ?></label><br>
        <input type="text" name="nim"
               maxlength="20" required value="<?= e($nim) ?>">
        <br><br>

        <label><?= e($teks[$lang]['nama']) ?></label><br>
        <input type="text" name="nama"
               maxlength="100" required value="<?= e($nama) ?>">
        <br><br>

        <label><?= e($teks[$lang]['prodi']) ?></label><br>
        <input type="text" name="prodi"
               maxlength="100" required value="<?= e($prodi) ?>">
        <br><br>

        <label><?= e($teks[$lang]['email']) ?></label><br>
        <input type="email" name="email"
               maxlength="100" required value="<?= e($email) ?>">
        <br><br>

        <label><?= e($teks[$lang]['jk']) ?></label><br>
        <select name="jenis_kelamin" required>
            <option value=""><?= e($teks[$lang]['pilih']) ?></option>
            <option value="Laki-laki"
                <?= $jk === 'Laki-laki' ? 'selected' : '' ?>>
                <?= e($teks[$lang]['laki']) ?>
            </option>
            <option value="Perempuan"
                <?= $jk === 'Perempuan' ? 'selected' : '' ?>>
                <?= e($teks[$lang]['perempuan']) ?>
            </option>
        </select>
        <br><br>

        <button type="submit">
            <?= e($teks[$lang]['simpan']) ?>
        </button>
        <a href="index.php"><?= e($teks[$lang]['batal']) ?></a>
    </form>
</body>
</html>
