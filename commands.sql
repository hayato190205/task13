create database nowalltask13;

use nowalltask13;

grant all on nowalltask13.* to testuser@localhost identified by '9999';

create table users(
id int primary key auto_increment,
name varchar(255),
email varchar(255),
created_at datetime
);

create table image(
id int primary key auto_increment,
name varchar(255),
title text,
file_name varchar(255),
created_at datetime,
updated_at datetime
);

