CREATE TABLE IF NOT EXISTS `aa` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `as` int(11) NOT NULL,
  `qw` int(11) NOT NULL,
  `er` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
