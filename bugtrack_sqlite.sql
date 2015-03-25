-- bugtrack.sql (SQLite version)

-- Revision 0.4, 15-Aug-2003, 13-Oct-2006, 8-Mar-2013

-- Ron Patterson, WildDog Design

create table bt_type (
    cd              text not null primary key,
    descr           text,
    active          char(1)
);

create table bt_groups (
    cd              text not null primary key,
    descr           text,
    active          char(1)
);

create table bt_users (
    uid             text not null primary key,
    lname           text,
    fname           text,
    email           text,
    active          char(1),
    roles           text,
    pw              text,
    bt_group        text
);

create table bt_bugs (
    id              integer not null primary key autoincrement,
    descr           text,
    product         text,
    user_nm         text, -- new 10/13/06
    bug_type        char(1), -- references bt_type,
    status          char(1), -- o=open, h=hold, w=working, c=closed
    priority        char(1), -- 1=high, 2=normal, 3=low
    comments        text,
    solution        text,
    assigned_to     text, -- references d20_person,
    bug_id          text, -- <group><id>
    entry_dtm       datetime,
    update_dtm      datetime,
    closed_dtm      datetime
);
create index btg_bid_idx on bt_bugs(bug_id);

create table bt_worklog (
    id              integer not null primary key autoincrement,
    bug_id          integer, -- references bt_bugs,
    user_nm         text, -- new 10/13/06
    comments        text,
    wl_public       char(1), -- y/n
    entry_dtm       datetime
);
create index btw_bid_idx on bt_worklog(bug_id);

-- attach file structure
-- /usr/local/data
-- /usr/local/data/{first 3 chars of hash}/{hash}

create table bt_attachments (
    id              integer not null primary key autoincrement,
    bug_id          integer, -- references bt_bugs,
    file_name       text,
    file_size       text,
    file_hash       text,
    entry_dtm       datetime
);

insert into bt_type values ('h','Hardware issue','y');
insert into bt_type values ('s','Software issue','y');
insert into bt_type values ('d','Database issue','y');
insert into bt_type values ('g','General issue','y');
insert into bt_type values ('n','Network issue','y');
insert into bt_type values ('m','EMail issue','y');
insert into bt_type values ('c','Calendar issue','y');
insert into bt_type values ('b','BugTrack issue','y');
insert into bt_type values ('w','Web browser issue','y');
insert into bt_type values ('x','Web server issue','y');
insert into bt_type values ('p','Desktop/laptop PC issue','y');
insert into bt_type values ('a','Desktop/laptop Mac issue','y');
insert into bt_type values ('z','Mobile/Palm/Pocket PC issue','y');
insert into bt_type values ('u','Unknown issue','y');
insert into bt_type values ('e','Enhancement','y');

insert into bt_groups values ('GC','Generic Company','y');
insert into bt_groups values ('WDD','WildDog Design','y');
insert into bt_groups values ('DOC','Dept of Corrections','y');

insert into bt_users values ('rlpatter','Patterson','Ron','ronlpatterson@me.com','y','admin','','WDD');
insert into bt_users values ('admin','Administrator','BugTrack','ronlpatterson@me.com','y','admin','','WDD');
