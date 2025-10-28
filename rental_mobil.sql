-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jun 2025 pada 11.33
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_mobil`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `mobil`
--

CREATE TABLE `mobil` (
  `id` int(11) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `status` enum('available','rented','booked') DEFAULT 'available',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fuel_type` varchar(20) DEFAULT 'Bensin',
  `transmission` varchar(20) DEFAULT 'Automatic',
  `seats` int(11) NOT NULL DEFAULT 5,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mobil`
--

INSERT INTO `mobil` (`id`, `brand`, `model`, `price_per_day`, `status`, `image`, `created_at`, `fuel_type`, `transmission`, `seats`, `type`) VALUES
(83, 'Toyota', 'Avanza', 300000.00, 'available', 'https://astradigitaldigiroomuat.blob.core.windows.net/storage-uat-001/jenis-mobil-avanza-dan-harganya.jpg ', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'MPV'),
(84, 'Toyota', 'Rush', 450000.00, 'available', 'https://carsgallery.co.id/blog/wp-content/uploads/2024/09/tipe-toyota-rush-2.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'SUV'),
(85, 'Toyota', 'Innova', 500000.00, 'available', 'https://www.toyota.astra.co.id/sites/default/files/2021-04/Exterior%20-%20Kijang%20Innova%20Limited%20Edition-min.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'MPV'),
(86, 'Toyota', 'Yaris', 400000.00, 'available', 'https://carsgallery.co.id/blog/wp-content/uploads/2024/09/tipe-toyota-yaris-yang-paling-bagus-2.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, ''),
(87, 'Toyota', 'Agya', 280000.00, 'available', 'https://www.toyotatanjungpinang.com/wp-content/uploads/2024/04/agya-gr-yellow-600x400-1.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'LCGC'),
(88, 'Honda', 'Brio', 290000.00, 'available', 'https://asset.honda-indonesia.com/variants/images/VmfueMMOko09BwpogWPFmBUShLbLDzik4wPP6AFz.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'Hatchback'),
(89, 'Honda', 'HR-V', 500000.00, 'available', 'https://asset.honda-indonesia.com/variants/images/oJic7dgAKxZEvCeIrLq0c2CHeNHNHuj7d7z9csoH.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'SUV'),
(90, 'Honda', 'Civic', 520000.00, 'available', 'https://www.olx.co.id/news/wp-content/uploads/2024/05/Harga-Honda-Civic-Turbo.webp', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'Sedan'),
(91, 'Honda', 'Mobilio', 350000.00, 'available', 'https://res.cloudinary.com/mufautoshow/image/upload/f_auto,f_auto/w_1200/v1619398190/moas/news/1619398194_review-honda-mobilio-new-2021-spesifikasi-lengkap-harganya.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'MPV'),
(92, 'Honda', 'Jazz', 370000.00, 'available', 'https://abigailrental.com/wp-content/uploads/2024/07/Honda-Jazz-Rental-Mobil.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'Hatchback'),
(93, 'Daihatsu', 'Xenia', 280000.00, 'available', 'https://res.cloudinary.com/mufautoshow/image/upload/f_auto,f_auto/w_1200/v1639489997/moas/news/1639489991_inovasi-daihatsu-all-new-xenia-2021-next-level-mpv-indonesia.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'MPV'),
(94, 'Daihatsu', 'Terios', 420000.00, 'available', 'https://www.daihatsulampungpusat.com/file/terios2023-04.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'SUV'),
(95, 'Daihatsu', 'Sigra', 270000.00, 'available', 'https://astradaihatsukaltimtara.com/wp-content/uploads/2022/03/1657170196182.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'LCGC'),
(96, 'Daihatsu', 'Luxio', 310000.00, 'available', 'https://astradaihatsuyogyakarta.com/wp-content/uploads/2024/03/360_Daihatsu_Luxio_White_-13250973986251142355.png.webp', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 8, 'MPV'),
(97, 'Daihatsu', 'Ayla', 260000.00, 'available', 'https://www.daihatsujatengjogja.com/images/produk/new-daihatsu-ayla-20.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'LCGC'),
(98, 'Mitsubishi', 'Xpander', 490000.00, 'available', 'https://www.sunstarmotor.id/wp-content/uploads/2021/11/Warna-New-Xpander-2023-2.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'MPV'),
(99, 'Mitsubishi', 'Pajero', 700000.00, 'available', 'https://cdn.antaranews.com/cache/1200x800/2021/01/12/Pajero-Sport-Dakar-Ultimate-4x2.png', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 7, 'SUV'),
(100, 'Mitsubishi', 'Outlander', 600000.00, 'available', 'https://www.dubicars.com/images/38cbe0/r_960x540/generations/generation_64ec3fedc6426_cc_2023mis070053_01_1280_x47.webp?6', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'SUV'),
(101, 'Mitsubishi', 'Triton', 550000.00, 'available', 'https://cdn.antaranews.com/cache/1200x800/2022/10/09/2023-Mitsubishi-Triton-Sport-Edition-AU-Spec-1.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'SUV'),
(102, 'Mitsubishi', 'Lancer', 400000.00, 'available', 'https://stimg.cardekho.com/images/carexteriorimages/930x620/Mitsubishi/Mitsubishi-Lancer/3379/1544677323023/front-left-side-47.jpg', '2025-06-06 15:36:19', 'Bensin', 'Automatic', 5, 'Sedan'),
(104, 'Lamborghini', 'Huracán', 5500000.00, 'available', 'https://www.lamborghini.com/sites/it-en/files/DAM/lamborghini/facelift_2019/model_gw/huracan/2023/model_chooser/huracan_tecnica_m.jpg', '2025-06-06 20:47:46', 'Bensin', 'Automatic', 2, 'Sport'),
(105, 'Lamborghini', 'Temerario', 4500000.00, 'available', 'https://www.lamborghini.com/sites/it-en/files/DAM/lamborghini/search/model/2025/temerario.png', '2025-06-06 20:47:46', 'Bensin', 'Automatic', 2, 'Sport'),
(106, 'Lamborghini', 'Revuelto', 3000000.00, 'available', 'https://www.lamborghini.com/sites/it-en/files/DAM/lamborghini/search/model/2025/revuelto.png', '2025-06-06 20:47:46', 'Bensin', 'Automatic', 2, 'Sport'),
(107, 'Bugatti', 'Chiron', 4000000.00, 'available', 'https://i.ytimg.com/vi/KzneDRjGufM/maxresdefault.jpg', '2025-06-06 20:47:46', 'Bensin', 'Automatic', 2, 'Sport'),
(108, 'Porsche', '718 cayman gt4 rs', 3500000.00, 'available', 'https://friedrich-pm.com/wp-content/uploads/2023/04/fuer-den-porsche-cayman-gt4-rs-beitragsbild.jpg', '2025-06-06 20:47:46', 'Bensin', 'Automatic', 2, 'Sport'),
(109, 'Honda', 'CR-V', 450000.00, 'available', 'https://www.hondasolobaru.co.id/wp-content/uploads/2023/11/crv-hybrid.png', '2025-06-06 20:59:14', 'Bensin', 'Automatic', 5, 'SUV');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sewa`
--

