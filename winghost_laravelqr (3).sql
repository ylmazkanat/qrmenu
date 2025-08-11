-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 11 Ağu 2025, 02:33:49
-- Sunucu sürümü: 10.5.29-MariaDB-cll-lve
-- PHP Sürümü: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `winghost_laravelqr`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `facebook` varchar(191) DEFAULT NULL,
  `instagram` varchar(191) DEFAULT NULL,
  `twitter` varchar(191) DEFAULT NULL,
  `youtube` varchar(191) DEFAULT NULL,
  `linkedin` varchar(191) DEFAULT NULL,
  `whatsapp` varchar(191) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `longitude` varchar(191) DEFAULT NULL,
  `city` varchar(191) NOT NULL,
  `state` varchar(191) NOT NULL,
  `zip_code` varchar(191) NOT NULL,
  `address` text NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(191) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(191) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `businesses`
--

CREATE TABLE `businesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `plan` enum('free','basic','premium','enterprise') NOT NULL DEFAULT 'free',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `plan_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `businesses`
--

INSERT INTO `businesses` (`id`, `owner_id`, `name`, `slug`, `description`, `logo`, `phone`, `address`, `tax_number`, `email`, `website`, `plan`, `is_active`, `plan_expires_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'Test İşletmesi', 'test-isletmesi', 'Bu bir test işletmesidir. Birden fazla restoranı vardır.', NULL, '+90 555 123 4567', 'Test Mahallesi, Test Caddesi No:1, İstanbul', '1234567890', 'info@testisletmesi.com', NULL, 'basic', 1, NULL, '2025-07-22 14:53:33', '2025-07-22 14:53:33');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `business_subscriptions`
--

CREATE TABLE `business_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive','cancelled','expired','pending_cancellation') NOT NULL DEFAULT 'active',
  `started_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `transaction_id` varchar(191) DEFAULT NULL,
  `usage_limits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`usage_limits`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `payment_date` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `business_subscriptions`
--

INSERT INTO `business_subscriptions` (`id`, `business_id`, `package_id`, `status`, `started_at`, `expires_at`, `cancelled_at`, `amount_paid`, `payment_method`, `transaction_id`, `usage_limits`, `created_at`, `updated_at`, `is_paid`, `payment_date`) VALUES
(10, 1, 1, 'active', '2025-08-08 00:50:04', '2025-09-07 21:00:00', NULL, 100.00, 'admin_manual', 'ADMIN_1754625004', NULL, '2025-08-08 00:50:04', '2025-08-09 00:52:11', 1, '2025-08-15 21:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `restaurant_id`, `name`, `slug`, `image`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Ana Yemekler', NULL, 'categories/1753682927_category_688713ef0c2db.png', 1, '2025-07-22 14:53:35', '2025-07-28 03:08:47'),
(2, 1, 'İçecekler', NULL, 'categories/1753683149_category_688714cd8d098.jpg', 2, '2025-07-22 14:53:35', '2025-07-28 03:12:29'),
(3, 1, 'Tatlılar', NULL, 'categories/1753683212_category_6887150c8bbbf.jfif', 3, '2025-07-22 14:53:35', '2025-07-28 03:13:32'),
(4, 1, 'Başlangıçlar', NULL, 'categories/1753683184_category_688714f0c28cc.jpg', 4, '2025-07-22 14:53:35', '2025-07-28 03:13:04'),
(5, 2, 'Pizzalar', NULL, NULL, 1, '2025-07-22 14:53:35', '2025-07-22 14:53:35'),
(6, 2, 'İçecekler', NULL, NULL, 2, '2025-07-22 14:53:35', '2025-07-22 14:53:35'),
(7, 2, 'Salata', NULL, NULL, 3, '2025-07-22 14:53:35', '2025-07-22 14:53:35');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `domain_mappings`
--

CREATE TABLE `domain_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `domain_type` enum('subdomain','custom') NOT NULL DEFAULT 'subdomain',
  `domain` varchar(191) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kitchen_views`
--

CREATE TABLE `kitchen_views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `kitchen_views`
--

INSERT INTO `kitchen_views` (`id`, `order_id`, `seen`, `seen_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-07-23 16:17:58', '2025-07-22 15:28:15', '2025-07-23 16:17:58'),
(2, 2, 1, '2025-07-23 16:18:09', '2025-07-22 15:42:02', '2025-07-23 16:18:09'),
(3, 3, 1, '2025-07-23 16:18:08', '2025-07-22 17:05:20', '2025-07-23 16:18:08'),
(4, 4, 1, '2025-07-23 16:18:07', '2025-07-22 18:22:27', '2025-07-23 16:18:07'),
(5, 5, 1, '2025-07-23 16:18:04', '2025-07-22 18:24:34', '2025-07-23 16:18:04'),
(6, 6, 1, '2025-07-23 16:18:05', '2025-07-22 18:28:34', '2025-07-23 16:18:05'),
(7, 7, 1, '2025-07-23 16:18:07', '2025-07-22 19:00:35', '2025-07-23 16:18:07'),
(8, 8, 1, '2025-07-22 19:23:13', '2025-07-22 19:23:04', '2025-07-22 19:23:13'),
(9, 9, 1, '2025-07-23 09:00:11', '2025-07-23 09:00:07', '2025-07-23 09:00:11'),
(10, 10, 1, '2025-07-23 09:03:05', '2025-07-23 09:02:35', '2025-07-23 09:03:05'),
(11, 11, 1, '2025-07-23 09:04:03', '2025-07-23 09:03:57', '2025-07-23 09:04:03'),
(12, 12, 1, '2025-07-23 09:04:56', '2025-07-23 09:04:43', '2025-07-23 09:04:56'),
(13, 13, 1, '2025-07-23 09:13:30', '2025-07-23 09:07:03', '2025-07-23 09:13:30'),
(14, 14, 1, '2025-07-23 09:13:43', '2025-07-23 09:13:25', '2025-07-23 09:13:43'),
(15, 15, 1, '2025-07-23 09:17:25', '2025-07-23 09:14:48', '2025-07-23 09:17:25'),
(16, 16, 1, '2025-07-23 10:40:45', '2025-07-23 10:40:33', '2025-07-23 10:40:45'),
(17, 18, 1, '2025-07-23 11:38:47', '2025-07-23 11:38:31', '2025-07-23 11:38:47'),
(18, 19, 1, '2025-07-23 11:41:13', '2025-07-23 11:41:00', '2025-07-23 11:41:13'),
(19, 20, 1, '2025-07-23 11:51:57', '2025-07-23 11:51:40', '2025-07-23 11:51:57'),
(20, 21, 1, '2025-07-23 12:09:07', '2025-07-23 12:08:59', '2025-07-23 12:09:07'),
(21, 22, 0, NULL, '2025-07-23 12:14:05', '2025-07-23 12:14:05'),
(22, 23, 1, '2025-07-23 12:15:08', '2025-07-23 12:15:03', '2025-07-23 12:15:08'),
(23, 24, 1, '2025-07-23 12:16:41', '2025-07-23 12:15:33', '2025-07-23 12:16:41'),
(24, 25, 1, '2025-07-23 13:13:35', '2025-07-23 13:13:20', '2025-07-23 13:13:35'),
(25, 26, 1, '2025-07-23 13:13:37', '2025-07-23 13:13:28', '2025-07-23 13:13:37'),
(26, 27, 1, '2025-07-23 13:15:43', '2025-07-23 13:15:32', '2025-07-23 13:15:43'),
(27, 28, 1, '2025-07-23 13:20:59', '2025-07-23 13:20:50', '2025-07-23 13:20:59'),
(28, 29, 1, '2025-07-23 13:33:20', '2025-07-23 13:32:55', '2025-07-23 13:33:20'),
(29, 30, 1, '2025-07-23 13:35:12', '2025-07-23 13:34:53', '2025-07-23 13:35:12'),
(30, 31, 1, '2025-07-23 13:35:13', '2025-07-23 13:35:02', '2025-07-23 13:35:13'),
(31, 32, 1, '2025-07-23 13:41:51', '2025-07-23 13:41:38', '2025-07-23 13:41:51'),
(32, 33, 1, '2025-07-23 13:49:31', '2025-07-23 13:49:06', '2025-07-23 13:49:31'),
(33, 34, 1, '2025-07-23 13:57:42', '2025-07-23 13:57:33', '2025-07-23 13:57:42'),
(34, 35, 1, '2025-07-23 14:00:56', '2025-07-23 13:59:00', '2025-07-23 14:00:56'),
(35, 36, 1, '2025-07-23 14:02:55', '2025-07-23 14:02:49', '2025-07-23 14:02:55'),
(36, 37, 1, '2025-07-23 14:28:03', '2025-07-23 14:27:50', '2025-07-23 14:28:03'),
(37, 38, 1, '2025-07-23 14:39:53', '2025-07-23 14:39:41', '2025-07-23 14:39:53'),
(38, 39, 0, NULL, '2025-07-23 15:11:34', '2025-07-23 15:11:34'),
(39, 40, 1, '2025-07-23 15:22:12', '2025-07-23 15:19:41', '2025-07-23 15:22:12'),
(40, 41, 0, NULL, '2025-07-23 15:24:30', '2025-07-23 15:24:30'),
(41, 42, 0, NULL, '2025-07-23 15:28:26', '2025-07-23 15:28:26'),
(42, 43, 0, NULL, '2025-07-23 15:39:53', '2025-07-23 15:39:53'),
(43, 44, 0, NULL, '2025-07-23 15:42:43', '2025-07-23 15:42:43'),
(44, 45, 0, NULL, '2025-07-23 15:51:49', '2025-07-23 15:51:49'),
(45, 46, 1, '2025-07-23 16:21:01', '2025-07-23 16:20:51', '2025-07-23 16:21:01'),
(46, 47, 0, NULL, '2025-07-23 16:22:08', '2025-07-23 16:22:08'),
(47, 48, 1, '2025-07-23 16:22:57', '2025-07-23 16:22:52', '2025-07-23 16:22:57'),
(48, 49, 0, NULL, '2025-07-23 16:34:03', '2025-07-23 16:34:03'),
(49, 50, 1, '2025-07-23 16:52:27', '2025-07-23 16:45:02', '2025-07-23 16:52:27'),
(50, 51, 0, NULL, '2025-07-23 16:47:37', '2025-07-23 16:47:37'),
(51, 52, 0, NULL, '2025-07-23 16:48:34', '2025-07-23 16:48:34'),
(52, 53, 1, '2025-07-28 11:33:35', '2025-07-23 16:50:00', '2025-07-28 11:33:35'),
(53, 54, 0, NULL, '2025-07-23 16:50:14', '2025-07-23 16:50:14'),
(54, 55, 1, '2025-07-23 16:55:59', '2025-07-23 16:55:31', '2025-07-23 16:55:59'),
(55, 56, 0, NULL, '2025-07-23 16:56:40', '2025-07-23 16:56:40'),
(56, 57, 1, '2025-07-28 11:36:42', '2025-07-28 11:36:27', '2025-07-28 11:36:42'),
(57, 58, 0, NULL, '2025-07-28 11:37:14', '2025-07-28 11:37:14');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_22_145202_create_restaurants_table', 1),
(5, '2025_07_22_145216_create_categories_table', 1),
(6, '2025_07_22_145219_create_products_table', 1),
(7, '2025_07_22_145222_create_orders_table', 1),
(8, '2025_07_22_145225_create_order_items_table', 1),
(9, '2025_07_22_145227_create_waiters_table', 1),
(10, '2025_07_22_145236_create_kitchen_views_table', 1),
(11, '2025_07_22_145240_create_domain_mappings_table', 1),
(12, '2025_07_22_162521_create_businesses_table', 1),
(13, '2025_07_22_162555_create_restaurant_staff_table', 1),
(14, '2025_07_22_222811_add_image_to_categories_table', 2),
(15, '2025_07_23_000000_update_categories_add_image_slug', 3),
(17, '2025_07_23_123936_create_tables_table', 4),
(18, '2025_07_23_132842_add_customer_name_to_orders_table', 5),
(19, '2025_07_23_164500_add_customer_name_to_orders', 5),
(20, '2025_07_23_164600_create_restaurant_order_settings_table', 5),
(21, '2025_07_23_142252_add_created_by_user_id_to_orders_table', 6),
(24, '2025_07_23_145553_update_orders_status_enum', 7),
(25, '2025_07_23_155252_add_payment_columns_to_orders_table', 7),
(26, '2025_07_23_155255_add_payment_columns_to_orders_table', 7),
(27, '2025_07_23_182110_add_cancelled_by_customer_to_orders_table', 8),
(28, '2025_07_23_182546_add_last_status_to_orders_table', 9),
(29, '2025_07_23_183552_add_kitchen_cancelled_status_to_orders_table', 10),
(30, '2025_07_23_185901_alter_orders_status_enum_add_musteri_iptal', 11),
(31, '2025_07_24_000001_add_social_and_color_to_restaurants_table', 12),
(32, '2025_07_27_222052_add_color_columns_to_restaurants_table', 13),
(34, '2025_07_28_031748_create_branches_table', 14),
(35, '2025_07_28_032310_add_youtube_linkedin_to_restaurants_table', 14),
(39, '2025_07_28_032716_add_working_hours_to_restaurants_table', 15),
(40, '2025_07_28_033308_create_reviews_table', 15),
(43, '2025_07_28_224741_increase_pin_code_length', 16),
(44, '2025_07_28_224706_encrypt_existing_pin_codes', 17),
(45, '2025_07_28_040340_add_translation_settings_to_restaurants_table', 18),
(46, '2025_07_29_001916_remove_pin_code_from_restaurant_staff_table', 19),
(47, '2025_07_29_001928_remove_pin_code_from_waiters_table', 19),
(48, '2025_08_04_021651_create_packages_table', 20),
(49, '2025_08_04_021658_create_business_subscriptions_table', 21),
(50, '2025_08_04_021703_create_package_features_table', 21),
(51, '2025_08_07_035655_add_pending_cancellation_to_business_subscriptions_status', 22),
(52, '2025_08_07_165537_add_payment_fields_to_business_subscriptions_table', 23);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `table_number` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `status` enum('pending','preparing','ready','delivered','cancelled','zafiyat','kitchen_cancelled','musteri_iptal') NOT NULL DEFAULT 'pending',
  `cancelled_by_customer` tinyint(1) NOT NULL DEFAULT 0,
  `last_status` varchar(191) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(191) DEFAULT NULL,
  `cash_received` decimal(8,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `restaurant_id`, `created_by_user_id`, `table_number`, `customer_name`, `status`, `cancelled_by_customer`, `last_status`, `total`, `created_at`, `updated_at`, `payment_method`, `cash_received`) VALUES
(57, 1, 3, 'masa2', NULL, 'ready', 0, NULL, 45.00, '2025-07-28 11:36:27', '2025-07-28 11:36:45', NULL, NULL),
(58, 1, 3, 'masa3', NULL, 'kitchen_cancelled', 0, NULL, 45.00, '2025-07-28 11:37:14', '2025-07-28 11:38:03', NULL, NULL),
(55, 1, NULL, 'masa3', 'Müşteri', 'delivered', 0, NULL, 13.00, '2025-07-23 16:55:31', '2025-07-23 16:56:29', NULL, NULL),
(56, 1, NULL, 'masa3', 'Müşteri', 'kitchen_cancelled', 0, NULL, 5.00, '2025-07-23 16:56:40', '2025-07-23 16:59:10', NULL, NULL),
(53, 1, NULL, 'masa2', 'Müşteri', 'delivered', 0, NULL, 5.00, '2025-07-23 16:50:00', '2025-07-28 11:33:55', NULL, NULL),
(54, 1, NULL, 'masa2', 'Müşteri', 'pending', 0, NULL, 8.00, '2025-07-23 16:50:14', '2025-07-23 16:50:14', NULL, NULL),
(52, 1, NULL, 'masa2', 'Müşteri', 'pending', 0, NULL, 18.00, '2025-07-23 16:48:34', '2025-07-23 16:48:34', NULL, NULL),
(51, 1, NULL, 'masa2', 'Müşteri', 'pending', 0, NULL, 8.00, '2025-07-23 16:47:37', '2025-07-23 16:47:37', NULL, NULL),
(50, 1, NULL, 'masa2', 'Müşteri', 'ready', 0, NULL, 5.00, '2025-07-23 16:45:01', '2025-07-23 16:52:43', NULL, NULL),
(49, 1, NULL, 'masa2', 'Müşteri', 'kitchen_cancelled', 0, NULL, 13.00, '2025-07-23 16:34:03', '2025-07-23 16:36:21', NULL, NULL),
(48, 1, NULL, 'masa2', 'Müşteri', 'delivered', 0, NULL, 5.00, '2025-07-23 16:22:51', '2025-07-23 16:28:25', NULL, NULL),
(47, 1, NULL, 'masa2', 'Müşteri', 'zafiyat', 0, NULL, 10.00, '2025-07-23 16:22:08', '2025-07-23 16:22:30', NULL, NULL),
(46, 1, NULL, 'masa2', 'Müşteri', 'kitchen_cancelled', 0, NULL, 5.00, '2025-07-23 16:20:51', '2025-07-23 16:21:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 45.00, NULL, '2025-07-22 15:28:15', '2025-07-22 15:28:15'),
(2, 1, 4, 1, 5.00, NULL, '2025-07-22 15:28:15', '2025-07-22 15:28:15'),
(3, 1, 3, 1, 85.00, NULL, '2025-07-22 15:28:15', '2025-07-22 15:28:15'),
(4, 2, 1, 1, 45.00, NULL, '2025-07-22 15:42:02', '2025-07-22 15:42:02'),
(5, 2, 2, 1, 55.00, NULL, '2025-07-22 15:42:02', '2025-07-22 15:42:02'),
(6, 3, 4, 1, 5.00, NULL, '2025-07-22 17:05:20', '2025-07-22 17:05:20'),
(7, 3, 3, 1, 85.00, NULL, '2025-07-22 17:05:20', '2025-07-22 17:05:20'),
(8, 3, 2, 1, 55.00, NULL, '2025-07-22 17:05:20', '2025-07-22 17:05:20'),
(9, 4, 1, 1, 45.00, NULL, '2025-07-22 18:22:27', '2025-07-22 18:22:27'),
(10, 4, 4, 1, 5.00, NULL, '2025-07-22 18:22:27', '2025-07-22 18:22:27'),
(11, 4, 2, 2, 55.00, NULL, '2025-07-22 18:22:27', '2025-07-22 18:22:27'),
(12, 5, 1, 1, 45.00, NULL, '2025-07-22 18:24:34', '2025-07-22 18:24:34'),
(13, 5, 4, 1, 5.00, NULL, '2025-07-22 18:24:34', '2025-07-22 18:24:34'),
(14, 6, 1, 1, 45.00, NULL, '2025-07-22 18:28:34', '2025-07-22 18:28:34'),
(15, 6, 2, 1, 55.00, NULL, '2025-07-22 18:28:34', '2025-07-22 18:28:34'),
(16, 7, 1, 1, 45.00, NULL, '2025-07-22 19:00:35', '2025-07-22 19:00:35'),
(17, 7, 4, 1, 5.00, NULL, '2025-07-22 19:00:35', '2025-07-22 19:00:35'),
(18, 8, 1, 1, 45.00, NULL, '2025-07-22 19:23:04', '2025-07-22 19:23:04'),
(19, 8, 4, 1, 5.00, NULL, '2025-07-22 19:23:04', '2025-07-22 19:23:04'),
(20, 9, 4, 1, 5.00, NULL, '2025-07-23 09:00:07', '2025-07-23 09:00:07'),
(21, 10, 1, 1, 45.00, NULL, '2025-07-23 09:02:35', '2025-07-23 09:02:35'),
(22, 10, 4, 1, 5.00, NULL, '2025-07-23 09:02:35', '2025-07-23 09:02:35'),
(23, 11, 1, 1, 45.00, NULL, '2025-07-23 09:03:57', '2025-07-23 09:03:57'),
(24, 11, 4, 1, 5.00, NULL, '2025-07-23 09:03:57', '2025-07-23 09:03:57'),
(25, 12, 1, 1, 45.00, NULL, '2025-07-23 09:04:43', '2025-07-23 09:04:43'),
(26, 12, 4, 1, 5.00, NULL, '2025-07-23 09:04:43', '2025-07-23 09:04:43'),
(27, 13, 1, 1, 45.00, NULL, '2025-07-23 09:07:03', '2025-07-23 09:07:03'),
(28, 13, 4, 1, 5.00, NULL, '2025-07-23 09:07:03', '2025-07-23 09:07:03'),
(29, 14, 1, 1, 45.00, NULL, '2025-07-23 09:13:25', '2025-07-23 09:13:25'),
(30, 15, 8, 1, 100.00, NULL, '2025-07-23 09:14:48', '2025-07-23 09:14:48'),
(31, 16, 1, 1, 45.00, NULL, '2025-07-23 10:40:33', '2025-07-23 10:40:33'),
(32, 18, 4, 1, 5.00, NULL, '2025-07-23 11:38:31', '2025-07-23 11:38:31'),
(33, 19, 1, 1, 45.00, NULL, '2025-07-23 11:41:00', '2025-07-23 11:41:00'),
(34, 19, 4, 1, 5.00, NULL, '2025-07-23 11:41:00', '2025-07-23 11:41:00'),
(35, 20, 1, 1, 45.00, NULL, '2025-07-23 11:51:40', '2025-07-23 11:51:40'),
(36, 20, 4, 1, 5.00, NULL, '2025-07-23 11:51:40', '2025-07-23 11:51:40'),
(37, 21, 1, 1, 45.00, NULL, '2025-07-23 12:08:59', '2025-07-23 12:08:59'),
(38, 21, 4, 1, 5.00, NULL, '2025-07-23 12:08:59', '2025-07-23 12:08:59'),
(39, 22, 1, 1, 45.00, NULL, '2025-07-23 12:14:05', '2025-07-23 12:14:05'),
(40, 22, 4, 1, 5.00, NULL, '2025-07-23 12:14:05', '2025-07-23 12:14:05'),
(41, 23, 1, 1, 45.00, NULL, '2025-07-23 12:15:03', '2025-07-23 12:15:03'),
(42, 24, 1, 1, 45.00, NULL, '2025-07-23 12:15:33', '2025-07-23 12:15:33'),
(43, 25, 1, 1, 45.00, NULL, '2025-07-23 13:13:20', '2025-07-23 13:13:20'),
(44, 26, 4, 1, 5.00, NULL, '2025-07-23 13:13:28', '2025-07-23 13:13:28'),
(45, 27, 4, 1, 5.00, NULL, '2025-07-23 13:15:32', '2025-07-23 13:15:32'),
(46, 28, 4, 1, 5.00, NULL, '2025-07-23 13:20:50', '2025-07-23 13:20:50'),
(47, 29, 1, 1, 45.00, NULL, '2025-07-23 13:32:55', '2025-07-23 13:32:55'),
(48, 30, 4, 1, 5.00, NULL, '2025-07-23 13:34:53', '2025-07-23 13:34:53'),
(49, 31, 5, 1, 8.00, NULL, '2025-07-23 13:35:02', '2025-07-23 13:35:02'),
(50, 32, 1, 1, 45.00, NULL, '2025-07-23 13:41:37', '2025-07-23 13:41:37'),
(51, 33, 4, 1, 5.00, NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(52, 34, 4, 1, 5.00, NULL, '2025-07-23 13:57:33', '2025-07-23 13:57:33'),
(53, 35, 4, 1, 5.00, NULL, '2025-07-23 13:58:59', '2025-07-23 13:58:59'),
(54, 36, 4, 1, 5.00, NULL, '2025-07-23 14:02:49', '2025-07-23 14:02:49'),
(55, 37, 4, 1, 5.00, NULL, '2025-07-23 14:27:49', '2025-07-23 14:27:49'),
(56, 38, 4, 2, 5.00, NULL, '2025-07-23 14:39:41', '2025-07-23 14:39:41'),
(57, 39, 4, 2, 5.00, NULL, '2025-07-23 15:11:34', '2025-07-23 15:11:34'),
(58, 40, 4, 1, 5.00, NULL, '2025-07-23 15:19:41', '2025-07-23 15:19:41'),
(59, 41, 4, 1, 5.00, NULL, '2025-07-23 15:24:30', '2025-07-23 15:24:30'),
(60, 42, 4, 3, 5.00, NULL, '2025-07-23 15:28:26', '2025-07-23 15:28:26'),
(61, 43, 4, 2, 5.00, NULL, '2025-07-23 15:39:53', '2025-07-23 15:39:53'),
(62, 44, 4, 2, 5.00, NULL, '2025-07-23 15:42:43', '2025-07-23 15:42:43'),
(63, 45, 4, 1, 5.00, NULL, '2025-07-23 15:51:49', '2025-07-23 15:51:49'),
(64, 46, 4, 1, 5.00, NULL, '2025-07-23 16:20:51', '2025-07-23 16:20:51'),
(65, 47, 4, 2, 5.00, NULL, '2025-07-23 16:22:08', '2025-07-23 16:22:08'),
(66, 48, 4, 1, 5.00, NULL, '2025-07-23 16:22:52', '2025-07-23 16:22:52'),
(67, 49, 4, 1, 5.00, NULL, '2025-07-23 16:34:03', '2025-07-23 16:34:03'),
(68, 49, 5, 1, 8.00, NULL, '2025-07-23 16:34:03', '2025-07-23 16:34:03'),
(69, 50, 4, 1, 5.00, NULL, '2025-07-23 16:45:02', '2025-07-23 16:45:02'),
(70, 51, 5, 1, 8.00, NULL, '2025-07-23 16:47:37', '2025-07-23 16:47:37'),
(71, 52, 5, 1, 8.00, NULL, '2025-07-23 16:48:34', '2025-07-23 16:48:34'),
(72, 52, 4, 2, 5.00, NULL, '2025-07-23 16:48:34', '2025-07-23 16:48:34'),
(73, 53, 4, 1, 5.00, NULL, '2025-07-23 16:50:00', '2025-07-23 16:50:00'),
(74, 54, 5, 1, 8.00, NULL, '2025-07-23 16:50:14', '2025-07-23 16:50:14'),
(75, 55, 4, 1, 5.00, NULL, '2025-07-23 16:55:31', '2025-07-23 16:55:31'),
(76, 55, 5, 1, 8.00, NULL, '2025-07-23 16:55:31', '2025-07-23 16:55:31'),
(77, 56, 4, 1, 5.00, NULL, '2025-07-23 16:56:40', '2025-07-23 16:56:40'),
(78, 57, 1, 1, 45.00, NULL, '2025-07-28 11:36:27', '2025-07-28 11:36:27'),
(79, 58, 1, 1, 45.00, NULL, '2025-07-28 11:37:14', '2025-07-28 11:37:14');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `packages`
--

INSERT INTO `packages` (`id`, `name`, `slug`, `description`, `price`, `billing_cycle`, `is_active`, `is_popular`, `sort_order`, `features`, `created_at`, `updated_at`) VALUES
(1, 'Başlangıç Paketi', 'baslangic-paketi', 'Bu paket küçük işletmeler için uygundur.', 100.00, 'monthly', 1, 1, 1, NULL, '2025-08-04 00:37:07', '2025-08-09 00:54:48'),
(2, 'Orta Paket', 'orta-paket', 'Tavsiye edilen pakettir.', 200.00, 'monthly', 1, 1, 2, NULL, '2025-08-04 00:38:24', '2025-08-09 00:56:24'),
(3, 'Büyük Paket', 'buyuk-paket', NULL, 300.00, 'monthly', 1, 0, 3, NULL, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(4, 'Sınırsız Paket', 'sinirsiz-paket', NULL, 400.00, 'monthly', 1, 0, 4, NULL, '2025-08-09 00:58:21', '2025-08-09 00:58:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `package_features`
--

CREATE TABLE `package_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `feature_key` varchar(100) NOT NULL,
  `feature_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `limit_value` int(11) DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `package_features`
--

INSERT INTO `package_features` (`id`, `package_id`, `feature_key`, `feature_name`, `description`, `limit_value`, `is_enabled`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'max_restaurants', 'Maksimum Restoran Sayısı', 'Maksimum Restoran Sayısı özelliği', 1, 1, 1, '2025-08-04 00:37:07', '2025-08-09 00:54:48'),
(2, 1, 'max_managers', 'Maksimum Müdür Hesabı', 'Maksimum Müdür Hesabı özelliği', 1, 1, 2, '2025-08-04 00:37:07', '2025-08-09 00:54:48'),
(3, 1, 'max_staff', 'Maksimum Çalışan Sayısı', 'Maksimum Çalışan Sayısı özelliği', 10, 1, 3, '2025-08-04 00:37:08', '2025-08-09 00:54:48'),
(4, 1, 'max_products', 'Maksimum Ürün Sayısı', 'Maksimum Ürün Sayısı özelliği', 30, 1, 4, '2025-08-04 00:37:08', '2025-08-09 00:54:48'),
(5, 1, 'max_categories', 'Maksimum Kategori Sayısı', 'Maksimum Kategori Sayısı özelliği', 10, 1, 5, '2025-08-04 00:37:08', '2025-08-06 00:38:41'),
(6, 1, 'custom_domain', 'Özel Domain Desteği', 'Özel Domain Desteği özelliği', 0, 0, 6, '2025-08-04 00:37:09', '2025-08-09 00:54:49'),
(7, 1, 'analytics', 'Analitik Raporları', 'Analitik Raporları özelliği', 1, 1, 7, '2025-08-04 00:37:09', '2025-08-09 00:54:49'),
(8, 1, 'multi_language', 'Çoklu Dil Desteği', 'Çoklu Dil Desteği özelliği', 1, 1, 8, '2025-08-04 00:37:09', '2025-08-04 00:37:09'),
(9, 1, 'priority_support', 'Öncelikli Destek', 'Öncelikli Destek özelliği', 0, 0, 9, '2025-08-04 00:37:09', '2025-08-04 00:37:09'),
(10, 1, 'advanced_menu_editor', 'Gelişmiş Menü Editörü', 'Gelişmiş Menü Editörü özelliği', 1, 1, 10, '2025-08-04 00:37:09', '2025-08-04 00:51:30'),
(11, 1, 'order_management', 'Sipariş Yönetimi', 'Sipariş Yönetimi özelliği', 0, 0, 11, '2025-08-04 00:37:09', '2025-08-09 00:54:49'),
(12, 1, 'customer_reviews', 'Müşteri Değerlendirmeleri', 'Müşteri Değerlendirmeleri özelliği', 0, 0, 12, '2025-08-04 00:37:09', '2025-08-04 00:37:09'),
(13, 1, 'loyalty_program', 'Sadakat Programı', 'Sadakat Programı özelliği', 0, 0, 13, '2025-08-04 00:37:09', '2025-08-04 00:37:09'),
(14, 1, 'marketing_tools', 'Pazarlama Araçları', 'Pazarlama Araçları özelliği', 0, 0, 14, '2025-08-04 00:37:09', '2025-08-04 00:37:09'),
(15, 1, 'api_access', 'API Erişimi', 'API Erişimi özelliği', 0, 0, 15, '2025-08-04 00:37:09', '2025-08-04 00:37:09'),
(16, 2, 'max_restaurants', 'Maksimum Restoran Sayısı', 'Maksimum Restoran Sayısı özelliği', 2, 1, 1, '2025-08-04 00:38:24', '2025-08-04 00:42:08'),
(17, 2, 'max_managers', 'Maksimum Müdür Hesabı', 'Maksimum Müdür Hesabı özelliği', 2, 1, 2, '2025-08-04 00:38:24', '2025-08-09 00:56:24'),
(18, 2, 'max_staff', 'Maksimum Çalışan Sayısı', 'Maksimum Çalışan Sayısı özelliği', 10, 1, 3, '2025-08-04 00:38:24', '2025-08-09 00:56:24'),
(19, 2, 'max_products', 'Maksimum Ürün Sayısı', 'Maksimum Ürün Sayısı özelliği', 100, 1, 4, '2025-08-04 00:38:24', '2025-08-09 00:56:25'),
(20, 2, 'max_categories', 'Maksimum Kategori Sayısı', 'Maksimum Kategori Sayısı özelliği', 20, 1, 5, '2025-08-04 00:38:24', '2025-08-09 00:56:25'),
(21, 2, 'custom_domain', 'Özel Domain Desteği', 'Özel Domain Desteği özelliği', 0, 0, 6, '2025-08-04 00:38:24', '2025-08-09 00:56:25'),
(22, 2, 'analytics', 'Analitik Raporları', 'Analitik Raporları özelliği', 1, 1, 7, '2025-08-04 00:38:25', '2025-08-09 00:56:25'),
(23, 2, 'multi_language', 'Çoklu Dil Desteği', 'Çoklu Dil Desteği özelliği', 1, 1, 8, '2025-08-04 00:38:25', '2025-08-04 00:42:28'),
(24, 2, 'priority_support', 'Öncelikli Destek', 'Öncelikli Destek özelliği', 0, 0, 9, '2025-08-04 00:38:25', '2025-08-04 00:42:08'),
(25, 2, 'advanced_menu_editor', 'Gelişmiş Menü Editörü', 'Gelişmiş Menü Editörü özelliği', 1, 1, 10, '2025-08-04 00:38:25', '2025-08-04 00:42:28'),
(26, 2, 'order_management', 'Sipariş Yönetimi', 'Sipariş Yönetimi özelliği', 1, 1, 11, '2025-08-04 00:38:25', '2025-08-09 00:56:25'),
(27, 2, 'customer_reviews', 'Müşteri Değerlendirmeleri', 'Müşteri Değerlendirmeleri özelliği', 1, 1, 12, '2025-08-04 00:38:25', '2025-08-09 00:56:25'),
(28, 2, 'loyalty_program', 'Sadakat Programı', 'Sadakat Programı özelliği', 0, 0, 13, '2025-08-04 00:38:25', '2025-08-04 00:38:25'),
(29, 2, 'marketing_tools', 'Pazarlama Araçları', 'Pazarlama Araçları özelliği', 1, 1, 14, '2025-08-04 00:38:25', '2025-08-09 00:56:25'),
(30, 2, 'api_access', 'API Erişimi', 'API Erişimi özelliği', 0, 0, 15, '2025-08-04 00:38:25', '2025-08-04 00:38:25'),
(31, 3, 'max_restaurants', 'Maksimum Restoran Sayısı', 'Maksimum Restoran Sayısı özelliği', 4, 1, 1, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(32, 3, 'max_managers', 'Maksimum Müdür Hesabı', 'Maksimum Müdür Hesabı özelliği', 5, 1, 2, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(33, 3, 'max_staff', 'Maksimum Çalışan Sayısı', 'Maksimum Çalışan Sayısı özelliği', 30, 1, 3, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(34, 3, 'max_products', 'Maksimum Ürün Sayısı', 'Maksimum Ürün Sayısı özelliği', 200, 1, 4, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(35, 3, 'max_categories', 'Maksimum Kategori Sayısı', 'Maksimum Kategori Sayısı özelliği', 30, 1, 5, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(36, 3, 'custom_domain', 'Özel Domain Desteği', 'Özel Domain Desteği özelliği', 1, 1, 6, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(37, 3, 'analytics', 'Analitik Raporları', 'Analitik Raporları özelliği', 1, 1, 7, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(38, 3, 'multi_language', 'Çoklu Dil Desteği', 'Çoklu Dil Desteği özelliği', 1, 1, 8, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(39, 3, 'priority_support', 'Öncelikli Destek', 'Öncelikli Destek özelliği', 0, 0, 9, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(40, 3, 'advanced_menu_editor', 'Gelişmiş Menü Editörü', 'Gelişmiş Menü Editörü özelliği', 1, 1, 10, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(41, 3, 'order_management', 'Sipariş Yönetimi', 'Sipariş Yönetimi özelliği', 1, 1, 11, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(42, 3, 'customer_reviews', 'Müşteri Değerlendirmeleri', 'Müşteri Değerlendirmeleri özelliği', 1, 1, 12, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(43, 3, 'loyalty_program', 'Sadakat Programı', 'Sadakat Programı özelliği', 0, 0, 13, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(44, 3, 'marketing_tools', 'Pazarlama Araçları', 'Pazarlama Araçları özelliği', 1, 1, 14, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(45, 3, 'api_access', 'API Erişimi', 'API Erişimi özelliği', 0, 0, 15, '2025-08-09 00:57:43', '2025-08-09 00:57:43'),
(46, 4, 'max_restaurants', 'Maksimum Restoran Sayısı', 'Maksimum Restoran Sayısı özelliği', 0, 0, 1, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(47, 4, 'max_managers', 'Maksimum Müdür Hesabı', 'Maksimum Müdür Hesabı özelliği', 0, 0, 2, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(48, 4, 'max_staff', 'Maksimum Çalışan Sayısı', 'Maksimum Çalışan Sayısı özelliği', 0, 0, 3, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(49, 4, 'max_products', 'Maksimum Ürün Sayısı', 'Maksimum Ürün Sayısı özelliği', 0, 0, 4, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(50, 4, 'max_categories', 'Maksimum Kategori Sayısı', 'Maksimum Kategori Sayısı özelliği', 0, 0, 5, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(51, 4, 'custom_domain', 'Özel Domain Desteği', 'Özel Domain Desteği özelliği', 1, 1, 6, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(52, 4, 'analytics', 'Analitik Raporları', 'Analitik Raporları özelliği', 1, 1, 7, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(53, 4, 'multi_language', 'Çoklu Dil Desteği', 'Çoklu Dil Desteği özelliği', 1, 1, 8, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(54, 4, 'priority_support', 'Öncelikli Destek', 'Öncelikli Destek özelliği', 1, 1, 9, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(55, 4, 'advanced_menu_editor', 'Gelişmiş Menü Editörü', 'Gelişmiş Menü Editörü özelliği', 1, 1, 10, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(56, 4, 'order_management', 'Sipariş Yönetimi', 'Sipariş Yönetimi özelliği', 1, 1, 11, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(57, 4, 'customer_reviews', 'Müşteri Değerlendirmeleri', 'Müşteri Değerlendirmeleri özelliği', 1, 1, 12, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(58, 4, 'loyalty_program', 'Sadakat Programı', 'Sadakat Programı özelliği', 1, 1, 13, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(59, 4, 'marketing_tools', 'Pazarlama Araçları', 'Pazarlama Araçları özelliği', 1, 1, 14, '2025-08-09 00:58:21', '2025-08-09 00:58:21'),
(60, 4, 'api_access', 'API Erişimi', 'API Erişimi özelliği', 1, 1, 15, '2025-08-09 00:58:21', '2025-08-09 00:58:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(191) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 100,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `restaurant_id`, `category_id`, `name`, `description`, `price`, `image`, `stock`, `is_available`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Köfte', 'Rize yöresinden özenle seçilmiş birinci kalite çay yaprakları ile demlenen geleneksel Türk çayımız. Çayımız özel cam demlikte, geleneksel yöntemlerle demlenir ve sürekli taze tutulur. İnce belli bardaklarımızda, şeker ile birlikte servis edilir. Türk misafirperverliğinin en güzel sembollerinden biri olan çayımız, yemek sonrası mükemmel bir seçimdir.', 45.00, 'products/1753683245_product_6887152dd6f80.png', 90, 1, 0, '2025-07-22 14:53:35', '2025-07-28 11:37:29'),
(2, 1, 1, 'Tavuk Şiş', 'Özel baharat karışımımızla marine edilmiş tavuk göğsü etinden hazırlanan şişlerimiz, açık ateşte usta ellerle pişirilir. Marine işlemi minimum 24 saat sürer ve böylece etin tam kıvamını alması sağlanır. Yanında tereyağlı pilav, közlenmiş sebzeler ve taze mevsim salatası ile servis edilir. Tavuklarımız çiftlik tavuğu olup, doğal beslenmiş tavuklardan seçilir.', 55.00, NULL, 100, 1, 2, '2025-07-22 14:53:35', '2025-07-22 14:53:35'),
(3, 1, 1, 'Karışık Izgara', 'Restoran şefimizin özel karışık ızgara tabağı; dana köfte, tavuk şiş ve kuzu şiş üçlüsünden oluşur. Her bir et çeşidi kendine özgü baharat karışımları ile marine edilir ve açık ateşte mükemmel pişirme tekniği ile hazırlanır. Yanında bulgur pilavı, közlenmiş domates ve biber, taze soğan, turşu çeşitleri ve özel acı ezme ile zengin bir sunum yapılır. 2-3 kişi için idealdir.', 85.00, NULL, 100, 1, 3, '2025-07-22 14:53:35', '2025-07-22 14:53:35'),
(4, 1, 2, 'Çay', 'Rize yöresinden özenle seçilmiş birinci kalite çay yaprakları ile demlenen geleneksel Türk çayımız. Çayımız özel cam demlikte, geleneksel yöntemlerle demlenir ve sürekli taze tutulur. İnce belli bardaklarımızda, şeker ile birlikte servis edilir. Türk misafirperverliğinin en güzel sembollerinden biri olan çayımız, yemek sonrası mükemmel bir seçimdir.', 5.00, NULL, 81, 1, 1, '2025-07-22 14:53:35', '2025-07-23 16:57:02'),
(5, 1, 2, 'Ayran', 'Taze yoğurt ve saf su ile hazırlanan ev yapımı ayranımız, hiçbir koruyucu madde içermez. Geleneksel yöntemlerle çırpılan ayranımız, yemeklerinizin yanında mükemmel bir serinlik sağlar.', 8.00, NULL, 95, 1, 2, '2025-07-22 14:53:35', '2025-07-23 16:55:31'),
(6, 2, 5, 'Margherita Pizza', 'Klasik Margherita pizza - mozarella, domates sosu, fesleğen', 35.00, NULL, 100, 1, 1, '2025-07-22 14:53:35', '2025-07-22 14:53:35'),
(7, 2, 5, 'Pepperoni Pizza', 'Pepperoni, mozarella, domates sosu', 42.00, NULL, 100, 1, 2, '2025-07-22 14:53:36', '2025-07-22 14:53:36'),
(8, 1, 2, 'Bira', 'alman birasi', 100.00, NULL, 100, 0, 0, '2025-07-23 09:14:31', '2025-07-27 21:16:43');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `table_count` int(11) NOT NULL DEFAULT 10,
  `working_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`working_hours`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `translation_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `default_language` varchar(10) NOT NULL DEFAULT 'tr',
  `supported_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`supported_languages`)),
  `custom_domain` varchar(191) DEFAULT NULL,
  `primary_color` varchar(7) NOT NULL DEFAULT '#f19c01',
  `secondary_color` varchar(7) NOT NULL DEFAULT '#212121',
  `subdomain` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `instagram` varchar(191) DEFAULT NULL,
  `whatsapp` varchar(191) DEFAULT NULL,
  `twitter` varchar(191) DEFAULT NULL,
  `youtube` varchar(191) DEFAULT NULL,
  `linkedin` varchar(191) DEFAULT NULL,
  `working_hours_text` text DEFAULT NULL,
  `facebook` varchar(191) DEFAULT NULL,
  `color_primary` varchar(10) DEFAULT NULL,
  `color_secondary` varchar(10) DEFAULT NULL,
  `color_cart` varchar(10) DEFAULT NULL,
  `wifi_password` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `restaurants`
--

INSERT INTO `restaurants` (`id`, `business_id`, `restaurant_manager_id`, `name`, `slug`, `description`, `logo`, `phone`, `email`, `website`, `address`, `table_count`, `working_hours`, `is_active`, `translation_enabled`, `default_language`, `supported_languages`, `custom_domain`, `primary_color`, `secondary_color`, `subdomain`, `created_at`, `updated_at`, `instagram`, `whatsapp`, `twitter`, `youtube`, `linkedin`, `working_hours_text`, `facebook`, `color_primary`, `color_secondary`, `color_cart`, `wifi_password`) VALUES
(1, 1, 3, 'Test Restaurant', 'test-restaurant', 'Bu bir test restoranıdır. Lezzetli yemekler ve kaliteli hizmet.', 'restaurants/zaMPPgaBRqTMflqLO7xxtGF2lo04Hm7iREqlt5m2.jpg', '+90 555 987 6543', 'info@restaurant.com', 'https://restaurant.com', 'Restoran Mahallesi, Lezzet Caddesi No:5, İstanbul', 20, '{\"monday\":{\"open\":\"09:00\",\"close\":\"23:00\"},\"tuesday\":{\"open\":\"09:00\",\"close\":\"23:00\"},\"wednesday\":{\"open\":\"09:00\",\"close\":\"23:00\"},\"thursday\":{\"open\":\"09:00\",\"close\":\"23:00\"},\"friday\":{\"open\":\"09:00\",\"close\":\"24:00\"},\"saturday\":{\"open\":\"09:00\",\"close\":\"24:00\"},\"sunday\":{\"open\":\"10:00\",\"close\":\"22:00\"}}', 1, 0, 'tr', '[\"tr\",\"en\"]', NULL, '#f19c01', '#212121', NULL, '2025-07-22 14:53:34', '2025-07-28 22:37:42', 'https://instagram.com/restaurant', '+905551234567', 'https://twitter.com/restaurant', 'https://youtube.com/restaurant', 'https://linkedin.com/company/restaurant', NULL, 'https://facebook.com/restaurant', '#ffd600', '#000000', '#ffd600', '12345'),
(2, 1, NULL, 'Pizza House', 'pizza-house', 'En lezzetli pizzalar burada!', NULL, '+90 555 111 2233', NULL, NULL, 'Pizza Mahallesi, İtalyan Caddesi No:10, İstanbul', 15, NULL, 1, 0, 'tr', NULL, NULL, '#f19c01', '#212121', NULL, '2025-07-22 14:53:35', '2025-07-22 14:53:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 1, 3, 'yeni restoran', 'yeni-restoran', 'adrestest1', NULL, '55555555', NULL, NULL, 'adres test', 10, NULL, 1, 0, 'tr', NULL, NULL, '#f19c01', '#212121', NULL, '2025-07-22 16:20:33', '2025-07-22 16:20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `restaurant_order_settings`
--

CREATE TABLE `restaurant_order_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `enabled_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`enabled_categories`)),
  `ordering_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `restaurant_order_settings`
--

INSERT INTO `restaurant_order_settings` (`id`, `restaurant_id`, `enabled_categories`, `ordering_enabled`, `created_at`, `updated_at`) VALUES
(1, 1, '[2]', 1, '2025-07-23 10:38:44', '2025-07-23 11:36:59');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `restaurant_staff`
--

CREATE TABLE `restaurant_staff` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('restaurant_manager','cashier','waiter','kitchen') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `restaurant_staff`
--

INSERT INTO `restaurant_staff` (`id`, `restaurant_id`, `user_id`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'restaurant_manager', 1, '2025-07-22 14:53:35', '2025-07-22 14:53:35'),
(2, 1, 4, 'waiter', 1, '2025-07-22 14:53:35', '2025-07-28 21:13:39'),
(3, 1, 5, 'waiter', 1, '2025-07-22 14:53:35', '2025-07-28 21:13:40'),
(4, 1, 6, 'kitchen', 1, '2025-07-22 14:53:35', '2025-07-28 21:13:40'),
(5, 1, 7, 'cashier', 1, '2025-07-22 14:53:35', '2025-07-28 21:13:40'),
(6, 3, 3, 'restaurant_manager', 1, '2025-07-22 16:20:33', '2025-07-22 16:20:33'),
(7, 1, 8, 'waiter', 1, '2025-07-22 16:33:51', '2025-07-28 21:13:40');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `customer_email` varchar(191) DEFAULT NULL,
  `rating` int(11) NOT NULL COMMENT '1-5 arası puan',
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('qU73dld0GT5UddfftEXyaQjP2WfNoKdSMYCPcooq', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Trae/1.100.3 Chrome/132.0.6834.210 Electron/34.5.1 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZ0dDQ1ZRMVFpNlV2eGtQY3FLSmprYU91aE5KRFRaa3lWYkFVWDdraSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYnVzaW5lc3MvcGFja2FnZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1754868012),
('Fh99i2a33HxAebuepeyGR6qZsyWpNs9AkkzACgFX', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZHNvNkVQTFNzRGVDSDB0NERQbGd5ZzZjNkhqTVYzb0tEM3dQdzdURiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wYWNrYWdlcy80L2VkaXQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1754868752);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(20) NOT NULL,
  `qr_code` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `capacity` int(11) DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `tables`
--

INSERT INTO `tables` (`id`, `restaurant_id`, `table_number`, `qr_code`, `is_active`, `capacity`, `location`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'masa1', NULL, 1, 4, 'mekan', 'not', '2025-07-23 10:09:30', '2025-07-23 10:09:30'),
(2, 1, 'masa2', NULL, 1, 8, 'bahçe', NULL, '2025-07-23 10:09:53', '2025-07-23 10:09:53'),
(4, 1, 'masa3', NULL, 1, 6, '2 kat', 'test', '2025-07-23 12:16:09', '2025-07-23 12:16:09');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('admin','business_owner','restaurant_manager','cashier','waiter','kitchen') NOT NULL DEFAULT 'business_owner',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@qrmenu.com', NULL, '$2y$12$qD99TxhFh2Bt2d0JugPoyemYZnJMw16gR5DVrso/H9gkp7mK5qq5a', 'admin', NULL, '2025-07-22 14:53:33', '2025-07-22 14:53:33'),
(2, 'İşletme Sahibi', 'isletme@qrmenu.com', NULL, '$2y$12$3x7Q0wguDsJpyQNo82iKxearSHPX6SHdoSSYIL5pDADQ8oLCScIJq', 'business_owner', NULL, '2025-07-22 14:53:33', '2025-07-22 14:53:33'),
(3, 'Restoran Müdürü', 'mudur@restaurant.com', NULL, '$2y$12$ykMBMsQkk/og1/g0AlgJPeAV8dDYl4cLzBHd.w6pjNBNlcqBMopV2', 'restaurant_manager', NULL, '2025-07-22 14:53:33', '2025-07-22 14:53:33'),
(4, 'Garson Ali', 'garson1@restaurant.com', NULL, '$2y$12$xG69FdpEwbtWk8kaqzBB7ubKKr3.C0m7/NI/dMidrJvinD/U/FJZe', 'waiter', NULL, '2025-07-22 14:53:34', '2025-07-22 14:53:34'),
(5, 'Garson Ayşe', 'garson2@restaurant.com', NULL, '$2y$12$ZH4NHXD1cT5Ve0YlKzqNte50RZ3VQGLFpCgZJF5Fh3A5edBm/Cs06', 'waiter', NULL, '2025-07-22 14:53:34', '2025-07-22 14:53:34'),
(6, 'Aşçı Mehmet', 'asci1@restaurant.com', NULL, '$2y$12$W4uH74pBSYRVKzmMnFtaBeWhudkEv2zATM8qA9e6/oJ.eFGQRSgVq', 'kitchen', NULL, '2025-07-22 14:53:34', '2025-07-22 14:53:34'),
(7, 'Kasiyer Fatma', 'kasiyer1@restaurant.com', NULL, '$2y$12$OWiOFeotxSDTVEdxgv4CO.jlTkBoyuYoac45vHwi52btr3b.01vOK', 'cashier', NULL, '2025-07-22 14:53:34', '2025-07-22 14:53:34'),
(8, 'garson 3', 'garson3@restaurant.com', NULL, '$2y$12$YC6yz8AnaKy7PHiIDpjU/OmdAErJX.1LIubdvYJ1ntCUqVF2o0bRG', 'waiter', NULL, '2025-07-22 16:33:51', '2025-07-22 16:33:51');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `waiters`
--

CREATE TABLE `waiters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `businesses_slug_unique` (`slug`),
  ADD KEY `businesses_owner_id_foreign` (`owner_id`);

--
-- Tablo için indeksler `business_subscriptions`
--
ALTER TABLE `business_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_subscriptions_business_id_foreign` (`business_id`),
  ADD KEY `business_subscriptions_package_id_foreign` (`package_id`);

--
-- Tablo için indeksler `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Tablo için indeksler `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_restaurant_id_foreign` (`restaurant_id`);

--
-- Tablo için indeksler `domain_mappings`
--
ALTER TABLE `domain_mappings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain_mappings_domain_unique` (`domain`),
  ADD KEY `domain_mappings_restaurant_id_foreign` (`restaurant_id`);

--
-- Tablo için indeksler `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Tablo için indeksler `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Tablo için indeksler `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kitchen_views`
--
ALTER TABLE `kitchen_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kitchen_views_order_id_foreign` (`order_id`);

--
-- Tablo için indeksler `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `orders_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Tablo için indeksler `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Tablo için indeksler `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `packages_slug_unique` (`slug`);

--
-- Tablo için indeksler `package_features`
--
ALTER TABLE `package_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_features_package_id_feature_key_unique` (`package_id`,`feature_key`);

--
-- Tablo için indeksler `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_restaurant_id_foreign` (`restaurant_id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Tablo için indeksler `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurants_slug_unique` (`slug`),
  ADD KEY `restaurants_business_id_foreign` (`business_id`),
  ADD KEY `restaurants_restaurant_manager_id_foreign` (`restaurant_manager_id`);

--
-- Tablo için indeksler `restaurant_order_settings`
--
ALTER TABLE `restaurant_order_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_order_settings_restaurant_id_foreign` (`restaurant_id`);

--
-- Tablo için indeksler `restaurant_staff`
--
ALTER TABLE `restaurant_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_staff_restaurant_id_user_id_unique` (`restaurant_id`,`user_id`),
  ADD KEY `restaurant_staff_user_id_foreign` (`user_id`);

--
-- Tablo için indeksler `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_restaurant_id_is_approved_index` (`restaurant_id`,`is_approved`);

--
-- Tablo için indeksler `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Tablo için indeksler `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_restaurant_id_table_number_unique` (`restaurant_id`,`table_number`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Tablo için indeksler `waiters`
--
ALTER TABLE `waiters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `waiters_restaurant_id_foreign` (`restaurant_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `business_subscriptions`
--
ALTER TABLE `business_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `domain_mappings`
--
ALTER TABLE `domain_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kitchen_views`
--
ALTER TABLE `kitchen_views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Tablo için AUTO_INCREMENT değeri `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Tablo için AUTO_INCREMENT değeri `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `package_features`
--
ALTER TABLE `package_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `restaurant_order_settings`
--
ALTER TABLE `restaurant_order_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `restaurant_staff`
--
ALTER TABLE `restaurant_staff`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `waiters`
--
ALTER TABLE `waiters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
