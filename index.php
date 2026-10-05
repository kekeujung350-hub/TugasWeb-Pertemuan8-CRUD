<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pdo = Database::getInstance()->getConnection();

// ---- Pencarian + pagination (bonus) ----
$q       = trim($_GET['q'] ?? '');
$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));

$where  = '';
$params = [];
if ($q !== '') {
    $where = 'WHERE p.nama_produk LIKE :q1 OR p.kode LIKE :q2
              OR k.nama_kategori LIKE :q3 OR s.nama_supplier LIKE :q4';
    $like = '%' . $q . '%';
    $params = [':q1' => $like, ':q2' => $like, ':q3' => $like, ':q4' => $like];
}

$joinSql = 'FROM produk p
            JOIN kategori k ON k.id = p.kategori_id
            JOIN supplier s ON s.id = p.supplier_id';

// total data
$stmt = $pdo->prepare("SELECT COUNT(*) $joinSql $where");
$stmt->execute($params);
$total = (int) $stmt->fetchColumn();

$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// data halaman ini (JOIN 3 tabel: produk + kategori + supplier)
$sql = "SELECT p.id, p.kode, p.nama_produk, p.harga, p.stok,
               k.nama_kategori, s.nama_supplier
        $joinSql $where
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$produk = $stmt->fetchAll();

function page_url(int $p, string $q): string
{
    return 'index.php?' . http_build_query(['q' => $q, 'page' => $p]);
}

$pageTitle = 'Daftar Produk';
require __DIR__ . '/includes/header.php';
?>
<h1>Daftar Produk</h1>

<form method="get" class="search">
    <input type="search" name="q" placeholder="Cari kode, nama, kategori, atau supplier..." value="<?= e($q) ?>">
    <button type="submit" class="btn btn-primary">Cari</button>
    <?php if ($q !== ''): ?><a href="index.php" class="btn btn-ghost">Reset</a><?php endif; ?>
</form>

<div class="table-wrap">
<table>
    <thead>
        <tr>
            <th>#</th><th>Kode</th><th>Nama Produk</th><th>Kategori</th>
            <th>Supplier</th><th class="num">Harga</th><th class="num">Stok</th><th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$produk): ?>
        <tr><td colspan="8" class="empty">Data tidak ditemukan.</td></tr>
    <?php endif; ?>
    <?php foreach ($produk as $i => $row): ?>
        <tr>
            <td><?= e($offset + $i + 1) ?></td>
            <td><code><?= e($row['kode']) ?></code></td>
            <td><?= e($row['nama_produk']) ?></td>
            <td><span class="badge"><?= e($row['nama_kategori']) ?></span></td>
            <td><?= e($row['nama_supplier']) ?></td>
            <td class="num"><?= e(rupiah($row['harga'])) ?></td>
            <td class="num"><?= e($row['stok']) ?></td>
            <td class="row-actions">
                <a href="edit.php?id=<?= e($row['id']) ?>" class="btn btn-sm">Edit</a>
                <a href="delete.php?id=<?= e($row['id']) ?>" class="btn btn-sm btn-danger">Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="pagination">
    <span>Total <?= e($total) ?> produk &middot; Halaman <?= e($page) ?> dari <?= e($totalPages) ?></span>
    <div>
        <?php if ($page > 1): ?><a class="btn btn-sm" href="<?= e(page_url($page - 1, $q)) ?>">&laquo; Sebelumnya</a><?php endif; ?>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="btn btn-sm <?= $p === $page ? 'btn-primary' : '' ?>" href="<?= e(page_url($p, $q)) ?>"><?= e($p) ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a class="btn btn-sm" href="<?= e(page_url($page + 1, $q)) ?>">Berikutnya &raquo;</a><?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
