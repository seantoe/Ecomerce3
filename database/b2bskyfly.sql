-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2024 at 05:39 PM
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
-- Database: `b2bskyfly`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(30) NOT NULL,
  `order_cost` decimal(65,2) NOT NULL,
  `order_status` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `contact` text NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `proof_of_payment` varchar(255) NOT NULL,
  `id_team` int(11) NOT NULL,
  `id_appoint` int(11) NOT NULL,
  `appointed_to` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_cost`, `order_status`, `user_id`, `firstname`, `lastname`, `address`, `contact`, `order_date`, `proof_of_payment`, `id_team`, `id_appoint`, `appointed_to`) VALUES
(7, 7350.00, 'Paid', 7, 'Sean', 'Alvarez', 'Laguna', '09169978376', '2024-02-22 08:28:22', '', 6, 3, 'sales agent1'),
(8, 7700.00, 'Paid', 7, 'Sean', 'Alvarez', 'Laguna', '09169978376', '2024-02-22 08:28:51', '../images/15 (Whiteboard) (1).png', 6, 3, 'sales agent1'),
(10, 3500.00, 'Paid', 5, 'Cj', 'Morales', 'Pilipinas', '123123', '2024-02-24 04:50:14', '../images/15 (Whiteboard) (1).png', 6, 4, 'sales ahente2'),
(14, 3500.00, 'Pending', 53, 'badet', 'alvarez', 'Laguna', '123123123', '2024-02-28 17:34:34', '../images/Screenshot 2024-02-23 124526.png', 0, 0, ''),
(20, 1750.00, 'Not Paid', 53, 'badet', 'alvarez', 'Laguna', '123123123', '2024-03-01 08:27:08', '', 6, 6, 'team leader1'),
(21, 1750.00, 'Pending', 52, 'Aaron', 'Calle', 'Cavite', '123123', '2024-03-01 09:02:28', '../images/IMG_8726.PNG', 0, 0, 'No one appointed'),
(23, 1750.00, 'Not Paid', 53, 'badet', 'alvarez', 'Laguna', '123123123', '2024-03-01 14:16:17', '', 6, 3, 'sales agent1'),
(28, 5250.00, 'Paid', 5, 'Cj', 'Morales', 'Laguna', '123123', '2024-03-01 15:12:00', '', 6, 6, 'team leader1'),
(30, 1750.00, 'Not Paid', 52, 'Aaron', 'Calle', 'Cavite', '123123', '2024-03-02 06:39:37', '', 0, 0, 'No one appointed'),
(32, 4550.00, 'Pending', 5, 'Cj', 'Morales', 'Laguna', '123123', '2024-03-02 14:18:00', '../images/15 (Whiteboard) (1).png', 6, 6, 'team leader1'),
(53, 700.00, 'Not Paid', 5, 'Cj', 'Morales', 'Laguna', '123123', '2024-03-04 04:24:31', '', 6, 6, 'team leader1');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_price` decimal(6,2) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(255) NOT NULL,
  `id_team` int(11) NOT NULL,
  `id_appoint` int(11) NOT NULL,
  `appointed_to` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `product_name`, `product_image`, `product_price`, `product_quantity`, `user_id`, `order_date`, `order_status`, `id_team`, `id_appoint`, `appointed_to`) VALUES
