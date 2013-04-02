/*
-rw-------  1 www  wheel  224193 May 28 22:45 TIS24___mail-print-sample.pdf
-rw-------  1 www  wheel  110558 May 22 18:18 TIS29___iPlanetMacConduit.sit
-rw-------  1 www  wheel  176311 May  1 21:23 TIS37___whitespace.pdf
-rw-------  1 www  wheel  132183 May  1 21:30 TIS38___notext.pdf
-rw-------  1 www  wheel    6885 May  8 16:28 TIS43___FMPlogs030508.txt
-rw-------  1 www  wheel     578 Apr 30 12:41 TIS4___d20_login.txt
-rw-------  1 www  wheel     167 Apr 30 12:43 TIS4___queries.txt
-rw-------  1 www  wheel    3288 May 15 10:51 TIS57___FolderError.gif
-rw-------  1 www  wheel  131087 May 28 23:34 TIS62___whitespace2.pdf
-rw-------  1 www  wheel   91636 Jul 24 14:11 TIS76___badtext.pdf
*/
--insert into bt_attachments values (0,24,'mail-print-sample.pdf','224193 Bytes',load_file('/tmp/TIS24___mail-print-sample.pdf'),now());
insert into bt_attachments values (0,29,'iPlanetMacConduit.sit','110558 Bytes',load_file('/tmp/TIS29___iPlanetMacConduit.sit'),now());
insert into bt_attachments values (0,37,'whitespace.pdf','176311 Bytes',load_file('/tmp/TIS37___whitespace.pdf'),now());
insert into bt_attachments values (0,38,'notext.pdf','132183 Bytes',load_file('/tmp/TIS38___notext.pdf'),now());
insert into bt_attachments values (0,43,'FMPlogs030508.txt','6885 Bytes',load_file('/tmp/TIS43___FMPlogs030508.txt'),now());
insert into bt_attachments values (0,4,'d20_login.txt','578 Bytes',load_file('/tmp/TIS4___d20_login.txt'),now());
insert into bt_attachments values (0,4,'queries.txt','167 Bytes',load_file('/tmp/TIS4___queries.txt'),now());
insert into bt_attachments values (0,57,'FolderError.gif','3288 Bytes',load_file('/tmp/TIS57___FolderError.gif'),now());
insert into bt_attachments values (0,62,'whitespace2.pdf','131087 Bytes',load_file('/tmp/TIS62___whitespace2.pdf'),now());
insert into bt_attachments values (0,76,'badtext.pdf','91636 Bytes',load_file('/tmp/TIS76___badtext.pdf'),now());
