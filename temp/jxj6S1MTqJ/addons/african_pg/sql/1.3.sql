ALTER TABLE `combined_orders` ADD `request` VARCHAR(190) NULL DEFAULT NULL AFTER `grand_total`;
ALTER TABLE `combined_orders` ADD `receipt` VARCHAR(190) NULL DEFAULT NULL AFTER `request`;

COMMIT;