(29, 7, 4, 'Menthol', '../images/menthol.png', 350.00, 21, 7, '2024-02-22 08:28:22', 'Paid', 6, 3, 'sales agent1'),
(30, 8, 6, 'Taro', '../images/taro.png', 350.00, 12, 7, '2024-02-22 08:28:51', 'Paid', 6, 3, 'sales agent1'),
(31, 8, 8, 'Mung Ice', '../images/mung ice.png', 350.00, 10, 7, '2024-02-22 08:28:51', 'Paid', 6, 3, 'sales agent1'),
(33, 10, 1, 'Watermelon', '../images/water.png', 350.00, 10, 5, '2024-02-24 04:50:14', 'Paid', 6, 4, 'sales ahente2'),
(39, 14, 7, 'Peach', '../images/peach.png', 350.00, 10, 53, '2024-02-28 17:34:34', '', 0, 0, ''),
(45, 20, 2, 'Strawberry', '../images/straw.png', 350.00, 5, 53, '2024-03-01 08:27:08', '', 6, 6, 'team leader1'),
(46, 21, 2, 'Strawberry', '../images/straw.png', 350.00, 5, 52, '2024-03-01 09:02:28', '', 0, 0, 'No one appointed'),
(48, 23, 2, 'Strawberry', '../images/straw.png', 350.00, 5, 53, '2024-03-01 14:16:17', '', 6, 3, 'sales agent1'),
(49, 24, 2, 'Strawberry', '../images/straw.png', 350.00, 5, 5, '2024-03-01 15:04:05', '', 6, 6, 'team leader1'),
(50, 28, 1, 'Watermelon', '../images/water.png', 350.00, 10, 5, '2024-03-01 15:12:00', '', 6, 6, 'team leader1'),
(51, 28, 4, 'Menthol', '../images/menthol.png', 350.00, 5, 5, '2024-03-01 15:12:00', '', 6, 6, 'team leader1'),
(53, 30, 1, 'Watermelon', '../images/water.png', 350.00, 5, 52, '2024-03-02 06:39:37', '', 0, 0, 'No one appointed'),
(56, 32, 1, 'Watermelon', '../images/water.png', 350.00, 8, 5, '2024-03-02 14:18:00', '', 6, 6, 'team leader1'),
(57, 32, 2, 'Strawberry', '../images/straw.png', 350.00, 5, 5, '2024-03-02 14:18:00', '', 6, 6, 'team leader1'),
(78, 53, 1, 'Watermelon', '../images/water.png', 350.00, 2, 5, '2024-03-04 04:24:31', '', 6, 6, 'team leader1');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `product_quantity` int(11) DEFAULT NULL,
  `product_price` int(30) NOT NULL,
  `product_category` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_description`, `product_image`, `product_quantity`, `product_price`, `product_category`, `date_created`, `date_updated`) VALUES
(1, 'Watermelon', 'description sample 123', '../images/water.png', 72, 350, 'Great-es', '2024-02-28 02:51:40', '2024-03-02 22:11:13'),
(2, 'Strawberry', 'description sample 123', '../images/straw.png', 80, 350, 'Great-es', '2024-02-28 02:52:09', '2024-03-03 13:58:59'),
(3, 'Caramel Popcorn', 'description sample 123', '../images/caramel popcorn.png', 88, 350, 'Great-es', '2024-02-28 02:52:18', '2024-02-28 02:52:18'),
(4, 'Menthol', 'description sample 123', '../images/menthol.png', 100, 350, 'Great-es', '2024-02-28 02:52:26', '2024-02-28 02:52:26'),
(5, 'White Peach Oolong', 'description sample 123', '../images/white peach oolong.png', 10, 350, 'Great-es', '2024-02-28 02:52:37', '2024-02-28 02:52:37'),
(6, 'Taro', 'description sample 123', '../images/taro.png', 100, 350, 'Great-es', '2024-02-28 02:52:47', '2024-02-28 02:52:47'),
(7, 'Peach', 'description sample 123', '../images/peach.png', 75, 350, 'Great-es', '2024-02-28 02:52:58', '2024-02-29 00:34:56'),
(8, 'Mung Ice', 'description sample 123', '../images/mung ice.png', 85, 350, 'Great-es', '2024-02-28 02:53:04', '2024-02-28 02:53:04');

-- --------------------------------------------------------

--
-- Table structure for table `products_category`
--

CREATE TABLE `products_category` (
  `id_category` int(11) NOT NULL,
  `product_category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_category`
--

INSERT INTO `products_category` (`id_category`, `product_category`) VALUES
(1, 'Great-es'),
(3, 'Team-X');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) NOT NULL,
  `image1` varchar(255) NOT NULL,
  `image2` varchar(255) NOT NULL,
  `contact1` text DEFAULT NULL,
  `contact2` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `title`, `description`, `logo`, `image1`, `image2`, `contact1`, `contact2`) VALUES
(1, 'GREAT-ES', ' Established in Philippines with strong R&amp;D background, SKYFLY MICROENTERPRISES VAPE SHOP has been researching and exporting e-cigarettes since 2020.Featuring innovative design, a wide variety of flavors and consistent premium quality, our products have gained great reputation in overseas market. Our brand is well known in the Philippines. We only use top quality E-liquid and raw materials in the production to ensure best user experience.We also have a sales team in the Middle East that helps to expand our market from Asia to the Middle East, and all the way to Europe. Now we’re looking for distributors and partners worldwide. ', '../images/logo.png', '../images/1000_F_565687895_Ij4RPuaaDJLYpUvy3wiGJk9jULms9ejA.jpg', '../images/war.png', 'Tel: (02)8688-9329', 'Add.: Prestige tower, 16th floor, 1605, F. Ortigas Jr. Ave, Ortigas, Manila');

-- --------------------------------------------------------

--
-- Table structure for table `settings2`
--

CREATE TABLE `settings2` (
  `id` int(11) NOT NULL,
  `about1` text NOT NULL,
  `about2` text NOT NULL,
  `about3` text NOT NULL,
  `about4` text NOT NULL,
  `image1` varchar(255) NOT NULL,
  `image2` varchar(255) NOT NULL,
  `image3` varchar(255) NOT NULL,
  `image4` varchar(255) NOT NULL,
  `image5` varchar(255) NOT NULL,
  `image6` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings2`
