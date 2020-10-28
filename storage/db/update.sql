CREATE TABLE `shares` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `title` varchar(50) NOT NULL DEFAULT '' COMMENT '股票中文名称',
    `code` varchar(30) NOT NULL DEFAULT '' COMMENT '股票代码',
    `type` tinyint NOT NULL DEFAULT 0 COMMENT '股票类型，0=未知；1=深市；2=沪市；',
    `belongs_to` tinyint NOT NULL DEFAULT 0 COMMENT '股票归属；1=沪市A股；2=深圳A股；3=深市中小板；4=创业板',
    `type_unknow_record` varchar(255) NOT NULL DEFAULT '' COMMENT '如果type值为0，则本字段记录下原始传入的type值',
    `issue_date` varchar(30) NOT NULL DEFAULT '' COMMENT '发行日期',
    `issue_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '发行日期15:00:00时间的时间戳',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    `sdb_last_update_time` int NOT NULL DEFAULT 0 COMMENT '股票每天详情数据（shares_details_byday）最后更新时间（以更新时间为准）',
    `company_name` varchar(100) NOT NULL DEFAULT '' COMMENT '公司名称',
    `company_name_once_used` varchar(100) NOT NULL DEFAULT '' COMMENT '公司曾用名',
    `sw_cate` varchar(100) NOT NULL DEFAULT '' COMMENT '所属申万行业',
    `main_business` varchar(255) NOT NULL DEFAULT '' COMMENT '主营业务',
    `product_names` varchar(255) NOT NULL DEFAULT '' COMMENT '产品名称',
    `intro` varchar(1000) NOT NULL DEFAULT '' COMMENT '公司简介',
    `is_explode_cate` tinyint NOT NULL DEFAULT 0 COMMENT '是否已拆分分类；0=否；1=是',
    `explode_cate_time` int NOT NULL DEFAULT 0 COMMENT '拆分分类时间',
    `cate_1` varchar(10) NOT NULL DEFAULT '' COMMENT '所属申万行业分类1',
    `cate_2` varchar(10) NOT NULL DEFAULT '' COMMENT '所属申万行业分类2',
    `province` varchar(10) NOT NULL DEFAULT '' COMMENT '所在省份',
    `is_deprecated` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否弃用；0=否；1=是',
    PRIMARY KEY (`id`),
    KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票信息表';


CREATE TABLE `plate` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `name` varchar(30) NOT NULL DEFAULT '' COMMENT '板块名称',
    `code` varchar(30) NOT NULL DEFAULT '' COMMENT '板块代码',
    `type` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '板块类型；0=无；1=行业板块；2=概念板块；3=地区板块',
    `come_from` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '板块来源参照；0=自定义1；1=东海通；2=益盟-智盈；3=通达信; 4=同花顺1',
    `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '同概念别名，多个以逗号分隔',
    `pid` int unsigned NOT NULL DEFAULT 0 COMMENT '上级id',
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票行业板块表';


CREATE TABLE `shares__plate` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `plate__id` int unsigned NOT NULL DEFAULT 0 COMMENT '行业板块表id'
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_plate__id` (`plate__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票&行业板块中间表';


CREATE TABLE `plate_prosperity_index_statistics_day` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `plate__id` tinyint NOT NULL DEFAULT 0 COMMENT '行业板块表id',
    `numb` varchar(30) NOT NULL DEFAULT '' COMMENT '指数值',
    `plate_volume` varchar(50) NOT NULL DEFAULT '' COMMENT '板块总成交量',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情日期,格式：YYYY-mm-dd',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情日期15:00:00时间的时间戳',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_plate__id` (`plate__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='板块每日繁荣指数统计表';


CREATE TABLE `plate_prosperity_index_statistics_month` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `plate__id` tinyint NOT NULL DEFAULT 0 COMMENT '行业板块表id',
    `numb` varchar(30) NOT NULL DEFAULT '' COMMENT '指数值',
    `plate_volume` varchar(50) NOT NULL DEFAULT '' COMMENT '板块总成交量',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '年月值,格式：YYYY-mm',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '年月值YYYY-mm-01 15:00:00时间的时间戳',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_plate__id` (`plate__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='板块每月繁荣指数统计表';


