create table if not exists b_iplogicberu_profile
(
	ID int(11) NOT NULL auto_increment,
	NAME varchar(255) NOT NULL,
	ACTIVE char(1) NULL default 'Y',
	SORT int(5) NULL default 100,
	SITE varchar(2) NOT NULL,
	SCHEME varchar(3) NOT NULL,
	IBLOCK_TYPE varchar(50) NOT NULL,
	IBLOCK_ID int(11) NOT NULL,
	COMPANY VARCHAR(255) NULL,
	TAX_SYSTEM varchar(14) NULL,
	VAT VARCHAR(6) NULL,
	BASE_URL varchar(100) NULL,
	CLIENT_ID varchar(255) NULL,
	COMPAIN_ID varchar(100) NULL,
	SEND_TOKEN varchar(255) NULL,
	GET_TOKEN varchar(255) NULL,
	STORE VARCHAR(255) NULL,
	BUSINESS_ID varchar(100) NULL,
	USER_ID INT(11) NULL,
	DELIVERY INT(11) NULL,
	PAYMENTS INT(11) NULL,
	PERSON_TYPE INT(2) NULL,
	STATUSES TEXT NULL,
	PAYMENT_METHODS varchar(255) NULL,
	PRIMARY KEY (ID)
);

create table if not exists b_iplogicberu_prop
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	NAME varchar(255) NOT NULL,
	TYPE varchar(255) NULL,
	VALUE varchar(255) NULL,
	PRIMARY KEY (ID),
	INDEX (PROFILE_ID)
);

create table if not exists b_iplogicberu_attr
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	PROP_ID varchar(11) NOT NULL,
	NAME varchar(255) NOT NULL,
	TYPE varchar(255) NULL,
	VALUE varchar(255) NULL,
	PRIMARY KEY (ID),
	INDEX (PROFILE_ID)
);

create table if not exists b_iplogicberu_order
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	EXT_ID int(64) NOT NULL,
	ORDER_ID int(11) NOT NULL,
	STATE varchar(255) NOT NULL,
	STATE_CODE varchar(255) NULL,
	UNIX_TIMESTAMP int(15) NOT NULL,
	HUMAN_TIME varchar(19) NOT NULL,
	FAKE char(1) NOT NULL default 'N',
	SHIPMENT_ID int(64) NULL,
	SHIPMENT_DATE varchar(10) NULL,
	SHIPMENT_TIMESTAMP int(15) NULL,
	DELIVERY_NAME varchar(255) NULL,
	DELIVERY_ID varchar(255) NULL,
	BOXES_SENT char(1) NOT NULL default 'N',
	READY_TIME int(15) NULL default 0,
	COURIER varchar(255) NULL,
	PRIMARY KEY (ID),
	INDEX (EXT_ID)
);

create table if not exists b_iplogicberu_api_log
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	UNIX_TIMESTAMP int(15) NOT NULL,
	HUMAN_TIME varchar(19) NOT NULL,
	TYPE char(2) NOT NULL,
	STATE char(2) NOT NULL,
	URL varchar(255) NOT NULL,
	REQUEST_TYPE varchar(6) NOT NULL,
	REQUEST text NULL,
	REQUEST_H text NOT NULL,
	RESPOND text NULL,
	RESPOND_H text NULL,
	STATUS int(3) NULL,
	ERROR varchar(255) NULL,
	PRIMARY KEY (ID)
);

create table if not exists b_iplogicberu_task
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	UNIX_TIMESTAMP int(15) NOT NULL,
	HUMAN_TIME varchar(19) NOT NULL,
	TYPE varchar(20) NULL,
	STATE char(2) NOT NULL,
	ENTITY_ID varchar(255) NULL,
	TRYING int(3) NULL,
	PRIMARY KEY (ID),
	INDEX (UNIX_TIMESTAMP)
);

