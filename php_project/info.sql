/*
	num: 제품 번호(주 키, 자동 증가)
	name: 제품 이름
	cost: 제품 가격 
	type: 제품 유형 
*/

create table product_unit_info
(
	num int not null auto_increment, 
	name char(200) not null, 
	cost long not null, 
	type char(200) not null, 
	primary key(num)
);

/*
	num: 제품세트 번호(주 키, 자동 증가)
	name: 제품 세트 이름
	cost: 제품 세트 가격
	type: 제품 세트 유형
	comp1~comp10: 세트 구성품의 번호 1~10(최대 10개까지 구성 가능)
*/

create table product_set_info
(
	num int not null auto_increment, 
	name char(200) not null, 
	cost long not null, 
	type char(200) not null, 
	comp1_num int, 
	comp2_num int, 
	comp3_num int, 
	comp4_num int, 
	comp5_num int, 
	comp6_num int, 
	comp7_num int, 
	comp8_num int, 
	comp9_num int, 
	comp10_num int, 
	primary key(num)
);

