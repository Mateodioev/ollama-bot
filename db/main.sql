CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint PRIMARY KEY,
  `model` VARCHAR(20) NOT NULL,
  `rank` int NOT NULL
);

CREATE TABLE IF NOT EXISTS `userRank` (
  `id` int PRIMARY KEY,
  `name` VARCHAR(20) NOT NULL
);

ALTER TABLE `users` ADD FOREIGN KEY (`rank`) REFERENCES `userRank` (`id`);

DELETE FROM `userRank`;

INSERT INTO `userRank` (id, name) VALUES (1, 'Banned'),
                                         (2, '2'),
                                         (3, '3'),
                                         (4, '4');
