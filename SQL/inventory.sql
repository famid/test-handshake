/*Table structure for table `inventory` */

DROP TABLE IF EXISTS `inventory`;

CREATE TABLE `inventory` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) NOT NULL,
  `variant` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` double(10,2) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `product_stock_id` bigint(20) NOT NULL,
  `batch_no` varchar(255) DEFAULT NULL,
  `warehouse_owner_id` bigint(20) NOT NULL,
  `bin_location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
