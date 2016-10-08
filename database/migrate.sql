CREATE TABLE IF NOT EXISTS `restaurant_chains` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)     NOT NULL,
  `created_at` TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `restaurant_accounts` (
  `id`                  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_chain_id` INT(10) UNSIGNED NOT NULL,
  `password_hash`       VARCHAR(255)     NOT NULL,
  `created_at`          TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`          TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `restaurant_accounts_restaurant_chain_id_fk`
  FOREIGN KEY (`restaurant_chain_id`)
  REFERENCES `restaurant_chains` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `restaurant_locations` (
  `id`                  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`                VARCHAR(255)     NOT NULL,
  `restaurant_chain_id` INT(10) UNSIGNED NOT NULL,
  `created_at`          TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`          TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `restaurant_locations_restaurant_chain_id_fk`
  FOREIGN KEY (`restaurant_chain_id`)
  REFERENCES `restaurant_chains` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `restaurant_tables` (
  `id`                     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `number`                 INT UNSIGNED     NOT NULL,
  `restaurant_location_id` INT(10) UNSIGNED NOT NULL,
  `created_at`             TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`             TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `restaurant_tables_restaurant_location_id_fk`
  FOREIGN KEY (`restaurant_location_id`)
  REFERENCES `restaurant_locations` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `bills` (
  `id`                  INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `restaurant_table_id` INT(10) UNSIGNED NOT NULL,
  `served_by`           VARCHAR(255)     NOT NULL,
  `created_at`          TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`          TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `payments_restaurant_table_id_fk`
  FOREIGN KEY (`restaurant_table_id`)
  REFERENCES `restaurant_tables` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `bill_items` (
  `id`          INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bill_id`     INT(10) UNSIGNED NOT NULL,
  `amount`      INT              NOT NULL DEFAULT '0',
  `description` VARCHAR(255)     NOT NULL,
  `created_at`  TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`  TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `payments_bill_id_fk`
  FOREIGN KEY (`bill_id`)
  REFERENCES `bills` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)     NOT NULL,
  `email`      VARCHAR(255)     NOT NULL,
  `created_at` TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `payment_providers` (
  `id`         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)     NOT NULL,
  `created_at` TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `payments` (
  `id`                     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `amount`                 INT              NOT NULL DEFAULT '0',
  `payment_provider_id`    INT(10) UNSIGNED NOT NULL,
  `restaurant_location_id` INT(10) UNSIGNED NOT NULL, # denormalised
  `bill_id`                INT(10) UNSIGNED NOT NULL,
  `user_id`                INT(10) UNSIGNED NOT NULL,
  `organisation`           VARCHAR(255)     NOT NULL,
  `last_4_digits`          CHAR(4)          NOT NULL,
  `fraud_risk`             VARCHAR(255)     NOT NULL,
  `device_os`              VARCHAR(255)     NULL     DEFAULT NULL,
  `device_model`           VARCHAR(255)     NULL     DEFAULT NULL,
  `created_at`             TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP,
  `updated_at`             TIMESTAMP                 DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`             TIMESTAMP        NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `payments_payment_provider_id_fk`
  FOREIGN KEY (`payment_provider_id`)
  REFERENCES `payment_providers` (`id`),
  CONSTRAINT `payments_restaurant_location_id_fk`
  FOREIGN KEY (`restaurant_location_id`)
  REFERENCES `restaurant_locations` (`id`),
  CONSTRAINT `payments_user_id_fk`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`),
  CONSTRAINT `payments_bill_id_foreign`
  FOREIGN KEY (`bill_id`)
  REFERENCES `bills` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;
