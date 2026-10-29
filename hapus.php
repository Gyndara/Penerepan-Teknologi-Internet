
<?php
require 'config.php';
wajibLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

periksaCSRF();

$id = filter_var(
    $_POST['id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    exit('ID tidak valid.');
}

$stmt = $pdo->prepare('DELETE FROM mahasiswa WHERE id = ?');
$stmt->execute([$id]);

header('Location: index.php');
exit;
?>
