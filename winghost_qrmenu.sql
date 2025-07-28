-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 27 Tem 2025, 20:03:02
-- Sunucu sürümü: 10.5.26-MariaDB-cll-lve
-- PHP Sürümü: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `winghost_qrmenu`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `addons`
--

CREATE TABLE `addons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`files`)),
  `item_id` varchar(255) NOT NULL,
  `license_code` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `apartment` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `analytics`
--

CREATE TABLE `analytics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `analytic_sections`
--

CREATE TABLE `analytic_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `analytic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `section` tinyint(4) NOT NULL DEFAULT 5,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip_code` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `branches`
--

INSERT INTO `branches` (`id`, `name`, `email`, `phone`, `latitude`, `longitude`, `city`, `state`, `zip_code`, `address`, `status`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'Mirpur-1 (main)', 'mirpur@inilabs.xyz', '+536464646464', '23.8042375', '90.3525979', 'Mirpur-1', 'Dhaka', '1216', 'House : 25, Road No: 2, Block A, Mirpur-1, Dhaka 1216', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(2, 'La Puerta Izmir', NULL, NULL, NULL, NULL, 'izmir', 'cigili', '35620', 'bostanlı', 5, NULL, NULL, NULL, NULL, '2025-01-20 17:54:57', '2025-01-20 19:08:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `capture_payment_notifications`
--

CREATE TABLE `capture_payment_notifications` (
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `is_cryptocurrency` tinyint(3) UNSIGNED NOT NULL,
  `exchange_rate` decimal(13,2) DEFAULT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `symbol`, `code`, `is_cryptocurrency`, `exchange_rate`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'Dollars', '$', 'USD', 10, 1.00, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'TL', '₺', 'TRY', 10, 0.00, NULL, NULL, NULL, NULL, '2025-01-20 12:44:52', '2025-01-20 12:45:03');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `default_access`
--

CREATE TABLE `default_access` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `default_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `default_access`
--

