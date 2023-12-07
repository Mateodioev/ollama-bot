CREATE TABLE `users` (
  `id` int PRIMARY KEY,
  `model` int NOT NULL,
  `rank` int NOT NULL
);

CREATE TABLE `userRank` (
  `id` int PRIMARY KEY,
  `name` VARCHAR(20) NOT NULL
);

ALTER TABLE `users` ADD FOREIGN KEY (`model`) REFERENCES `ollamaModel` (`id`);
ALTER TABLE `users` ADD FOREIGN KEY (`rank`) REFERENCES `userRank` (`id`);

INSERT INTO `userRank` (id, name) VALUES (1, 'Banned');
INSERT INTO `userRank` (id, name) VALUES (2, '2');
INSERT INTO `userRank` (id, name) VALUES (3, '3');
INSERT INTO `userRank` (id, name) VALUES (4, '4');
