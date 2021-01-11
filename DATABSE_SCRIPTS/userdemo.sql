-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2021 at 06:26 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `userdemo`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_public_group` tinyint(1) NOT NULL DEFAULT 0,
  `group_owner_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `group_desc`, `is_public_group`, `group_owner_id`, `created_at`, `updated_at`) VALUES
(1, 'Mahantesh-Public-Group', 'Hey Folks..Its Mahantesh-Public-Group..Any logged in user can join this group', 0, 1, '2021-01-11 14:24:18', '2021-01-11 14:24:18'),
(2, 'Ketan.Yekae-Public-Group', 'Hey Folks..Its Ketan.Yekale-Public-Group..Any logged in user can join this group', 1, 3, '2021-01-11 14:26:03', '2021-01-11 14:39:48'),
(3, 'Ketan-Private-Group', 'Hey Folks..Its Ketan.fadf-Private-Group-Only Ketan can add or remove users in this group..', 0, 3, '2021-01-11 14:26:03', '2021-01-11 14:39:48'),
(4, 'Mahantesh-Private-Group', 'Hey Folks..Its Mahantesh-Private-Group..Only Mahantesh can add or remove users in this group..', 0, 3, '2021-01-11 14:41:53', '2021-01-11 14:41:53');

-- --------------------------------------------------------

--
-- Table structure for table `groups_members`
--

CREATE TABLE `groups_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `group_member_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2021_01_05_120559_create_users_table', 1),
(2, '2021_01_11_132721_create_groups_table', 1),
(3, '2021_01_11_133420_create_groups_members_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `mobile`, `address`, `api_key`, `created_at`, `updated_at`) VALUES
(1, 'Mahantesh', '$2y$10$kD2aDgEqc.qVAl9a61uLBe9t8Ja0JJh/f39j9e0uhpgeAFSLCjVaW', 'mahantesh@gmail.com', 1221, 'fasdfaf', 'RVc3d29kR1NaVUpaVmhPTXlvVU56OGYxYmk5aVhGeVQ5Q2Zjc09vcw==', '2021-01-06 16:52:16', '2021-01-11 06:56:58'),
(2, 'Mahesh', '$2y$10$IRFdRJm2VBS9d1z/S5CkGugALQbpRtTosDPg4qHXhH/mxZm6gwm52', 'mahesh@gmail.com', 12212, 'fasdfaf', NULL, '2021-01-06 18:19:38', '2021-01-10 19:40:50'),
(3, 'Ketan', '$2y$10$YZQVptPiBncd/1qwVpNhQu7ViUzmuwC/ODkwxmbq3B1xqPiRYWaJe', 'ketan1@gmail.com', 12321, 'fasdfaf', 'S1d5QjZLd0RUMmNlZGhzZ2VDQ3p2MVZFQkZkcUVCeU9hMmJxWlZyVw==', '2021-01-06 18:25:38', '2021-01-11 16:54:25'),
(4, 'xyz', '$2y$10$OoCWyNJKv4pHm2uKgRMLU.gPS0j/HPq7hygeg2szfl5MwdpX.CZ5u', 'xyz111@gmail.com', 21211, 'fasdfaf', NULL, '2021-01-06 18:25:38', '2021-01-11 07:00:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `groups_group_name_unique` (`group_name`),
  ADD KEY `groups_group_owner_id_foreign` (`group_owner_id`);

--
-- Indexes for table `groups_members`
--
ALTER TABLE `groups_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `groups_members_group_id_foreign` (`group_id`),
  ADD KEY `groups_members_group_member_id_foreign` (`group_member_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_mobile_unique` (`mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `groups_members`
--
ALTER TABLE `groups_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_group_owner_id_foreign` FOREIGN KEY (`group_owner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `groups_members`
--
ALTER TABLE `groups_members`
  ADD CONSTRAINT `groups_members_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `groups_members_group_member_id_foreign` FOREIGN KEY (`group_member_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
