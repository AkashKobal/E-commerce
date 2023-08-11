CREATE TABLE `wholesale_prices` (
  `id` int(11) NOT NULL,
  `product_stock_id` int(11) NOT NULL,
  `min_qty` int(11) NOT NULL DEFAULT '0',
  `max_qty` int(11) NOT NULL DEFAULT '0',
  `price` double(20,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wholesale_prices`
--
ALTER TABLE `wholesale_prices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wholesale_prices`
--
ALTER TABLE `wholesale_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;