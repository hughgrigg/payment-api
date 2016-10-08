USE `payment_api`;

INSERT INTO `restaurant_chains` (`id`, `name`) VALUES (123, 'Krusty Krab');


INSERT INTO `restaurant_locations` (
  `id`,
  `restaurant_chain_id`,
  `name`
) VALUES (
  456,
  123,
  'Under the Sea'
);

INSERT INTO `restaurant_accounts` (
  `id`,
  `restaurant_chain_id`,
  `password_hash`
) VALUES (
  789,
  123,
  '$2y$10$PnlQAqyGQCSl6iYxf4Jp0uGR.9WpqWRBjCRiyExvTlIZ4W0Y93H4u' # 'secret'
);

INSERT INTO `restaurant_tables` (`restaurant_location_id`, `number`)
VALUES (456, 5);

INSERT INTO `payment_providers` (`id`, `name`)
VALUES (123, 'Pineapple Payments Inc.');

INSERT INTO `users` (`id`, `name`, `email`)
VALUES (456, 'SpongeBob SquarePants', 'spongebob.squarepants@localhost');
