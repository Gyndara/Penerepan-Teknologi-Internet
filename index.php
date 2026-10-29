
<?php
require 'config.php';
wajibLogin();

$stmt = $pdo->query(
    'SELECT * FROM mahasiswa ORDER BY id DESC'
);
$data = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?= e($lang === 'id' ? 'id' : 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= e($teks[$lang]['judul']) ?></title>
</head>
<body>
    <h1><?= e($teks[$lang]['judul']) ?></h1>

    <p>
        <?= e($_SESSION['username']) ?> |
        <a href="index.php?lang=id">Bahasa Indonesia</a> |
        <a href="index.php?lang=en">English</a> |
        <a href="logout.php"><?= e($teks[$lang]['keluar']) ?></a>
    </p>

    <h2><?= e($teks[$lang]['daftar']) ?></h2>

    <p>
        <a href="tambah.php">
            <?= e($teks[$lang]['tambah']) ?>
        </a>
    </p>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th><?= e($teks[$lang]['nim']) ?></th>
                <th><?= e($teks[$lang]['nama']) ?></th>
                <th><?= e($teks[$lang]['prodi']) ?></th>
                <th><?= e($teks[$lang]['email']) ?></th>
                <th><?= e($teks[$lang]['jk']) ?></th>
                <th><?= e($teks[$lang]['aksi']) ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $m): ?>
            <tr>
                <td><?= e($m['nim']) ?></td>
                <td><?= e($m['nama']) ?></td>
                <td><?= e($m['prodi']) ?></td>
                <td><?= e($m['email']) ?></td>
                <td>
                    <?php
                    if ($lang === 'en') {
                        echo e(
                            $m['jenis_kelamin'] === 'Laki-laki'
                            ? 'Male' : 'Female'
                        );
                    } else {
                        echo e($m['jenis_kelamin']);
                    }
                    ?>
                </td>
                <td>
                    <a href="edit.php?id=<?= (int)$m['id'] ?>">
                        <?= e($teks[$lang]['edit']) ?>
                    </a>

                    <form method="POST" action="hapus.php">
                        <input type="hidden" name="csrf_token"
                            value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="id"
                            value="<?= (int)$m['id'] ?>">
                        <button type="submit"
                            onclick="return confirm('<?= e($teks[$lang]['konfirmasi']) ?>')">
                            <?= e($teks[$lang]['hapus']) ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
