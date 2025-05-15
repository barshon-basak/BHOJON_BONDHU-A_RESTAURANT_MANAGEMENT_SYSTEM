-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 26, 2025 at 10:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'barshon123', '$2y$10$cDwsS2JustVJKMJ7PP8NZ./.RAN678r8cO04jQ2xentbI9dWgpDPu', '2025-04-11 19:13:01'),
(2, 'zunayed123', '$2y$10$pWBdqZN49lE8Nq.N0XVgf.fuMPBfUCNKzSVGctv5SBLl4.NICp2uW', '2025-04-11 19:13:01');

-- --------------------------------------------------------

--
-- Table structure for table `app_feedback`
--

CREATE TABLE `app_feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dish_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `dish_id`, `quantity`, `created_at`) VALUES
(9, 5, 6, 1, '2025-04-19 05:00:32');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_address`
--

CREATE TABLE `delivery_address` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_address`
--

INSERT INTO `delivery_address` (`id`, `order_id`, `address`, `created_at`, `updated_at`) VALUES
(1, 12, 'aaa', '2025-04-26 07:06:49', '2025-04-26 07:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `dishes`
--

CREATE TABLE `dishes` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dishes`
--

INSERT INTO `dishes` (`id`, `restaurant_id`, `name`, `description`, `price`, `image_url`, `status`, `created_at`) VALUES
(1, 2, 'Firecracker Pepperoni', 'Thin crust, extra cheese, loaded with spicy pepperoni.', 800.00, 'https://www.homeruninnpizza.com/wp-content/uploads/2022/12/firecracker-376x376.jpg', 'available', '2025-04-15 05:37:59'),
(2, 3, 'Grilled Chicken Rancher', 'Tender grilled chicken with ranch and fresh greens.', 300.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3VGt8wmN4pMM_b-CoTdk2jDtIYg_5vIXluw&s', 'available', '2025-04-15 06:17:54'),
(3, 3, 'Crispy Smash Veggie', 'Spiced veggie patty, double cheese, and tangy mayo.', 280.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIs1vbvh9E00G6f4VNuQAvb4FAKT73eH0jsg&s', 'available', '2025-04-15 06:18:52'),
(4, 3, 'Cheese Lava Burger', 'Melted cheese oozes from the center of this beefy delight.', 350.00, 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjHbRKJeMdQNNZUBmvq7-kIIBQF8YWn6mWDhOLsfcBxD2Ffk5J_eYEGARrCAPg_vSzYKsi5GXFf3Ym0oJ18XwOA165ulRiVwwjI2uAmW1na1fP3iFDQ7lMb80MlkhFIVbYW0dtPpxeP2441QOBVc84E6oRGCnlmXbvve4VIeggHSg6CcugElWSDA7jiDmw/w1200-', 'available', '2025-04-15 06:21:47'),
(5, 4, 'Shorshe Ilish', 'Hilsa fish cooked in mustard seed gravy, classic Bengali taste.', 380.00, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQVGBZb7CdHbnemo5GJcX0yeD59HtD0zgjYdQ&s', 'available', '2025-04-15 06:23:55'),
(6, 4, 'Chingri Malai Curry', 'Prawns in creamy coconut gravy, subtly spiced.', 350.00, 'https://atanurrannagharrecipe.com/wp-content/uploads/2023/01/Chingri-Macher-Malai-Curry-Photo-02.jpg', 'available', '2025-04-15 06:24:47'),
(7, 4, 'Luchi & Alur Dom', 'Puffy fried breads served with rich potato curry.', 200.00, 'https://i0.wp.com/foodtrails25.com/wp-content/uploads/2019/04/img_7076_ezy-watermark_29-04-2019_08-09-27pm.jpg?fit=1600%2C1067&ssl=1', 'available', '2025-04-15 06:25:34');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `order_id`, `amount`, `created_at`) VALUES
(1, 1, 730.00, '2025-04-15 06:57:20'),
(2, 4, 2830.00, '2025-04-19 05:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `payment_method`) VALUES
(1, NULL, 730.00, 'completed', '2025-04-15 06:57:11', 'COD'),
(2, 5, 300.00, 'cancelled', '2025-04-19 04:57:25', 'COD'),
(3, 5, 0.00, 'cancelled', '2025-04-19 04:58:05', 'COD'),
(4, 5, 2830.00, 'processing', '2025-04-19 05:00:14', 'COD'),
(5, NULL, 380.00, 'pending', '2025-04-23 13:08:35', 'COD'),
(6, NULL, 200.00, 'pending', '2025-04-23 13:13:23', 'BKASH'),
(7, NULL, 350.00, 'pending', '2025-04-23 13:15:57', 'COD'),
(8, NULL, 280.00, 'pending', '2025-04-23 13:18:38', 'COD'),
(9, NULL, 300.00, 'pending', '2025-04-23 13:54:04', 'COD'),
(10, NULL, 800.00, 'pending', '2025-04-23 14:05:19', 'COD'),
(11, NULL, 350.00, 'pending', '2025-04-26 06:58:40', 'COD'),
(12, NULL, 280.00, 'pending', '2025-04-26 07:06:49', 'COD');

-- --------------------------------------------------------

--
-- Table structure for table `order_extra_info`
--

CREATE TABLE `order_extra_info` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `extra_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `dish_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `dish_id`, `quantity`, `price`) VALUES
(1, 1, 5, 1, 380.00),
(2, 1, 6, 1, 350.00),
(3, 2, 2, 1, 300.00),
(4, 4, 6, 7, 350.00),
(5, 4, 5, 1, 380.00),
(6, 5, 5, 1, 380.00),
(7, 6, 7, 1, 200.00),
(8, 7, 6, 1, 350.00),
(9, 8, 3, 1, 280.00),
(10, 9, 2, 1, 300.00),
(11, 10, 1, 1, 800.00),
(12, 11, 6, 1, 350.00),
(13, 12, 3, 1, 280.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `order_id`, `method`, `created_at`) VALUES
(1, 7, 'CARD', '2025-04-23 13:15:57'),
(2, 8, 'CARD', '2025-04-23 13:18:38'),
(3, 9, 'COD', '2025-04-23 13:54:04'),
(4, 10, 'COD', '2025-04-23 14:05:19'),
(5, 11, 'COD', '2025-04-26 06:58:40'),
(6, 12, 'COD', '2025-04-26 07:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `description`, `image_url`, `status`, `created_at`) VALUES
(1, 'Brew & Bloom', 'coffee & drinks', 'https://media.istockphoto.com/id/1007194468/photo/people-drinking-coffee-high-angle-view.jpg?s=612x612&w=0&k=20&c=dIdvLUd5X_QB1Zjf0noSuno0OpZqyIN4uhzL0mLi-u8=', 'active', '2025-04-14 18:26:30'),
(2, 'Slice Society', 'Neon lights, urban beats, and a slice for every craving. Freshly fired in a stone oven, where crust is king.', 'https://media.istockphoto.com/id/184946701/photo/pizza.jpg?s=612x612&w=0&k=20&c=97rc0VIi-s3mn4xe4xDy9S-XJ_Ohbn92XaEMaiID_eY=', 'active', '2025-04-15 05:37:03'),
(3, 'Bun & Beast', 'Where burgers are not just food, they’re stacked experiences. Juicy, messy, and unforgettable.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfCWqWe4M96kabO935_J-VXLgOY9Fwcm0LFsfSPSJn_qD259di5l6TkHH0t5FzelCNOJQ&usqp=CAU', 'active', '2025-04-15 06:16:48'),
(4, 'Shonar Bangla Bites', 'Traditional flavors of Bengal served with love and mustard oil. A tribute to home-style cooking.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXaCXEJq-Gmeh14Iu5D_1_vI0e_YZKOyLJAQ&s', 'active', '2025-04-15 06:22:34'),
(5, 'Kacchi Kingdom', 'Royal flavors wrapped in fragrant rice and tender meat. Authentic kacchi, done right.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSONv9j_CwAS4e1dAGWTWI6X5Za-6vFnwvpnA&s', 'active', '2025-04-15 06:26:48'),
(6, 'Wrap City', 'Where bold flavors are wrapped up tight. Quick, juicy, and handheld goodness for every craving.', 'https://images.deliveryhero.io/image/fd-bd/bd-logos/cx5tp-logo.jpg', 'active', '2025-04-15 06:29:15'),
(7, 'Green Fork Café', 'Wholesome, colorful, and deliciously plant-powered. No meat, no problem—just pure flavor.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIxuDVSOHhkXWd-reUJpUbXg7fK1dN_MD7Zg&s', 'active', '2025-04-15 06:29:57'),
(8, 'BASAK SPECIAL', 'trdfuygbiufviuyf', 'https://img.freepik.com/free-vector/isolated-bamboos-white-background_1308-114423.jpg?t=st=1745042413~exp=1745046013~hmac=36b09d1e560b1c18627e40dd833d0d012b530e2ef75a623fb34de360a55250b4&w=740', 'inactive', '2025-04-19 05:21:21'),
(9, 'aaa', 'aaaaaa', 'https://www.bing.com/images/search?view=detailV2&ccid=uLA0cvtY&id=AD7A3CAFCC2188C4543759E3A8ABFCC6CE9C12E9&thid=OIP.uLA0cvtYzIrCdPVYGm7E-QHaEK&mediaurl=https%3a%2f%2fichef.bbci.co.uk%2ffood%2fic%2ffood_16x9_1600%2frecipes%2frib-eye_steak_with_61963_16x9.j', 'active', '2025-04-26 06:29:39'),
(10, 'Barshon Basak', 'qqqq', 'https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg', 'active', '2025-04-26 06:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_status`
--

CREATE TABLE `restaurant_status` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_status`
--

INSERT INTO `restaurant_status` (`id`, `restaurant_id`, `status`, `updated_at`) VALUES
(1, 10, 'active', '2025-04-26 06:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `restaurant_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(4, 8, 5, 1, 'SALA', '2025-04-19 08:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `created_at`, `verified`) VALUES
(3, 'Faizur Rahman Zunayed', 'faizurzunayed7717@gmail.com', '01783746578', '$2y$10$ePQqVYIhrYiUPpBbnJF65Ojuwne1LkaQ4QRzLl4rdnkOY.cis.4sS', '2025-04-19 04:40:14', 0),
(4, 'Faizur Rahman Zunayed', 'asdf@c.v', '01783746578', '$2y$10$6C7QOW55qEl43YrthAx2ruve9.3PCN5lrNgSCeW2J9d0yb6i616.C', '2025-04-19 04:51:34', 0),
(5, 'Faizur Rahman Zunayed', 'a@r.s', '01783746578', '$2y$10$6SXG1y2fuprraFYT42ZAU.O6jXp.cqFiXRyFWJhZSDKAF8u8zBVky', '2025-04-19 04:52:36', 0),
(11, 'Barshon Basak', 'barshonbasak@gmail.com', '01791107495', '$2y$10$rquNzUJ1VqFXqA80HXn80eTW9uBpGLlUhwJUc6ZHcxYjVmPdEU2YS', '2025-04-26 07:50:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_photos`
--

CREATE TABLE `user_photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `app_feedback`
--
ALTER TABLE `app_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indexes for table `delivery_address`
--
ALTER TABLE `delivery_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_extra_info`
--
ALTER TABLE `order_extra_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `dish_id` (`dish_id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_methods_order` (`order_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_status`
--
ALTER TABLE `restaurant_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_restaurant_status_restaurant` (`restaurant_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_photos_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `app_feedback`
--
ALTER TABLE `app_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `delivery_address`
--
ALTER TABLE `delivery_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dishes`
--
ALTER TABLE `dishes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_extra_info`
--
ALTER TABLE `order_extra_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `restaurant_status`
--
ALTER TABLE `restaurant_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_photos`
--
ALTER TABLE `user_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_feedback`
--
ALTER TABLE `app_feedback`
  ADD CONSTRAINT `app_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_address`
--
ALTER TABLE `delivery_address`
  ADD CONSTRAINT `delivery_address_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dishes`
--
ALTER TABLE `dishes`
  ADD CONSTRAINT `dishes_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_extra_info`
--
ALTER TABLE `order_extra_info`
  ADD CONSTRAINT `order_extra_info_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_extra_info_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`dish_id`) REFERENCES `dishes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `fk_payment_methods_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `restaurant_status`
--
ALTER TABLE `restaurant_status`
  ADD CONSTRAINT `fk_restaurant_status_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD CONSTRAINT `fk_user_photos_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
