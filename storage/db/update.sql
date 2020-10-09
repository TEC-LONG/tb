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
    PRIMARY KEY (`id`),
    KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票信息表';


CREATE TABLE `plate` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `name` varchar(30) NOT NULL DEFAULT '' COMMENT '板块名称',
    `code` varchar(30) NOT NULL DEFAULT '' COMMENT '板块代码',
    `type` tinyint NOT NULL DEFAULT 0 COMMENT '板块类型；0=无；1=行业板块；2=概念板块；3=地区板块',
    `come_from` tinyint NOT NULL DEFAULT 0 COMMENT '板块来源参照；0=自定义；1=东海通；2=益盟-智盈；3=通达信',
    `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '同概念别名，多个以逗号分隔',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票行业板块表';


CREATE TABLE `shares__plate` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `plate__id` tinyint NOT NULL DEFAULT 0 COMMENT '行业板块表id',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
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

alter table plate_prosperity_index_statistics_day add `plate_volume` varchar(50) NOT NULL DEFAULT '' COMMENT '板块总成交量';


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

alter table plate_prosperity_index_statistics_month add `plate_volume` varchar(50) NOT NULL DEFAULT '' COMMENT '板块总成交量';


CREATE TABLE `shares_details_byday` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
    `shares__id` int NOT NULL DEFAULT 0 COMMENT '股票信息表id',
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
    `step` tinyint NOT NULL DEFAULT 0 COMMENT '所处步骤位置；0=无；1=拆分完原始数据',
    `ma5_price` varchar(30) NOT NULL DEFAULT '' COMMENT '5日均价',
    `ma10_price` varchar(30) NOT NULL DEFAULT '' COMMENT '10日均价',
    `ma20_price` varchar(30) NOT NULL DEFAULT '' COMMENT '20日均价',
    `ma30_price` varchar(30) NOT NULL DEFAULT '' COMMENT '30日均价',
    `ma60_price` varchar(30) NOT NULL DEFAULT '' COMMENT '60日均价',
    `ma120_price` varchar(30) NOT NULL DEFAULT '' COMMENT '120日均价',
    `ma240_price` varchar(30) NOT NULL DEFAULT '' COMMENT '240日均价',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_active_date_timestamp` (`active_date_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='股票详情表（按天记录)';


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
    `shares__id` int NOT NULL DEFAULT 0 COMMENT '股票信息表id',
    `shares_details_byday__id` int NOT NULL DEFAULT 0 COMMENT '股票详情表id',
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
    `ma_angle_time` int NOT NULL DEFAULT 0 COMMENT '均线角统计日期时间戳',
    `ma5_price` varchar(30) NOT NULL DEFAULT '' COMMENT '5日均价',
    `ma4_price` varchar(30) NOT NULL DEFAULT '' COMMENT '4日均价',
    `ma10_price` varchar(30) NOT NULL DEFAULT '' COMMENT '10日均价',
    `ma9_price` varchar(30) NOT NULL DEFAULT '' COMMENT '9日均价',
    `ma15_price` varchar(30) NOT NULL DEFAULT '' COMMENT '15日均价',
    `ma14_price` varchar(30) NOT NULL DEFAULT '' COMMENT '14日均价',
    `ma20_price` varchar(30) NOT NULL DEFAULT '' COMMENT '20日均价',
    `ma19_price` varchar(30) NOT NULL DEFAULT '' COMMENT '19日均价',
    `ma30_price` varchar(30) NOT NULL DEFAULT '' COMMENT '30日均价',
    `ma29_price` varchar(30) NOT NULL DEFAULT '' COMMENT '29日均价',
    `ma60_price` varchar(30) NOT NULL DEFAULT '' COMMENT '60日均价',
    `ma59_price` varchar(30) NOT NULL DEFAULT '' COMMENT '59日均价',
    `ma120_price` varchar(30) NOT NULL DEFAULT '' COMMENT '120日均价',
    `ma119_price` varchar(30) NOT NULL DEFAULT '' COMMENT '119日均价',
    `ma240_price` varchar(30) NOT NULL DEFAULT '' COMMENT '240日均价',
    `ma239_price` varchar(30) NOT NULL DEFAULT '' COMMENT '239日均价',
    `ma_price_time` int NOT NULL DEFAULT 0 COMMENT '均价统计日期时间戳',
    `created_time` int NOT NULL DEFAULT 0 COMMENT '数据创建时间',
    PRIMARY KEY (`id`),
    KEY `idx_shares__id` (`shares__id`),
    KEY `idx_shares_details_byday__id` (`shares_details_byday__id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='每日详情均线相关统计表';


truncate shares;
truncate shares_details_byday;

alter table shares_details_byday ADD INDEX `idx_shares__id` (`shares__id`) USING BTREE;


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


/**
 * plate__id
 */
# 电力
select id from plate where code='991003';


/**
 * shares__id
 */
# 电力
select id from shares where code in (
    688390
    601778
    000591
    000862
    601908
    601619
    300853
    
)