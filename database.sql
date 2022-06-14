-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 10, 2022 at 05:10 PM
-- Server version: 5.7.38-0ubuntu0.18.04.1
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance_history`
--

CREATE TABLE `balance_history` (
  `Id` int(11) NOT NULL,
  `difference` int(11) NOT NULL,
  `new_balance` int(11) NOT NULL,
  `date_operation` date NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `balance_history`
--

-- --------------------------------------------------------

--
-- Table structure for table `controllers`
--

CREATE TABLE `controllers` (
  `id` int(11) NOT NULL,
  `direction` enum('In','Out') NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `remarks` varchar(255) NOT NULL,
  `from_To` varchar(99) NOT NULL,
  `type` enum('Driver','Controller','Client') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `controllers`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `coupon_id` int(30) NOT NULL,
  `campaign` int(30) NOT NULL,
  `giver` int(30) NOT NULL,
  `receiver` int(30) NOT NULL,
  `coupon_code` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `status` enum('Ready','Used by Receiver','Used by Both') COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `coupons`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupon_campaigns`
--

CREATE TABLE `coupon_campaigns` (
  `campaign_id` int(30) NOT NULL,
  `name` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `Campaign_code` varchar(30) COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `coupon_campaigns`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customerNumber` int(11) NOT NULL,
  `customerName` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `phone` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `addressLine1` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `addressLine2` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `city` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `postalCode` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('Individual','Business') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `password_customers` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driverNumber` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `driverFirstName` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `driverLastName` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `driver_address1` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `driver_address2` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `driver_city` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT 'Tangier',
  `phone_number` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `driver_coordinates` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `driver_availability` text COLLATE latin1_general_ci NOT NULL,
  `last_paid` date DEFAULT NULL,
  `ratePerOrder` int(11) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `driver_email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `external` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `drivers`
--

-- --------------------------------------------------------

--
-- Table structure for table `ecom_orders`
--

CREATE TABLE `ecom_orders` (
  `id_order` int(11) NOT NULL,
  `order_customer` int(11) NOT NULL,
  `order_driver_assigned` int(11) DEFAULT NULL,
  `order_for` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_from` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_phone` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_items_cost` int(11) NOT NULL,
  `status` enum('New','Canceled','Dispatched','Picked up','Delivered','Out of Reach','Returned') COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci NOT NULL,
  `order_instructions` varchar(1000) COLLATE latin1_general_ci DEFAULT NULL,
  `order_address1` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_address2` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_city` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Tangier',
  `order_zipcode` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '90000',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_pickedup` timestamp NULL DEFAULT NULL,
  `date_delivered` timestamp NULL DEFAULT NULL,
  `order_delivery_cost` int(11) NOT NULL DEFAULT '20',
  `driver_delivery_cost` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_type` enum('Simple','Van') COLLATE latin1_general_ci NOT NULL,
  `order_source` enum('Phone','App','Whatsapp','Text Message','Email','Other') COLLATE latin1_general_ci NOT NULL,
  `OrderNo` int(11) DEFAULT NULL,
  `ProductId` bigint(20) DEFAULT NULL,
  `include_invoice` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `ecom_orders`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_number` int(11) NOT NULL,
  `invoice_client` int(11) DEFAULT NULL,
  `invoice_amount` double DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_status` enum('New','Sent','Paid','Cancelled') COLLATE latin1_general_ci DEFAULT NULL,
  `invoice_file_link` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `invoice_tva` tinyint(1) DEFAULT '0',
  `tva_invoice_file_link` varchar(255) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `invoices`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoices_ecom`
--

CREATE TABLE `invoices_ecom` (
  `invoice_ecom_number` int(11) NOT NULL,
  `invoice_ecom` int(11) DEFAULT NULL,
  `invoice_ecom_amount` double DEFAULT NULL,
  `invoice_ecom_date` date DEFAULT NULL,
  `invoice_ecom_status` enum('New','Sent','Paid','Cancelled') COLLATE latin1_general_ci DEFAULT NULL,
  `invoice_ecom_file_link` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `invoice_ecom_tva` tinyint(1) DEFAULT '0',
  `tva_invoice_ecom_file_link` varchar(255) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `invoices_ecom`
--
-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `order_customer` int(11) NOT NULL,
  `order_driver_assigned` int(11) DEFAULT NULL,
  `order_for` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_from` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_phone` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_items_cost` int(11) NOT NULL,
  `status` enum('New','Canceled','Dispatched','Picked up','Delivered') COLLATE latin1_general_ci NOT NULL,
  `description` text COLLATE latin1_general_ci NOT NULL,
  `order_instructions` varchar(1000) COLLATE latin1_general_ci DEFAULT NULL,
  `order_address1` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_address2` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `order_city` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT 'Tangier',
  `order_zipcode` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '90000',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_pickedup` timestamp NULL DEFAULT NULL,
  `date_delivered` timestamp NULL DEFAULT NULL,
  `order_delivery_cost` int(11) NOT NULL DEFAULT '20',
  `driver_delivery_cost` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_type` enum('Simple','Van') COLLATE latin1_general_ci NOT NULL,
  `order_source` enum('Phone','App','Whatsapp','Text Message','Email','API','Other') COLLATE latin1_general_ci NOT NULL,
  `coupon_code` varchar(199) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `orders`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductId` bigint(20) NOT NULL,
  `Name` varchar(128) NOT NULL,
  `customerNumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(128) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`) VALUES
