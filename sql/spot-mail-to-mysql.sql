CREATE TABLE IF NOT EXISTS `SpotMe2` (
  `id` smallint(6) NOT NULL auto_increment,
  `type` varchar(4) NOT NULL COMMENT 'Spot Msg Type',
  `lng` decimal(10,7) NOT NULL COMMENT 'Longitude',
  `lat` decimal(10,7) NOT NULL COMMENT 'Latitude',
  `msg` text NOT NULL COMMENT 'Message Body',
  `time` int(11) NOT NULL COMMENT 'Unix Timestamp',
  `tag` tinytext NOT NULL COMMENT 'Trip Tag',
  `img` text NOT NULL COMMENT 'Image',
  `notes` text NOT NULL COMMENT 'Notes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=274 ;
