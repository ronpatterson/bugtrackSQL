<?php
//require("../session.php");
// buglist2.php
// Ron Patterson, WildDog Design
// PDO version
// return a standard <select> for a lookup table
// connect to the database 
require("bugcommon.php");
$max=50;
$start=isset($_GET["start"]) ? $_GET["start"] : "";
if ($start == "") $start=0;
$ttl="BugTrack Attachments List";
$dbh = $db->getHandle();
?>
Click on the Bug ID to see the bug record.<br>
Click on the File name to see or download the file.
<p>
<table border="1" cellspacing="0" cellpadding="3" class="worklog">
<tr><th>Bug ID</th><th>File name</th><th>Size</th><th>Date uploaded</th>
</tr>
<?php
// execute query 
$sql = "select count(*) from bt_attachments";
$count = $dbh->querySingle($sql);
//echo $count;
$cnt2=0; $out="";
if ($count > 0) {
	$sql = "select id,bug_id,file_name,file_size,entry_dtm from bt_attachments order by bug_id limit $start,$max";
	$stmt = $dbh->query($sql);
	// loop thru all rows
    while ($arr = $stmt->fetchArray(SQLITE3_NUM)) {
		list($aid,$bid,$fname,$size,$entry_dtm) = $arr;
		$entry_dt = date("m/d/Y g:i a",strtotime($entry_dtm));
		$out .= <<<END
<tr valign="top">
	<td align="center"><a href="#" onclick="return bt_show(event,{$bid},'{$nextlink}');"><b>{$bid}</b></a></td>
	<td><a href="get_file.php?id={$aid}"><b>{$fname}</b></a></td>
	<td>{$size}</td>
	<td>{$entry_dt}</td>
</tr>
END;
		$cnt2++;
	}
	echo $out;
}
$start+=$cnt2;
?>
</tr></table><br>
<b><?php echo $cnt; ?> Attachments found. (<?php echo $cnt2; ?> shown)</b><p>
<?php
//if ($start < $cnt) echo "<a href='buglist2.php?$nextlink&start=$start'><b>Next -&gt;</b></a><p>\n";
?>
<a href="#" onclick="return bt_bugadd(event,'add');"><b>Add a new bug entry</b></a>
<p>
<!-- <a href=viewphp1.php><b>View PHP code modules</b></a--><p>
<br>
</center>
