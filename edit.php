
<?php
require 'config.php';
wajibLogin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_var(
        $_POST['id'] ?? null,
        FILTER_VALIDATE_INT
    );
}

if (!$id || $id < 1) {
    exit('ID tidak valid.');
}

$stmt = $pdo->prepare('SELECT * FROM mahasiswa WHERE id = ?');
$stmt->execute([$id]);
$m = $stmt->fetch();

if (!$m) {
    exit($teks[$lang]['tidak_ada']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    periksaCSRF();

    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $prodi = trim($_POST['prodi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $jk = $_POST['jenis_kelamin'] ?? '';

    if ($nim === '' || $nama === '' || $prodi === '' ||
        $email === '' || $jk === '') {
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
                'UPDATE mahasiswa
                 SET nim = ?, nama = ?, prodi = ?,
                     email = ?, jenis_kelamin = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $nim, $nama, $prodi, $email, $jk, $id
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'NIM sudah digunakan mahasiswa lain.';
            } else {
                throw $e;
            }
        }
    }

    $m = [
        'nim' => $nim,
        'nama' => $nama,
        'prodi' => $prodi,
        'email' => $email,
        'jenis_kelamin' => $jk
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= e($lang === 'id' ? 'id' : 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= e($teks[$lang]['edit']) ?></title>
</head>
<body>
    <h1><?= e($teks[$lang]['edit']) ?> Mahasiswa</h1>

    <p>
        <a href="edit.php?id=<?= (int)$id ?>&lang=id">Bahasa Indonesia</a> |
        <a href="edit.php?id=<?= (int)$id ?>&lang=en">English</a>
    </p>

    <?php if ($error !== ''): ?>
        <p><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="edit.php">
        <input type="hidden" name="csrf_token"
               value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <label><?= e($teks[$lang]['nim']) ?></label><br>
        <input name="nim" maxlength="20" required
               value="<?= e($m['nim']) ?>">
        <br><br>

        <label><?= e($teks[$lang]['nama']) ?></label><br>
        <input name="nama" maxlength="100" required
               value="<?= e($m['nama']) ?>">
        <br><br>

        <label><?= e($teks[$lang]['prodi']) ?></label><br>
        <input name="prodi" maxlength="100" required
               value="<?= e($m['prodi']) ?>">
        <br><br>

        <label><?= e($teks[$lang]['email']) ?></label><br>
        <input type="email" name="email" maxlength="100" required
               value="<?= e($m['email']) ?>">
        <br><br>

        <label><?= e($teks[$lang]['jk']) ?></label><br>
        <select name="jenis_kelamin" required>
            <option value=""><?= e($teks[$lang]['pilih']) ?></option>
            <option value="Laki-laki"
                <?= $m['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>
                <?= e($teks[$lang]['laki']) ?>
            </option>
            <option value="Perempuan"
                <?= $m['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>
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
