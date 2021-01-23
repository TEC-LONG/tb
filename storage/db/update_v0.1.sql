CREATE TABLE `tl_shares_standard_statistics` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `statistics_rules__id` int unsigned NOT NULL DEFAULT 0 COMMENT '统计规则项名称表id',
    `type1` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '类型1；1=个股，2=所有股',
    `ma_type` smallint unsigned NOT NULL DEFAULT 0 COMMENT '均线类型；0=无，5=5日均线，后续以此类推；值包括：5,10,15,20,30,60,120,240',
    `period_type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '统计周期类型；0=所有,3=3年周期,5=5年周期,10=10年周期',
    `minv` int DEFAULT NULL COMMENT '极小值',
    `maxv` int DEFAULT NULL COMMENT '极大值',
    `descr` varchar(1000) NOT NULL DEFAULT '' COMMENT '统计描述',
    `content` text COMMENT '额外统计内容',
    `shares__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_statistics_rules__id` (`statistics_rules__id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='股票&行业板块中间表';