--

INSERT INTO `settings2` (`id`, `about1`, `about2`, `about3`, `about4`, `image1`, `image2`, `image3`, `image4`, `image5`, `image6`) VALUES
(1, 'Why Choose Us?', 'Established in Philippines with strong R&D background, SKYFLY MICROENTERPRISES VAPE SHOP has been researching and exporting e-cigarettes since 2020.', 'Featuring innovative design, a wide variety of flavors and consistent premium quality, our products have gained great reputation in overseas market. Our brand is well known in the Philippines. We only use top quality E-liquid and raw materials in the production to ensure best user experience.', 'Established in Philippines with strong R&D background, SKYFLY MICROENTERPRISES VAPE SHOP has been researching and exporting e-cigarettes since 2020.', '../images/z.png', '../images/w.jpg', '../images/q.jpg', '../images/e.png', '../images/d.png', '../images/a.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `access_level` int(11) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `id_appoint` int(11) NOT NULL,
  `id_team` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `contact` varchar(255) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `pfp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `access_level`, `position`, `id_appoint`, `id_team`, `firstname`, `lastname`, `address`, `contact`, `valid_id`, `pfp`) VALUES
(1, 'admincj', '202cb962ac59075b964b07152d234b70', 1, 'Admin', 0, 0, 'CJ', 'Morales', '', '1231230913', '', '../images/rizal.jpg'),
(2, 'adminsean', '202cb962ac59075b964b07152d234b70', 1, 'Admin', 0, 0, 'Sean', 'Alvarez', '', '09169978376', '', '../images/pfp.jpg'),
(3, 'sales1', '202cb962ac59075b964b07152d234b70', 4, 'Sales Agent', 0, 6, 'sales', 'agent1', 'Laguna', '123123', '', '../images/finger of death.png'),
(4, 'sales2', '202cb962ac59075b964b07152d234b70', 4, 'Sales Agent', 0, 6, 'sales', 'ahente2', 'Laguna', '123123123', '', ''),
(5, 'usercj', 'e10adc3949ba59abbe56e057f20f883e', 5, 'Client', 6, 6, 'Cj', 'Morales', 'Laguna', '123123', '', '../images/WIN_20240223_10_22_33_Pro.jpg'),
(6, 'team1', '202cb962ac59075b964b07152d234b70', 3, 'Team Leader', 0, 6, 'team', 'leader1', 'Laguna', '09123123', '', '../images/Add a little bit of body text.png'),
(7, 'usersean', '202cb962ac59075b964b07152d234b70', 5, 'Client', 3, 0, 'Sean', 'Alvarez', 'Laguna', '09169978376', '', '../images/dabbing-dog-cool-dabbing-pug-eq-designs.jpg'),
(52, 'useraaron', '202cb962ac59075b964b07152d234b70', 5, 'Client', 0, 0, 'Aaron', 'Calle', 'Cavite', '123123', '', '../images/drstrange.png'),
(53, 'userbadet', '202cb962ac59075b964b07152d234b70', 5, 'Client', 6, 6, 'badet', 'alvarez', 'Laguna', '123123123', '', '../images/Screenshot 2024-02-21 190041.png'),
(70, 'team2', '202cb962ac59075b964b07152d234b70', 3, 'Team Leader', 0, 70, 'team', 'leader2', 'Cavite', '123', '', '../images/b31da43778a488de959eef0695f9e161.jpg'),
(71, 'salescav1', '202cb962ac59075b964b07152d234b70', 4, 'Sales Agent', 0, 70, 'sales', 'cavite1', 'Cavite', '123', '', ''),
(72, 'salescav2', '202cb962ac59075b964b07152d234b70', 4, 'Sales Agent', 0, 70, 'sales', 'cavite2', 'Cavite', '12312313', '', ''),
(75, 'usercyrill', '202cb962ac59075b964b07152d234b70', 5, 'Client', 0, 0, 'Cyrill', 'Manaog', 'Cavite', '9123', '../images/IMG_8726.PNG', '');

-- --------------------------------------------------------

--
-- Table structure for table `users_ban`
--

CREATE TABLE `users_ban` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_ban`
--

INSERT INTO `users_ban` (`id`, `email`) VALUES
(3, 'userangel');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `product_name` (`product_name`);

--
-- Indexes for table `products_category`
--
ALTER TABLE `products_category`
  ADD PRIMARY KEY (`id_category`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings2`
--
ALTER TABLE `settings2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users_ban`
--
ALTER TABLE `users_ban`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products_category`
--
ALTER TABLE `products_category`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings2`
--
ALTER TABLE `settings2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `users_ban`
--
ALTER TABLE `users_ban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
