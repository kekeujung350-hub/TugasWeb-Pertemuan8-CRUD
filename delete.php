<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getInstance()->getConnection();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT p.id, p.kode, p.nama_produk, p.harga, p.stok, k.nama_kategori, s.nama_supplier
     FROM produk p
     JOIN kategori k ON k.id = p.kategori_id
     JOIN supplier s ON s.id = p.supplier_id
     WHERE p.id = :id'
);
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    set_flash('error', 'Produk tidak ditemukan.');
    redirect('index.php');
}

// Eksekusi hapus hanya lewat POST (setelah user menekan tombol konfirmasi)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $del = $pdo->prepare('DELETE FROM produk WHERE id = :id');
        $del->execute([':id' => $id]);
        set_flash('success', 'Produk "' . $produk['nama_produk'] . '" berhasil dihapus.');
    } catch (PDOException $ex) {
        set_flash('error', 'Gagal menghapus produk.');
    }
    redirect('index.php');
}

$pageTitle = 'Hapus Produk';
require __DIR__ . '/includes/header.php';
?>
<h1>Konfirmasi Hapus</h1>
<div class="card confirm">
    <p>Apakah Anda yakin ingin menghapus produk berikut? Tindakan ini tidak dapat dibatalkan.</p>
    <dl>
        <dt>Kode</dt><dd><?= e($produk['kode']) ?></dd>
        <dt>Nama</dt><dd><?= e($produk['nama_produk']) ?></dd>
        <dt>Kategori</dt><dd><?= e($produk['nama_kategori']) ?></dd>
        <dt>Supplier</dt><dd><?= e($produk['nama_supplier']) ?></dd>
        <dt>Harga</dt><dd><?= e(rupiah($produk['harga'])) ?></dd>
        <dt>Stok</dt><dd><?= e($produk['stok']) ?></dd>
    </dl>
    <form method="post" class="actions">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($produk['id']) ?>">
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        <a href="index.php" class="btn btn-ghost">Batal</a>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