create table if not exists b_iplogicberu_error
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	UNIX_TIMESTAMP int(15) NOT NULL,
	HUMAN_TIME varchar(19) NOT NULL,
	ERROR varchar(255) NULL,
	DETAILS mediumtext NOT NULL,
	STATE char(2) NOT NULL,
	LOG int(15) NULL,
	PRIMARY KEY (ID)
);

create table if not exists b_iplogicberu_product
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	PRODUCT_ID int(11) NULL,
	SKU_ID varchar(150) NULL,
	MARKET_SKU bigint(64) NULL,
	NAME text NULL,
	VENDOR varchar(255) NULL,
	AVAILABILITY char(1) NULL DEFAULT "N",
	STATE varchar(100) NULL,
	REJECT_REASON varchar(255) NULL,
	REJECT_NOTES text NULL,
	DETAILS mediumtext NULL,
	PRICE varchar(12) NULL,
	OLD_PRICE VARCHAR(12) NULL,
    STOCK_FIT VARCHAR(5) NULL,
    PRICE_TIME VARCHAR(19) NULL,
    STOCK_TIME VARCHAR(19) NULL,
	HIDDEN char(1) NULL DEFAULT "N",
	FOR_DELETE char(1) NULL DEFAULT "N",
	PRIMARY KEY (ID),
	INDEX (SKU_ID),
	INDEX (PROFILE_ID)
);

create table if not exists b_iplogicberu_box
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	EXT_ID int(64) NULL DEFAULT '0',
	ORDER_ID int(11) NOT NULL,
	NUM int(3) NOT NULL,
	WEIGHT int(64) NOT NULL,
	WIDTH int(64) NOT NULL,
	HEIGHT int(64) NOT NULL,
	DEPTH int(64) NOT NULL,
	PRIMARY KEY (ID),
	INDEX (ORDER_ID)
);

create table if not exists b_iplogicberu_box_link
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	ORDER_ID int(11) NOT NULL,
	BOX_ID int(11) NOT NULL,
	SKU_ID varchar(150) NOT NULL,
	PRIMARY KEY (ID)
);

create table if not exists b_iplogicberu_interval
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	DELIVERY_ID int(11) NOT NULL,
	DAY varchar(3) NOT NULL,
	TIME_FROM varchar(5) NOT NULL,
	TIME_TO varchar(5) NOT NULL,
	PRIMARY KEY (ID),
	INDEX (ID)
);

create table if not exists b_iplogicberu_outlet
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	DELIVERY_ID int(11) NOT NULL,
	NAME varchar(255) NOT NULL,
	CODE int(64) NOT NULL,
	PRIMARY KEY (ID),
	INDEX (CODE)
);

create table if not exists b_iplogicberu_delivery
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	ACTIVE char(1) NULL default 'Y',
	SORT int(5) NULL default 100,
	TYPE varchar(8) NOT NULL,
	NAME varchar(50) NOT NULL,
	PAYMENT_ALLOW varchar(1) NOT NULL DEFAULT 'N',
    DAY_FROM varchar(3) NOT NULL,
    DAY_TO varchar(3) NOT NULL,
    PRIMARY KEY (ID),
	INDEX (PROFILE_ID)
);

create table if not exists b_iplogicberu_holiday
(
	ID int(11) NOT NULL auto_increment,
	PROFILE_ID int(5) NOT NULL,
	DELIVERY_ID int(11) NOT NULL,
	DATE varchar(10) NOT NULL,
	TIMESTAMP int(15) NOT NULL,
	PRIMARY KEY (ID),
	INDEX (TIMESTAMP)
);

create table if not exists b_iplogicberu_rights
(
	ID int(10) NOT NULL auto_increment,
	ENTITY_TYPE varchar(20) NOT NULL,
	ENTITY_ID int(10) NOT NULL,
	GROUP_ID int(10) NOT NULL,
	TASK_ID int(10) NOT NULL,
	PRIMARY KEY (ID),
	INDEX (ENTITY_ID),
	INDEX (GROUP_ID)
);