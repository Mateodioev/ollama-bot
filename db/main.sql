CREATE TABLE IF NOT EXISTS `userRank` (`id` int PRIMARY KEY, `name` VARCHAR(20) NOT NULL);

CREATE TABLE
  IF NOT EXISTS `users` (
    `id` bigint PRIMARY KEY,
    `model` VARCHAR(20) NOT NULL,
    `rank` int NOT NULL,
    FOREIGN KEY (`rank`) REFERENCES `userRank` (`id`)
  );


INSERT INTO `userRank` (id, name)
SELECT * FROM (
  SELECT 1, 'Banned' UNION ALL
  SELECT 2, '2' UNION ALL
  SELECT 3, '3' UNION ALL
  SELECT 4, '4'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM `userRank`);