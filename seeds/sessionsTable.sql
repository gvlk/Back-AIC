CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `accessToken` varchar(300) DEFAULT NULL,
  `refreshToken` varchar(300) DEFAULT NULL,
  `dueAccessToken` varchar(200) DEFAULT NULL,
  `dueRefreshToken` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `sessions`
  ADD CONSTRAINT `userSession` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;
