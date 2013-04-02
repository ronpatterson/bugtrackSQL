-- bugtrack.sql

-- Revision 0.3, 15-Aug-2003, 13-Oct-2006

-- Ron Patterson, WildDog Design

create table bt_type (
	cd				char(1) not null primary key,
	descr			varchar(255),
	active			char(1)
);

create table bt_bugs (
	id				integer not null primary key auto_increment,
	descr			varchar(255),
	product			varchar(255),
	user_nm			varchar(50), -- new 10/13/06
	bug_type		char(1), -- references bt_type,
	status			char(1), -- o=open, h=hold, w=working, c=closed
	priority		char(1), -- 1=high, 2=normal, 3=low
	comments		text,
	solution		text,
	assigned_to		varchar(50), -- references d20_person,
	bug_id			varchar(20), -- <group><id>
	entry_dtm		datetime,
	update_dtm		datetime,
	closed_dtm		datetime
);

create table bt_worklog (
	id				integer not null primary key auto_increment,
	bug_id			integer, -- references bt_bugs,
	user_nm			varchar(50), -- new 10/13/06
	comments		text,
	entry_dtm		datetime
);
create index bt_bid_idx on bt_worklog(bug_id);

create table bt_attachments (
	id				integer not null primary key auto_increment,
	bug_id			integer, -- references bt_bugs,
	file_name		varchar(100),
	file_size		varchar(30),
	attachment		mediumblob,
	entry_dtm		datetime
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
