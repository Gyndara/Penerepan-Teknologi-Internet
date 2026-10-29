
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$db   = 'db_mahasiswa';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    exit('Koneksi database gagal. Periksa konfigurasi.');
}

if (isset($_GET['lang'])) {
    $bahasa = $_GET['lang'];

    if (in_array($bahasa, ['id', 'en'], true)) {
        $_SESSION['lang'] = $bahasa;
    }

    $halaman = basename($_SERVER['PHP_SELF']);

if ($halaman === 'edit.php' && isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id && $id > 0) {
        $halaman .= '?id=' . $id;
    } else {
        $halaman = 'index.php';
    }
}

header('Location: ' . $halaman);
exit;
}

$lang = $_SESSION['lang'] ?? 'id';

$teks = [
    'id' => [
        'judul' => 'Manajemen Data Mahasiswa',
        'daftar' => 'Daftar Mahasiswa',
        'tambah' => 'Tambah Mahasiswa',
        'edit' => 'Edit',
        'hapus' => 'Hapus',
        'nim' => 'NIM',
        'nama' => 'Nama',
        'prodi' => 'Program Studi',
        'email' => 'Email',
        'jk' => 'Jenis Kelamin',
        'aksi' => 'Aksi',
        'simpan' => 'Simpan',
        'batal' => 'Batal',
        'keluar' => 'Logout',
        'login' => 'Login',
        'username' => 'Username',
        'password' => 'Password',
        'berhasil' => 'Data berhasil disimpan.',
        'kosong' => 'Semua kolom wajib diisi.',
        'tidak_ada' => 'Data tidak ditemukan.',
        'konfirmasi' => 'Yakin ingin menghapus data ini?',
        'pilih' => '-- Pilih --',
        'laki' => 'Laki-laki',
        'perempuan' => 'Perempuan'
    ],
    'en' => [
        'judul' => 'Student Management System',
        'daftar' => 'Student List',
        'tambah' => 'Add Student',
        'edit' => 'Edit',
        'hapus' => 'Delete',
        'nim' => 'Student ID',
        'nama' => 'Name',
        'prodi' => 'Study Program',
        'email' => 'Email',
        'jk' => 'Gender',
        'aksi' => 'Actions',
        'simpan' => 'Save',
        'batal' => 'Cancel',
        'keluar' => 'Logout',
        'login' => 'Login',
        'username' => 'Username',
        'password' => 'Password',
        'berhasil' => 'Data saved successfully.',
        'kosong' => 'All fields are required.',
        'tidak_ada' => 'Record not found.',
        'konfirmasi' => 'Are you sure you want to delete this record?',
        'pilih' => '-- Select --',
        'laki' => 'Male',
        'perempuan' => 'Female'
    ]
];

function e($nilai) {
    return htmlspecialchars(
        (string) $nilai,
        ENT_QUOTES,
        'UTF-8'
    );
}

function wajibLogin() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function periksaCSRF() {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Token keamanan tidak valid.');
    }
}
?>
