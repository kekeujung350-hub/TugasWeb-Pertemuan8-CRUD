<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getInstance()->getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM produk WHERE id = :id');
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('index.php');
}

$kategoriList = get_kategori($pdo);
$supplierList = get_supplier($pdo);

// form pre-filled dengan data dari database
$errors = [];
$data = [
    'kode'        => $produk['kode'],
    'nama_produk' => $produk['nama_produk'],
    'kategori_id' => $produk['kategori_id'],
    'supplier_id' => $produk['supplier_id'],
    'harga'       => (string) (int) $produk['harga'],
    'stok'        => (string) $produk['stok'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$data, $errors] = validate_produk($pdo, $_POST, $id);

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE produk
                 SET kode = :kode, nama_produk = :nama, kategori_id = :kategori,
                     supplier_id = :supplier, harga = :harga, stok = :stok
                 WHERE id = :id'
            );
            $stmt->execute([
                ':kode'     => $data['kode'],
                ':nama'     => $data['nama_produk'],
                ':kategori' => $data['kategori_id'],
                ':supplier' => $data['supplier_id'],
                ':harga'    => $data['harga'],
                ':stok'     => (int) $data['stok'],
                ':id'       => $id,
            ]);
            set_flash('success', 'Produk "' . $data['nama_produk'] . '" berhasil diperbarui.');
            redirect('index.php');
        } catch (PDOException $ex) {
            set_flash('error', 'Gagal memperbarui produk. Silakan coba lagi.');
            redirect('edit.php?id=' . $id);
        }
    }
}

$pageTitle   = 'Edit Produk';
$submitLabel = 'Simpan Perubahan';
require __DIR__ . '/includes/header.php';
?>
<h1>Edit Produk</h1>
<?php require __DIR__ . '/includes/_form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