(1, 'app_name', 'Amar'),
(2, 'app_address1', 'Résidence Mayssil, Num. 51'),
(3, 'app_address2', 'Rue Mansour Dahbi'),
(4, 'app_city', 'Tanger'),
(5, 'app_postalCode', '90000'),
(6, 'app_phone', '0644011011'),
(7, 'app_if', '15182588'),
(8, 'app_ice', '001849662000061'),
(9, 'app_rc', '66249'),
(10, 'app_patente', '50470832'),
(11, 'app_rib', '007 640 0005922000001014 47'),
(12, 'app_ReportsEmail', 'test@test.ma,test2@test.ma'),
(13, 'app_DriverPaymentNotificationEmail', 'test@test.ma,test2@test.ma'),
(14, 'app_InvoiceCounter', '57'),
(15, 'app_ReportCounter', '57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `first_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `last_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `phone_number` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `phone_number2` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `address` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `city` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `zipcode` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_ip` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `role` enum('Admin','Dispatcher','Controller') COLLATE latin1_general_ci DEFAULT 'Dispatcher'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `phone_number`, `phone_number2`, `address`, `city`, `zipcode`, `last_login`, `last_login_ip`, `role`) VALUES
(13, 'admin', 'c3284d0f94606de1fd2af172aba15bf3|2|e86cebe1', 'Admin', 'Admin', 'admin@test.ma', '+21200000000', '', '', '', '', '2021-06-07 11:56:32', '91.132.177.145', 'Admin'),

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance_history`
--
ALTER TABLE `balance_history`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `FK_balance_history_customers` (`customer_id`);

--
-- Indexes for table `controllers`
--
ALTER TABLE `controllers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `coupon_campaigns`
--
ALTER TABLE `coupon_campaigns`
  ADD PRIMARY KEY (`campaign_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customerNumber`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driverNumber`);

--
-- Indexes for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  ADD PRIMARY KEY (`id_order`),
  ADD UNIQUE KEY `OrderNo` (`OrderNo`),
  ADD KEY `FK_UserOrder` (`user_id`),
  ADD KEY `ProductId` (`ProductId`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_number`);

--
-- Indexes for table `invoices_ecom`
--
ALTER TABLE `invoices_ecom`
  ADD PRIMARY KEY (`invoice_ecom_number`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `FK_UserOrder` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductId`),
  ADD KEY `user_id` (`customerNumber`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `balance_history`
--
ALTER TABLE `balance_history`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `controllers`
--
ALTER TABLE `controllers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `coupon_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `coupon_campaigns`
--
ALTER TABLE `coupon_campaigns`
  MODIFY `campaign_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customerNumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=546;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driverNumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3213;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `invoices_ecom`
--
ALTER TABLE `invoices_ecom`
  MODIFY `invoice_ecom_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3244;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductId` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7896513;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance_history`
--
ALTER TABLE `balance_history`
  ADD CONSTRAINT `FK_balance_history_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customerNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `balance_history_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customerNumber`);

--
-- Constraints for table `ecom_orders`
--
ALTER TABLE `ecom_orders`
  ADD CONSTRAINT `FK_UserEcomOrder` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ecom_orders_ibfk_1` FOREIGN KEY (`ProductId`) REFERENCES `products` (`ProductId`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_UserOrder` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`customerNumber`) REFERENCES `customers` (`customerNumber`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
