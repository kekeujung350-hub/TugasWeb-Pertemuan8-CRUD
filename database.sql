-- Tugas Rutin 8 - CRUD Inventaris
CREATE DATABASE IF NOT EXISTS inventaris_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;

CREATE TABLE kategori (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE supplier (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(120) NOT NULL,
    kontak VARCHAR(30) NULL,
    alamat VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (supplier_id)
        REFERENCES supplier(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'), ('Alat Tulis'), ('Perabot'), ('Makanan & Minuman'), ('Perlengkapan Kebersihan');

INSERT INTO supplier (nama_supplier, kontak, alamat) VALUES
('PT Medan Jaya Elektronik', '061-4521000', 'Jl. Gatot Subroto No. 12, Medan'),
('CV Sumatera Stationery', '061-7788123', 'Jl. Sisingamangaraja No. 45, Medan'),
('UD Furnitur Nusantara', '0812-6000-1111', 'Jl. Williem Iskandar No. 8, Deli Serdang'),
('PT Pangan Sejahtera', '061-8890077', 'Jl. Pancing No. 21, Medan'),
('CV Bersih Selalu', '0813-7000-2222', 'Jl. Setia Budi No. 33, Medan');

INSERT INTO produk (kode, nama_produk, kategori_id, supplier_id, harga, stok) VALUES
('ELK-001', 'Mouse Wireless', 1, 1, 85000, 40),
('ELK-002', 'Keyboard Mekanik', 1, 1, 350000, 15),
('ATK-001', 'Pulpen Gel 0.5mm (box)', 2, 2, 42000, 120),
('ATK-002', 'Buku Tulis A5', 2, 2, 8500, 300),
('PRB-001', 'Kursi Kantor Ergonomis', 3, 3, 725000, 9),
('PRB-002', 'Meja Belajar Lipat', 3, 3, 450000, 12),
('MKN-001', 'Air Mineral 600ml (dus)', 4, 4, 38000, 80),
('KBS-001', 'Cairan Pembersih Lantai 1L', 5, 5, 24000, 55);