INSERT INTO `default_access` (`id`, `name`, `user_id`, `default_id`, `created_at`, `updated_at`) VALUES
(1, 'branch_id', 1, 2, '2025-01-20 12:04:28', '2025-01-20 19:11:48'),
(2, 'branch_id', 4, 1, '2025-01-20 19:10:00', '2025-01-20 19:10:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `dining_tables`
--

CREATE TABLE `dining_tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `dining_tables`
--

INSERT INTO `dining_tables` (`id`, `name`, `slug`, `size`, `qr_code`, `branch_id`, `status`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'masa 1', 'mirpur-1-main-masa-1', 10, 'storage/qr_codes/5w4L0us6wB.png', 1, 5, NULL, NULL, NULL, NULL, '2025-01-20 13:02:57', '2025-01-20 13:02:57'),
(2, 'masa2', 'mirpur-1-main-masa2', 2, 'storage/qr_codes/SGAX1BrOIQ.png', 1, 5, NULL, NULL, NULL, NULL, '2025-01-20 17:56:07', '2025-01-20 17:56:07');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `gateway_options`
--

CREATE TABLE `gateway_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_id` bigint(20) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `option` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` tinyint(4) NOT NULL,
  `activities` longtext DEFAULT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `gateway_options`
--

INSERT INTO `gateway_options` (`id`, `model_id`, `model_type`, `option`, `value`, `type`, `activities`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 3, 'App\\Models\\PaymentGateway', 'paypal_app_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(2, 3, 'App\\Models\\PaymentGateway', 'paypal_client_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(3, 3, 'App\\Models\\PaymentGateway', 'paypal_client_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(4, 3, 'App\\Models\\PaymentGateway', 'paypal_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(5, 3, 'App\\Models\\PaymentGateway', 'paypal_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(6, 4, 'App\\Models\\PaymentGateway', 'stripe_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(7, 4, 'App\\Models\\PaymentGateway', 'stripe_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(8, 4, 'App\\Models\\PaymentGateway', 'stripe_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(9, 4, 'App\\Models\\PaymentGateway', 'stripe_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(10, 5, 'App\\Models\\PaymentGateway', 'flutterwave_public_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(11, 5, 'App\\Models\\PaymentGateway', 'flutterwave_secret_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(12, 5, 'App\\Models\\PaymentGateway', 'flutterwave_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(13, 5, 'App\\Models\\PaymentGateway', 'flutterwave_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(14, 6, 'App\\Models\\PaymentGateway', 'paystack_public_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(15, 6, 'App\\Models\\PaymentGateway', 'paystack_secret_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(16, 6, 'App\\Models\\PaymentGateway', 'paystack_payment_url', 'https://api.paystack.co', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(17, 6, 'App\\Models\\PaymentGateway', 'paystack_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(18, 6, 'App\\Models\\PaymentGateway', 'paystack_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(19, 7, 'App\\Models\\PaymentGateway', 'sslcommerz_store_name', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(20, 7, 'App\\Models\\PaymentGateway', 'sslcommerz_store_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(21, 7, 'App\\Models\\PaymentGateway', 'sslcommerz_store_password', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(22, 7, 'App\\Models\\PaymentGateway', 'sslcommerz_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(23, 7, 'App\\Models\\PaymentGateway', 'sslcommerz_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(24, 8, 'App\\Models\\PaymentGateway', 'mollie_api_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(25, 8, 'App\\Models\\PaymentGateway', 'mollie_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(26, 8, 'App\\Models\\PaymentGateway', 'mollie_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(27, 9, 'App\\Models\\PaymentGateway', 'senangpay_merchant_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(28, 9, 'App\\Models\\PaymentGateway', 'senangpay_secret_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(29, 9, 'App\\Models\\PaymentGateway', 'senangpay_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(30, 9, 'App\\Models\\PaymentGateway', 'senangpay_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(31, 10, 'App\\Models\\PaymentGateway', 'bkash_app_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(32, 10, 'App\\Models\\PaymentGateway', 'bkash_app_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(33, 10, 'App\\Models\\PaymentGateway', 'bkash_username', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(34, 10, 'App\\Models\\PaymentGateway', 'bkash_password', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(35, 10, 'App\\Models\\PaymentGateway', 'bkash_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(36, 10, 'App\\Models\\PaymentGateway', 'bkash_status', '', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(37, 11, 'App\\Models\\PaymentGateway', 'paytm_merchant_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(38, 11, 'App\\Models\\PaymentGateway', 'paytm_merchant_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(39, 11, 'App\\Models\\PaymentGateway', 'paytm_merchant_website', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(40, 11, 'App\\Models\\PaymentGateway', 'paytm_channel', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(41, 11, 'App\\Models\\PaymentGateway', 'paytm_industry_type', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(42, 11, 'App\\Models\\PaymentGateway', 'paytm_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(43, 11, 'App\\Models\\PaymentGateway', 'paytm_status', '', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(44, 12, 'App\\Models\\PaymentGateway', 'razorpay_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(45, 12, 'App\\Models\\PaymentGateway', 'razorpay_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(46, 12, 'App\\Models\\PaymentGateway', 'razorpay_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(47, 12, 'App\\Models\\PaymentGateway', 'razorpay_status', '', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(48, 13, 'App\\Models\\PaymentGateway', 'mercadopago_client_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(49, 13, 'App\\Models\\PaymentGateway', 'mercadopago_client_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(50, 13, 'App\\Models\\PaymentGateway', 'mercadopago_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(51, 13, 'App\\Models\\PaymentGateway', 'mercadopago_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(52, 14, 'App\\Models\\PaymentGateway', 'cashfree_app_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(53, 14, 'App\\Models\\PaymentGateway', 'cashfree_secret_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(54, 14, 'App\\Models\\PaymentGateway', 'cashfree_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(55, 14, 'App\\Models\\PaymentGateway', 'cashfree_status', '', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(56, 15, 'App\\Models\\PaymentGateway', 'payfast_merchant_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(57, 15, 'App\\Models\\PaymentGateway', 'payfast_merchant_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(58, 15, 'App\\Models\\PaymentGateway', 'payfast_passphrase', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(59, 15, 'App\\Models\\PaymentGateway', 'payfast_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(60, 15, 'App\\Models\\PaymentGateway', 'payfast_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(61, 16, 'App\\Models\\PaymentGateway', 'skrill_merchant_email', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(62, 16, 'App\\Models\\PaymentGateway', 'skrill_merchant_api_password', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(63, 16, 'App\\Models\\PaymentGateway', 'skrill_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(64, 16, 'App\\Models\\PaymentGateway', 'skrill_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(65, 17, 'App\\Models\\PaymentGateway', 'phonepe_merchant_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(66, 17, 'App\\Models\\PaymentGateway', 'phonepe_merchant_user_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(67, 17, 'App\\Models\\PaymentGateway', 'phonepe_key_index', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(68, 17, 'App\\Models\\PaymentGateway', 'phonepe_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(69, 17, 'App\\Models\\PaymentGateway', 'phonepe_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(70, 17, 'App\\Models\\PaymentGateway', 'phonepe_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(71, 18, 'App\\Models\\PaymentGateway', 'telr_store_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(72, 18, 'App\\Models\\PaymentGateway', 'telr_store_auth_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(73, 18, 'App\\Models\\PaymentGateway', 'telr_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(74, 18, 'App\\Models\\PaymentGateway', 'telr_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(75, 19, 'App\\Models\\PaymentGateway', 'iyzico_api_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(76, 19, 'App\\Models\\PaymentGateway', 'iyzico_secret_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(77, 19, 'App\\Models\\PaymentGateway', 'iyzico_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(78, 19, 'App\\Models\\PaymentGateway', 'iyzico_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(79, 20, 'App\\Models\\PaymentGateway', 'pesapal_consumer_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(80, 20, 'App\\Models\\PaymentGateway', 'pesapal_consumer_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(81, 20, 'App\\Models\\PaymentGateway', 'pesapal_ipn_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(82, 20, 'App\\Models\\PaymentGateway', 'pesapal_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(83, 20, 'App\\Models\\PaymentGateway', 'pesapal_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(84, 21, 'App\\Models\\PaymentGateway', 'midtrans_server_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(85, 21, 'App\\Models\\PaymentGateway', 'midtrans_mode', '', 10, '{\"5\":\"sandbox\",\"10\":\"live\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(86, 21, 'App\\Models\\PaymentGateway', 'midtrans_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(87, 1, 'App\\Models\\SmsGateway', 'twilio_account_sid', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(88, 1, 'App\\Models\\SmsGateway', 'twilio_auth_token', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(89, 1, 'App\\Models\\SmsGateway', 'twilio_from', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(90, 1, 'App\\Models\\SmsGateway', 'twilio_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(91, 2, 'App\\Models\\SmsGateway', 'clickatell_apikey', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(92, 2, 'App\\Models\\SmsGateway', 'clickatell_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(93, 3, 'App\\Models\\SmsGateway', 'nexmo_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(94, 3, 'App\\Models\\SmsGateway', 'nexmo_secret', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(95, 3, 'App\\Models\\SmsGateway', 'nexmo_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(96, 4, 'App\\Models\\SmsGateway', 'msg91_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(97, 4, 'App\\Models\\SmsGateway', 'msg91_sender_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(98, 4, 'App\\Models\\SmsGateway', 'msg91_template_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(99, 4, 'App\\Models\\SmsGateway', 'msg91_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(100, 5, 'App\\Models\\SmsGateway', 'twofactor_module', 'PROMO_SMS', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(101, 5, 'App\\Models\\SmsGateway', 'twofactor_from', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(102, 5, 'App\\Models\\SmsGateway', 'twofactor_api_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(103, 5, 'App\\Models\\SmsGateway', 'twofactor_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(104, 6, 'App\\Models\\SmsGateway', 'bulksms_username', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(105, 6, 'App\\Models\\SmsGateway', 'bulksms_password', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(106, 6, 'App\\Models\\SmsGateway', 'bulksms_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(107, 7, 'App\\Models\\SmsGateway', 'bulksmsbd_api_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(108, 7, 'App\\Models\\SmsGateway', 'bulksmsbd_sender_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(109, 7, 'App\\Models\\SmsGateway', 'bulksmsbd_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(110, 8, 'App\\Models\\SmsGateway', 'telesign_api_key', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(111, 8, 'App\\Models\\SmsGateway', 'telesign_customer_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(112, 8, 'App\\Models\\SmsGateway', 'telesign_sender_id', '', 5, '\"\"', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(113, 8, 'App\\Models\\SmsGateway', 'telesign_status', '10', 10, '{\"5\":\"enable\",\"10\":\"disable\"}', NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_category_id` bigint(20) UNSIGNED NOT NULL,
  `tax_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `caution` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `price` decimal(13,6) NOT NULL DEFAULT 0.000000,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `item_type` tinyint(4) NOT NULL DEFAULT 5,
  `order` bigint(20) NOT NULL DEFAULT 1,
  `is_featured` tinyint(4) NOT NULL DEFAULT 5,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `items`
--

INSERT INTO `items` (`id`, `item_category_id`, `tax_id`, `name`, `slug`, `caution`, `description`, `price`, `status`, `item_type`, `order`, `is_featured`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'burger', 'burger', NULL, NULL, 100.000000, 5, 5, 1, 5, NULL, NULL, NULL, NULL, NULL, '2025-01-20 15:42:45', '2025-01-20 15:42:45'),
(2, 1, NULL, 'burger1', 'burger1', NULL, NULL, 25.000000, 5, 5, 1, 5, NULL, NULL, NULL, NULL, NULL, '2025-01-20 15:51:44', '2025-01-20 15:51:44');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `item_addons`
--

CREATE TABLE `item_addons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `addon_item_id` bigint(20) UNSIGNED NOT NULL,
  `addon_item_variation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addon_item_variation`)),
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `item_attributes`
--

CREATE TABLE `item_attributes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `item_categories`
--

CREATE TABLE `item_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `sort` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `item_categories`
--

INSERT INTO `item_categories` (`id`, `name`, `slug`, `description`, `status`, `sort`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'burger', 'burger', NULL, 5, 1, NULL, NULL, NULL, NULL, '2025-01-20 13:04:24', '2025-01-20 13:04:24');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `item_extras`
--

CREATE TABLE `item_extras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(13,6) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `item_variations`
--

CREATE TABLE `item_variations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `item_attribute_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(13,6) NOT NULL DEFAULT 0.000000,
  `caution` longtext DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `display_mode` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `display_mode`, `status`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 5, 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(3, 'German', 'de', 5, 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(4, 'Arabic', 'ar', 10, 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(7, 'Türkçe', 'tr', 5, 5, NULL, NULL, NULL, NULL, '2025-01-20 12:43:25', '2025-01-20 12:43:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `media`
--

INSERT INTO `media` (`id`, `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\PaymentGateway', 1, 'e64231e9-e0aa-4836-a63b-2767a72779b6', 'payment-gateway', 'cash-on-delivery', 'cash-on-delivery.png', 'image/png', 'public', 'public', 3437, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:08', '2025-01-20 12:04:08'),
(2, 'App\\Models\\PaymentGateway', 2, 'f58d64ea-f3ab-4bd9-8cfb-905ce4117de3', 'payment-gateway', 'credit', 'credit.png', 'image/png', 'public', 'public', 3885, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(3, 'App\\Models\\PaymentGateway', 3, '4615981e-1ef9-47b5-9470-1de22e2b1e20', 'payment-gateway', 'paypal', 'paypal.png', 'image/png', 'public', 'public', 3809, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(4, 'App\\Models\\PaymentGateway', 4, 'e1de68c9-013a-4bb0-aadd-1a1ac3fd6e7d', 'payment-gateway', 'stripe', 'stripe.png', 'image/png', 'public', 'public', 3635, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(5, 'App\\Models\\PaymentGateway', 5, 'e0902116-0814-45f6-a54d-13963771e543', 'payment-gateway', 'flutterwave', 'flutterwave.png', 'image/png', 'public', 'public', 5191, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(6, 'App\\Models\\PaymentGateway', 6, '79f51c23-7e95-4938-a1d6-cc8733b73d49', 'payment-gateway', 'paystack', 'paystack.png', 'image/png', 'public', 'public', 4195, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(7, 'App\\Models\\PaymentGateway', 7, 'b11fcbd8-a920-4a77-9cc7-90762070c4e0', 'payment-gateway', 'sslcommerz', 'sslcommerz.png', 'image/png', 'public', 'public', 4546, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(8, 'App\\Models\\PaymentGateway', 8, 'cf0c895d-e4a3-4800-8b10-9921851e2283', 'payment-gateway', 'mollie', 'mollie.png', 'image/png', 'public', 'public', 8116, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(9, 'App\\Models\\PaymentGateway', 9, 'ab64b2d1-aa98-4c28-bfb3-1360515efd6b', 'payment-gateway', 'senangpay', 'senangpay.png', 'image/png', 'public', 'public', 6541, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(10, 'App\\Models\\PaymentGateway', 10, '1c96427a-8244-49e7-aa23-633aa7d21d59', 'payment-gateway', 'bkash', 'bkash.png', 'image/png', 'public', 'public', 5282, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(11, 'App\\Models\\PaymentGateway', 11, '4916e8ef-58c6-4e17-abd9-33b62dcd2d9e', 'payment-gateway', 'paytm', 'paytm.png', 'image/png', 'public', 'public', 3285, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(12, 'App\\Models\\PaymentGateway', 12, 'c1ccc09b-26b8-490e-88e5-4352fe7954af', 'payment-gateway', 'razorpay', 'razorpay.png', 'image/png', 'public', 'public', 4847, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(13, 'App\\Models\\PaymentGateway', 13, '108b68fb-86d5-48a7-8e57-871ee2946418', 'payment-gateway', 'mercadopago', 'mercadopago.png', 'image/png', 'public', 'public', 11423, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(14, 'App\\Models\\PaymentGateway', 14, '4c24e4f5-9101-4bd4-9f9f-e169e56a8a5a', 'payment-gateway', 'cashfree', 'cashfree.png', 'image/png', 'public', 'public', 4940, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(15, 'App\\Models\\PaymentGateway', 15, '285297fe-77da-4301-adb4-df83f3f837a3', 'payment-gateway', 'payfast', 'payfast.png', 'image/png', 'public', 'public', 2173, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(16, 'App\\Models\\PaymentGateway', 16, '53d04ed9-02f0-4710-b197-d3e4228e7754', 'payment-gateway', 'skrill', 'skrill.png', 'image/png', 'public', 'public', 7074, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(17, 'App\\Models\\PaymentGateway', 17, '8e4abe2b-35af-4074-b812-b8d891628fe4', 'payment-gateway', 'phonepe', 'phonepe.png', 'image/png', 'public', 'public', 4417, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(18, 'App\\Models\\PaymentGateway', 18, '12d0132f-bbc1-4c70-a0a7-322a9b3640bf', 'payment-gateway', 'telr', 'telr.png', 'image/png', 'public', 'public', 7594, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(19, 'App\\Models\\PaymentGateway', 19, '530e5dde-7869-43b6-913a-48c45a3678c8', 'payment-gateway', 'iyzico', 'iyzico.png', 'image/png', 'public', 'public', 7652, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(20, 'App\\Models\\PaymentGateway', 20, '4d7fac16-92a9-4943-b98d-ba4a8ac62722', 'payment-gateway', 'pesapal', 'pesapal.png', 'image/png', 'public', 'public', 9373, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(21, 'App\\Models\\PaymentGateway', 21, 'b8ceea09-7bd5-4a7d-b522-40e0bf0b06ff', 'payment-gateway', 'midtrans', 'midtrans.png', 'image/png', 'public', 'public', 5877, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(22, 'App\\Models\\Language', 1, '0f418ca8-6981-44a3-b53f-7e2b4874e932', 'language', 'english', 'english.png', 'image/png', 'public', 'public', 1149, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(24, 'App\\Models\\Language', 3, 'f6fd5d49-cfb7-409f-912d-7337691f6b86', 'language', 'german', 'german.png', 'image/png', 'public', 'public', 1835, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(25, 'App\\Models\\Language', 4, '5e5f92c7-9ad3-4ff2-80c2-55a07336b55f', 'language', 'arabic', 'arabic.png', 'image/png', 'public', 'public', 4388, '[]', '[]', '[]', '[]', 1, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(30, 'App\\Models\\NotificationSetting', 37, '1d7173bc-5e19-4841-b6bf-094721617a42', 'notification-file', 'qrmenu-d6e96-firebase-adminsdk-fbsvc-766c9329bd', 'service-account-file.json', 'application/json', 'public', 'public', 2376, '[]', '[]', '[]', '[]', 1, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(32, 'App\\Models\\User', 1, 'a4668b92-56dd-4a70-8cdf-748c1111da73', 'profile', '67124506', '67124506.jpg', 'image/jpeg', 'public', 'public', 27928, '[]', '[]', '[]', '[]', 1, '2025-01-21 01:37:20', '2025-01-21 01:37:20'),
(33, 'App\\Models\\Language', 7, '593fe074-4c66-4467-9f78-43b08636e3cb', 'language', 'indir', 'indir.png', 'image/png', 'public', 'public', 571, '[]', '[]', '[]', '[]', 1, '2025-01-21 01:43:08', '2025-01-21 01:43:08');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `parent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `priority` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `menus`
--

INSERT INTO `menus` (`id`, `name`, `language`, `url`, `icon`, `status`, `parent`, `type`, `priority`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'dashboard', 'dashboard', 'lab lab-dashboard', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(2, 'Items', 'items', 'items', 'lab lab-items', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(3, 'Dining Tables', 'dining_tables', 'dining-tables', 'lab lab-dining-table', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(4, 'Pos & Orders', 'pos_and_orders', '#', 'lab lab-pos', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(5, 'POS', 'pos', 'pos', 'lab lab-pos', 1, 4, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(6, 'POS Orders', 'pos_orders', 'pos-orders', 'lab lab-pos-orders', 1, 4, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(7, 'Table Orders', 'table_orders', 'table-orders', 'lab lab-reserve-line', 1, 4, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(8, 'Promo', 'promo', '#', 'lab ', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(9, 'Offers', 'offers', 'offers', 'lab lab-offers', 1, 8, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(10, 'Users', 'users', '#', 'lab ', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(11, 'Administrators', 'administrators', 'administrators', 'lab lab-administrators', 1, 10, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(12, 'Customers', 'customers', 'customers', 'lab lab-customers', 1, 10, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(13, 'Employees', 'employees', 'employees', 'lab lab-employee', 1, 10, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(14, 'Accounts', 'accounts', '#', 'lab ', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(15, 'Transactions', 'transactions', 'transactions', 'lab lab-transactions', 1, 14, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(16, 'Reports', 'reports', '#', 'lab ', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(17, 'Sales Report', 'sales_report', 'sales-report', 'lab lab-sales-report', 1, 16, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(18, 'Items Report', 'items_report', 'items-report', 'lab lab-items-report', 1, 16, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(19, 'Credit Balance Report', 'credit_balance_report', 'credit-balance-report', 'lab lab-credit-balance-report', 1, 16, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(20, 'Setup', 'setup', '#', 'lab ', 1, 0, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05'),
(21, 'Settings', 'settings', 'settings', 'lab lab-settings', 1, 20, 1, 100, '2025-01-20 12:04:05', '2025-01-20 12:04:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `menu_sections`
--

CREATE TABLE `menu_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `menu_sections`
--

INSERT INTO `menu_sections` (`id`, `name`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'Header Section', NULL, NULL, NULL, NULL, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(2, 'Footer Section', NULL, NULL, NULL, NULL, '2025-01-20 12:04:06', '2025-01-20 12:04:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `menu_templates`
--

CREATE TABLE `menu_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `menu_templates`
--

INSERT INTO `menu_templates` (`id`, `name`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'Contact Us', NULL, NULL, NULL, NULL, '2025-01-20 12:04:06', '2025-01-20 12:04:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `message_histories`
--

CREATE TABLE `message_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `text` longtext DEFAULT NULL,
  `is_read` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2020_09_10_080029_create_menus_table', 1),
(6, '2022_05_01_142407_create_permission_tables', 1),
(7, '2022_05_24_204620_create_settings_table', 1),
(8, '2022_05_25_124629_create_currencies_table', 1),
(9, '2022_06_26_055545_create_default_access_table', 1),
(10, '2022_08_10_143500_create_media_table', 1),
(11, '2022_10_31_015126_create_pesapals_table', 1),
(12, '2022_11_17_110125_create_branches_table', 1),
(13, '2022_11_17_110157_create_languages_table', 1),
(14, '2022_11_17_110300_create_addresses_table', 1),
(15, '2022_11_17_110428_create_item_categories_table', 1),
(16, '2022_11_17_110455_create_offers_table', 1),
(17, '2022_11_17_110459_create_taxes_table', 1),
(18, '2022_11_17_110514_create_items_table', 1),
(19, '2022_11_17_110541_create_item_attributes_table', 1),
(20, '2022_11_17_110621_create_item_variations_table', 1),
(21, '2022_11_17_110650_create_item_extras_table', 1),
(22, '2022_11_17_110810_create_orders_table', 1),
(23, '2022_11_17_110832_create_order_items_table', 1),
(24, '2022_11_17_111737_create_offer_items_table', 1),
(25, '2022_11_17_113842_create_menu_sections_table', 1),
(26, '2022_11_17_114040_create_menu_templates_table', 1),
(27, '2022_11_17_114144_create_analytics_table', 1),
(28, '2022_11_17_114516_create_analytics_sections_table', 1),
(29, '2022_11_17_114835_create_payment_gateways_table', 1),
(30, '2022_11_17_115136_create_sms_gateways_table', 1),
(31, '2022_11_17_115341_create_gateway_options_table', 1),
(32, '2022_11_17_115716_create_addons_table', 1),
(33, '2022_11_17_120130_create_notifications_table', 1),
(34, '2022_11_17_120408_create_messages_table', 1),
(35, '2022_11_17_120624_create_message_histories_table', 1),
(36, '2022_11_17_120626_create_pages_table', 1),
(37, '2022_11_17_120627_create_item_addons_table', 1),
(38, '2022_11_23_125038_create_push_notifications_table', 1),
(39, '2023_01_09_111734_create_time_slots_table', 1),
(40, '2023_02_20_180253_create_order_addresses_table', 1),
(41, '2023_03_06_154954_create_otps_table', 1),
(42, '2023_03_23_143747_create_transactions_table', 1),
(43, '2023_03_23_170303_create_capture_payment_notifications_table', 1),
(44, '2023_03_27_140107_create_notification_alerts_table', 1),
(45, '2023_07_19_135307_add_soft_delete_column_to_users_table', 1),
(46, '2023_07_20_095727_add_total_tax_to_orders_table', 1),
(47, '2023_07_20_095843_add_tax_to_order_items_table', 1),
(48, '2023_09_05_133748_create_dining_tables_table', 1),
(49, '2023_11_18_154743_add_dining_table_id_to_order_table', 1),
(50, '2024_01_22_172712_add_display_mode_to_languages_table', 1),
(51, '2024_03_07_095727_add_sort_to_item_categories_table', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 4),
(7, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) NOT NULL,
  `data` longtext DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notification_alerts`
--

CREATE TABLE `notification_alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `language` varchar(255) NOT NULL,
  `mail_message` varchar(255) DEFAULT NULL,
  `sms_message` varchar(255) DEFAULT NULL,
  `push_notification_message` varchar(255) DEFAULT NULL,
  `mail` tinyint(4) DEFAULT NULL,
  `sms` tinyint(4) DEFAULT NULL,
  `push_notification` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `notification_alerts`
--

INSERT INTO `notification_alerts` (`id`, `name`, `language`, `mail_message`, `sms_message`, `push_notification_message`, `mail`, `sms`, `push_notification`, `created_at`, `updated_at`) VALUES
(1, 'Admin And Branch Manager New Order Message', 'admin_and_branch_manager_new_order_message', 'You have a new order.', 'You have a new order.', 'You have a new order.', 10, 10, 10, '2025-01-20 12:04:06', '2025-01-20 12:04:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `offers`
--

CREATE TABLE `offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `amount` decimal(13,6) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `offer_items`
--

CREATE TABLE `offer_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `offer_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_serial_no` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `subtotal` decimal(13,6) NOT NULL,
  `discount` decimal(13,6) DEFAULT 0.000000,
  `delivery_charge` decimal(13,6) DEFAULT 0.000000,
  `total_tax` decimal(13,6) DEFAULT NULL,
  `total` decimal(13,6) NOT NULL,
  `order_type` tinyint(4) NOT NULL DEFAULT 5,
  `order_datetime` timestamp NOT NULL DEFAULT '2025-01-20 00:01:02',
  `delivery_time` varchar(255) DEFAULT NULL,
  `preparation_time` int(11) NOT NULL DEFAULT 0,
  `is_advance_order` tinyint(4) NOT NULL DEFAULT 5,
  `payment_method` bigint(20) NOT NULL DEFAULT 1,
  `payment_status` tinyint(4) NOT NULL DEFAULT 10,
  `status` tinyint(4) NOT NULL,
  `dining_table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_boy_id` bigint(20) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `order_serial_no`, `token`, `user_id`, `branch_id`, `subtotal`, `discount`, `delivery_charge`, `total_tax`, `total`, `order_type`, `order_datetime`, `delivery_time`, `preparation_time`, `is_advance_order`, `payment_method`, `payment_status`, `status`, `dining_table_id`, `delivery_boy_id`, `reason`, `source`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, '2001251', NULL, 2, 1, 100.000000, 0.000000, 0.000000, 0.000000, 100.000000, 20, '2025-01-20 17:59:13', NULL, 30, 10, 1, 10, 1, 1, NULL, NULL, '5', NULL, NULL, NULL, NULL, '2025-01-20 17:59:13', '2025-01-20 17:59:13');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_addresses`
--

CREATE TABLE `order_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `apartment` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `discount` decimal(13,6) NOT NULL,
  `tax_name` varchar(255) DEFAULT NULL,
  `tax_rate` decimal(13,6) DEFAULT NULL,
  `tax_type` tinyint(4) DEFAULT NULL,
  `tax_amount` decimal(13,6) DEFAULT NULL,
  `price` decimal(13,6) NOT NULL,
  `item_variations` longtext DEFAULT NULL,
  `item_extras` longtext DEFAULT NULL,
  `item_variation_total` decimal(13,6) DEFAULT 0.000000,
  `item_extra_total` decimal(13,6) DEFAULT 0.000000,
  `total_price` decimal(13,6) DEFAULT 0.000000,
  `instruction` text DEFAULT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `branch_id`, `item_id`, `quantity`, `discount`, `tax_name`, `tax_rate`, `tax_type`, `tax_amount`, `price`, `item_variations`, `item_extras`, `item_variation_total`, `item_extra_total`, `total_price`, `instruction`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 0.000000, NULL, 0.000000, 5, 0.000000, 100.000000, '[]', '[]', 0.000000, 0.000000, 100.000000, '', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `otps`
--

CREATE TABLE `otps` (
  `phone` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `menu_section_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `misc` longtext DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `payment_gateways`
--

INSERT INTO `payment_gateways` (`id`, `name`, `slug`, `misc`, `status`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'Cash On Delivery', 'cash-on-delivery', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(2, 'Credit', 'credit', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(3, 'Paypal', 'paypal', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(4, 'Stripe', 'stripe', '{\"input\":[\"stripe.stripeInput.blade.php\"],\"js\":[\"stripe.stripeJs.blade.php\"],\"submit\":true}', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(5, 'Flutterwave', 'flutterwave', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(6, 'Paystack', 'paystack', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(7, 'SslCommerz', 'sslcommerz', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(8, 'Mollie', 'mollie', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(9, 'Senangpay', 'senangpay', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(10, 'Bkash', 'bkash', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(11, 'Paytm', 'paytm', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:09', '2025-01-20 12:04:09'),
(12, 'Razorpay', 'razorpay', '{\"input\":[],\"js\":[\"razorpay.razorpayJs.blade.php\"],\"submit\":false}', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(13, 'Mercadopago', 'mercadopago', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(14, 'Cashfree', 'cashfree', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(15, 'Payfast', 'payfast', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(16, 'Skrill', 'skrill', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(17, 'PhonePe', 'phonepe', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(18, 'Telr', 'telr', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(19, 'Iyzico', 'iyzico', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(20, 'Pesapal', 'pesapal', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(21, 'Midtrans', 'midtrans', 'null', 10, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `name` varchar(125) NOT NULL,
  `guard_name` varchar(125) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `parent` bigint(20) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `permissions`
--

INSERT INTO `permissions` (`id`, `title`, `name`, `guard_name`, `url`, `parent`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'dashboard', 'sanctum', 'dashboard', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(2, 'Items', 'items', 'sanctum', 'items', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(3, 'Items Create', 'items_create', 'sanctum', 'items/create', 2, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(4, 'Items Edit', 'items_edit', 'sanctum', 'items/edit', 2, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(5, 'Items Delete', 'items_delete', 'sanctum', 'items/delete', 2, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(6, 'Items Show', 'items_show', 'sanctum', 'items/show', 2, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(7, 'Dining Tables', 'dining-tables', 'sanctum', 'dining-tables', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(8, 'Dining Tables Create', 'dining_tables_create', 'sanctum', 'dining-table/create', 7, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(9, 'Dining Tables Edit', 'dining_tables_edit', 'sanctum', 'dining-table/edit', 7, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(10, 'Dining Tables Delete', 'dining_tables_delete', 'sanctum', 'dining-tables/delete', 7, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(11, 'Dining Tables Show', 'dining_tables_show', 'sanctum', 'dining-tables/show', 7, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(12, 'POS', 'pos', 'sanctum', 'pos', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(13, 'POS Orders', 'pos-orders', 'sanctum', 'pos-orders', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(14, 'Table Orders', 'table-orders', 'sanctum', 'table-orders', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(15, 'Offers', 'offers', 'sanctum', 'offers', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(16, 'Offers Create', 'offers_create', 'sanctum', 'offers/create', 15, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(17, 'Offers Edit', 'offers_edit', 'sanctum', 'offers/edit', 15, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(18, 'Offers Delete', 'offers_delete', 'sanctum', 'offers/delete', 15, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(19, 'Offers Show', 'offers_show', 'sanctum', 'offers/show', 15, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(20, 'Administrators', 'administrators', 'sanctum', 'administrators', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(21, 'Administrators Create', 'administrators_create', 'sanctum', 'administrators/create', 20, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(22, 'Administrators Edit', 'administrators_edit', 'sanctum', 'administrators/edit', 20, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(23, 'Administrators Delete', 'administrators_delete', 'sanctum', 'administrators/delete', 20, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(24, 'Administrators Show', 'administrators_show', 'sanctum', 'administrators/show', 20, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(25, 'Delivery Boys', 'delivery-boys', 'sanctum', 'delivery-boys', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(26, 'Delivery Boys Create', 'delivery-boys_create', 'sanctum', 'delivery-boys/create', 25, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(27, 'Delivery Boys Edit', 'delivery-boys_edit', 'sanctum', 'delivery-boys/edit', 25, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(28, 'Delivery Boys Delete', 'delivery-boys_delete', 'sanctum', 'delivery-boys/delete', 25, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(29, 'Delivery Boys Show', 'delivery-boys_show', 'sanctum', 'delivery-boys/show', 25, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(30, 'Customers', 'customers', 'sanctum', 'customers', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(31, 'Customers Create', 'customers_create', 'sanctum', 'customers/create', 30, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(32, 'Customers Edit', 'customers_edit', 'sanctum', 'customers/edit', 30, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(33, 'Customers Delete', 'customers_delete', 'sanctum', 'customers/delete', 30, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(34, 'Customers Show', 'customers_show', 'sanctum', 'customers/show', 30, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(35, 'Employees', 'employees', 'sanctum', 'employees', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(36, 'Employees Create', 'employees_create', 'sanctum', 'employees/create', 35, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(37, 'Employees Edit', 'employees_edit', 'sanctum', 'employees/edit', 35, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(38, 'Employees Delete', 'employees_delete', 'sanctum', 'employees/delete', 35, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(39, 'Employees Show', 'employees_show', 'sanctum', 'employees/show', 35, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(40, 'Transactions', 'transactions', 'sanctum', 'transactions', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(41, 'Sales Report', 'sales-report', 'sanctum', 'sales-report', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(42, 'Items Report', 'items-report', 'sanctum', 'items-report', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(43, 'Credit Balance Report', 'credit-balance-report', 'sanctum', 'credit-balance-report', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(44, 'Settings', 'settings', 'sanctum', 'settings', 0, '2025-01-20 12:04:06', '2025-01-20 12:04:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '21534d13a31dc58896b158d2fd07ce558dc0ac0d2d2b3d57790f3d83b12110b2', '[\"*\"]', '2025-01-20 12:04:39', NULL, '2025-01-20 12:04:28', '2025-01-20 12:04:39'),
(6, 'App\\Models\\User', 1, 'auth_token', 'cc6d221a98852ba4994842e33d82fe8d929cd28fb6b2828e9308543468b5babf', '[\"*\"]', '2025-01-21 02:06:36', NULL, '2025-01-20 19:11:40', '2025-01-21 02:06:36');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pesapals`
--

CREATE TABLE `pesapals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone_number` bigint(20) UNSIGNED DEFAULT NULL,
  `billing_address_line_1` varchar(255) DEFAULT NULL,
  `billing_address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `zip_code` varchar(255) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `amount` text NOT NULL,
  `currency` varchar(255) NOT NULL,
  `language` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `merchant_reference` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` text DEFAULT NULL,
  `tracking_id` text DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(2, 'Customer', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(3, 'Waiter', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(4, 'Chef', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(5, 'Branch Manager', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(6, 'POS Operator', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06'),
(7, 'Stuff', 'sanctum', '2025-01-20 12:04:06', '2025-01-20 12:04:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 5),
(1, 6),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(7, 5),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(12, 5),
(12, 6),
(13, 1),
(13, 5),
(13, 6),
(14, 1),
(14, 5),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(25, 5),
(26, 1),
(26, 5),
(27, 1),
(27, 5),
(28, 1),
(28, 5),
(29, 1),
(29, 5),
(30, 1),
(30, 5),
(31, 1),
(31, 5),
(32, 1),
(32, 5),
(33, 1),
(33, 5),
(34, 1),
(34, 5),
(35, 1),
(35, 5),
(36, 1),
(36, 5),
(37, 1),
(37, 5),
(38, 1),
(38, 5),
(39, 1),
(39, 5),
(40, 1),
(40, 5),
(41, 1),
(41, 5),
(42, 1),
(43, 1),
(44, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(255) DEFAULT NULL,
  `key` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `settingable_type` varchar(255) DEFAULT NULL,
  `settingable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `group`, `key`, `payload`, `settingable_type`, `settingable_id`, `created_at`, `updated_at`) VALUES
(1, 'company', 'company_name', '{\"$value\":\"QR Menu\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:25', '2025-01-21 02:01:25'),
(2, 'company', 'company_email', '{\"$value\":\"info@yilmazkanat.com\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:26', '2025-01-21 02:01:26'),
(3, 'company', 'company_phone', '{\"$value\":\"+905555555555\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:26', '2025-01-21 02:01:26'),
(4, 'company', 'company_website', '{\"$value\":\"https:\\/\\/qrmenu.yilmazkanat.com\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:26', '2025-01-21 02:01:26'),
(5, 'company', 'company_city', '{\"$value\":\"\\u0130zmir\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:26', '2025-01-21 02:01:26'),
(6, 'company', 'company_state', '{\"$value\":\"\\u00c7i\\u011fli\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:27', '2025-01-21 02:01:27'),
(7, 'company', 'company_country_code', '{\"$value\":\"TUR\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:27', '2025-01-21 02:01:27'),
(8, 'company', 'company_zip_code', '{\"$value\":\"1216\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:27', '2025-01-21 02:01:27'),
(9, 'company', 'company_address', '{\"$value\":\"House : 25, Road No: 2, Block A, Mirpur-1, Dhaka 1216\",\"$cast\":null}', NULL, NULL, '2025-01-21 02:01:27', '2025-01-21 02:01:27'),
(10, 'site', 'site_date_format', '{\"$value\":\"d-m-Y\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(11, 'site', 'site_time_format', '{\"$value\":\"H:i\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(12, 'site', 'site_default_timezone', '{\"$value\":\"Asia\\/Dhaka\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(13, 'site', 'site_default_branch', '{\"$value\":1,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(14, 'site', 'site_default_currency', '{\"$value\":1,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(15, 'site', 'site_default_currency_symbol', '{\"$value\":\"$\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(16, 'site', 'site_currency_position', '{\"$value\":5,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(17, 'site', 'site_digit_after_decimal_point', '{\"$value\":\"2\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(18, 'site', 'site_email_verification', '{\"$value\":5,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(19, 'site', 'site_phone_verification', '{\"$value\":10,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(20, 'site', 'site_default_language', '{\"$value\":7,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(21, 'site', 'site_google_map_key', '{\"$value\":\"----\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(22, 'site', 'site_copyright', '{\"$value\":\"https:\\/\\/yilmazkanat.com\\/\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(23, 'site', 'site_language_switch', '{\"$value\":5,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(24, 'site', 'site_app_debug', '{\"$value\":10,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(25, 'site', 'site_auto_update', '{\"$value\":10,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(26, 'site', 'site_online_payment_gateway', '{\"$value\":10,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(27, 'site', 'site_default_sms_gateway', '{\"$value\":null,\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(28, 'site', 'site_food_preparation_time', '{\"$value\":\"30\",\"$cast\":null}', NULL, NULL, '2025-01-20 19:07:49', '2025-01-20 19:07:49'),
(29, 'notification', 'notification_fcm_public_vapid_key', '{\"$value\":\"BK1YDaxHFc-ZzZLUJFK0PEqcUW70tvxEzWvUImvhEJHnrJHREHqN03abQZ-aV0JZFNa-e2e7VFOy5fRPkIyOBms\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(30, 'notification', 'notification_fcm_api_key', '{\"$value\":\"AIzaSyBjMgzA9UQ2DXHGoL05Xmdhv_UQvYxlv7I\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(31, 'notification', 'notification_fcm_auth_domain', '{\"$value\":\"qrmenu-d6e96.firebaseapp.com\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(32, 'notification', 'notification_fcm_project_id', '{\"$value\":\"qrmenu-d6e96\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(33, 'notification', 'notification_fcm_storage_bucket', '{\"$value\":\"qrmenu-d6e96.firebasestorage.app\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(34, 'notification', 'notification_fcm_messaging_sender_id', '{\"$value\":\"377975712158\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(35, 'notification', 'notification_fcm_app_id', '{\"$value\":\"1:377975712158:web:ee032776ebc520dbfc4e8f\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(36, 'notification', 'notification_fcm_measurement_id', '{\"$value\":\"G-T3MVMF6MP4\",\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(37, 'notification', 'notification_fcm_json_file', '{\"$value\":{},\"$cast\":null}', NULL, NULL, '2025-01-20 18:23:57', '2025-01-20 18:23:57'),
(38, 'mail', 'mail_mailer', '{\"$value\":\"smtp\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(39, 'mail', 'mail_host', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(40, 'mail', 'mail_port', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(41, 'mail', 'mail_username', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(42, 'mail', 'mail_password', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(43, 'mail', 'mail_encryption', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(44, 'mail', 'mail_from_name', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(45, 'mail', 'mail_from_email', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(46, 'order_setup', 'order_setup_food_preparation_time', '{\"$value\":\"30\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(47, 'order_setup', 'order_setup_schedule_order_slot_duration', '{\"$value\":\"30\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(48, 'order_setup', 'order_setup_takeaway', '{\"$value\":5,\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(49, 'order_setup', 'order_setup_delivery', '{\"$value\":5,\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(50, 'order_setup', 'order_setup_free_delivery_kilometer', '{\"$value\":\"2\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(51, 'order_setup', 'order_setup_basic_delivery_charge', '{\"$value\":\"1\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(52, 'order_setup', 'order_setup_charge_per_kilo', '{\"$value\":\"1\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(53, 'otp', 'otp_type', '{\"$value\":\"5\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(54, 'otp', 'otp_digit_limit', '{\"$value\":\"4\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(55, 'otp', 'otp_expire_time', '{\"$value\":\"10\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11'),
(56, 'theme', 'theme_logo', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(57, 'theme', 'theme_favicon_logo', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(58, 'theme', 'theme_footer_logo', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(59, 'license', 'license_key', '{\"$value\":\"DEMO-KEY-123\",\"$cast\":null}', NULL, NULL, '2025-01-21 01:55:47', '2025-01-21 01:55:47'),
(60, 'social_media', 'social_media_facebook', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(61, 'social_media', 'social_media_youtube', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(62, 'social_media', 'social_media_instagram', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(63, 'social_media', 'social_media_twitter', '{\"$value\":\"\",\"$cast\":null}', NULL, NULL, '2025-01-20 12:04:12', '2025-01-20 12:04:12'),
(64, 'license', 'is_verified', '{\"$value\":true,\"$cast\":null}', NULL, NULL, '2025-01-21 01:55:47', '2025-01-21 01:55:47'),
(65, 'license', 'verified_at', '{\"$value\":\"2025-01-21T04:55:47+06:00\",\"$cast\":\"Carbon\\\\Carbon\"}', NULL, NULL, '2025-01-21 01:55:47', '2025-01-21 01:55:47'),
(66, 'license', 'status', '{\"$value\":\"active\",\"$cast\":null}', NULL, NULL, '2025-01-21 01:55:47', '2025-01-21 01:55:47');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sms_gateways`
--

CREATE TABLE `sms_gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `misc` longtext DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `sms_gateways`
--

INSERT INTO `sms_gateways` (`id`, `name`, `slug`, `misc`, `status`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `created_at`, `updated_at`) VALUES
(1, 'Twilio', 'twilio', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(2, 'Clickatell', 'clickatell', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(3, 'Nexmo', 'nexmo', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(4, 'Msg91', 'msg91', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(5, '2Factor', 'twofactor', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(6, 'Bulksms', 'bulksms', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(7, 'Bulksmsbd', 'bulksmsbd', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10'),
(8, 'Telesign', 'telesign', 'null', 5, NULL, NULL, NULL, NULL, '2025-01-20 12:04:10', '2025-01-20 12:04:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `tax_rate` decimal(13,6) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `time_slots`
--

CREATE TABLE `time_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `opening_time` varchar(255) NOT NULL,
  `closing_time` varchar(255) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sign` varchar(255) NOT NULL DEFAULT '+',
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_no` varchar(255) NOT NULL,
  `amount` decimal(13,6) NOT NULL DEFAULT 0.000000,
  `payment_method` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'payment',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `device_token` varchar(255) DEFAULT NULL,
  `web_token` varchar(255) DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT 0,
  `country_code` varchar(255) DEFAULT NULL,
  `is_guest` tinyint(3) UNSIGNED NOT NULL DEFAULT 10,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '5=Active, 10=Inactive',
  `balance` decimal(13,6) NOT NULL DEFAULT 0.000000,
  `creator_type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `editor_type` varchar(255) DEFAULT NULL,
  `editor_id` bigint(20) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `username`, `email_verified_at`, `password`, `device_token`, `web_token`, `branch_id`, `country_code`, `is_guest`, `status`, `balance`, `creator_type`, `creator_id`, `editor_type`, `editor_id`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Yılmaz KANAT', 'yilmazkanatceng@gmail.com', '5465464364', 'admin', '2025-01-20 12:04:10', '$2y$10$Qfhz2ecNFEbIcHCdN6oIuOl3g8LC5TWmSfiXMgNz7Y5WAUTau3Bca', NULL, NULL, 0, '+90', 10, 5, 0.000000, NULL, NULL, NULL, NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:47:15', NULL),
(2, 'Walking Customer', 'walkingcustomer@example.com', '125444455', 'default-customer', '2025-01-20 12:04:11', '$2y$10$mKGAZvb3j0pl6W0L0l/iIuQO0m5n.0D9KLN0t4HJNdkznZhwR3Yra', NULL, NULL, 0, '+880', 10, 5, 0.000000, NULL, NULL, NULL, NULL, NULL, '2025-01-20 12:04:11', '2025-01-20 12:04:11', NULL),
(3, 'garson', 'garson@yilmazkanat.com', '555', 'garson1528003432', '2025-01-20 15:56:02', '$2y$10$VViEF2WI5FQNT5sutEkVU.Pb6G/vRkd2pINxvw8fbG/bVBv1W/vfK', NULL, NULL, 1, '+90', 10, 5, 0.000000, NULL, NULL, NULL, NULL, NULL, '2025-01-20 15:56:02', '2025-01-20 15:56:02', NULL),
(4, 'La Puerta', 'lapuerta@yilmazkanat.com', '555555', 'lapuerta2146601322', '2025-01-20 19:09:47', '$2y$10$mSXoVtJpK0vUHafHuws1mOW7NyTw/Y4G5rvWnPgFuEVcoxhGWs7Pe', NULL, NULL, 1, '+90', 10, 5, 0.000000, NULL, NULL, NULL, NULL, NULL, '2025-01-20 19:09:47', '2025-01-20 19:09:47', NULL);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_foreign` (`user_id`);

--
-- Tablo için indeksler `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `analytic_sections`
--
ALTER TABLE `analytic_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `analytic_sections_analytic_id_foreign` (`analytic_id`);

--
-- Tablo için indeksler `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `default_access`
--
ALTER TABLE `default_access`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `dining_tables`
--
ALTER TABLE `dining_tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dining_tables_slug_unique` (`slug`),
  ADD KEY `dining_tables_branch_id_foreign` (`branch_id`);

--
-- Tablo için indeksler `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Tablo için indeksler `gateway_options`
--
ALTER TABLE `gateway_options`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `items_item_category_id_foreign` (`item_category_id`),
  ADD KEY `items_tax_id_foreign` (`tax_id`);

--
-- Tablo için indeksler `item_addons`
--
ALTER TABLE `item_addons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_addons_item_id_foreign` (`item_id`),
  ADD KEY `item_addons_addon_item_id_foreign` (`addon_item_id`);

--
-- Tablo için indeksler `item_attributes`
--
ALTER TABLE `item_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `item_categories`
--
ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `item_extras`
--
ALTER TABLE `item_extras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_extras_item_id_foreign` (`item_id`);

--
-- Tablo için indeksler `item_variations`
--
ALTER TABLE `item_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_variations_item_id_foreign` (`item_id`),
  ADD KEY `item_variations_item_attribute_id_foreign` (`item_attribute_id`);

--
-- Tablo için indeksler `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Tablo için indeksler `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `menu_sections`
--
ALTER TABLE `menu_sections`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `menu_templates`
--
ALTER TABLE `menu_templates`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_user_id_foreign` (`user_id`);

--
-- Tablo için indeksler `message_histories`
--
ALTER TABLE `message_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_histories_message_id_foreign` (`message_id`),
  ADD KEY `message_histories_user_id_foreign` (`user_id`);

--
-- Tablo için indeksler `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Tablo için indeksler `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `notification_alerts`
--
ALTER TABLE `notification_alerts`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `offer_items`
--
ALTER TABLE `offer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offer_items_offer_id_foreign` (`offer_id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_branch_id_foreign` (`branch_id`);

--
-- Tablo için indeksler `order_addresses`
--
ALTER TABLE `order_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_addresses_order_id_foreign` (`order_id`),
  ADD KEY `order_addresses_user_id_foreign` (`user_id`);

--
-- Tablo için indeksler `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_branch_id_foreign` (`branch_id`),
  ADD KEY `order_items_item_id_foreign` (`item_id`);

--
-- Tablo için indeksler `otps`
--
ALTER TABLE `otps`
  ADD KEY `otps_phone_index` (`phone`);

--
-- Tablo için indeksler `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pages_menu_section_id_foreign` (`menu_section_id`);

--
-- Tablo için indeksler `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Tablo için indeksler `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Tablo için indeksler `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Tablo için indeksler `pesapals`
--
ALTER TABLE `pesapals`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Tablo için indeksler `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_settingable_type_settingable_id_index` (`settingable_type`,`settingable_id`);

--
-- Tablo için indeksler `sms_gateways`
--
ALTER TABLE `sms_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `addons`
--
ALTER TABLE `addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `analytic_sections`
--
ALTER TABLE `analytic_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `default_access`
--
ALTER TABLE `default_access`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `dining_tables`
--
ALTER TABLE `dining_tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `gateway_options`
--
ALTER TABLE `gateway_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- Tablo için AUTO_INCREMENT değeri `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `item_addons`
--
ALTER TABLE `item_addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `item_attributes`
--
ALTER TABLE `item_attributes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `item_categories`
--
ALTER TABLE `item_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `item_extras`
--
ALTER TABLE `item_extras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `item_variations`
--
ALTER TABLE `item_variations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Tablo için AUTO_INCREMENT değeri `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `menu_sections`
--
ALTER TABLE `menu_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `menu_templates`
--
ALTER TABLE `menu_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `message_histories`
--
ALTER TABLE `message_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `notification_alerts`
--
ALTER TABLE `notification_alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `offers`
--
ALTER TABLE `offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `offer_items`
--
ALTER TABLE `offer_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `order_addresses`
--
ALTER TABLE `order_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Tablo için AUTO_INCREMENT değeri `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `pesapals`
--
ALTER TABLE `pesapals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Tablo için AUTO_INCREMENT değeri `sms_gateways`
--
ALTER TABLE `sms_gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `analytic_sections`
--
ALTER TABLE `analytic_sections`
  ADD CONSTRAINT `analytic_sections_analytic_id_foreign` FOREIGN KEY (`analytic_id`) REFERENCES `analytics` (`id`);

--
-- Tablo kısıtlamaları `dining_tables`
--
ALTER TABLE `dining_tables`
  ADD CONSTRAINT `dining_tables_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Tablo kısıtlamaları `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `item_categories` (`id`),
  ADD CONSTRAINT `items_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`);

--
-- Tablo kısıtlamaları `item_addons`
--
ALTER TABLE `item_addons`
  ADD CONSTRAINT `item_addons_addon_item_id_foreign` FOREIGN KEY (`addon_item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `item_addons_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Tablo kısıtlamaları `item_extras`
--
ALTER TABLE `item_extras`
  ADD CONSTRAINT `item_extras_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Tablo kısıtlamaları `item_variations`
--
ALTER TABLE `item_variations`
  ADD CONSTRAINT `item_variations_item_attribute_id_foreign` FOREIGN KEY (`item_attribute_id`) REFERENCES `item_attributes` (`id`),
  ADD CONSTRAINT `item_variations_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Tablo kısıtlamaları `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `message_histories`
--
ALTER TABLE `message_histories`
  ADD CONSTRAINT `message_histories_message_id_foreign` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  ADD CONSTRAINT `message_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `offer_items`
--
ALTER TABLE `offer_items`
  ADD CONSTRAINT `offer_items_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`);

--
-- Tablo kısıtlamaları `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `order_addresses`
--
ALTER TABLE `order_addresses`
  ADD CONSTRAINT `order_addresses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `order_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Tablo kısıtlamaları `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_menu_section_id_foreign` FOREIGN KEY (`menu_section_id`) REFERENCES `menu_sections` (`id`);

--
-- Tablo kısıtlamaları `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
