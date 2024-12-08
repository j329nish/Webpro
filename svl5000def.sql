use j329nish;
drop table if exists svl5000;
create table svl5000 (
    level int not null,
    id int not null,
    word char(80),
    meaning text,
    primary key(level, id)
);