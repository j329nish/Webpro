use j329nish;
drop table if exists user;
create table user ( 
 level int not null, 
 id int not null, 
 dt1 datetime, 
 dt2 datetime, 
 primary key(level, id) 
);