CREATE TABLE `shares_details_byday` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `original_data` varchar(1000) NOT NULL DEFAULT '' COMMENT '原始数据',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情产生日期',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情产生日期15:00:00时间的时间戳',
    `day_start_price` varchar(20) NOT NULL DEFAULT '' COMMENT '当日开盘价',
    `day_end_price` varchar(20) NOT NULL DEFAULT '' COMMENT '当日收盘价',
    `day_max_price` varchar(20) NOT NULL DEFAULT '' COMMENT '当日最高价',
    `day_min_price` varchar(20) NOT NULL DEFAULT '' COMMENT '当日最低价',
    `last_day_end_price` varchar(20) NOT NULL DEFAULT '' COMMENT '昨日收盘价',
    `uad_price` varchar(20) NOT NULL DEFAULT '' COMMENT '涨跌额(up and down price)',
    `uad_range` varchar(20) NOT NULL DEFAULT '' COMMENT '涨跌幅(up and down range)',
    `volume` varchar(50) NOT NULL DEFAULT '' COMMENT '成交量',
    `transaction_amount` varchar(50) NOT NULL DEFAULT '' COMMENT '成交金额',
    `step` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '所处步骤位置；0=无；1=拆分完原始数据',
    `is_year_xingao` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '当日最高价是否创过去一年新高，0=否；1=是',
    `has_statistics_year_xingao` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否已统计一年新高指标，0=否；1=是',
    `is_year_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '当日最低价是否创过去一年新低，0=否；1=是',
    `has_statistics_year_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否已统计一年新低指标，0=否；1=是',
    `is_3month_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '当日最低价是否创过去一个季度新低，0=否；1=是',
    `has_statistics_3month_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否已统计一个季度新低指标，0=否；1=是',
    `is_month_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '当日最低价是否创过去一个月新低，0=否；1=是',
    `has_statistics_month_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否已统计一个月新低指标，0=否；1=是',
    `is_5day_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '当日最低价是否创过去5日新低，0=否；1=是',
    `has_statistics_5day_xindi` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '是否已统计5日新低指标，0=否；1=是',
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    `channel` tinyint unsigned NOT NULL DEFAULT 1 COMMENT '数据渠道，1=网易；2=凤凰财经',
    `total_shizhi` varchar(50) NOT NULL DEFAULT '' COMMENT '总市值',
    `deal_shizhi` varchar(50) NOT NULL DEFAULT '' COMMENT '成交市值',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_active_date_timestamp` (`active_date_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票详情表（按天记录)';


CREATE TABLE `daily_weight_ths1` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `sdb__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票详情表（按天记录）id',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情产生日期',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情产生日期15:00:00时间的时间戳',
    `weight_c1` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '所属一级板块中的当日市值权重',
    `weight_c2` tinyint unsigned NOT NULL DEFAULT 0 COMMENT '所属二级板块中的当日市值权重',
    `day_start_price` varchar(20) NOT NULL DEFAULT '' COMMENT '当日开盘价',
    `uad_price` varchar(20) NOT NULL DEFAULT '' COMMENT '涨跌额(up and down price)',
    `uad_range` varchar(20) NOT NULL DEFAULT '' COMMENT '涨跌幅(up and down range)',
    `volume` varchar(50) NOT NULL DEFAULT '' COMMENT '成交量',
    `transaction_amount` varchar(50) NOT NULL DEFAULT '' COMMENT '成交金额',
    `total_shizhi` varchar(50) NOT NULL DEFAULT '' COMMENT '总市值',
    `deal_shizhi` varchar(50) NOT NULL DEFAULT '' COMMENT '成交市值',
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_sdb__id` (`sdb__id`),
    KEY `idx_active_date_timestamp` (`active_date_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日个股权重表（按同花顺1板块分类统计）';

insert into daily_weight_ths1 (shares__id, sdb__id, active_date, active_date_timestamp, day_start_price, uad_price, uad_range, volume, transaction_amount, total_shizhi, deal_shizhi, created_time) (select sdb.shares__id, sdb.id, sdb.active_date, sdb.active_date_timestamp, sdb.day_start_price, sdb.uad_price, sdb.uad_range, sdb.volume, sdb.transaction_amount, sdb.total_shizhi, sdb.deal_shizhi, sdb.created_time from shares_details_byday sdb left join shares s on sdb.shares__id=s.id where s.sdb_last_update_time<>0)


CREATE TABLE `daily_index_ths1` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `plate__id` int unsigned NOT NULL DEFAULT 0 COMMENT '',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情产生日期',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情产生日期15:00:00时间的时间戳',
    `day_end_index` smallint unsigned NOT NULL DEFAULT '' COMMENT '当日收盘板块指数',
    `day_max_index` smallint unsigned NOT NULL DEFAULT '' COMMENT '当日最高板块指数',
    `day_min_index` smallint unsigned NOT NULL DEFAULT '' COMMENT '当日最低板块指数',
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_plate__id` (`plate__id`),
    KEY `idx_active_date_timestamp` (`active_date_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日板块指数表（按同花顺1板块分类统计）';


CREATE TABLE `sdb_statistics` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `shares_details_byday__id` int NOT NULL DEFAULT 0 COMMENT '股票详情表id',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情产生日期',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情产生日期15:00:00时间的时间戳',
    `volume_multiply_price` varchar(60) NOT NULL DEFAULT '' COMMENT '量价积',
    `elongation` varchar(60) NOT NULL DEFAULT '' COMMENT '成交量伸缩率',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_shares_details_byday__id` (`shares_details_byday__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票详情每日统计表';


CREATE TABLE `sdb_statistics_price_deviate_probability` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `shares_details_byday__id` int NOT NULL DEFAULT 0 COMMENT '股票详情表id',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情产生日期',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情产生日期15:00:00时间的时间戳',
    `sdb_max` varchar(10) NOT NULL DEFAULT '' COMMENT '历史最高价偏离率',
    `sdb_month` varchar(10) NOT NULL DEFAULT '' COMMENT '当月最高价偏离率',
    `sdb_quarter` varchar(10) NOT NULL DEFAULT '' COMMENT '当季最高价偏离率',
    `sdb_halfyear` varchar(10) NOT NULL DEFAULT '' COMMENT '当前所处半年周期最高价偏离率',
    `sdb_year` varchar(10) NOT NULL DEFAULT '' COMMENT '当前所处一年周期最高价偏离率',
    `d5` varchar(10) NOT NULL DEFAULT '' COMMENT '最近5天最高价偏离率',
    `d10` varchar(10) NOT NULL DEFAULT '' COMMENT '最近10天最高价偏离率',
    `d15` varchar(10) NOT NULL DEFAULT '' COMMENT '最近15天最高价偏离率',
    `d20` varchar(10) NOT NULL DEFAULT '' COMMENT '最近20天最高价偏离率',
    `d22` varchar(10) NOT NULL DEFAULT '' COMMENT '最近22天最高价偏离率',
    `d30` varchar(10) NOT NULL DEFAULT '' COMMENT '最近30天最高价偏离率',
    `d60` varchar(10) NOT NULL DEFAULT '' COMMENT '最近60天最高价偏离率',
    `d120` varchar(10) NOT NULL DEFAULT '' COMMENT '最近120天最高价偏离率',
    `d240` varchar(10) NOT NULL DEFAULT '' COMMENT '最近240天最高价偏离率',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_shares_details_byday__id` (`shares_details_byday__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日详情量价偏离率统计表';


CREATE TABLE `sdb_statistics_moving_average` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `shares_details_byday__id` int unsigned NOT NULL DEFAULT 0 COMMENT '股票详情表id',
    `active_date` varchar(30) NOT NULL DEFAULT '' COMMENT '行情产生日期',
    `active_date_timestamp` int unsigned NOT NULL DEFAULT 0 COMMENT '行情产生日期15:00:00时间的时间戳',
    `ma5_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '5日均线角(moving average of 5 days)(横轴一个时间单位为10，纵轴价差百分比*100)',
    `ma10_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '10日均线角(moving average of 10 days)',
    `ma15_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '15日均线角(moving average of 15 days)',
    `ma20_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '20日均线角(moving average of 20 days)',
    `ma30_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '30日均线角(moving average of 30 days)',
    `ma60_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '60日均线角(moving average of 60 days)',
    `ma120_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '120日均线角(moving average of 120 days)',
    `ma240_angle` varchar(30) NOT NULL DEFAULT '' COMMENT '240日均线角(moving average of 240 days)',
    `ma_angle_time` int unsigned NOT NULL DEFAULT 0 COMMENT '均线角统计日期时间戳',
    `ma5_price` varchar(30) NOT NULL DEFAULT '' COMMENT '5日均价',
    `ma10_price` varchar(30) NOT NULL DEFAULT '' COMMENT '10日均价',
    `ma15_price` varchar(30) NOT NULL DEFAULT '' COMMENT '15日均价',
    `ma20_price` varchar(30) NOT NULL DEFAULT '' COMMENT '20日均价',
    `ma30_price` varchar(30) NOT NULL DEFAULT '' COMMENT '30日均价',
    `ma60_price` varchar(30) NOT NULL DEFAULT '' COMMENT '60日均价',
    `ma120_price` varchar(30) NOT NULL DEFAULT '' COMMENT '120日均价',
    `ma240_price` varchar(30) NOT NULL DEFAULT '' COMMENT '240日均价',
    `ma_price_time` int unsigned NOT NULL DEFAULT 0 COMMENT '均价统计日期时间戳',
    `created_time` int unsigned NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_shares_details_byday__id` (`shares_details_byday__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日详情均线相关统计表';


truncate shares;
truncate shares_details_byday;

alter table shares_details_byday ADD INDEX `idx_shares__id` (`shares__id`) USING BTREE;



/* 年新高 */
SELECT
	 (@i:=@i+1) as '序号',
	t.* 
FROM
    (select @i:=0) as it,
	(
SELECT
	s.`code` AS `股票代码`,
	s.title AS `名称`,
	s.company_name AS `公司全称`,
	p1.name AS `分类(一级)`,
	p.name AS `分类(二级)`,
	sdb.day_max_price as `年新高价格`,
    sdb.active_date,
    p1.id as p1_id,
    p.id as p2_id
FROM
	shares AS s
	LEFT JOIN shares_details_byday sdb ON sdb.shares__id = s.id 
	LEFT JOIN shares__plate sp ON s.id = sp.shares__id 
	LEFT JOIN plate p ON sp.plate__id = p.id
	LEFT JOIN (select * from plate where 1) as p1 ON p1.id = p.pid
WHERE
	sdb.active_date_timestamp = '1603465200' 
	AND sdb.is_year_xingao = 1 
group by s.id
order by p1.id, p.id
	) AS t;

/* 年新低 */
SELECT
	 (@i:=@i+1) as '序号',
	t.* 
FROM
    (select @i:=0) as it,
	(
SELECT
	s.`code` AS `股票代码`,
	s.title AS `名称`,
	s.company_name AS `公司全称`,
	p1.name AS `分类(一级)`,
	p.name AS `分类(二级)`,
	sdb.day_min_price as `年新低价格`,
	sdb.active_date,
    p1.id as p1_id,
    p.id as p2_id
FROM
	shares AS s
	LEFT JOIN shares_details_byday sdb ON sdb.shares__id = s.id 
	LEFT JOIN shares__plate sp ON s.id = sp.shares__id 
	LEFT JOIN plate p ON sp.plate__id = p.id
	LEFT JOIN (select * from plate where 1) as p1 ON p1.id = p.pid
WHERE
	sdb.active_date_timestamp = '1603810800' 
	AND sdb.is_year_xindi = 1 
group by s.id
order by p1.id, p.id
	) AS t;

/* 月新低 */
SELECT
	 (@i:=@i+1) as '序号',
	t.* 
FROM
    (select @i:=0) as it,
	(
SELECT
	s.`code` AS `股票代码`,
	s.title AS `名称`,
	s.company_name AS `公司全称`,
	p1.name AS `分类(一级)`,
	p.name AS `分类(二级)`,
	sdb.day_min_price as `月新低价格`,
	sdb.active_date,
    p1.id as p1_id,
    p.id as p2_id
FROM
	shares AS s
	LEFT JOIN shares_details_byday sdb ON sdb.shares__id = s.id 
	LEFT JOIN shares__plate sp ON s.id = sp.shares__id 
	LEFT JOIN plate p ON sp.plate__id = p.id
	LEFT JOIN (select * from plate where 1) as p1 ON p1.id = p.pid
WHERE
	sdb.active_date_timestamp = '1603810800' 
	AND sdb.is_month_xindi = 1 
group by s.id
order by p1.id, p.id
	) AS t;

/* 季度新低 */
SELECT
	 (@i:=@i+1) as '序号',
	t.* 
FROM
    (select @i:=0) as it,
	(
SELECT
	s.`code` AS `股票代码`,
	s.title AS `名称`,
	s.company_name AS `公司全称`,
	p1.name AS `分类(一级)`,
	p.name AS `分类(二级)`,
	sdb.day_min_price as `季新低价格`,
	sdb.active_date,
    p1.id as p1_id,
    p.id as p2_id
FROM
	shares AS s
	LEFT JOIN shares_details_byday sdb ON sdb.shares__id = s.id 
	LEFT JOIN shares__plate sp ON s.id = sp.shares__id 
	LEFT JOIN plate p ON sp.plate__id = p.id
	LEFT JOIN (select * from plate where 1) as p1 ON p1.id = p.pid
WHERE
	sdb.active_date_timestamp = '1603810800' 
	AND sdb.is_3month_xindi = 1 
group by s.id
order by p1.id, p.id
	) AS t;




SELECT
	active_date,
	day_end_price,
	day_max_price,
	day_min_price,
	is_year_xingao,
	is_year_xindi,
	is_3month_xindi,
	is_month_xindi,
	is_5day_xindi 
FROM
	shares_details_byday 
WHERE
	shares__id = 1 
ORDER BY
	active_date_timestamp;
	
update shares_details_byday set has_statistics_year_xindi=0, has_statistics_3month_xindi=0, has_statistics_month_xindi=0, has_statistics_5day_xindi=0 where shares__id<100;

update shares_details_byday set has_statistics_year_xingao=0 where 1;


insert into plate
(name, code, type, come_from)
values
('船舶制造', '991304', 1, 1),
('航天航空', '991309', 1, 1),
('电子元件', '991006', 1, 1),
('安防设备', '991300', 1, 1),
('纺织服饰', '991008', 1, 1),
('酒类', '991020', 1, 1),
('医疗类', '991028', 1, 1),
('专业设备', '991137', 1, 1),
('食品饮料', '991013', 1, 1),
('化工类', '991011', 1, 1),
('家电类', '991014', 1, 1),
('木业家具', '991024', 1, 1),
('造纸印刷', '991035', 1, 1),
('仪器仪表', '991033', 1, 1),
('材料类', '991303', 1, 1),
('医药制造', '991316', 1, 1),
('农牧饲鱼', '991021', 1, 1),
('玻璃陶瓷', '991302', 1, 1),
('包装材料', '991301', 1, 1),
('电子信息', '991147', 1, 1),
('水泥建材', '991313', 1, 1),
('通讯类', '991314', 1, 1),
('软件服务', '991004', 1, 1),
('金属制品', '991143', 1, 1),
('机械类', '991027', 1, 1),
('交运物流', '991136', 1, 1),
('农药兽药', '991311', 1, 1),
('塑胶制品', '991015', 1, 1),
('综合类', '991025', 1, 1),
('电信网络', '991135', 1, 1),
('保险类', '991255', 1, 1),
('文教休闲', '991315', 1, 1),
('输配电气', '991312', 1, 1),
('汽车类', '991140', 1, 1),
('交运设备', '991026', 1, 1),
('化纤类', '991310', 1, 1),
('旅游酒店', '991018', 1, 1),
('银行类', '991017', 1, 1),
('贵金属', '991308', 1, 1),
('电力', '991003', 1, 1),
('民航机场', '991318', 1, 1),
('有色', '991034', 1, 1),
('环保工程', '991139', 1, 1),
('公用事业', '991010', 1, 1),
('工程建设', '991306', 1, 1),
('化肥类', '991138', 1, 1),
('钢铁类', '991009', 1, 1),
('房地产', '991007', 1, 1),
('工艺商品', '991307', 1, 1),
('珠宝首饰', '991317', 1, 1),
('文化传媒', '991032', 1, 1),
('港口水运', '991305', 1, 1),
('高速公路', '991016', 1, 1),
('装修装饰', '991141', 1, 1),
('煤炭', '991019', 1, 1),
('国际贸易', '991142', 1, 1),
('商业百货', '991031', 1, 1),
('券商类', '991036', 1, 1),
('石油类', '991144', 1, 1),
('园林工程', '991002', 1, 1),
('多元金融', '991289', 1, 1);


update shares set tmp='建筑-基础建设-水利建设' where code='600068';
update shares set tmp='互联网-互联网服务-综合互联网服务' where code='600070';
update shares set tmp='电子设备-光电子器件-光学元件' where code='600071';
update shares set tmp='建筑-建筑施工-专业工程' where code='600072';
update shares set tmp='食品饮料-食品-食品综合' where code='600073';
update shares set tmp='基础化工-化学原料-氯碱' where code='600075';
update shares set tmp='农林牧渔-林业-木材加工' where code='600076';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600077';
update shares set tmp='基础化工-化学原料-磷化工' where code='600078';
update shares set tmp='医药生物-化学制药-化学制剂' where code='600079';
update shares set tmp='医药生物-生物医药-生物医药' where code='600080';
update shares set tmp='交运设备-汽车-汽车零部件' where code='600081';
update shares set tmp='房地产-房地产开发-园区开发' where code='600082';
update shares set tmp='建筑-建筑施工-专业工程' where code='600083';
update shares set tmp='食品饮料-饮料-葡萄酒' where code='600084';
update shares set tmp='医药生物-中药生产-中药生产' where code='600085';
update shares set tmp='轻工制造-珠宝首饰-珠宝首饰' where code='600086';
update shares set tmp='文化传媒-影视动漫-影视' where code='600088';
update shares set tmp='电气设备-输变电设备-其他输变电' where code='600089';
update shares set tmp='医药生物-医药商业-医药商业' where code='600090';
update shares set tmp='基础化工-化学原料-氯碱' where code='600091';
update shares set tmp='金融-非银行金融-其他非银行金融' where code='600093';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600094';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600095';
update shares set tmp='基础化工-化肥农药-磷肥' where code='600096';
update shares set tmp='农林牧渔-渔业-海洋渔业' where code='600097';
update shares set tmp='公用事业-电力-火电' where code='600098';
update shares set tmp='机械设备-通用设备-其他通用机械' where code='600099';
update shares set tmp='信息技术-计算机硬件-PC、服务器及硬件' where code='600100';
update shares set tmp='公用事业-电力-水电' where code='600101';
update shares set tmp='轻工制造-造纸印刷-造纸' where code='600103';
update shares set tmp='交运设备-汽车-乘用车' where code='600104';
update shares set tmp='信息技术-通信设备-通信传输设备' where code='600105';
update shares set tmp='交通运输-公路铁路-高速公路' where code='600106';
update shares set tmp='纺织服装-服装家纺-服装' where code='600107';
update shares set tmp='农林牧渔-农业-种植业' where code='600108';
update shares set tmp='金融-非银行金融-证券' where code='600109';
update shares set tmp='电气设备-输变电设备-其他输变电设备' where code='600110';
update shares set tmp='有色金属-稀有金属-稀土' where code='600111';
update shares set tmp='电气设备-输变电设备-其他输变电设备' where code='600112';
update shares set tmp='商贸零售-商业物业经营-专业市场' where code='600113';
update shares set tmp='机械设备-金属制品-金属制品' where code='600114';
update shares set tmp='交通运输-航空机场-航空' where code='600115';
update shares set tmp='公用事业-电力-水电' where code='600116';
update shares set tmp='钢铁-钢铁-特钢' where code='600117';
update shares set tmp='信息技术-卫星应用-卫星应用' where code='600118';
update shares set tmp='交通运输-物流-物流' where code='600119';
update shares set tmp='金融-非银行金融-其他非银行金融' where code='600120';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600121';
update shares set tmp='商贸零售-零售-连锁' where code='600122';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600123';
update shares set tmp='交通运输-公路铁路-铁路运输' where code='600125';
update shares set tmp='钢铁-钢铁-普钢' where code='600126';
update shares set tmp='农林牧渔-农业-农产品加工' where code='600127';
update shares set tmp='综合-综合-综合' where code='600128';
update shares set tmp='医药生物-中药生产-中药生产' where code='600129';
update shares set tmp='信息技术-通信设备-通信终端设备' where code='600130';
update shares set tmp='信息技术-通信运营-通信运营' where code='600131';
update shares set tmp='食品饮料-饮料-啤酒' where code='600132';
update shares set tmp='建筑-基础建设-其他基础建设' where code='600133';
update shares set tmp='基础化工-化学制品-其他化学制品' where code='600135';
update shares set tmp='文化传媒-影视动漫-影视' where code='600136';
update shares set tmp='纺织服装-服装家纺-服装' where code='600137';
update shares set tmp='休闲、生活及专业服务-休闲服务-旅游服务' where code='600138';
update shares set tmp='金融-非银行金融-其他非银行金融' where code='600139';
update shares set tmp='基础化工-化学原料-磷化工' where code='600141';
update shares set tmp='基础化工-化学新材料-化学新材料' where code='600143';
update shares set tmp='商贸零售-贸易-贸易' where code='600145';
update shares set tmp='纺织服装-服装家纺-服装' where code='600146';
update shares set tmp='交运设备-汽车-汽车零部件' where code='600148';
update shares set tmp='商贸零售-贸易-贸易' where code='600149';
update shares set tmp='国防与装备-船舶与海洋装备-船舶' where code='600150';
update shares set tmp='电气设备-电源设备-太阳能' where code='600151';
update shares set tmp='电气设备-其他电气设备-其他电气' where code='600152';
update shares set tmp='交通运输-物流-物流' where code='600153';
update shares set tmp='金融-非银行金融-证券' where code='600155';
update shares set tmp='纺织服装-纺织-棉纺' where code='600156';
update shares set tmp='公用事业-电力-火电' where code='600157';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600158';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600159';
update shares set tmp='基础化工-化学原料-氯化工' where code='600160';
update shares set tmp='医药生物-生物医药-生物医药' where code='600161';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600162';
update shares set tmp='公用事业-电力-其他发电' where code='600163';
update shares set tmp='医药生物-化学制药-化学原料药' where code='600165';
update shares set tmp='交运设备-汽车-商用车' where code='600166';
update shares set tmp='公用事业-电力-其他发电' where code='600167';
update shares set tmp='公用事业-水务-水务' where code='600168';
update shares set tmp='机械设备-专用设备-其他专用机械' where code='600169';
update shares set tmp='建筑-建筑施工-房屋建筑' where code='600170';
update shares set tmp='电子设备-半导体-集成电路' where code='600171';
update shares set tmp='有色金属-金属非金属新材料-非金属新材料' where code='600172';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600173';
update shares set tmp='基础化工-合成纤维及树脂-玻纤' where code='600176';
update shares set tmp='纺织服装-服装家纺-服装' where code='600177';
update shares set tmp='交运设备-汽车-汽车零部件' where code='600178';
update shares set tmp='交通运输-物流-物流' where code='600179';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600180';
update shares set tmp='基础化工-橡胶制品-轮胎' where code='600182';
update shares set tmp='电子设备-电子元件-电子元件' where code='600183';
update shares set tmp='国防与装备-地面装备-地面装备' where code='600184';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600185';
update shares set tmp='食品饮料-食品-调味品' where code='600186';
update shares set tmp='公用事业-水务-水务' where code='600187';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600188';
update shares set tmp='食品饮料-饮料-软饮料' where code='600189';
update shares set tmp='交通运输-港口航运-港口' where code='600190';
update shares set tmp='农林牧渔-农业-农产品加工' where code='600191';
update shares set tmp='电气设备-输变电设备-电气自控设备' where code='600192';
update shares set tmp='综合-综合-综合' where code='600193';
update shares set tmp='农林牧渔-畜牧业-动物用药' where code='600195';
update shares set tmp='医药生物-生物医药-生物医药' where code='600196';
update shares set tmp='食品饮料-饮料-白酒' where code='600197';
update shares set tmp='信息技术-通信设备-通信传输设备' where code='600198';
update shares set tmp='食品饮料-饮料-白酒' where code='600199';
update shares set tmp='医药生物-医药商业-医药商业' where code='600200';
update shares set tmp='农林牧渔-畜牧业-动物用药' where code='600201';
update shares set tmp='机械设备-通用设备-制冷空调设备' where code='600202';
update shares set tmp='电子设备-消费电子设备-消费电子设备' where code='600203';
update shares set tmp='有色金属-稀有金属-其他稀有小金属' where code='600206';
update shares set tmp='综合-综合-综合' where code='600207';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600208';
update shares set tmp='休闲、生活及专业服务-休闲服务-酒店' where code='600209';
update shares set tmp='轻工制造-造纸印刷-包装印刷' where code='600210';
update shares set tmp='医药生物-化学制药-化学制剂' where code='600211';
update shares set tmp='公用事业-电力-其他发电' where code='600212';
update shares set tmp='交运设备-汽车-商用车' where code='600213';
update shares set tmp='房地产-房地产开发-园区开发' where code='600215';
update shares set tmp='医药生物-化学制药-化学原料药' where code='600216';
update shares set tmp='公用事业-环保-环保' where code='600217';
update shares set tmp='机械设备-通用设备-内燃机' where code='600218';
update shares set tmp='有色金属-基本金属-铝' where code='600219';
update shares set tmp='纺织服装-纺织-毛纺' where code='600220';
update shares set tmp='交通运输-航空机场-航空' where code='600221';
update shares set tmp='医药生物-中药生产-中药生产' where code='600222';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600223';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600225';
update shares set tmp='基础化工-化肥农药-农药' where code='600226';
update shares set tmp='医药生物-化学制药-化学制剂' where code='600227';
update shares set tmp='基础化工-化学原料-其他化学原料' where code='600228';
update shares set tmp='文化传媒-平面媒体-平面媒体' where code='600229';
update shares set tmp='基础化工-化学原料-聚氨酯' where code='600230';
update shares set tmp='钢铁-钢铁-普钢' where code='600231';
update shares set tmp='纺织服装-纺织-丝绸' where code='600232';
update shares set tmp='交通运输-物流-物流' where code='600233';
update shares set tmp='房地产-房地产服务-房地产服务' where code='600234';
update shares set tmp='轻工制造-造纸印刷-造纸' where code='600235';
update shares set tmp='公用事业-电力-水电' where code='600236';
update shares set tmp='电子设备-电子元件-电子元件' where code='600237';
update shares set tmp='食品饮料-饮料-其他酒类' where code='600238';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600239';
update shares set tmp='电气设备-电源设备-储能设备' where code='600241';
update shares set tmp='互联网-互联网服务-其他互联网服务' where code='600242';
update shares set tmp='机械设备-通用设备-机床设备' where code='600243';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600246';
update shares set tmp='商贸零售-贸易-贸易' where code='600247';
update shares set tmp='建筑-建筑施工-专业工程' where code='600248';
update shares set tmp='基础化工-化学制品-日用化学品' where code='600249';
update shares set tmp='商贸零售-贸易-贸易' where code='600250';
update shares set tmp='农林牧渔-农业-农产品加工' where code='600251';
update shares set tmp='医药生物-中药生产-中药生产' where code='600252';
update shares set tmp='有色金属-基本金属-铜' where code='600255';
update shares set tmp='化石能源-石油天然气-石油加工' where code='600256';
update shares set tmp='农林牧渔-渔业-淡水渔业' where code='600257';
update shares set tmp='休闲、生活及专业服务-休闲服务-酒店' where code='600258';
update shares set tmp='有色金属-稀有金属-稀土' where code='600259';
update shares set tmp='信息技术-通信设备-通信终端设备' where code='600260';
update shares set tmp='家电-照明设备-照明设备' where code='600261';
update shares set tmp='交运设备-汽车-乘用车' where code='600262';
update shares set tmp='农林牧渔-林业-木材加工' where code='600265';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600266';
update shares set tmp='医药生物-化学制剂-化学原料药' where code='600267';
update shares set tmp='电气设备-输变电设备-电气自控设备' where code='600268';
update shares set tmp='交通运输-公路铁路-高速公路' where code='600269';
update shares set tmp='信息技术-计算机硬件-专用计算机设备' where code='600271';
update shares set tmp='医药生物-医药商业-医药商业' where code='600272';
update shares set tmp='基础化工-化学制品-其他化学制品' where code='600273';
update shares set tmp='农林牧渔-畜牧业-养殖' where code='600275';
update shares set tmp='医药生物-化学制药-化学制剂' where code='600276';
update shares set tmp='基础化工-化学原料-氯碱' where code='600277';
update shares set tmp='商贸零售-贸易-贸易' where code='600278';
update shares set tmp='交通运输-港口航运-港口' where code='600279';
update shares set tmp='商贸零售-零售-百货' where code='600280';
update shares set tmp='商贸零售-贸易-贸易' where code='600281';
update shares set tmp='钢铁-钢铁-普钢' where code='600282';
update shares set tmp='公用事业-水务-水务' where code='600283';
update shares set tmp='建筑-基础建设-路桥建设' where code='600284';
update shares set tmp='医药生物-中药生产-中药生产' where code='600285';
update shares set tmp='商贸零售-贸易-贸易' where code='600287';
update shares set tmp='电子设备-电子设备制造-电子设备制造' where code='600288';
update shares set tmp='信息技术-通信设备-通信配套服务' where code='600289';
update shares set tmp='电气设备-输变电设备-其他输变电设备' where code='600290';
update shares set tmp='金融-非银行金融-保险' where code='600291';
update shares set tmp='公用事业-环保-环保' where code='600292';
update shares set tmp='信息技术-通信设备-通信终端设备' where code='600293';
update shares set tmp='钢铁-铁矿石-铁矿石' where code='600295';
update shares set tmp='交运设备-汽车-汽车销售' where code='600297';
update shares set tmp='食品饮料-食品-食品综合' where code='600298';
update shares set tmp='基础化工-化学制品-其他化学制品' where code='600299';
update shares set tmp='食品饮料-饮料-软饮料' where code='600300';


update shares set tmp='商贸零售-贸易-贸易' where code='600301';
update shares set tmp='机械设备-专用设备-纺织服装机械' where code='600302';
update shares set tmp='交运设备-汽车-乘用车' where code='600303';
update shares set tmp='食品饮料-食品-调味品' where code='600305';
update shares set tmp='商贸零售-零售-百货' where code='600306';
update shares set tmp='钢铁-钢铁-普钢' where code='600307';
update shares set tmp='轻工制造-造纸印刷-造纸' where code='600308';
update shares set tmp='基础化工-化学原料-聚氨酯' where code='600309';
update shares set tmp='公用事业-电力-水电' where code='600310';
update shares set tmp='有色金属-贵金属-黄金' where code='600311';
update shares set tmp='电气设备-输变电设备-其他输变电设备' where code='600312';
update shares set tmp='基础化工-化肥农药-复合肥' where code='600313';
update shares set tmp='基础化工-化学制品-日用化学品' where code='600315';
update shares set tmp='国防与装备-航空航天装备-航空装备' where code='600316';
update shares set tmp='交通运输-港口航运-港口' where code='600317';
update shares set tmp='金融-非银行金融-其他非银行金融' where code='600318';
update shares set tmp='基础化工-化学制品-其他化学制品' where code='600319';
update shares set tmp='机械设备-通用设备-起重运输设备' where code='600320';
update shares set tmp='建材-其他建材-其他建材' where code='600321';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600322';
update shares set tmp='公用事业-环保-环保' where code='600323';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600325';
update shares set tmp='建材-水泥-水泥' where code='600326';
update shares set tmp='交运设备-汽车-汽车销售' where code='600327';
update shares set tmp='基础化工-化学原料-无机盐' where code='600328';
update shares set tmp='医药生物-中药生产-中药生产' where code='600329';
update shares set tmp='有色金属-金属非金属新材料-磁性材料' where code='600330';
update shares set tmp='有色金属-基本金属-铅锌' where code='600331';
update shares set tmp='医药生物-中药生产-中药生产' where code='600332';
update shares set tmp='公用事业-燃气-燃气' where code='600333';
update shares set tmp='交运设备-汽车-汽车销售' where code='600335';
update shares set tmp='家电-白色家电-白色家电' where code='600336';
update shares set tmp='轻工制造-家具-家具制造' where code='600337';
update shares set tmp='有色金属-基本金属-铅锌' where code='600338';
update shares set tmp='化石能源-石油天然气-油田服务' where code='600339';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600340';
update shares set tmp='国防与装备-航空航天装备-航天装备' where code='600343';
update shares set tmp='信息技术-通信设备-通信传输设备' where code='600345';
update shares set tmp='基础化工-合成纤维及树脂-涤纶' where code='600346';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600348';
update shares set tmp='交通运输-公路铁路-高速公路' where code='600350';
update shares set tmp='医药生物-中药生产-中药生产' where code='600351';
update shares set tmp='基础化工-化学制品-印染化学品' where code='600352';
update shares set tmp='电子设备-电子器件-其他电子器件' where code='600353';
update shares set tmp='农林牧渔-农业-种子' where code='600354';
update shares set tmp='信息技术-计算机软件-行业应用软件' where code='600355';
update shares set tmp='轻工制造-造纸印刷-造纸' where code='600356';
update shares set tmp='文化传媒-营销服务-营销服务' where code='600358';
update shares set tmp='农林牧渔-农业-种植业' where code='600359';
update shares set tmp='电子设备-半导体-半导体分立器件' where code='600360';
update shares set tmp='商贸零售-零售-超市' where code='600361';
update shares set tmp='有色金属-基本金属-铜' where code='600362';
update shares set tmp='电子设备-光电子器件-LED' where code='600363';
update shares set tmp='食品饮料-饮料-葡萄酒' where code='600365';
update shares set tmp='有色金属-金属非金属新材料-磁性材料' where code='600366';
update shares set tmp='基础化工-化学原料-无机盐' where code='600367';
update shares set tmp='交通运输-公路铁路-高速公路' where code='600368';
update shares set tmp='金融-非银行金融-证券' where code='600369';
update shares set tmp='纺织服装-纺织-印染' where code='600370';
update shares set tmp='农林牧渔-农业-种子' where code='600371';
update shares set tmp='国防与装备-航空航天装备-航空装备' where code='600372';
update shares set tmp='文化传媒-平面媒体-平面媒体' where code='600373';
update shares set tmp='交运设备-汽车-商用车' where code='600375';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600376';
update shares set tmp='交通运输-公路铁路-高速公路' where code='600377';
update shares set tmp='基础化工-化学制品-其他化学制品' where code='600378';
update shares set tmp='电气设备-其他电气设备-其他电气设备' where code='600379';
update shares set tmp='金融-非银行金融-其他非银行金融' where code='600380';
update shares set tmp='食品饮料-食品-食品综合' where code='600381';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600382';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600383';
update shares set tmp='轻工制造-珠宝首饰-珠宝首饰' where code='600385';
update shares set tmp='交运设备-汽车-汽车服务' where code='600386';
update shares set tmp='基础化工-化肥农药-复合肥' where code='600387';
update shares set tmp='机械设备-专用设备-环保设备' where code='600388';
update shares set tmp='基础化工-农药化肥-农药' where code='600389';
update shares set tmp='金融-非银行金融-其他非银行金融' where code='600390';
update shares set tmp='国防与装备-航空航天装备-航天装备' where code='600391';
update shares set tmp='有色金属-稀有金属-稀土' where code='600392';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600393';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600395';
update shares set tmp='公用事业-电力-火电' where code='600396';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600397';
update shares set tmp='纺织服装-服装家纺-服装' where code='600398';
update shares set tmp='钢铁-钢铁-特钢' where code='600399';
update shares set tmp='纺织服装-服装家纺-服装' where code='600400';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600403';
update shares set tmp='电气设备-电源设备-储能设备' where code='600405';
update shares set tmp='电气设备-输变电设备-电气自控设备' where code='600406';
update shares set tmp='化石能源-煤炭-焦炭' where code='600408';
update shares set tmp='基础化工-化学原料-纯碱' where code='600409';
update shares set tmp='信息技术-计算机软件-其他软件服务' where code='600410';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600415';
update shares set tmp='商贸零售-贸易-贸易' where code='600416';
update shares set tmp='交运设备-汽车-乘用车' where code='600418';
update shares set tmp='食品饮料-食品-乳制品' where code='600419';
update shares set tmp='医药生物-化学制药-化学制剂' where code='600420';
update shares set tmp='休闲、生活及专业服务-专业服务-其他专业服务' where code='600421';
update shares set tmp='医药生物-中药生产-中药生产' where code='600422';
update shares set tmp='基础化工-化肥农药-氮肥' where code='600423';
update shares set tmp='建材-水泥-水泥' where code='600425';
update shares set tmp='基础化工-化肥农药-氮肥' where code='600426';
update shares set tmp='交通运输-港口航运-航运' where code='600428';
update shares set tmp='食品饮料-食品-乳制品' where code='600429';
update shares set tmp='轻工制造-造纸印刷-造纸' where code='600433';
update shares set tmp='国防与装备-地面装备-地面装备' where code='600435';
update shares set tmp='医药生物-中药生产-中药生产' where code='600436';
update shares set tmp='农林牧渔-畜牧业-饲料' where code='600438';
update shares set tmp='轻工制造-其他轻工-其他轻工' where code='600439';
update shares set tmp='机械设备-通用设备-其他通用机械' where code='600444';
update shares set tmp='信息技术-计算机软件-行业应用软件' where code='600446';
update shares set tmp='纺织服装-纺织-印染' where code='600448';
update shares set tmp='建材-水泥-水泥' where code='600449';
update shares set tmp='公用事业-电力-火电' where code='600452';
update shares set tmp='信息技术-计算机软件-其他软件服务' where code='600455';
update shares set tmp='有色金属-稀有金属-其他稀有小金属' where code='600456';
update shares set tmp='交运设备-汽车-汽车销售' where code='600458';
update shares set tmp='有色金属-稀有金属-其他稀有小金属' where code='600459';
update shares set tmp='电子设备-半导体-集成电路' where code='600460';
update shares set tmp='公用事业-水务-水务' where code='600461';
update shares set tmp='电子设备-电子器件-其他电子器件' where code='600462';
update shares set tmp='房地产-房地产开发-园区开发' where code='600463';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600466';
update shares set tmp='农林牧渔-渔业-海洋渔业' where code='600467';
update shares set tmp='电气设备-输变电设备-其他输变电设备' where code='600468';
update shares set tmp='基础化工-橡胶制品-轮胎' where code='600469';
update shares set tmp='基础化工-化肥农药-磷肥' where code='600470';
update shares set tmp='电气设备-电源设备-综合电力设备商' where code='600475';
update shares set tmp='信息技术-计算机软件-行业应用软件' where code='600476';
update shares set tmp='建筑-钢结构-钢结构' where code='600477';
update shares set tmp='有色金属-金属非金属新材料-电池材料' where code='600478';
update shares set tmp='医药生物-中药生产-中药生产' where code='600479';
update shares set tmp='交运设备-汽车-汽车零部件' where code='600480';
update shares set tmp='机械设备-通用设备-制冷空调设备' where code='600481';
update shares set tmp='国防与装备-船舶与海洋装备-船舶制造' where code='600482';
update shares set tmp='公用事业-电力-火电' where code='600483';
update shares set tmp='基础化工-化肥农药-农药' where code='600486';
update shares set tmp='信息技术-通信设备-通信传输设备' where code='600487';
update shares set tmp='医药生物-化学制药-化学原料药' where code='600488';
update shares set tmp='有色金属-贵金属-黄金' where code='600489';
update shares set tmp='有色金属-基本金属-铜' where code='600490';
update shares set tmp='建筑-基础建设-其他基础建设' where code='600491';
update shares set tmp='纺织服装-纺织-棉纺' where code='600493';
update shares set tmp='交运设备-铁路设备-铁路专用设备' where code='600495';
update shares set tmp='建筑-钢结构-钢结构' where code='600496';
update shares set tmp='有色金属-基本金属-铅锌' where code='600497';
update shares set tmp='信息技术-通信设备-通信传输设备' where code='600498';
update shares set tmp='机械设备-专用设备-其他专用机械' where code='600499';
update shares set tmp='基础化工-化学原料-其他化学原料' where code='600500';
update shares set tmp='机械设备-通用设备-基础件' where code='600501';
update shares set tmp='建筑-建筑施工-专业工程' where code='600502';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600503';
update shares set tmp='公用事业-电力-水电' where code='600505';
update shares set tmp='农林牧渔-农业-种植业' where code='600506';
update shares set tmp='钢铁-钢铁-普钢' where code='600507';
update shares set tmp='化石能源-煤炭-煤炭开采洗选' where code='600508';
update shares set tmp='公用事业-电力-其他发电' where code='600509';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600510';
update shares set tmp='医药生物-医药商业-医药商业' where code='600511';
update shares set tmp='建筑-建筑施工-专业工程' where code='600512';
update shares set tmp='医药生物-化学制药-化学制剂' where code='600513';
update shares set tmp='房地产-房地产开发-房地产开发' where code='600515';
update shares set tmp='有色金属-金属非金属新材料-非金属新材料' where code='600516';
update shares set tmp='电气设备-输变电设备-其他输变电设备' where code='600517';
update shares set tmp='医药生物-中药生产-中药生产' where code='600518';
update shares set tmp='食品饮料-饮料-白酒' where code='600519';
update shares set tmp='机械设备-通用设备-其他通用机械' where code='600520';
update shares set tmp='医药生物-化学制药-化学原料药' where code='600521';
update shares set tmp='信息技术-通信设备-通信传输设备' where code='600522';
update shares set tmp='交运设备-汽车-汽车零部件' where code='600523';
update shares set tmp='有色金属-金属非金属新材料-电池材料' where code='600525';
update shares set tmp='机械设备-专用设备-环保设备' where code='600526';
update shares set tmp='基础化工-合成纤维及树脂-涤纶' where code='600527';
update shares set tmp='建筑-钢结构-钢结构' where code='600528';
update shares set tmp='医药生物-医疗器械-医疗器械' where code='600529';
update shares set tmp='医药生物-生物医药-生物医药' where code='600530';
update shares set tmp='有色金属-基本金属-铅锌' where code='600531';
update shares set tmp='有色金属-基本金属-铅锌' where code='600531';