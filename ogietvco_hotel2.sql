-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 18 Nov 2025 pada 05.36
-- Versi server: 10.6.17-MariaDB-cll-lve
-- Versi PHP: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ogietvco_hotel2`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'rizal', '$2y$10$EhDVx1DdZwoL3N.3dzQFfOmFBCVM.Txe1TUMtUMoaOZRKkVh8A98K', '2025-10-27 10:44:50'),
(2, 'admin', '$2y$10$B7S3GImnNUA1U3sSEYXoBe2R8Q9hyV7M4m/xtLbaK0lb3mDWc3AX6', '2025-11-05 08:38:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password_hash`) VALUES
(1, 'rizal', '$2y$10$BMqW9yUJ2fZMjEVpMcvsPuwIj8VmgIzwpl69Ox0Mi3.pOwikGvMO6');

-- --------------------------------------------------------

--
-- Struktur dari tabel `amenity_requests`
--

CREATE TABLE `amenity_requests` (
  `id` int(11) NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `items` text DEFAULT NULL COMMENT 'JSON array of requested items',
  `status` enum('Pending','Delivered','Cancelled') DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `amenity_requests`
--

INSERT INTO `amenity_requests` (`id`, `room_number`, `guest_name`, `items`, `status`, `requested_at`) VALUES
(1, '-', 'Guest', '[{\"id\":\"11\",\"name\":\"Handuk Tambahan\",\"qty\":2},{\"id\":\"12\",\"name\":\"Bantal Tambahan\",\"qty\":1},{\"id\":\"13\",\"name\":\"Perlengkapan Mandi\",\"qty\":1},{\"id\":\"14\",\"name\":\"Sajadah\",\"qty\":1},{\"id\":\"15\",\"name\":\"Air Mineral\",\"qty\":1}]', 'Pending', '2025-11-11 09:59:13'),
(2, '-', 'Guest', '[{\"id\":\"11\",\"name\":\"Handuk Tambahan\",\"qty\":2},{\"id\":\"12\",\"name\":\"Bantal Tambahan\",\"qty\":1},{\"id\":\"13\",\"name\":\"Perlengkapan Mandi\",\"qty\":1}]', 'Pending', '2025-11-15 08:51:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `app_settings`
--

CREATE TABLE `app_settings` (
  `id` int(11) NOT NULL,
  `package` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `app_settings`
--

INSERT INTO `app_settings` (`id`, `package`, `status`, `updated_at`) VALUES
(1, 'com.google.android.youtube.tv', 1, '2025-11-05 08:32:40'),
(2, 'com.netflix.ninja', 1, '2025-11-05 08:32:40'),
(3, 'in.startv.hotstar.dplus.tv', 1, '2025-11-05 08:32:40'),
(4, 'com.vidio.android.tv', 1, '2025-11-05 08:32:40'),
(5, 'com.spotify.tv.android', 1, '2025-11-05 08:32:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dining_menu`
--

CREATE TABLE `dining_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `image_url` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dining_menu`
--

INSERT INTO `dining_menu` (`id`, `name`, `description`, `price`, `image_url`, `status`) VALUES
(1, 'Nasi Goreng Spesial', NULL, 25000, 'uploads/dining/menu_1762403060_6896.jpg', 'active'),
(2, 'Mie Goreng Seafood', NULL, 28000, 'uploads/dining/menu_1762403046_6137.jpg', 'active'),
(3, 'Sate Ayam Madura', NULL, 32000, 'uploads/dining/menu_1762403025_4402.jpg', 'active'),
(4, 'Soto Ayam Lamongan', NULL, 27000, 'uploads/dining/menu_1762402938_2826.jpg', 'active'),
(5, 'Ayam Penyet Sambal Ijo', NULL, 30000, 'uploads/dining/menu_1762402926_5896.jpg', 'active'),
(6, 'Capcay Kuah', NULL, 26000, 'uploads/dining/menu_1762402914_6848.jpg', 'active'),
(7, 'Teh Manis Dingin', NULL, 8000, 'uploads/dining/menu_1762402903_3623.jpg', 'active'),
(8, 'Kopi Hitam Tubruk', NULL, 10000, 'uploads/dining/menu_1762402893_5054.jpg', 'active'),
(9, 'Jus Alpukat', NULL, 15000, 'uploads/dining/menu_1762402873_2600.jpg', 'active'),
(10, 'Pisang Goreng Keju', NULL, 18000, 'uploads/dining/menu_1762402863_8844.jpg', 'active'),
(11, 'Nasi Goreng Terasi', NULL, 35000, 'uploads/dining/menu_1762402852_1742.jpg', 'active'),
(14, 'Mie Aceh', NULL, 100000, 'uploads/dining/menu_1762751245_7527.jpg', 'active'),
(15, 'Mie rebus', NULL, 85000, 'uploads/dining/menu_1762751152_1853.jpg', 'active');

-- --------------------------------------------------------

--
-- Struktur dari tabel `global_settings`
--

CREATE TABLE `global_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `global_settings`
--

INSERT INTO `global_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'launcher_enabled', '1'),
(4, 'default_volume', '10'),
(10, 'system_version', 'v251110-070051'),
(13, 'splash_enabled', '0'),
(16, 'launcher_bg', ''),
(17, 'launcher_home_bg', 'https://ogietv.com/AHotel/uploads/homebg/launcher_home_bg.jpg'),
(26, 'loading_logo_url', 'https://ogietv.com/AHotel/uploads/logo/loading_logo.png?v=1762812096'),
(29, 'custom_greeting_title', 'Welcome'),
(30, 'custom_welcome_greeting', 'Selamat datang di YelloHotel.\r\nKami sangat senang menyambut Anda sebagai tamu istimewa kami.\r\nNikmati kenyamanan kamar, layanan ramah, serta suasana modern dan kreatif yang telah kami siapkan untuk membuat masa inap Anda lebih berkesan.\r\nJika Anda membutuhkan bantuan kapan saja, tim kami selalu siap melayani dengan sepenuh hati.\r\nSelamat beristirahat & enjoy your stay!\r\n— Branch Manager, YelloHotel'),
(31, 'custom_greeting_image', 'uploads/greeting/greeting_img.jpg?v=1763337023');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guest_checkin`
--

CREATE TABLE `guest_checkin` (
  `id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `checkin_time` datetime NOT NULL DEFAULT current_timestamp(),
  `checkout_time` datetime DEFAULT NULL,
  `status` enum('checked_in','checked_out') NOT NULL DEFAULT 'checked_in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `guest_checkin`
--

INSERT INTO `guest_checkin` (`id`, `room_number`, `guest_name`, `checkin_time`, `checkout_time`, `status`) VALUES
(1, '101', 'Riza', '2025-11-12 19:42:56', '2025-11-12 19:43:18', 'checked_out'),
(2, '999', 'Mr. Muhammad Rizal', '2025-11-15 11:11:43', '2025-11-15 11:14:52', 'checked_out'),
(3, '999', 'Mr. Muhammad Rizal', '2025-11-15 11:43:16', '2025-11-15 11:48:30', 'checked_out'),
(4, '929', 'Mr. Muhammad Rizal2', '2025-11-15 11:44:29', NULL, 'checked_out'),
(5, '929', 'Mr. Muhammad Rizal2', '2025-11-15 11:45:00', NULL, 'checked_in'),
(6, '020', 'Mr. Tamu Simulasi', '2025-11-15 11:45:39', '2025-11-15 21:36:34', 'checked_out'),
(7, '999', 'Mr. Tamu999 Simulasi', '2025-11-15 11:49:06', '2025-11-15 11:54:21', 'checked_out'),
(8, '202', 'Mr.Richard Richard 000', '2025-11-15 13:50:58', NULL, 'checked_out'),
(9, '202', 'Mr.Richard Richard 000', '2025-11-15 13:51:01', '2025-11-15 13:52:16', 'checked_out'),
(10, '202', 'Mr.Richard Richard 000', '2025-11-15 13:55:27', NULL, 'checked_out'),
(11, '202', 'Mr.Richard Richard 000', '2025-11-15 13:55:27', NULL, 'checked_out'),
(12, '202', 'Mr.Richard Richard 000', '2025-11-15 13:55:30', NULL, 'checked_out'),
(13, '202', 'Mr.Richard Richard 000', '2025-11-15 13:55:36', NULL, 'checked_out'),
(14, '202', 'Mr.Richard Richard 000', '2025-11-15 13:55:37', NULL, 'checked_out'),
(15, '202', 'Mr.Richard Richard 000', '2025-11-15 13:57:35', NULL, 'checked_in'),
(16, '101', 'Mrs Little Missi', '2025-11-15 21:32:06', '2025-11-15 21:36:30', 'checked_out'),
(17, '999', 'Mrs. Little Missi', '2025-11-15 21:34:16', '2025-11-15 21:42:03', 'checked_out'),
(18, '999', 'onix latukolan', '2025-11-15 22:47:34', '2025-11-15 23:20:53', 'checked_out'),
(19, '999', 'Tamu SimulasiXX', '2025-11-15 23:20:53', '2025-11-15 23:43:00', 'checked_out');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_amenities`
--

CREATE TABLE `hotel_amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `status` enum('Available','Requestable','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_dining`
--

CREATE TABLE `hotel_dining` (
  `id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_facilities`
--

CREATE TABLE `hotel_facilities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `hotel_facilities`
--

INSERT INTO `hotel_facilities` (`id`, `name`, `icon_path`, `description`, `is_active`) VALUES
(1, 'Classic', 'uploads/facilities/facility_1762374139_7591.jpg', '', 1),
(2, 'Hotel Service', 'uploads/facilities/facility_1762374173_2294.jpg', '', 1),
(3, 'Breakfast', 'uploads/facilities/facility_1762374195_8430.jpg', '', 1),
(4, 'Sales', 'uploads/facilities/facility_1762374266_8271.jpg', '', 1),
(5, 'Bedroom', 'uploads/facilities/facility_1762374289_5827.jpg', '', 1),
(9, 'random 123', 'uploads/facilities/facility_1762672461_6616.jpeg', 'test 123', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_info`
--

CREATE TABLE `hotel_info` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `hotel_info`
--

INSERT INTO `hotel_info` (`id`, `title`, `description`, `icon_path`, `sort_order`, `created_at`) VALUES
(1, 'Hotel Kami', '', 'uploads/info/info_1762383411_1981.jpg', 0, '2025-11-05 22:56:51'),
(2, 'Check in', '', 'uploads/info/info_1762383434_1467.jpg', 0, '2025-11-05 22:57:14'),
(3, 'Analitik', '', 'uploads/info/info_1762383448_2523.jpg', 0, '2025-11-05 22:57:28'),
(4, 'Selamat Datang', '', 'uploads/info/info_1762383478_7320.jpg', 0, '2025-11-05 22:57:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_orders`
--

CREATE TABLE `hotel_orders` (
  `id` int(11) NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `items` text DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Confirmed','Delivered','Cancelled') DEFAULT 'Pending',
  `ordered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `hotel_orders`
--

INSERT INTO `hotel_orders` (`id`, `room_number`, `guest_name`, `items`, `total_price`, `status`, `ordered_at`) VALUES
(1, '-', 'Guest', '[{\"id\":\"9\",\"name\":\"Jus Alpukat\",\"price\":15000,\"qty\":1}]', 15000.00, 'Pending', '2025-11-06 06:52:15'),
(2, '-', 'Guest', '[{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":1}]', 18000.00, 'Pending', '2025-11-06 07:07:08'),
(3, '-', 'Guest', '[{\"id\":\"4\",\"name\":\"Soto Ayam Lamongan\",\"price\":27000,\"qty\":1},{\"id\":\"5\",\"name\":\"Ayam Penyet Sambal Ijo\",\"price\":30000,\"qty\":1},{\"id\":\"6\",\"name\":\"Capcay Kuah\",\"price\":26000,\"qty\":2},{\"id\":\"7\",\"name\":\"Teh Manis Dingin\",\"price\":8000,\"qty\":3}]', 133000.00, 'Pending', '2025-11-06 10:50:00'),
(4, '-', 'Guest', '[{\"id\":\"9\",\"name\":\"Jus Alpukat\",\"price\":15000,\"qty\":1},{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":2},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":2}]', 121000.00, 'Pending', '2025-11-06 11:47:12'),
(5, '-', 'Guest', '[{\"id\":\"9\",\"name\":\"Jus Alpukat\",\"price\":15000,\"qty\":1},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":1}]', 50000.00, 'Pending', '2025-11-06 14:06:00'),
(6, '-', 'Guest', '[{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":1},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":1}]', 53000.00, 'Pending', '2025-11-06 15:28:45'),
(7, '-', 'Guest', '[{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":2},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":2}]', 106000.00, 'Pending', '2025-11-06 15:43:23'),
(8, '-', 'Guest', '[{\"id\":\"6\",\"name\":\"Capcay Kuah\",\"price\":26000,\"qty\":1},{\"id\":\"8\",\"name\":\"Kopi Hitam Tubruk\",\"price\":10000,\"qty\":1},{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":1}]', 54000.00, 'Pending', '2025-11-06 22:55:39'),
(9, '-', 'Guest', '[{\"id\":\"9\",\"name\":\"Jus Alpukat\",\"price\":15000,\"qty\":1},{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":1}]', 33000.00, 'Pending', '2025-11-07 00:13:12'),
(10, '-', 'Guest', '[{\"id\":\"9\",\"name\":\"Jus Alpukat\",\"price\":15000,\"qty\":3},{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":3}]', 99000.00, 'Pending', '2025-11-07 02:17:17'),
(11, '-', 'Guest', '[{\"id\":\"1\",\"name\":\"Nasi Goreng Spesial\",\"price\":25000,\"qty\":4},{\"id\":\"2\",\"name\":\"Mie Goreng Seafood\",\"price\":28000,\"qty\":3},{\"id\":\"3\",\"name\":\"Sate Ayam Madura\",\"price\":32000,\"qty\":1},{\"id\":\"4\",\"name\":\"Soto Ayam Lamongan\",\"price\":27000,\"qty\":1},{\"id\":\"5\",\"name\":\"Ayam Penyet Sambal Ijo\",\"price\":30000,\"qty\":1},{\"id\":\"6\",\"name\":\"Capcay Kuah\",\"price\":26000,\"qty\":1},{\"id\":\"7\",\"name\":\"Teh Manis Dingin\",\"price\":8000,\"qty\":1}]', 307000.00, 'Pending', '2025-11-07 02:41:01'),
(12, '-', 'Guest', '[{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":1},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":1},{\"id\":\"12\",\"name\":\"ayam bakar\",\"price\":40000,\"qty\":1}]', 93000.00, 'Pending', '2025-11-07 07:38:14'),
(13, '-', 'Guest', '[{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":1}]', 35000.00, 'Pending', '2025-11-07 08:57:37'),
(14, '-', 'Guest', '[{\"id\":\"10\",\"name\":\"Pisang Goreng Keju\",\"price\":18000,\"qty\":2},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":2}]', 106000.00, 'Pending', '2025-11-07 20:34:58'),
(15, '-', 'Guest', '[{\"id\":\"2\",\"name\":\"Mie Goreng Seafood\",\"price\":28000,\"qty\":1},{\"id\":\"3\",\"name\":\"Sate Ayam Madura\",\"price\":32000,\"qty\":1},{\"id\":\"4\",\"name\":\"Soto Ayam Lamongan\",\"price\":27000,\"qty\":1},{\"id\":\"15\",\"name\":\"Mie rebus\",\"price\":85000,\"qty\":2}]', 257000.00, 'Pending', '2025-11-10 05:06:59'),
(16, '-', 'Guest', '[{\"id\":\"8\",\"name\":\"Kopi Hitam Tubruk\",\"price\":10000,\"qty\":1},{\"id\":\"11\",\"name\":\"Nasi Goreng Terasi\",\"price\":35000,\"qty\":1},{\"id\":\"14\",\"name\":\"Mie Aceh\",\"price\":100000,\"qty\":1}]', 145000.00, 'Pending', '2025-11-11 10:04:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `launcher_settings`
--

CREATE TABLE `launcher_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `launcher_settings`
--

INSERT INTO `launcher_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'is_launcher_enabled', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `managed_devices`
--

CREATE TABLE `managed_devices` (
  `id` int(11) NOT NULL,
  `device_id` varchar(100) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_seen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `managed_devices`
--

INSERT INTO `managed_devices` (`id`, `device_id`, `device_name`, `room_number`, `registered_at`, `is_active`, `last_seen`) VALUES
(81, 'TV-2GTMX3', 'smart tv office', '101', '2025-11-09 07:12:06', 1, '0000-00-00 00:00:00'),
(82, 'TV-0XMUGI', '222', '222', '2025-11-10 05:37:48', 1, '0000-00-00 00:00:00'),
(86, 'TV-PYRY2T', 'TCL 14', '14', '2025-11-11 03:37:01', 1, '0000-00-00 00:00:00'),
(87, 'TV-EY4FMM', '000', '000', '2025-11-11 09:49:37', 1, '0000-00-00 00:00:00'),
(88, 'TV-N44N1U', 'M Rizal', '020', '2025-11-11 12:03:08', 1, '0000-00-00 00:00:00'),
(89, 'TV-W3XKO0', 'TCL 14', '11', '2025-11-11 13:49:58', 1, '0000-00-00 00:00:00'),
(90, 'TV-W3XKOO', 'TV KANTOR', '111', '2025-11-11 13:51:15', 1, '0000-00-00 00:00:00'),
(91, 'TV-0F9NBQ', 'TCL 14', 'office', '2025-11-12 12:01:28', 1, '0000-00-00 00:00:00'),
(93, 'TV-2B7SN4', 'tv_server', '202', '2025-11-15 06:18:57', 1, '0000-00-00 00:00:00'),
(94, 'TB-YLOLB1', '333', '333', '2025-11-16 08:19:56', 1, '0000-00-00 00:00:00'),
(95, 'TV-8QPWIU', '444', '444', '2025-11-16 08:20:51', 1, '0000-00-00 00:00:00'),
(96, 'TV-BKES3H', '111', '111', '2025-11-16 09:56:33', 1, '0000-00-00 00:00:00'),
(97, 'TV-GB96VC', '333', '333', '2025-11-16 10:40:15', 1, '0000-00-00 00:00:00'),
(99, 'TV-225SA7', '999', '999', '2025-11-16 23:42:29', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `order_items` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Processed','Delivered','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_amenities`
--

CREATE TABLE `room_amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_path` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `room_amenities`
--

INSERT INTO `room_amenities` (`id`, `name`, `description`, `icon_path`, `category`, `created_at`) VALUES
(11, 'Handuk Tambahan', 'Handuk mandi ekstra (1 buah)', 'uploads/amenities/amenity_1762854506_5026.jpg', 'general', '2025-11-11 09:16:28'),
(12, 'Bantal Tambahan', 'Bantal tidur ekstra (1 buah)', 'uploads/amenities/amenity_1762854489_6848.jpg', 'general', '2025-11-11 09:16:28'),
(13, 'Perlengkapan Mandi', 'Sabun, Shampoo, Sikat Gigi', 'uploads/amenities/amenity_1762854476_7748.jpg', 'general', '2025-11-11 09:16:28'),
(14, 'Sajadah', 'Alat sholat (1 set)', 'uploads/amenities/amenity_1762854461_1137.jpg', 'general', '2025-11-11 09:16:28'),
(15, 'Air Mineral', 'Air mineral botol (2 botol)', 'uploads/amenities/amenity_1762854450_2040.jpg', 'general', '2025-11-11 09:16:28'),
(16, 'Teko Kopi', 'Kopi, teh, susu', 'uploads/amenities/amenity_1762855317_8455.jpg', 'general', '2025-11-11 10:01:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_apps`
--

CREATE TABLE `system_apps` (
  `id` int(11) NOT NULL,
  `app_key` varchar(50) NOT NULL,
  `app_name` varchar(100) NOT NULL,
  `icon_path` varchar(255) NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `android_package` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `system_apps`
--

INSERT INTO `system_apps` (`id`, `app_key`, `app_name`, `icon_path`, `is_visible`, `sort_order`, `android_package`) VALUES
(1, 'information', 'Information', 'img/information.png', 1, 0, NULL),
(2, 'dining', 'Dining Room', 'img/diningroom.png', 1, 1, NULL),
(3, 'amenities', 'Amenities', 'img/amenities.png', 1, 2, NULL),
(4, 'facilities', 'Facilities', 'img/facilities.png', 1, 3, NULL),
(5, 'tv', 'TV Channel', 'img/tv.png', 1, 4, 'tv.ogietv.launcher'),
(6, 'youtube', 'YouTube', 'img/youtube.png', 1, 5, 'com.google.android.youtube.tv'),
(7, 'netflix', 'Netflix', 'img/netflix.png', 1, 6, 'com.netflix.ninja'),
(8, 'spotify', 'Spotify', 'img/spotify.png', 1, 7, 'com.spotify.tv.android'),
(9, 'disney', 'Disney+ Hotstar', 'img/disney.png', 1, 8, 'in.startv.hotstar.dplus.tv'),
(10, 'vidio', 'Vidio', 'img/vidio.png', 1, 9, 'com.vidio.android.tv'),
(20, '_hannel_okal', 'Channel Lokal', 'https://ogietv.com/AHotel/uploads/icons/icon_1762833285.png', 1, 5, 'com.ctcorp.hospitality');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_apps_backup`
--

CREATE TABLE `system_apps_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `app_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `app_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `icon_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `android_package` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `system_apps_backup`
--

INSERT INTO `system_apps_backup` (`id`, `app_key`, `app_name`, `icon_path`, `is_visible`, `sort_order`, `android_package`) VALUES
(1, 'information', 'Information', 'img/information.png', 1, 0, NULL),
(2, 'hotel', 'Dining Room', 'img/diningroom.png', 1, 1, NULL),
(3, 'diningroom', 'Amenities', 'img/amenities.png', 1, 2, NULL),
(4, 'facilities', 'Facilities', 'img/facilities.png', 1, 3, NULL),
(5, 'amenities', 'TV Channel', 'img/tv.png', 1, 4, NULL),
(6, 'tv', 'TV Channel', 'img/tv.png', 1, 5, NULL),
(7, 'youtube', 'YouTube', 'img/youtube.png', 1, 6, 'com.google.android.youtube.tv'),
(8, 'netflix', 'Netflix', 'img/netflix.png', 1, 7, 'com.netflix.ninja'),
(9, 'spotify', 'Spotify', 'img/spotify.png', 1, 8, 'com.spotify.tv.android'),
(10, 'disney', 'Disney+ Hotstar', 'img/disney.png', 1, 9, 'in.disney.hotstar'),
(13, 'vidio', 'Vidio', 'img/vidio.png', 1, 11, 'com.vidio.android.tv');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_marquee`
--

CREATE TABLE `system_marquee` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `system_marquee`
--

INSERT INTO `system_marquee` (`id`, `content`, `is_active`, `last_updated`) VALUES
(1, 'Please Welcome TakeOff', 1, '2025-11-15 06:35:22');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `amenity_requests`
--
ALTER TABLE `amenity_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dining_menu`
--
ALTER TABLE `dining_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `global_settings`
--
ALTER TABLE `global_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `guest_checkin`
--
ALTER TABLE `guest_checkin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_number` (`room_number`,`status`);

--
-- Indeks untuk tabel `hotel_amenities`
--
ALTER TABLE `hotel_amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hotel_dining`
--
ALTER TABLE `hotel_dining`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hotel_facilities`
--
ALTER TABLE `hotel_facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hotel_info`
--
ALTER TABLE `hotel_info`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hotel_orders`
--
ALTER TABLE `hotel_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `launcher_settings`
--
ALTER TABLE `launcher_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `managed_devices`
--
ALTER TABLE `managed_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_id` (`device_id`);

--
-- Indeks untuk tabel `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `room_amenities`
--
ALTER TABLE `room_amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_apps`
--
ALTER TABLE `system_apps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_key` (`app_key`);

--
-- Indeks untuk tabel `system_marquee`
--
ALTER TABLE `system_marquee`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `amenity_requests`
--
ALTER TABLE `amenity_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `dining_menu`
--
ALTER TABLE `dining_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `global_settings`
--
ALTER TABLE `global_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `guest_checkin`
--
ALTER TABLE `guest_checkin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `hotel_amenities`
--
ALTER TABLE `hotel_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_dining`
--
ALTER TABLE `hotel_dining`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_facilities`
--
ALTER TABLE `hotel_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `hotel_info`
--
ALTER TABLE `hotel_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `hotel_orders`
--
ALTER TABLE `hotel_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `launcher_settings`
--
ALTER TABLE `launcher_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `managed_devices`
--
ALTER TABLE `managed_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT untuk tabel `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `room_amenities`
--
ALTER TABLE `room_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `system_apps`
--
ALTER TABLE `system_apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `system_marquee`
--
ALTER TABLE `system_marquee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
