<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getInstance()->getConnection();
$kategoriList = get_kategori($pdo);
$supplierList = get_supplier($pdo);

$errors = [];
$data = ['kode' => '', 'nama_produk' => '', 'kategori_id' => 0, 'supplier_id' => 0, 'harga' => '', 'stok' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$data, $errors] = validate_produk($pdo, $_POST);

    if (!$errors) {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO produk (kode, nama_produk, kategori_id, supplier_id, harga, stok)
                 VALUES (:kode, :nama, :kategori, :supplier, :harga, :stok)'
            );
            $stmt->execute([
                ':kode'     => $data['kode'],
                ':nama'     => $data['nama_produk'],
                ':kategori' => $data['kategori_id'],
                ':supplier' => $data['supplier_id'],
                ':harga'    => $data['harga'],
                ':stok'     => (int) $data['stok'],
            ]);
            set_flash('success', 'Produk "' . $data['nama_produk'] . '" berhasil ditambahkan.');
            redirect('index.php');
        } catch (PDOException $ex) {
            set_flash('error', 'Gagal menyimpan produk. Silakan coba lagi.');
            redirect('create.php');
        }
    }
}

$pageTitle   = 'Tambah Produk';
$submitLabel = 'Simpan Produk';
require __DIR__ . '/includes/header.php';
?>
<h1>Tambah Produk</h1>
<?php require __DIR__ . '/includes/_form.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
