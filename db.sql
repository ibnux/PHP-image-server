
CREATE TABLE `images` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `md5` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `size` int(11) NOT NULL,
  `realname` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `path` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `content_type` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `upload_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;