CREATE TABLE `sewa` (
  `id` int(11) NOT NULL,
  `mobil_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `rental_days` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','awaiting_confirmation','rejected','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sewa`
--

INSERT INTO `sewa` (`id`, `mobil_id`, `user_id`, `tanggal_mulai`, `tanggal_selesai`, `total_harga`, `rental_days`, `metode_pembayaran`, `bukti_pembayaran`, `status`, `created_at`, `updated_at`) VALUES
(31, 104, 38, '2025-06-09', '2025-06-10', 11000000.00, 2, NULL, NULL, 'cancelled', '2025-06-08 08:48:52', '2025-06-08 08:49:10'),
(32, 109, 38, '2025-06-09', '2025-06-10', 900000.00, 2, 'cash', NULL, 'pending', '2025-06-08 08:49:47', '2025-06-08 08:54:03'),
(33, 104, 38, '2025-06-09', '2025-06-10', 11000000.00, 2, 'transfer_bca', 'uploads/payments/payment_33_1749372633.png', 'confirmed', '2025-06-08 08:50:08', '2025-06-08 09:30:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `metode_pembayaran` varchar(20) NOT NULL,
  `durasi` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(38, 'Henry Nugraha', 'henrynugraha1210@gmail.com', '$2y$10$8OraBxRz1vXZz/NS6f23Y.kgV7yn5tkeVVAR1QDuKdfecD64th3sa', 'user'),
(40, 'Admin', 'admin@rental.com', '$2y$10$EZjLNy5bXhiaPYkvHTDUOOoKN5tRoRgjJDUjLiYskezzP75E2xN.y', 'admin'),
(46, 'eben', 'eben@rental.com', '$2y$10$mQuUPzHOIX4GopDRVSorwexpoiKX9xNtEOtoW67VnuPI4Vl79zH5e', 'user'),
(47, 'eben', 'eben1@rental.com', '$2y$10$6koqwmLV2L/3cXfqek4JjO1lVuv2ZkpypQmNc6AYMf1yciqqbny8S', 'admin'),
(49, 'eben', 'eben2@rental.com', '$2y$10$hBMELbBB8Ef4JMO/KTIkS.lu/9Honm64OkUFxZXm3luPv9ryT578C', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sewa`
--
ALTER TABLE `sewa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mobil_id` (`mobil_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mobil` (`id_mobil`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT untuk tabel `sewa`
--
ALTER TABLE `sewa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `sewa`
--
ALTER TABLE `sewa`
  ADD CONSTRAINT `sewa_ibfk_1` FOREIGN KEY (`mobil_id`) REFERENCES `mobil` (`id`),
  ADD CONSTRAINT `sewa_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
